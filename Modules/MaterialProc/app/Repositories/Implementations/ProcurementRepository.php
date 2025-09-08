<?php

namespace Modules\MaterialProc\Repositories\Implementations;

use App\Traits\LogTransactionTrait;
use Illuminate\Support\Facades\DB;
use Modules\MaterialProc\Models\Procurement;
use Modules\MaterialProc\Repositories\Interfaces\ProcurementRepositoryInterface;

class ProcurementRepository implements ProcurementRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MaterialProc';
    protected $modelName = 'PROCUREMENT';
    protected $table = 'procurement_ts';
    protected $materialTable = 'material_master';

    private $procurementJoin = [
        'supplier:id,supplier',
        'material:id,material_description',
        'plant:id,description',
        'history'
    ];

    // for Dashboard
    public function getTotalByDateGroup(?string $startDate, ?string $endDate, array $params)
    {
        switch ($params['date_group'] ?? 'weekly') {
            case 'yearly':
                $dateFormat = "YYYY";
                break;
            case 'monthly':
                $dateFormat = "YYYY-MM";
                break;
            case 'weekly':
                $dateFormat = "IYYY-IW"; // ISO year + week format
                break;
            case 'daily':
            default:
                $dateFormat = "YYYY-MM-DD";
                break;
        }

        $query = DB::table($this->table)
            ->join("$this->materialTable as m", "id_material", "=", "m.id")
            ->selectRaw("
                TO_CHAR(eta, ?) AS period,
                m.material_description,
                m.id_group,
                m.id_category,
                SUM(qty) AS total_qty
            ", [$dateFormat]);

        if (isset($startDate) && isset($endDate)) {
            $query->whereBetween('eta', [$startDate, $endDate]);
        }
        // Material
        if (isset($params['categories'])) {
            $query->whereIn('m.id_category', $params['categories']);
        }
        if (isset($params['groups'])) {
            $query->whereIn('m.id_group', $params['groups']);
        }

        $query->groupByRaw("period , m.material_description, m.id_group, m.id_category")
            ->orderByRaw("period ASC");
        return $query->get();
    }

    public function query()
    {
        return Procurement::query()
            ->with($this->procurementJoin);
    }

    public function all()
    {
        return Procurement::all();
    }

    public function findById(int $id)
    {
        return Procurement::findOrFail($id)->load($this->procurementJoin);
    }

    public function create(array $data)
    {
        $model = Procurement::create($data);
        $this->log(logType: 'CREATE', model: $model, data: $data);
        return $model;
    }

    public function update(int $id, array $data)
    {
        $model = $this->findById($id);
        $this->log(logType: 'UPDATE', model: $model, data: $data);
        $model->update($data);
        return $model->load($this->procurementJoin);
    }

    public function delete(int $id)
    {
        $model = $this->findById($id);
        $model->delete();
        $this->log(logType: 'DELETE', model: $model, data: $id);
        return $model;
    }
}
