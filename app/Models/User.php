<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama', 'username', 'email', 'password', 'kontak', 'id_role', 'status_akun', 'dibuat_oleh'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function engineer()
    {
        return $this->hasOne(Engineer::class, 'id_pengguna', 'id_pengguna');
    }
}