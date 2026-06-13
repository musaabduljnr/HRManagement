<?php

namespace App\Modules\Pim\Repositories;

use DB;
use App\Repositories\EloquentRepository;
use App\User;
use Carbon\Carbon;
use App\Modules\Pim\Repositories\Interfaces\EmployeeRepositoryInterface;

class EmployeeRepository extends EloquentRepository implements EmployeeRepositoryInterface
{
    protected $allowedAttributes = ['model'];

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->where('role', $this->model::USER_ROLE_EMPLOYEE)->get();
    }
 

    public function getBirthdays($date = false)
    {
        if (!$date) {
            $date = Carbon::now();
        } else {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        $items = $this->model
            ->where('role', $this->model::USER_ROLE_EMPLOYEE)
            ->whereRaw(
                DB::connection()->getDriverName() === 'sqlite'
                    ? "strftime('%m', birth_date) = ?"
                    : 'MONTH(birth_date) = ?',
                [str_pad($date->month, 2, '0', STR_PAD_LEFT)]
            )
            ->get();

        $events = [];
        foreach ($items as $key => $value) {
            $birthday = Carbon::createFromFormat('Y-m-d', $value->birth_date);
            $events[]= [
                'title' => $value->first_name.' '.$value->last_name,
                'start' => Carbon::createFromDate(null, $birthday->month, $birthday->day)->format('Y-m-d')
            ];
        }

        return $events;
    }

    public function pluckName()
    {
        $concat = DB::connection()->getDriverName() === 'sqlite'
            ? 'first_name || " " || last_name'
            : 'CONCAT(first_name, " ", last_name)';

        return $this->model->select(DB::raw("$concat as name, id"))
            ->where('role', $this->model::USER_ROLE_EMPLOYEE)
            ->pluck('name', 'id');
    }

    public function getSelect2Data($filter = '', $offset = 0, $limit = 10)
    {
        $qry = DB::table('users')
            ->select('id', 'first_name', 'last_name', 'email');
        if ($filter) {
            $concat = DB::connection()->getDriverName() === 'sqlite'
                ? 'first_name || " " || last_name'
                : 'CONCAT(first_name, " ", last_name)';
            $qry->whereRaw("$concat like ?", [$filter.'%'])
                ->orWhere('first_name', 'like', $filter.'%')
                ->orWhere('last_name', 'like', $filter.'%');
        }
        $total = $qry->count();
        $items = $qry->skip($offset)->take($limit)->get();
        return ['incomplete_results' => false, 'total_count' => $total, 'items' => $items];
    }

    public function getSelect2Selection($id)
    {
        $items = $this->model->find($id);
        return ['incomplete_results' => false, 'total_count' => 1, 'items' => $items];
    }
}
