<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncKuyuCbs extends Command
{
    protected $signature = 'sync:kuyu-cbs';

    protected $description = 'Sync cbs_x, cbs_y from tesis_ariza_kayitlari to kuyular by matching kuyu_no';

    public function handle()
    {
        $updated = DB::update('
            UPDATE kuyular k
            INNER JOIN (
                SELECT kuyu_no, cbs_x, cbs_y
                FROM tesis_ariza_kayitlari
                WHERE kuyu_no IS NOT NULL AND cbs_x IS NOT NULL AND cbs_y IS NOT NULL
                GROUP BY kuyu_no, cbs_x, cbs_y
            ) a ON k.kuyu_no = a.kuyu_no
            SET k.cbs_x = a.cbs_x, k.cbs_y = a.cbs_y
            WHERE k.kuyu_no IS NOT NULL
        ');

        $this->info("{$updated} kuyu kaydı güncellendi.");
    }
}
