<?php

namespace App\Traits;

use App\Models\Log\LogTransaction;

trait LogTransactionTrait
{
    protected function log(string $logType, $model, $data)
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $logMessage = $this->logMessage($model, $data, $logType == 'UPDATE');
        LogTransaction::create([
            'log_module' => $this->logModule ?? 'Module',
            'log_type' => $logType,
            'log_model' => $this->modelName ?? static::class,
            'log_description' => "{$logType} {$this->modelName} ID: {$model->id} | {$logMessage}",
        ]);
    }

    private function logMessage($model, $data, $isUpdate = false)
    {
        if (is_array($data)) {
            return collect($data)
                ->except(['_token', '_TOKEN', 'id']) // skip CSRF token
                ->map(function ($newValue, $key) use ($model, $isUpdate) {
                    $oldValue = $model->$key ?? 'null';
                    $newValueStr = is_array($newValue) ? json_encode($newValue) : $newValue;
                    $oldValueStr = is_array($oldValue) ? json_encode($oldValue) : $oldValue;

                    if ($isUpdate && $oldValueStr != $newValueStr) {
                        return strtoupper($key) . ": {$oldValueStr} >>> {$newValueStr}";
                    } else {
                        return strtoupper($key) . ": {$newValueStr}";
                    }
                })
                ->implode(' | ');
        }
        return ''; // if DELETE AND RESTORE
    }
}
