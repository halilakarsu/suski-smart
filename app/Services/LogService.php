<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogService
{
    /**
     * Aktivite logu kaydeder.
     *
     * @param string $action Aksiyon (örn: 'created', 'updated', 'deleted', 'login')
     * @param string|null $model Model adı
     * @param int|null $modelId Model ID'si
     * @param string|null $description Açıklama
     * @param array|null $oldData Eski veriler
     * @param array|null $newData Yeni veriler
     * @return ActivityLog
     */
    public static function log($action, $model = null, $modelId = null, $description = null, $oldData = null, $newData = null)
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip(),
        ]);
    }
}
