<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hamveri extends Model
{
    protected $table = 'hamveri';

    protected $fillable = [
        'import_log_id',
        'fatura_no',
        'row_hash',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}
