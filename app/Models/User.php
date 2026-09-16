<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    // Tambahin 'username_changed_at' ke dalam fillable
    protected $fillable = [
        'nama', 'username', 'email', 'password', 'kontak', 'id_role', 'status_akun', 'dibuat_oleh', 'username_changed_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    // Convert string dari database jadi format waktu (Carbon) biar gampang dihitung
    protected $casts = [
        'username_changed_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function engineer()
    {
        return $this->hasOne(Engineer::class, 'id_pengguna', 'id_pengguna');
    }
}