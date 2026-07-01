<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesisArac extends Model
{
    use HasFactory;

    protected $table = 'tesis_araclar';

    protected $fillable = [
        'sira_no',
        'plaka',
        'aracin_cinsi',
        'arac_tipi',
        'kullanici_personel',
        'irtibat',
        'kullanildigi_is',
    ];
}
