<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Engineer;
use App\Models\Tool;
use App\Models\Kunjungan;
use App\Models\AktivitasPekerjaan;
use App\Models\Dokumentasi;
use App\Models\Laporan;
use App\Models\BuktiPenyelesaian;
use App\Models\Pengeluaran;
use App\Models\CustomerSite;
use App\Models\KunjunganKonfirmasi;
use App\Models\PeminjamanTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KunjunganController extends Controller
{
    /**
     * Cari kunjungan berdasarkan NOMOR (bukan id angka),
     * karena URL memakai nomor kunjungan, misal: /kunjungan/vmsmit26001
     */
    private function cariKunjungan(string $nomor)
    {
        return Kunjungan::where('nomor', $nomor)->firstOrFail();
    }

    /**
     * Folder upload HARUS di webroot supaya file bisa dibuka dari browser.
     * Di hosting Oriper aplikasi ada di folder vms/ sedangkan webroot-nya
     * public_html di sebelahnya; public_path() menunjuk ke vms/public yang
     * BUKAN webroot sehingga foto dokumentasi tidak tampil (QA #19).
     * Deteksi layout itu di sini; di lokal tetap public_path biasa.
     */
    private function webUploadPath(string $sub)
    {
        $publicHtml = dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html';
        if (is_dir($publicHtml) && realpath(public_path()) !== realpath($publicHtml)) {
            return $publicHtml . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $sub;
        }
        return public_path('uploads' . DIRECTORY_SEPARATOR . $sub);
    }

    // Sinkronkan daftar konfirmasi: lead + support dapat status 'menunggu'
    // Engineer yang sudah ada tidak di-reset (kecuali diganti)
    private function syncKonfirmasi(Kunjungan $kunjungan, array $supportIds = [])
    {
        $engineerIds = array_unique(array_merge([$kunjungan->id_engineer], $supportIds));
        foreach ($engineerIds as $eid) {
            if (!$eid) continue;
            KunjunganKonfirmasi::firstOrCreate(
                ['id_kunjungan' => $kunjungan->id_kunjungan, 'id_engineer' => $eid],
                ['status' => 'menunggu']
            );
        }
        // Hapus konfirmasi engineer yang sudah tidak terlibat
        KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->whereNotIn('id_engineer', $engineerIds)
            ->delete();
    }

    // Cek peran engineer yang login terhadap kunjungan: 'lead', 'support', atau null
    private function peranEngineer(Kunjungan $kunjungan)
    {
        $user = Auth::user();
        if ($user->id_role != 3) return null;
        $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
        if (!$engineer) return null;
        if ($kunjungan->id_engineer == $engineer->id_engineer) return 'lead';
        if ($kunjungan->supportEngineers()->where('engineers.id_engineer', $engineer->id_engineer)->exists()) return 'support';
        return null;
    }

    // Pastikan hanya lead engineer yang boleh melakukan aksi ini

    // 1. Tampilkan List Kunjungan
    public function index(Request $request)
    {
        $user = Auth::user();
        // OPTIMASI: Tambahkan 'site' di eager loading
        $query = Kunjungan::with(['customer', 'site', 'engineer.user', 'tools', 'supportEngineers.user']);

        // Jika engineer, filter hanya kunjungan miliknya (sebagai lead ATAU support)
        if ($user->id_role == 3) {
            $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
            if ($engineer) {
                $query->where(function($q) use ($engineer) {
                    $q->where('id_engineer', $engineer->id_engineer)
                      ->orWhereHas('supportEngineers', function($sq) use ($engineer) {
                          $sq->where('engineers.id_engineer', $engineer->id_engineer);
                      });
                });
            }
        }

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', '%' . $search . '%')
                  ->orWhere('pekerjaan', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('nama_perusahaan', 'like', '%' . $search . '%');
                  });
            });
        }

        // Fitur Sortir
        $sort = $request->get('sort', 'terbaru');
        if ($sort == 'terlama') {
            $query->oldest('id_kunjungan');
        } else {
            $query->latest('id_kunjungan');
        }

        $kunjunganList = $query->paginate(10)->appends($request->all());
        
        $customers = Customer::orderBy('nama_perusahaan')->get();
        $engineers = Engineer::with('user')
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'engineers.id_pengguna')
            ->where('engineers.status_ketersediaan', 'Tersedia')
            ->orderBy('pengguna.nama')
            ->select('engineers.*')
            ->get();
        $tools = Tool::where('status_ketersediaan', 'Tersedia')->orderBy('nama_alat')->get();
        // QA #9: catatan tugas aktif per engineer untuk form penugasan
        $tugasAktif = Kunjungan::tugasAktifPerEngineer();

        return view('kunjungan.index', compact('kunjunganList', 'customers', 'engineers', 'tools', 'tugasAktif'));
    }

    /**
     * Validasi tim kunjungan (QA #4 & #10):
     * - lead tidak boleh merangkap jadi support di kunjungan yang sama
     * - tidak boleh ada engineer yang bentrok jadwal di tanggal + jam sama
     * Mengembalikan pesan error, atau null jika aman.
     */
    private function cekTimValid(Request $request, $kecualiId = null)
    {
        $support = $request->support_engineers ?? [];
        if ($request->id_engineer && in_array($request->id_engineer, $support)) {
            return 'Engineer yang ditugaskan sebagai lead tidak boleh juga dipilih sebagai tim support.';
        }
        $timIds = array_unique(array_filter(array_merge([$request->id_engineer], $support)));
        foreach ($timIds as $eid) {
            $bentrok = Kunjungan::cariBentrok($eid, $request->tanggal, $request->waktu, $kecualiId);
            if ($bentrok) {
                $eng = Engineer::with('user')->find($eid);
                $nama = $eng->user->nama ?? 'Engineer';
                return $nama . ' sudah ter-assign di kunjungan ' . $bentrok->nomor . ' pada tanggal ' . $bentrok->tanggal . ' jam ' . substr((string) $bentrok->waktu, 0, 5) . '. Pilih jam lain atau engineer lain.';
            }
        }
        return null;
    }

    /**
     * Alamat kunjungan SELALU mengikuti data master: alamat site jika dipilih,
     * jika tidak maka alamat customer. Tidak bisa diubah manual dari form.
     */
    private function resolveLokasi($idSite, $idCustomer)
    {
        if ($idSite) {
            $site = \App\Models\CustomerSite::find($idSite);
            if ($site && trim((string) $site->alamat_lengkap) !== '') {
                return $site->alamat_lengkap;
            }
        }
        $customer = \App\Models\Customer::find($idCustomer);
        return $customer->alamat ?? '';
    }

    /**
     * QA #7: jumlah tools yang dipilih tidak boleh melebihi stok tersedia
     * (stok gudang + jumlah yang sudah direncanakan untuk kunjungan ini).
     */
    private function cekQtyTools(Request $request, $kunjungan = null)
    {
        foreach (($request->tools ?? []) as $tid) {
            $qty = max(1, (int) ($request->tools_qty[$tid] ?? 1));
            $tool = Tool::find($tid);
            if (!$tool) continue;
            $punya = 0;
            if ($kunjungan) {
                $rel = $kunjungan->tools->firstWhere('id_tool', (int) $tid);
                $punya = $rel ? (int) ($rel->pivot->jumlah ?? 0) : 0;
            }
            if ($qty > $tool->stok + $punya) {
                return 'Jumlah ' . $tool->nama_alat . ' melebihi stok yang tersedia (tersisa ' . ($tool->stok + $punya) . ').';
            }
        }
        return null;
    }

    /**
     * QA #8: engineer yang ditugaskan (atau pimpinan/admin) merevisi tools
     * yang dibawa untuk kunjungan ini — tambah/kurang tool & jumlahnya.
     * Stok dan catatan peminjaman menyesuaikan otomatis bila tools sudah aktif.
     */
    public function revisiTools(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }
        if (in_array($kunjungan->status, ['Selesai', 'Reschedule'])) {
            return redirect()->back()->with('error', 'Tools kunjungan yang sudah selesai tidak bisa direvisi.');
        }

        // QA ronde 2 #9: revisi tools terkunci setelah ada engineer yang check-in
        // (tools fisik sudah dibawa ke lokasi klien, tidak bisa diubah lagi).
        $sudahCheckin = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->whereNotNull('waktu_mulai')
            ->exists();
        if ($sudahCheckin || $kunjungan->status === 'Dikerjakan') {
            return redirect()->back()->with('error', 'Revisi tools terkunci: engineer sudah check-in di lokasi klien, tools yang dibawa tidak dapat diubah lagi.');
        }

        $request->validate([
            'tools' => 'nullable|array',
            'tools.*' => 'exists:tools,id_tool',
            'tools_qty' => 'nullable|array',
        ]);

        if ($errQty = $this->cekQtyTools($request, $kunjungan)) {
            return redirect()->back()->with('error', $errQty)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $kunjungan) {
                $toolBaru = [];
                foreach (($request->tools ?? []) as $tid) {
                    $toolBaru[(int) $tid] = max(1, (int) ($request->tools_qty[$tid] ?? 1));
                }
                $this->sinkronToolsKunjungan($kunjungan->fresh(), $toolBaru);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->back()->with('success', 'Tools kunjungan berhasil direvisi. Stok & catatan peminjaman sudah menyesuaikan.');
    }

    // 2. Buat Kunjungan Baru
    public function store(Request $request)
    {
        $request->validate([
            'id_customer' => 'required|exists:customers,id_customer',
            'id_site' => 'nullable|exists:customer_sites,id_site',
            'id_engineer' => 'required|exists:engineers,id_engineer',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'patokan' => 'nullable|string|max:500',
            'pekerjaan' => 'required|string|max:150',
            'tools' => 'nullable|array',
            'tools.*' => 'exists:tools,id_tool',
            'tools_qty' => 'nullable|array',
            'support_engineers' => 'nullable|array|max:4',
            'support_engineers.*' => 'exists:engineers,id_engineer',
        ]);

        // QA #4 & #10: validasi tim (dobel peran + bentrok jadwal)
        if ($errTim = $this->cekTimValid($request)) {
            return redirect()->back()->with('error', $errTim)->withInput();
        }

        // QA #7: jumlah tools tidak boleh melebihi stok
        if ($errQty = $this->cekQtyTools($request)) {
            return redirect()->back()->with('error', $errQty)->withInput();
        }

        // Alamat dikunci: selalu sinkron dari site/customer yang dipilih
        $lokasi = $this->resolveLokasi($request->id_site, $request->id_customer);

        DB::transaction(function () use ($request, $lokasi) {
            // Nomor kunjungan berurutan dari Format Nomor (prefix) yang bisa diatur di Master Data
            $nomorKunjungan = \App\Models\FormatNomor::generate('kunjungan');

            $kunjungan = Kunjungan::create([
                'nomor' => $nomorKunjungan,
                'id_customer' => $request->id_customer,
                'id_site' => $request->id_site,
                'id_engineer' => $request->id_engineer,
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'lokasi' => $lokasi,
                'patokan' => $request->patokan,
                'pekerjaan' => $request->pekerjaan,
                'status' => 'Terjadwal',
            ]);

            if ($request->filled('tools')) {
                // Tools hanya direncanakan dulu (attach); stok berkurang & tercatat
                // per engineer SETELAH seluruh engineer setuju (lihat aktifkanToolsKunjungan)
                // QA #7: jumlah per tool bisa dipilih dari form (tools_qty)
                $syncData = [];
                foreach ($request->tools as $toolId) {
                    $syncData[$toolId] = ['jumlah' => max(1, (int) ($request->tools_qty[$toolId] ?? 1))];
                }
                $kunjungan->tools()->sync($syncData);
            }

            if ($request->filled('support_engineers')) {
                $kunjungan->supportEngineers()->attach($request->support_engineers);
            }
            // Buat daftar konfirmasi untuk lead + support
            $this->syncKonfirmasi($kunjungan, $request->support_engineers ?? []);
        });

        return redirect()->back()->with('success', 'Jadwal Kunjungan berhasil dibuat!');
    }

    // 3. Update Kunjungan
    public function update(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $request->validate([
            'id_customer' => 'required|exists:customers,id_customer',
            'id_site' => 'nullable|exists:customer_sites,id_site',
            'id_engineer' => 'required|exists:engineers,id_engineer',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'patokan' => 'nullable|string|max:500',
            'pekerjaan' => 'required|string|max:150',
            'tools' => 'nullable|array',
            'tools.*' => 'exists:tools,id_tool',
            'tools_qty' => 'nullable|array',
            'support_engineers' => 'nullable|array|max:4',
            'support_engineers.*' => 'exists:engineers,id_engineer',
        ]);

        // QA #4 & #10: validasi tim (dobel peran + bentrok jadwal)
        if ($errTim = $this->cekTimValid($request, $kunjungan->id_kunjungan)) {
            return redirect()->back()->with('error', $errTim)->withInput();
        }

        // QA #7: jumlah tools tidak boleh melebihi stok
        if ($errQty = $this->cekQtyTools($request, $kunjungan)) {
            return redirect()->back()->with('error', $errQty)->withInput();
        }

        // Alamat dikunci: selalu sinkron dari site/customer yang dipilih
        $lokasi = $this->resolveLokasi($request->id_site, $request->id_customer);

        DB::transaction(function () use ($request, $kunjungan, $lokasi) {
            $statusBaru = $kunjungan->status == 'Reschedule' ? 'Terjadwal' : $kunjungan->status;
            $alasan = $kunjungan->status == 'Reschedule' ? null : $kunjungan->alasan_reschedule;

            $kunjungan->update([
                'id_customer' => $request->id_customer,
                'id_site' => $request->id_site,
                'id_engineer' => $request->id_engineer,
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'lokasi' => $lokasi,
                'patokan' => $request->patokan,
                'pekerjaan' => $request->pekerjaan,
                'status' => $statusBaru,
                'alasan_reschedule' => $alasan,
            ]);

            $toolBaru = [];
            foreach (($request->tools ?? []) as $tid) {
                $toolBaru[(int) $tid] = max(1, (int) ($request->tools_qty[$tid] ?? 1));
            }
            $this->sinkronToolsKunjungan($kunjungan, $toolBaru);

            if ($request->filled('support_engineers')) {
                $kunjungan->supportEngineers()->sync($request->support_engineers);
            } else {
                $kunjungan->supportEngineers()->detach();
            }
            // Jika tools sudah aktif & tim berubah, pastikan engineer baru kebagian catatan
            $kunjungan->refresh();
            if ($this->toolsSudahAktif($kunjungan)) {
                $this->aktifkanToolsKunjungan($kunjungan);
            }
            // Sinkronkan daftar konfirmasi
            $this->syncKonfirmasi($kunjungan->fresh(), $request->support_engineers ?? []);
        });

        return redirect()->back()->with('success', 'Data Kunjungan berhasil diperbarui!');
    }

    // 4. Hapus Kunjungan
    public function destroy($id)
    {
        $kunjungan = $this->cariKunjungan($id);

        // QA ronde 2 #2: hak hapus per role —
        // Admin/Kepala (role 1): bebas semua status.
        // Pimpinan (role 2): hanya bila BELUM diterima engineer mana pun.
        $user = Auth::user();
        if ($user->id_role == 3) {
            abort(403, 'Anda tidak berhak menghapus kunjungan.');
        }
        if ($user->id_role == 2 && $kunjungan->sudahAdaYangMenerima()) {
            return redirect()->back()->with('error', 'Kunjungan tidak dapat dihapus karena sudah diterima oleh engineer. Penghapusan kunjungan yang sudah diterima/berjalan/selesai hanya dapat dilakukan oleh Admin.');
        }

        DB::transaction(function () use ($kunjungan) {
            // Kembalikan semua tools yang masih dipinjam untuk kunjungan ini
            $this->sinkronToolsKunjungan($kunjungan, []);
            $kunjungan->delete();
        });

        return redirect()->back()->with('success', 'Jadwal Kunjungan berhasil dihapus secara permanen!');
    }

    /**
     * Sinkron tools kunjungan sekaligus mengatur stok & riwayat peminjaman.
     * - Tool yang dilepas  -> peminjaman ditandai Dikembalikan, stok bertambah.
     * - Tool yang ditambah -> validasi stok, peminjaman baru, stok berkurang.
     */
    /**
     * Sinkron tools kunjungan (daftar rencana tools).
     * - Jika tools kunjungan BELUM diaktifkan (belum semua engineer setuju):
     *   hanya sync attach, tanpa ubah stok / catatan peminjaman.
     * - Jika SUDAH diaktifkan: tool dilepas -> batalkan catatan + kembalikan stok;
     *   tool ditambah -> aktifkan langsung (stok + catatan per engineer).
     */
    /**
     * Sinkron tools kunjungan (daftar rencana tools + jumlahnya, QA #7).
     * - Jika tools kunjungan BELUM diaktifkan (belum semua engineer setuju):
     *   hanya sync attach, tanpa ubah stok / catatan peminjaman.
     * - Jika SUDAH diaktifkan: tool dilepas -> batalkan catatan + kembalikan
     *   stok sesuai total jumlah grup; jumlah berubah -> stok & catatan lead
     *   disesuaikan selisihnya; tool ditambah -> aktifkan langsung.
     * $toolBaru format: [id_tool => jumlah] (atau list id untuk pemanggil lama).
     */
    private function sinkronToolsKunjungan(Kunjungan $kunjungan, array $toolBaru)
    {
        $mapBaru = [];
        foreach ($toolBaru as $k => $v) {
            if (is_int($k)) {
                $mapBaru[$k] = max(1, (int) $v);
            } else {
                $mapBaru[(int) $v] = 1;
            }
        }

        $pivotLama = $kunjungan->tools->mapWithKeys(
            fn($t) => [(int) $t->id_tool => (int) ($t->pivot->jumlah ?? 1)]
        )->toArray();

        $dilepas = array_diff_key($pivotLama, $mapBaru);
        $sudahAktif = $this->toolsSudahAktif($kunjungan);

        $syncData = [];
        foreach ($mapBaru as $toolId => $qty) {
            $syncData[$toolId] = ['jumlah' => $qty];
        }
        $kunjungan->tools()->sync($syncData);

        // Tool dilepas -> batalkan catatan semua engineer + kembalikan stok (total grup)
        foreach ($dilepas as $toolId => $qtyLama) {
            if ($sudahAktif) {
                $totalGrup = (int) PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                    ->where('id_tool', $toolId)
                    ->where('status', 'Dipinjam')
                    ->sum('jumlah');
                PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                    ->where('id_tool', $toolId)
                    ->where('status', 'Dipinjam')
                    ->update([
                        'status' => 'Dibatalkan',
                        'tanggal_kembali' => now(),
                        'keterangan' => 'Dibatalkan: tool dilepas dari kunjungan ' . $kunjungan->nomor,
                    ]);
                if ($totalGrup > 0) {
                    Tool::where('id_tool', $toolId)->increment('stok', $totalGrup);
                }
            }
        }

        if ($sudahAktif) {
            // Jumlah tool yang tetap dipakai berubah -> sesuaikan stok & catatan lead
            foreach ($mapBaru as $toolId => $qty) {
                if (!isset($pivotLama[$toolId])) continue;
                $delta = $qty - $pivotLama[$toolId];
                if ($delta === 0) continue;
                $toolModel = Tool::lockForUpdate()->find($toolId);
                if (!$toolModel) continue;
                if ($delta > 0) {
                    if ($toolModel->stok < $delta) {
                        throw new \Exception("Stok " . $toolModel->nama_alat . " tidak mencukupi untuk menambah jumlah (tersisa " . $toolModel->stok . ").");
                    }
                    $toolModel->decrement('stok', $delta);
                } else {
                    $toolModel->increment('stok', abs($delta));
                }
                $leadRec = PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                    ->where('id_tool', $toolId)
                    ->where('id_engineer', $kunjungan->id_engineer)
                    ->where('status', 'Dipinjam')
                    ->first();
                if ($leadRec) {
                    $leadRec->update(['jumlah' => max(0, (int) $leadRec->jumlah + $delta)]);
                }
            }
            // Tool ditambah setelah aktif -> langsung aktifkan
            $this->aktifkanToolsKunjungan($kunjungan);
        }
    }

    /**
     * Apakah tools kunjungan sudah diaktifkan (stok sudah berkurang)?
     */
    private function toolsSudahAktif(Kunjungan $kunjungan)
    {
        return PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('status', 'Dipinjam')
            ->exists();
    }

    /**
     * Aktifkan tools kunjungan: kurangi stok 1x per tool, buat catatan
     * peminjaman untuk MASING-MASING engineer (lead + support).
     * Idempotent: aman dipanggil berulang.
     */
    private function aktifkanToolsKunjungan(Kunjungan $kunjungan)
    {
        $engineers = collect([$kunjungan->id_engineer])
            ->merge($kunjungan->supportEngineers()->pluck('engineers.id_engineer'))
            ->filter()->unique()->values();

        $kunjungan->load('tools');

        foreach ($kunjungan->tools as $tool) {
            $sudahAktif = PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_tool', $tool->id_tool)
                ->where('status', 'Dipinjam')
                ->exists();

            if (!$sudahAktif) {
                $toolModel = Tool::lockForUpdate()->find($tool->id_tool);
                $qty = max(1, (int) ($tool->pivot->jumlah ?? 1));
                if (!$toolModel || $toolModel->stok < $qty) {
                    throw new \Exception("Stok " . ($toolModel->nama_alat ?? 'tool') . " tidak mencukupi (tersisa " . ($toolModel->stok ?? 0) . ", dibutuhkan " . $qty . ").");
                }
                $toolModel->decrement('stok', $qty);
            }

            // Catatan per engineer (termasuk engineer yang baru ditambahkan).
            // QA #11: jumlah fisik HANYA dicatat di catatan LEAD engineer;
            // catatan support jumlahnya 0 agar total jumlah tercatat di
            // riwayat = jumlah alat fisik (tidak dobel saat tim isi 2+ orang).
            foreach ($engineers as $idEng) {
                $isLead = (int) $idEng === (int) $kunjungan->id_engineer;
                PeminjamanTool::firstOrCreate(
                    ['id_kunjungan' => $kunjungan->id_kunjungan, 'id_tool' => $tool->id_tool, 'id_engineer' => $idEng, 'status' => 'Dipinjam'],
                    ['jumlah' => $isLead ? (int) ($tool->pivot->jumlah ?? 1) : 0, 'tanggal_pinjam' => now(), 'keterangan' => 'Dipakai untuk kunjungan ' . $kunjungan->nomor . ($isLead ? '' : ' (alat tim, jumlah tercatat di lead)')]
                );
            }
        }
    }

    // 5. Detail Kunjungan
    public function show($id)
    {
        // OPTIMASI: Tambahkan 'site' di eager loading
        $kunjungan = Kunjungan::with([
            'customer', 
            'site',
            'engineer.user', 
            'tools', 
            'aktivitas.engineer.user', 
            'dokumentasi', 
            'laporan.buktiPenyelesaian',
            'laporan.pembuat.user',
            'pengeluaran.engineer.user',
            'supportEngineers.user',
            'konfirmasi.engineer.user'
        ])->where('nomor', $id)->firstOrFail();

        // Otorisasi: engineer hanya boleh lihat kunjungannya sendiri (lead/support)
        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        // QA #7/#8: daftar tools untuk form revisi tools di halaman detail
        $tools = Tool::where('status_ketersediaan', 'Tersedia')->orderBy('nama_alat')->get();
        $peran = $this->peranEngineer($kunjungan);

        // QA #15: opsi formulir yang dikelola admin
        $opsiJenisBiaya = \App\Models\FormOption::daftar('jenis_biaya');
        $opsiKategoriFoto = \App\Models\FormOption::daftar('kategori_foto');
        // QA #21: anggota tim kunjungan ini (untuk pilihan "dikeluarkan oleh")
        $timKunjungan = collect([$kunjungan->engineer])
            ->merge($kunjungan->supportEngineers)
            ->filter()
            ->unique('id_engineer')
            ->values();

        // QA ronde 2 #9: form revisi tools terkunci setelah ada check-in
        $revisiTerkunci = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->whereNotNull('waktu_mulai')
            ->exists();

        return view('kunjungan.show', compact('kunjungan', 'tools', 'peran', 'opsiJenisBiaya', 'opsiKategoriFoto', 'timKunjungan', 'revisiTerkunci'));
    }

    // 6. Engineer Check-in
    public function checkIn(Request $request, $id)
    {
        $kunjungan = Kunjungan::with('customer')->where('nomor', $id)->firstOrFail();

        // Lead & support boleh check-in, tapi harus terlibat di kunjungan ini
        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        if (!in_array($kunjungan->status, ['Terjadwal', 'Dikonfirmasi', 'Dikerjakan'])) {
            return redirect()->back()->with('error', 'Status kunjungan tidak valid untuk dilakukan Check-in.');
        }

        // QA ronde 2 #8: blokir check-in bila engineer masih punya tugas lain
        // yang sedang berjalan (sudah check-in, belum check-out).
        if ($user->id_role == 3) {
            $engineerjalan = Engineer::where('id_pengguna', $user->id_pengguna)->first();
            if ($engineerjalan) {
                $sedangBerjalan = AktivitasPekerjaan::where('id_engineer', $engineerjalan->id_engineer)
                    ->whereNotNull('waktu_mulai')
                    ->whereNull('waktu_selesai')
                    ->where('id_kunjungan', '!=', $kunjungan->id_kunjungan)
                    ->with('kunjungan')
                    ->first();
                if ($sedangBerjalan) {
                    return redirect()->back()->with('error', 'Gagal Check-In: Anda masih memiliki tugas kunjungan yang sedang berjalan (' . ($sedangBerjalan->kunjungan->nomor ?? '-') . '). Silakan selesaikan tugas sebelumnya terlebih dahulu.');
                }
            }
        }

        $request->validate([
            'lokasi_gps' => 'required|string',
        ]);

        $coords = explode(',', str_replace(' ', '', $request->lokasi_gps));
        $lat = $coords[0] ?? null;
        $lng = $coords[1] ?? null;

        $customer = $kunjungan->customer;
        $site = $kunjungan->id_site ? \App\Models\CustomerSite::find($kunjungan->id_site) : null;

        $targetLat = $site && $site->latitude ? $site->latitude : $customer->latitude;
        $targetLng = $site && $site->longitude ? $site->longitude : $customer->longitude;

        if (!$targetLat || !$targetLng) {
            return redirect()->back()->with('error', 'Gagal Check-in! Titik GPS klien/cabang belum diatur oleh Pimpinan. Harap hubungi atasan.');
        }

        $latEngineer = (float) $lat;
        $lonEngineer = (float) $lng;
        $latTarget = (float) $targetLat;
        $lonTarget = (float) $targetLng;

        $earthRadius = 6371;
        $dLat = deg2rad($latEngineer - $latTarget);
        $dLon = deg2rad($lonEngineer - $lonTarget);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latTarget)) * cos(deg2rad($latEngineer)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $jarakMeter = $earthRadius * $c * 1000;

        if ($jarakMeter > 100) {
            return redirect()->back()->with('error', 'Gagal Check-in! Anda berada di luar radius 100 meter dari lokasi kerja. Jarak Anda saat ini: ' . round($jarakMeter) . ' meter dari lokasi tujuan.');
        }

        // Cek apakah engineer ini sudah check-in
        $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
        $idEngineer = $engineer ? $engineer->id_engineer : null;
        if ($idEngineer) {
            $sudahCheckin = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_engineer', $idEngineer)
                ->whereNotNull('waktu_mulai')
                ->exists();
            if ($sudahCheckin) {
                return redirect()->back()->with('error', 'Anda sudah check-in untuk kunjungan ini.');
            }
        }

        $kunjungan->update([
            'status' => 'Dikerjakan',
            'check_in_latitude' => $lat,
            'check_in_longitude' => $lng
        ]);

        AktivitasPekerjaan::create([
            'id_kunjungan' => $kunjungan->id_kunjungan,
            'id_engineer' => $idEngineer,
            'waktu_mulai' => now(),
            'lokasi' => $request->lokasi_gps,
            'deskripsi' => 'Engineer tiba di lokasi dan memulai pengerjaan.',
        ]);

        return redirect()->back()->with('success', 'Check-in berhasil! Jarak Anda: ' . round($jarakMeter) . ' meter dari target.');
    }

    // 7. Engineer Upload Dokumentasi
    public function uploadDokumentasi(Request $request, $id)
    {
        $request->validate([
            'kategori_foto' => 'required|string|max:50',
            'foto' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,3gp,webm|max:51200',
            'keterangan' => 'nullable|string',
        ]);

        // QA #16: keterangan wajib diisi bila kategori foto "Lainnya"
        if ($request->kategori_foto === 'Lainnya' && !trim((string) $request->keterangan)) {
            return redirect()->back()->with('error', 'Keterangan foto wajib diisi jika memilih kategori Lainnya.')->withInput();
        }

        $kunjungan = $this->cariKunjungan($id);

        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $dokDir = $this->webUploadPath('dokumentasi');
        if (!is_dir($dokDir)) mkdir($dokDir, 0755, true);
        $file->move($dokDir, $filename);

        Dokumentasi::create([
            'id_kunjungan' => $kunjungan->id_kunjungan,
            'kategori_foto' => $request->kategori_foto,
            'file_foto' => 'uploads/dokumentasi/' . $filename,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Foto dokumentasi berhasil diunggah!');
    }

    // 8. Simpan Pengeluaran
    public function storePengeluaran(Request $request, $id)
    {
        $request->validate([
            'jenis_biaya' => 'required|string|max:100',
            'jenis_biaya_lainnya' => 'nullable|string|max:100',
            'id_engineer' => 'nullable|exists:engineers,id_engineer',
            'nominal' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'bukti_nota' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,3gp,webm|max:51200',
        ]);

        $kunjungan = $this->cariKunjungan($id);

        // QA #15: opsi "Lainnya" wajib diisi teks jenis biayanya
        $jenisBiaya = $request->jenis_biaya;
        if ($jenisBiaya === 'Lainnya') {
            $jenisBiaya = trim((string) $request->jenis_biaya_lainnya);
            if ($jenisBiaya === '') {
                return redirect()->back()->with('error', 'Jenis biaya wajib ditulis jika memilih Lainnya.')->withInput();
            }
        }

        // QA #21: pengeluaran dicatat per engineer. Default: engineer yang
        // sedang login; boleh memilih anggota tim lain yang ada di site.
        $user = Auth::user();
        $engLogin = $user->id_role == 3 ? Engineer::where('id_pengguna', $user->id_pengguna)->first() : null;
        $timIds = collect([$kunjungan->id_engineer])
            ->merge($kunjungan->supportEngineers()->pluck('engineers.id_engineer'))
            ->filter()->unique()->map(fn($v) => (int) $v)->toArray();
        $idPengeluar = $request->id_engineer ? (int) $request->id_engineer : ($engLogin->id_engineer ?? null);
        if ($idPengeluar && !in_array($idPengeluar, $timIds)) {
            $idPengeluar = $engLogin->id_engineer ?? null;
        }

        $path = null;
        if ($request->hasFile('bukti_nota')) {
            $file = $request->file('bukti_nota');
            $filename = time() . '_nota_' . $file->getClientOriginalName();
            $notaDir = $this->webUploadPath('pengeluaran');
            if (!is_dir($notaDir)) mkdir($notaDir, 0755, true);
            $file->move($notaDir, $filename);
            $path = 'uploads/pengeluaran/' . $filename;
        }

        Pengeluaran::create([
            'id_kunjungan' => $kunjungan->id_kunjungan,
            'id_engineer' => $idPengeluar,
            'jenis_biaya' => $jenisBiaya,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
            'bukti_nota' => $path,
            'status_ganti' => 'Belum Diganti',
        ]);

        return redirect()->back()->with('success', 'Pengeluaran operasional berhasil dicatat!');
    }

    // 8b. QA #18: Edit Pengeluaran (engineer yang terlibat / pimpinan)
    public function updatePengeluaran(Request $request, $id, $idPengeluaran)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        $request->validate([
            'jenis_biaya' => 'required|string|max:100',
            'id_engineer' => 'nullable|exists:engineers,id_engineer',
            'nominal' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'bukti_nota' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,3gp,webm|max:51200',
        ]);

        $peng = Pengeluaran::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_pengeluaran', $idPengeluaran)
            ->firstOrFail();

        $data = [
            'jenis_biaya' => $request->jenis_biaya,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
        ];

        // QA #21: pengeluar bisa dikoreksi, terbatas anggota tim kunjungan
        if ($request->filled('id_engineer')) {
            $timIds = collect([$kunjungan->id_engineer])
                ->merge($kunjungan->supportEngineers()->pluck('engineers.id_engineer'))
                ->filter()->unique()->map(fn($v) => (int) $v)->toArray();
            if (in_array((int) $request->id_engineer, $timIds)) {
                $data['id_engineer'] = (int) $request->id_engineer;
            }
        }

        if ($request->hasFile('bukti_nota')) {
            $file = $request->file('bukti_nota');
            $filename = time() . '_nota_' . $file->getClientOriginalName();
            $notaDir = $this->webUploadPath('pengeluaran');
            if (!is_dir($notaDir)) mkdir($notaDir, 0755, true);
            $file->move($notaDir, $filename);
            if ($peng->bukti_nota) {
                $lama = public_path($peng->bukti_nota);
                if (is_file($lama)) @unlink($lama);
            }
            $data['bukti_nota'] = 'uploads/pengeluaran/' . $filename;
        }

        $peng->update($data);

        return redirect()->back()->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    // 8c2. QA #22: Pimpinan menandai pengeluaran sudah/belum diganti perusahaan
    public function toggleGantiPengeluaran($id, $idPengeluaran)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if (!in_array($user->id_role, [1, 2])) {
            abort(403, 'Hanya Pimpinan yang dapat menandai status penggantian.');
        }

        $peng = Pengeluaran::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_pengeluaran', $idPengeluaran)
            ->firstOrFail();

        $baru = $peng->status_ganti === 'Sudah Diganti' ? 'Belum Diganti' : 'Sudah Diganti';
        $peng->update(['status_ganti' => $baru]);

        return redirect()->back()->with('success', 'Status penggantian diperbarui: ' . $baru . '.');
    }

    // 8c. QA #18: Hapus Pengeluaran (engineer yang terlibat / pimpinan)
    public function destroyPengeluaran($id, $idPengeluaran)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        $peng = Pengeluaran::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_pengeluaran', $idPengeluaran)
            ->firstOrFail();

        if ($peng->bukti_nota) {
            $lama = public_path($peng->bukti_nota);
            if (is_file($lama)) @unlink($lama);
        }
        $peng->delete();

        return redirect()->back()->with('success', 'Pengeluaran berhasil dihapus!');
    }

    // 8d. QA #18: Edit Dokumentasi (engineer yang terlibat / pimpinan)
    public function updateDokumentasi(Request $request, $id, $idDokumentasi)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        $request->validate([
            'kategori_foto' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,3gp,webm|max:51200',
        ]);

        // QA #16 berlaku juga saat edit
        if ($request->kategori_foto === 'Lainnya' && !trim((string) $request->keterangan)) {
            return redirect()->back()->with('error', 'Keterangan foto wajib diisi jika memilih kategori Lainnya.')->withInput();
        }

        $dok = Dokumentasi::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_dokumentasi', $idDokumentasi)
            ->firstOrFail();

        $data = [
            'kategori_foto' => $request->kategori_foto,
            'keterangan' => $request->keterangan,
        ];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $dokDir = $this->webUploadPath('dokumentasi');
            if (!is_dir($dokDir)) mkdir($dokDir, 0755, true);
            $file->move($dokDir, $filename);
            $lama = public_path($dok->file_foto);
            if (is_file($lama)) @unlink($lama);
            $data['file_foto'] = 'uploads/dokumentasi/' . $filename;
        }

        $dok->update($data);

        return redirect()->back()->with('success', 'Dokumentasi berhasil diperbarui!');
    }

    // 8e. QA #18: Hapus Dokumentasi (engineer yang terlibat / pimpinan)
    public function destroyDokumentasi($id, $idDokumentasi)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        $dok = Dokumentasi::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_dokumentasi', $idDokumentasi)
            ->firstOrFail();

        $lama = public_path($dok->file_foto);
        if (is_file($lama)) @unlink($lama);
        $dok->delete();

        return redirect()->back()->with('success', 'Dokumentasi berhasil dihapus!');
    }

    // 9. Check-out
    public function checkOut(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        // Harus terlibat di kunjungan ini
        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        $request->validate([
            'catatan' => 'required|string',
            'lokasi_gps' => 'required|string',
            'lewati_ttd' => 'nullable|boolean',
            'alasan_tanpa_ttd' => 'nullable|string|max:500',
        ]);

        // Revisi alur: check-out wajib laporan; TTD boleh dilewati dengan alasan
        // (ditandai untuk follow-up pimpinan agar kunjungan tidak nyangkut)
        if (!$kunjungan->laporan) {
            return redirect()->back()->with('error', 'Buat laporan dulu sebelum check-out.');
        }
        if (!$kunjungan->laporan->buktiPenyelesaian && !$kunjungan->laporan->ttd_dilewati) {
            if (!$request->boolean('lewati_ttd') || !trim($request->alasan_tanpa_ttd ?? '')) {
                return redirect()->back()->with('error', 'Selesaikan tanda tangan customer dulu, atau lewati TTD dengan alasan yang jelas.');
            }
            $kunjungan->laporan->update([
                'ttd_dilewati' => true,
                'alasan_tanpa_ttd' => trim($request->alasan_tanpa_ttd),
            ]);
        }

        $coords = explode(',', str_replace(' ', '', $request->lokasi_gps));
        $lat = $coords[0] ?? null;
        $lng = $coords[1] ?? null;

        // Check-out per engineer: update aktivitas miliknya sendiri
        $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
        $idEngineer = $engineer ? $engineer->id_engineer : null;

        $aktivitas = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_engineer', $idEngineer)
            ->whereNotNull('waktu_mulai')
            ->whereNull('waktu_selesai')
            ->latest()
            ->first();

        if (!$aktivitas) {
            return redirect()->back()->with('error', 'Anda belum check-in atau sudah check-out untuk kunjungan ini.');
        }

        $aktivitas->update([
            'waktu_selesai' => now(),
            'catatan' => $request->catatan,
            'lokasi_checkout' => $request->lokasi_gps,
        ]);

        // Update koordinat check-out kunjungan (terakhir yang check-out)
        $kunjungan->update([
            'check_out_latitude' => $lat,
            'check_out_longitude' => $lng
        ]);

        // Jika SEMUA engineer (lead + support) sudah check-out -> kunjungan Selesai
        // Tools TIDAK otomatis kembali; engineer wajib mengembalikan manual via halaman Pengembalian
        $allEngineerIds = array_unique(array_merge(
            [$kunjungan->id_engineer],
            $kunjungan->supportEngineers()->pluck('engineers.id_engineer')->toArray()
        ));
        $sudahCheckoutSemua = true;
        foreach ($allEngineerIds as $eid) {
            if (!$eid) continue;
            $co = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_engineer', $eid)
                ->whereNotNull('waktu_selesai')
                ->exists();
            if (!$co) { $sudahCheckoutSemua = false; break; }
        }
        if ($sudahCheckoutSemua) {
            $kunjungan->update(['status' => 'Selesai']);
            return redirect()->back()->with('success', 'Check-out berhasil! Semua engineer sudah check-out, kunjungan selesai.');
        }

        return redirect()->back()->with('success', 'Check-out berhasil! Menunggu engineer lain check-out.');
    }

    /**
     * Revisi alur: Pimpinan/Kepala Pimpinan bisa memaksa selesaikan kunjungan
     * yang nyangkut (misal support lupa check-out atau TTD bermasalah).
     * Tools tetap Dipinjam sampai dikembalikan manual.
     */
    public function forceSelesai(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if (!in_array($user->id_role, [1, 2])) {
            abort(403, 'Hanya Pimpinan yang dapat memaksa selesaikan kunjungan.');
        }

        $request->validate([
            'alasan_force_selesai' => 'required|string|max:500',
        ]);

        // Tutup semua aktivitas yang masih terbuka
        AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->whereNotNull('waktu_mulai')
            ->whereNull('waktu_selesai')
            ->update(['waktu_selesai' => now(), 'catatan' => 'Ditutup paksa oleh pimpinan.']);

        $kunjungan->update([
            'status' => 'Selesai',
            'catatan_force_selesai' => 'Diselesaikan paksa oleh ' . $user->nama . ': ' . trim($request->alasan_force_selesai),
        ]);

        return redirect()->back()->with('success', 'Kunjungan diselesaikan paksa. Tools yang dipinjam tetap tercatat sampai dikembalikan manual.');
    }

    // 9a. Buat Laporan (hanya 1x per kunjungan - siapa cepat dia dapat)
    public function buatLaporan($id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        // Jika sudah ada laporan, tolak
        if ($kunjungan->laporan) {
            return redirect()->back()->with('error', 'Laporan sudah dibuat oleh engineer lain. Hanya 1 laporan per kunjungan.');
        }

        // Harus sudah check-in dulu (laporan dibuat sebelum check-out)
        $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
        $idEngineer = $engineer ? $engineer->id_engineer : null;
        $sudahCheckin = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_engineer', $idEngineer)
            ->whereNotNull('waktu_mulai')
            ->exists();
        if ($user->id_role == 3 && !$sudahCheckin) {
            return redirect()->back()->with('error', 'Anda harus check-in dulu sebelum membuat laporan.');
        }

        $laporan = Laporan::firstOrCreate(
            ['id_kunjungan' => $kunjungan->id_kunjungan],
            [
                'tanggal_dibuat' => now(),
                'status_laporan' => 'Terbuat Otomatis'
            ]
        );

        // Catat siapa yang membuat
        if ($idEngineer && !$laporan->id_engineer_pembuat) {
            $laporan->update(['id_engineer_pembuat' => $idEngineer]);
        }

        return redirect()->back()->with('success', 'Laporan berhasil dibuat! Menunggu verifikasi tanda tangan customer.');
    }

    // 9b. Revisi Catatan Pekerjaan (sebelum laporan dikunci TTD customer)
    public function revisiCatatan(Request $request, $id)
    {
        $kunjungan = Kunjungan::with('laporan.buktiPenyelesaian')->where('nomor', $id)->firstOrFail();

        if (!$kunjungan->laporan || $kunjungan->laporan->buktiPenyelesaian) {
            return redirect()->back()->with('error', 'Catatan tidak dapat direvisi karena laporan sudah dikunci tanda tangan.');
        }

        $request->validate([
            'hasil_pekerjaan' => 'required|string',
            'catatan_tambahan' => 'nullable|string',
        ]);

        $kunjungan->laporan->update([
            'hasil_pekerjaan' => $request->hasil_pekerjaan,
            'catatan_tambahan' => $request->catatan_tambahan,
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil direvisi dan PDF otomatis mengikuti revisi terbaru.');
    }

    // 9c. Simpan Laporan Lengkap: Bagian 1 (data laporan) + Bagian 2 (TTD) dalam sekali jalan
    public function simpanLaporanLengkap(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        // Hanya 1 laporan per kunjungan
        if ($kunjungan->laporan) {
            return redirect()->back()->with('error', 'Laporan sudah dibuat. Gunakan Revisi Laporan Kunjungan untuk mengubahnya.');
        }

        // Harus sudah check-in dulu
        $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
        $idEngineer = $engineer ? $engineer->id_engineer : null;
        $sudahCheckin = AktivitasPekerjaan::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_engineer', $idEngineer)
            ->whereNotNull('waktu_mulai')
            ->exists();
        if ($user->id_role == 3 && !$sudahCheckin) {
            return redirect()->back()->with('error', 'Anda harus check-in dulu sebelum membuat laporan.');
        }

        $request->validate([
            'hasil_pekerjaan' => 'required|string',
            'catatan_tambahan' => 'nullable|string',
            'lewati_ttd' => 'nullable|boolean',
            'alasan_tanpa_ttd' => 'nullable|string|max:500',
            'signature' => 'nullable|string',
            'signature_engineer' => 'nullable|string',
        ]);

        // Bagian 2: TTD customer & engineer independen — cukup salah satu terisi.
        // (TTD yang belum ada bisa ditambahkan belakangan lewat revisi.)
        $lewati = $request->boolean('lewati_ttd');
        if ($lewati) {
            if (!trim($request->alasan_tanpa_ttd ?? '')) {
                return redirect()->back()->with('error', 'Wajib isi alasan jika tanda tangan customer dilewati.')->withInput();
            }
        } elseif (empty($request->signature) && empty($request->signature_engineer)) {
            return redirect()->back()->with('error', 'Isi minimal satu tanda tangan (customer atau engineer), atau centang Lewati TTD beserta alasannya.')->withInput();
        }

        $laporan = Laporan::firstOrCreate(
            ['id_kunjungan' => $kunjungan->id_kunjungan],
            ['tanggal_dibuat' => now(), 'status_laporan' => 'Terbuat Otomatis']
        );
        $laporan->update([
            'hasil_pekerjaan' => $request->hasil_pekerjaan,
            'catatan_tambahan' => $request->catatan_tambahan,
            'id_engineer_pembuat' => $laporan->id_engineer_pembuat ?: $idEngineer,
        ]);

        if ($lewati) {
            $laporan->update([
                'ttd_dilewati' => true,
                'alasan_tanpa_ttd' => trim($request->alasan_tanpa_ttd),
            ]);
            BuktiPenyelesaian::where('id_laporan', $laporan->id_laporan)->delete();
        } else {
            BuktiPenyelesaian::updateOrCreate(
                ['id_laporan' => $laporan->id_laporan],
                [
                    'tanda_tangan_customer' => $request->signature ?: null,
                    'tanda_tangan_engineer' => $request->signature_engineer ?: null,
                    'tanggal_tanda_tangan' => now(),
                    'status' => 'Ditandatangani',
                ]
            );
            $laporan->update(['ttd_dilewati' => false, 'alasan_tanpa_ttd' => null]);
        }

        $kunjungan->update(['draft_ttd_customer' => null, 'draft_ttd_engineer' => null, 'draft_hasil_pekerjaan' => null, 'draft_catatan_tambahan' => null]);

        return redirect()->route('kunjungan.show', $id)->with('success', 'Laporan berhasil dibuat! PDF laporan sudah tersedia di bawah.');
    }

    // 9d. Simpan Revisi Laporan Lengkap: Bagian 1 + Bagian 2 dibuka lagi
    public function simpanRevisiLaporan(Request $request, $id)
    {
        $kunjungan = Kunjungan::with('laporan.buktiPenyelesaian')->where('nomor', $id)->firstOrFail();

        $user = Auth::user();
        if ($user->id_role == 3 && !$this->peranEngineer($kunjungan)) {
            abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
        }

        if (!$kunjungan->laporan) {
            return redirect()->back()->with('error', 'Belum ada laporan untuk direvisi.');
        }

        // Laporan yang sudah disetujui pimpinan dikunci
        if ($kunjungan->laporan->status_approval == 'Disetujui') {
            return redirect()->back()->with('error', 'Laporan sudah disetujui pimpinan dan tidak dapat direvisi. Hubungi pimpinan jika perlu perubahan.');
        }

        $request->validate([
            'hasil_pekerjaan' => 'required|string',
            'catatan_tambahan' => 'nullable|string',
            'lewati_ttd' => 'nullable|boolean',
            'alasan_tanpa_ttd' => 'nullable|string|max:500',
            'signature' => 'nullable|string',
            'signature_engineer' => 'nullable|string',
        ]);

        $lewati = $request->boolean('lewati_ttd');
        if ($lewati && !trim($request->alasan_tanpa_ttd ?? '')) {
            return redirect()->back()->with('error', 'Wajib isi alasan jika tanda tangan customer dilewati.')->withInput();
        }

        $laporan = $kunjungan->laporan;
        $laporan->update([
            'hasil_pekerjaan' => $request->hasil_pekerjaan,
            'catatan_tambahan' => $request->catatan_tambahan,
        ]);

        if ($lewati) {
            $laporan->update([
                'ttd_dilewati' => true,
                'alasan_tanpa_ttd' => trim($request->alasan_tanpa_ttd),
            ]);
            BuktiPenyelesaian::where('id_laporan', $laporan->id_laporan)->delete();
        } else {
            // TTD customer & engineer independen: pad yang digambar ulang
            // mengganti TTD lama, pad yang dibiarkan kosong mempertahankan TTD lama.
            $adaBaru = !empty($request->signature) || !empty($request->signature_engineer);
            if ($adaBaru) {
                $bukti = BuktiPenyelesaian::firstOrNew(['id_laporan' => $laporan->id_laporan]);
                if (!empty($request->signature)) {
                    $bukti->tanda_tangan_customer = $request->signature;
                }
                if (!empty($request->signature_engineer)) {
                    $bukti->tanda_tangan_engineer = $request->signature_engineer;
                }
                $bukti->tanggal_tanda_tangan = now();
                $bukti->status = 'Ditandatangani';
                $bukti->save();
                $laporan->update(['ttd_dilewati' => false, 'alasan_tanpa_ttd' => null]);
            }
        }
        // Jika pad kosong & tidak lewati -> TTD lama dipertahankan apa adanya

        // Revisi atas permintaan pimpinan -> kembalikan ke Menunggu Persetujuan
        if ($laporan->status_approval == 'Revisi') {
            $laporan->update(['status_approval' => 'Menunggu Persetujuan', 'catatan_approval' => null]);
        }

        $kunjungan->update(['draft_ttd_customer' => null, 'draft_ttd_engineer' => null]);

        return redirect()->route('kunjungan.show', $id)->with('success', 'Revisi laporan tersimpan! PDF otomatis mengikuti data terbaru.');
    }

    // 10. Tanda Tangan Customer & Engineer
    public function verifySignature(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|string',
            'signature_engineer' => 'required|string',
        ]);

        $kunjungan = $this->cariKunjungan($id);
        $laporan = Laporan::where('id_kunjungan', $kunjungan->id_kunjungan)->firstOrFail();

        BuktiPenyelesaian::updateOrCreate(
            ['id_laporan' => $laporan->id_laporan],
            [
                'tanda_tangan_customer' => $request->signature,
                'tanda_tangan_engineer' => $request->signature_engineer,
                'tanggal_tanda_tangan' => now(),
                'status' => 'Ditandatangani',
            ]
        );

        // TTD selesai -> laporan terkunci, tapi kunjungan BELUM selesai.
        // Engineer masih harus check-out; status Selesai saat semua sudah check-out.
        $kunjungan->update(['draft_ttd_customer' => null, 'draft_ttd_engineer' => null]);

        return redirect()->route('kunjungan.show', $id)->with('success', 'Tanda tangan tersimpan dan terkunci! Silakan check-out untuk menyelesaikan kunjungan.');
    }

    // 10b. Simpan draft TTD otomatis (dipanggil via AJAX saat pad dikunci/diisi),
    // agar tanda tangan tidak hilang jika halaman di-refresh sebelum submit final.
    public function saveSignatureDraft(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:customer,engineer',
            'signature' => 'nullable|string|max:2000000',
        ]);

        $kunjungan = $this->cariKunjungan($id);

        // Jangan terima draft jika laporan sudah dikunci final
        if ($kunjungan->laporan && $kunjungan->laporan->buktiPenyelesaian) {
            return response()->json(['ok' => false, 'message' => 'Laporan sudah dikunci final.'], 400);
        }

        $field = $request->type === 'customer' ? 'draft_ttd_customer' : 'draft_ttd_engineer';
        $kunjungan->update([$field => $request->signature]);

        return response()->json(['ok' => true]);
    }

    // 10c. Simpan draft teks Bagian 1 (Hasil Pekerjaan + Catatan Tambahan) otomatis
    // via AJAX saat engineer mengetik, agar tidak hilang jika halaman di-refresh.
    // Konsep sama seperti draft TTD: tersimpan di server, direstore saat halaman dibuka.
    public function saveLaporanDraft(Request $request, $id)
    {
        $request->validate([
            'hasil_pekerjaan' => 'nullable|string|max:10000',
            'catatan_tambahan' => 'nullable|string|max:10000',
        ]);

        $kunjungan = $this->cariKunjungan($id);

        // Jangan terima draft jika laporan sudah dibuat (satu laporan per kunjungan)
        if ($kunjungan->laporan) {
            return response()->json(['ok' => false, 'message' => 'Laporan sudah dibuat.'], 400);
        }

        $kunjungan->update([
            'draft_hasil_pekerjaan' => $request->hasil_pekerjaan,
            'draft_catatan_tambahan' => $request->catatan_tambahan,
        ]);

        return response()->json(['ok' => true]);
    }

    // 11. Reschedule / Tolak Jadwal oleh Engineer
    // Saat ditolak: semua tools kunjungan ikut dilepas (stok kembali, peminjaman ditutup)
    public function reschedule(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $request->validate([
            'alasan_reschedule' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if ($user->id_role == 3) {
            // Penolakan per engineer: catat siapa yang menolak + alasannya
            $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
            if (!$engineer || !$this->peranEngineer($kunjungan)) {
                abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
            }
            KunjunganKonfirmasi::updateOrCreate(
                ['id_kunjungan' => $kunjungan->id_kunjungan, 'id_engineer' => $engineer->id_engineer],
                ['status' => 'ditolak', 'alasan_ditolak' => $request->alasan_reschedule, 'waktu_konfirmasi' => now()]
            );
            return redirect()->back()->with('success', 'Penolakan tercatat dan menunggu keputusan pimpinan. Jika penolakan disetujui, pimpinan akan mengganti Anda dengan engineer lain.');
        }

        // Pimpinan: tolak/reschedule seluruh kunjungan (logic lama - lepas tools)
        // Batalkan SEMUA catatan peminjaman (tiap engineer) + kembalikan stok sesuai total grup
        $toolIds = $kunjungan->tools()->pluck('tools.id_tool')->toArray();
        foreach ($toolIds as $toolId) {
            $totalGrup = (int) PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_tool', $toolId)
                ->where('status', 'Dipinjam')
                ->sum('jumlah');
            $dibatalkan = PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_tool', $toolId)
                ->where('status', 'Dipinjam')
                ->update(['status' => 'Dibatalkan', 'tanggal_kembali' => now(), 'keterangan' => 'Dibatalkan: kunjungan ditolak sebelum tools dibawa.']);
            if ($dibatalkan && $totalGrup > 0) {
                Tool::where('id_tool', $toolId)->increment('stok', $totalGrup);
            }
        }
        $kunjungan->tools()->detach();

        $kunjungan->update([
            'status' => 'Reschedule',
            'alasan_reschedule' => $request->alasan_reschedule,
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil ditolak, tools kunjungan dikembalikan ke stok, dan dikembalikan ke Pimpinan untuk dijadwalkan ulang.');
    }

    // 12. Konfirmasi / Terima Jadwal Kunjungan oleh Engineer (per engineer)
    public function terima($id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if ($user->id_role == 3) {
            $engineer = Engineer::where('id_pengguna', $user->id_pengguna)->first();
            if (!$engineer || !$this->peranEngineer($kunjungan)) {
                abort(403, 'Anda tidak terlibat dalam kunjungan ini.');
            }
            KunjunganKonfirmasi::updateOrCreate(
                ['id_kunjungan' => $kunjungan->id_kunjungan, 'id_engineer' => $engineer->id_engineer],
                ['status' => 'diterima', 'alasan_ditolak' => null, 'waktu_konfirmasi' => now()]
            );

            // Jika SEMUA engineer sudah terima -> kunjungan Dikonfirmasi
            $total = KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)->count();
            $diterima = KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)->where('status', 'diterima')->count();
            $semuaKonfirmasi = $total > 0 && $total == $diterima;
            if ($semuaKonfirmasi && $kunjungan->status == 'Terjadwal') {
                $kunjungan->update(['status' => 'Dikonfirmasi']);
                // Semua setuju -> stok tools berkurang & tercatat di tiap engineer
                $this->aktifkanToolsKunjungan($kunjungan->fresh());
            }

            // QA ronde 2 #6: pesan dinamis — teks "menunggu engineer lainnya"
            // hanya muncul bila memang masih ada yang belum konfirmasi.
            $pesanKonfirmasi = $semuaKonfirmasi
                ? 'Konfirmasi diterima!'
                : 'Konfirmasi diterima! Menunggu konfirmasi engineer lainnya.';
            return redirect()->back()->with('success', $pesanKonfirmasi);
        }

        $kunjungan->update(['status' => 'Dikonfirmasi']);
        // Pimpinan konfirmasi langsung -> tools ikut diaktifkan
        $this->aktifkanToolsKunjungan($kunjungan->fresh());
        return redirect()->back()->with('success', 'Jadwal kunjungan berhasil dikonfirmasi dan diterima!');
    }

    // 12a. QA #5: Pimpinan menyetujui penolakan engineer (baru boleh diganti)
    public function accPenolakan($id, $idEngineer)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if (!in_array($user->id_role, [1, 2])) {
            abort(403, 'Hanya Pimpinan yang dapat menyetujui penolakan.');
        }

        $konfirmasi = KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_engineer', $idEngineer)
            ->firstOrFail();

        if ($konfirmasi->status !== 'ditolak') {
            return redirect()->back()->with('error', 'Penolakan ini sudah diproses sebelumnya.');
        }

        $konfirmasi->update(['status' => 'ditolak_disetujui']);
        $nama = $konfirmasi->engineer->user->nama ?? 'Engineer';
        return redirect()->back()->with('success', 'Penolakan ' . $nama . ' disetujui. Silakan ganti dengan engineer lain.');
    }

    // 12a2. QA #5: Pimpinan menolak penolakan engineer (engineer tetap ditugaskan,
    // status konfirmasi kembali menunggu agar engineer mengkonfirmasi ulang)
    public function tolakPenolakan($id, $idEngineer)
    {
        $kunjungan = $this->cariKunjungan($id);

        $user = Auth::user();
        if (!in_array($user->id_role, [1, 2])) {
            abort(403, 'Hanya Pimpinan yang dapat menolak penolakan.');
        }

        $konfirmasi = KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_engineer', $idEngineer)
            ->firstOrFail();

        if ($konfirmasi->status !== 'ditolak') {
            return redirect()->back()->with('error', 'Penolakan ini sudah diproses sebelumnya.');
        }

        $konfirmasi->update(['status' => 'menunggu', 'alasan_ditolak' => null, 'waktu_konfirmasi' => null]);
        $nama = $konfirmasi->engineer->user->nama ?? 'Engineer';
        return redirect()->back()->with('success', 'Penolakan ' . $nama . ' ditolak. Engineer tetap ditugaskan dan diminta mengkonfirmasi ulang.');
    }

    // 12b. Pimpinan ganti engineer yang menolak dengan engineer lain
    public function gantiEngineer(Request $request, $id)
    {
        $kunjungan = $this->cariKunjungan($id);

        $request->validate([
            'id_engineer_lama' => 'required|exists:engineers,id_engineer',
            'id_engineer_baru' => 'required|exists:engineers,id_engineer|different:id_engineer_lama',
        ]);

        $lama = $request->id_engineer_lama;
        $baru = $request->id_engineer_baru;

        // QA #5: engineer yang menolak hanya boleh diganti setelah penolakannya
        // di-ACC pimpinan. Penggantian di luar jalur penolakan (engineer belum
        // merespons) tetap diperbolehkan.
        $konfLama = KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)
            ->where('id_engineer', $lama)
            ->first();
        if ($konfLama && $konfLama->status === 'ditolak') {
            return redirect()->back()->with('error', 'Penolakan engineer ini belum diputuskan. Setujui (ACC) dulu penolakannya sebelum mengganti engineer.');
        }

        DB::transaction(function () use ($kunjungan, $lama, $baru) {
            // Pengganti OTOMATIS mewarisi role yang menolak:
            // jika yang menolak lead -> pengganti jadi lead; jika support -> jadi support
            if ($kunjungan->id_engineer == $lama) {
                $kunjungan->update(['id_engineer' => $baru]);
            } else {
                // Jika support, ganti di pivot
                $kunjungan->supportEngineers()->detach($lama);
                $kunjungan->supportEngineers()->attach($baru);
            }
            // Hapus konfirmasi lama, buat baru status menunggu
            KunjunganKonfirmasi::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_engineer', $lama)->delete();
            KunjunganKonfirmasi::firstOrCreate(
                ['id_kunjungan' => $kunjungan->id_kunjungan, 'id_engineer' => $baru],
                ['status' => 'menunggu']
            );
            // Tools kunjungan ikut pindah ke engineer pengganti
            $sudahPunya = PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                ->where('id_engineer', $baru)
                ->where('status', 'Dipinjam')
                ->exists();
            if ($sudahPunya) {
                // Pengganti sudah punya catatan (misal tadinya support):
                // pindahkan jumlah tercatat engineer lama ke catatan pengganti
                // (per tool) agar total jumlah fisik tidak hilang (QA #11),
                // lalu hapus catatan lama.
                $catatanLama = PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                    ->where('id_engineer', $lama)
                    ->where('status', 'Dipinjam')
                    ->get();
                foreach ($catatanLama as $cl) {
                    if ($cl->jumlah > 0) {
                        $recBaru = PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                            ->where('id_tool', $cl->id_tool)
                            ->where('id_engineer', $baru)
                            ->where('status', 'Dipinjam')
                            ->first();
                        if ($recBaru) {
                            $recBaru->increment('jumlah', $cl->jumlah);
                        }
                    }
                    $cl->delete();
                }
            } else {
                PeminjamanTool::where('id_kunjungan', $kunjungan->id_kunjungan)
                    ->where('id_engineer', $lama)
                    ->where('status', 'Dipinjam')
                    ->update(['id_engineer' => $baru]);
            }
        });

        $namaBaru = Engineer::with('user')->find($baru);
        $roleDiganti = $kunjungan->id_engineer == $baru ? 'LEAD' : 'SUPPORT';
        return redirect()->back()->with('success', 'Engineer berhasil diganti dengan ' . ($namaBaru->user->nama ?? 'engineer baru') . ' sebagai ' . $roleDiganti . '. Menunggu konfirmasi darinya.');
    }
}