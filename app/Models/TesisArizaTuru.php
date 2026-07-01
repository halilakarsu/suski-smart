<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesisArizaTuru extends Model
{
    use HasFactory;

    protected $table = 'tesis_ariza_turleri';

    protected $fillable = ['ad'];
}
