<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncKuyuAboneNo extends Command
{
    protected $signature = 'sync:kuyu-abone';

    protected $description = 'Sync abone_no from tesis_ariza_kayitlari to kuyular by matching kuyu_no';

    public function handle()
    {
        $updated = DB::update('
            UPDATE kuyular k
            INNER JOIN (
                SELECT kuyu_no, abone_no
                FROM tesis_ariza_kayitlari
                WHERE kuyu_no IS NOT NULL AND abone_no IS NOT NULL
                GROUP BY kuyu_no, abone_no
            ) a ON k.kuyu_no = a.kuyu_no
            SET k.abone_no = a.abone_no
            WHERE k.kuyu_no IS NOT NULL
        ');

        $this->info("{$updated} kuyu kaydı güncellendi.");
    }
}
