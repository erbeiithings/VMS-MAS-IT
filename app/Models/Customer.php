<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id_customer';
    protected $guarded = [];

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'id_customer', 'id_customer');
    }
}