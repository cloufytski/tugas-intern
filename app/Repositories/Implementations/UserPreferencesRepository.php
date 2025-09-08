<?php

namespace App\Repositories\Implementations;

use App\Models\UserPreferences;
use App\Traits\LogTransactionTrait;
use Illuminate\Support\Facades\Auth;

class UserPreferencesRepository
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'USER_PREFERENCES';

    public function allByUserId(?string $menu)
    {
        $query = UserPreferences::where('id_user', Auth::user()->id);

        if ($menu) {
            $query->where('menu', $menu);
        }

        return $query->get();
    }

    public function updateOrCreate(array $data)
    {
        $model = UserPreferences::updateOrCreate([
            'log_module' => $data['log_module'],
            'id_user' => Auth::user()->id,
            'menu' => $data['menu'],
            'filter_tag' => $data['filter_tag'],
        ], [
            'value' => $data['value'],
        ]);
        $this->log(logType: 'UPDATE', model: $model, data: $data);
        return $model;
    }

    public function delete(string $menu, string $filterTag)
    {
        $model = UserPreferences::firstWhere([
            ['id_user', Auth::user()->id],
            ['menu', $menu],
            ['filter_tag', $filterTag]
        ]);
        $model->delete();
        $this->log(logType: 'DELETE', model: $model, data: $filterTag);
        return $model;
    }
}
