<?php

namespace App\Traits;

use App\Services\LogService;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            LogService::log(
                'Oluşturuldu',
                get_class($model),
                $model->id,
                class_basename($model) . " oluşturuldu.",
                null,
                $model->getAttributes()
            );
        });

        static::updated(function (Model $model) {
            $oldData = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newData = $model->getDirty();

            LogService::log(
                'Güncellendi',
                get_class($model),
                $model->id,
                class_basename($model) . " güncellendi.",
                $oldData,
                $newData
            );
        });

        static::deleted(function (Model $model) {
            LogService::log(
                'Silindi',
                get_class($model),
                $model->id,
                class_basename($model) . " silindi.",
                $model->getAttributes(),
                null
            );
        });
    }
}
