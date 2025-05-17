<?php

namespace App\Http\Controllers\api\app\hr\employee\status;

use App\Models\StatusTran;
use Illuminate\Http\Request;
use App\Models\EmployeeStatus;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\StatusTypeEnum;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class StatusController extends Controller
{
    //

    public function employeeStatusList($id)
    {

        $locale = App::getLocale();

        $empActSts =  EmployeeStatus::where('employee_id', $id)
            ->where('active', 1)->select('status_id')->first();

        $status =   StatusTran::join('status_types')
            ->select('id', 'value as status')
            ->whereNotIn('id', $empActSts->id)
            ->where('status_type_id', StatusTypeEnum::employement->value)->get();


        return response()->json(
            [
                $status
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }


    public function employeeStatuses($id)
    {
        $locale = App::getLocale();
        $status = DB::table('employees as emp')
            ->where('emp.id', $id)
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('emp.id', '=', 'empt.employee_id')
                    ->where('empt.language_name', '=', $locale);
            })
            ->join('employee_statuses as emps', 'emp.id', '=', 'emps.employee_id')
            ->join('status_trans as stt', function ($join) use ($locale) {
                $join->on('stt.status_id', '=', 'emps.status_id')
                    ->where('stt.language_name', '=', $locale);
            })
            ->join('users as us', 'us.id', '=', 'emps.user_id')
            ->select(
                'emps.id',
                DB::raw("CONCAT(empt.first_name, ' ', empt.last_name) as name"),
                'stt.value as status_name',
                'stt.status_id',
                'us.full_name as saved_by',
                'emps.description',
                'emps.active',
                'emps.created_at',
            )
            ->get();

        return response()->json(
            $status,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function statusUpdate(Request $request)
    {



        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'status_id' => 'required|integer|exists:statuses,id',
            'description' => 'nullable|string'
        ]);

        // 
        EmployeeStatus::where('employee_id', $request->employee_id)
            ->update(['active' => 0]);

        EmployeeStatus::create([
            'status_id' => $request->status_id,
            'employee_id' => $request->employee_id,
            'user_id' => $request->user()->id,
            'description' => $request->description,
            'active' => 1
        ]);


        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
