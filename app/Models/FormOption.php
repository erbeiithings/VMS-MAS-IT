<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormOption extends Model
{
    protected $table = 'form_options';
    protected $guarded = [];

    public static function daftar(string $grup)
    {
        return self::where('grup', $grup)->orderBy('nama')->pluck('nama')->toArray();
    }
}
