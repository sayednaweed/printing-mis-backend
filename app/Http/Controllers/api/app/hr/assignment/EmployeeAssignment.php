<?php

namespace App\Http\Controllers\api\app\hr\assignment;

use Illuminate\Http\Request;
use App\Models\PositionAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class EmployeeAssignment extends Controller
{
    public function employeeAssignments($id)
    {

        $locale = App::getLocale();
        $tr = [];

        //         id: string;
        //   hire_type: string;
        //   salary: string;
        //   shift: string;
        //   position: string;
        //   position_change_type: string;
        //   overtime_rate: string;
        //   currency: string;
        //   department: string;
        //   hire_date: string;


        $locale = App::getLocale();

        $tr = DB::table('position_assignments as poas')
            ->join('position_trans as pos', function ($join) use ($locale) {
                $join->on('pos.position_id', '=', 'poas.position_id')
                    ->where('pos.language_name', $locale);
            })
            ->join('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'poas.department_id')
                    ->where('dept.language_name', $locale);
            })
            ->join('hire_type_trans as ht', function ($join) use ($locale) {
                $join->on('ht.hire_type_id', '=', 'poas.hire_type_id')
                    ->where('ht.language_name', $locale);
            })
            ->join('shift_trans as sh', function ($join) use ($locale) {
                $join->on('sh.shift_id', '=', 'poas.shift_id')
                    ->where('sh.language_name', $locale);
            })
            ->join('currency_trans as cur', function ($join) use ($locale) {
                $join->on('cur.currency_id', '=', 'poas.currency_id')
                    ->where('cur.language_name', $locale);
            })
            ->leftJoin('position_change_type_trans as pct', function ($join) use ($locale) {
                $join->on('pct.position_change_type_id', '=', 'poas.position_change_type_id')
                    ->where('pct.language_name', $locale);
            })
            ->select(
                "poas.id",
                "ht.value as hire_type",
                "poas.salary",
                "sh.value as shift",
                "pos.value as position",
                "pct.value as position_change_type",
                "poas.overtime_rate",
                "cur.value as currency",
                "dept.value as department",
                "poas.hire_date"
            )
            ->where('poas.employee_id', $id)
            ->get();


        // return $tr->toSql();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
