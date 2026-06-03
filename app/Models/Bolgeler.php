<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bolgeler extends Model
{
    use HasFactory;

    protected $table = 'bolgeler';
    protected $fillable = ['bolge_adi', 'bolge_kodu'];
}
