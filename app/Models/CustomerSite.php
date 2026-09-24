<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSite extends Model
{
    protected $table = 'customer_sites';
    protected $primaryKey = 'id_site';
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }
}