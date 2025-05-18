<?php

namespace App\Http\Controllers\api\app\hr\assignment;

use Illuminate\Http\Request;
use App\Enums\Types\HireTypeEnum;
use App\Models\PositionAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\PositionAssignmentDuration;

class EmployeeAssignment extends Controller
{
    public function employeeAssignments($id)
    {
        $locale = App::getLocale();
        $tr = [];
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

    public function changePosition(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'position_id' => 'required|integer',
            'department_id' => 'required|integer',
            'hire_type_id' => 'required|integer',
            'shift_id' => 'required|integer',
            'salary' => 'required|string',
            'overtime_rate' => 'required|string',
            'currency_id' => 'required|integer',
            'position_change_type_id' => 'required|integer',
        ]);

        $postAss = PositionAssignment::create([
            'employee_id' => $request->employee_id,
            'position_id' => $request->position_id,
            'department_id' => $request->department_id,
            'hire_type_id' => $request->hire_type_id,
            'hire_date' => $request->hire_date,
            'shift_id' => $request->shift_id,
            'salary' => $request->salary,
            'overtime_rate' => $request->overtime_rate,
            'currency_id' => $request->currency_id,
            'position_change_type_id' => $request->position_change_type_id,
        ]);

        if (HireTypeEnum::permanent->value != $request->hire_type_id) {
            $request->validate([
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            PositionAssignmentDuration::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'position_assignment_id' => $postAss->id
            ]);
        }
        return response()->json(
            [
                'message' => __('app_translation.success'),
                'data' => [
                    'id' => $postAss->id,
                    'employee_id' => $request->employee_id,
                    'position' => $request->position,
                    'department' => $request->department,
                    'hire_type' => $request->hire_type,
                    'currency' => $request->currency,
                    "hire_date" => $postAss->created_at,
                    'shift' => $request->shift,
                    'salary' => $request->salary,
                    'overtime_rate' => $request->overtime_rate,
                    'position_change_type_id' => $request->position_change_type_id,
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
