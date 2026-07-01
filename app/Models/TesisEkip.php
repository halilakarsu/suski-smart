<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesisEkip extends Model
{
    use HasFactory;

    protected $table = 'tesis_ekipleri';

    protected $fillable = ['ad'];
}
