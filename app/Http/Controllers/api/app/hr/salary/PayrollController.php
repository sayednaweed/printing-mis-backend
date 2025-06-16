<?php

namespace App\Http\Controllers\api\app\hr\salary;

use App\Models\Payroll;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\PayrollPayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Attendance\AttendanceStatusEnum;

class PayrollController extends Controller
{
    public function salaries() {}
    public function salaryStore(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'year' => 'required|integer|min:2025|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'salary' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'employee_id' => 'required|exists:employees,id',
            'account_id' => 'required|exists:accounts,id',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        // Wrap in a transaction to ensure data integrity
        DB::beginTransaction();


        $payroll = Payroll::create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'payment_date' => now(),
            'salary' => $validated['salary'],
            'deductions' => $validated['deductions'],
            'net_pay' => $validated['salary'] - $validated['deductions'],
            'overtime_amount' => $validated['overtime_amount'] ?? 0,
            'employee_id' => $validated['employee_id'],
            'account_id' => $validated['account_id'],
        ]);

        PayrollPayment::create([
            'payroll_id' => $payroll->id,
            'paid_amount' => $validated['paid_amount'],
            'paid_date' => now(),
        ]);

        DB::commit();

        return response()->json(['message' => __('app_translation.success')], 201);
    }
    public function salaryPayment(Request $request)
    {


        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date'
        ]);

        $id = $request->employee_id;
        $locale = App::getLocale();
        $employee = DB::table('employees as emp')
            ->where('emp.id', $id)
            ->join('position_assignments as posa', function ($join) {
                $join->on('emp.id', '=', 'posa.employee_id')
                    ->whereRaw('posa.id = (
                 SELECT MAX(id) FROM position_assignments WHERE employee_id = emp.id
             )');
            })
            ->join('pay_period_trans as ppt', function ($join) use ($locale) {
                $join->on('ppt.pay_period_id', 'posa.pay_period_id')
                    ->where('ppt.language_name', $locale);
            })
            ->join('currencies as cur', 'cur.id', '=', 'posa.currency_id')
            ->select(
                'emp.picture as profile',
                'posa.overtime_rate',
                'ppt.value as pay_period',
                'posa.salary as month_salary',
                DB::raw("CONCAT(posa.salary, ' ', cur.symbol) as salary")
            )
            ->first();

        $advance = DB::table('employee_payments as empp')
            ->where('empp.employee_id', $id)
            ->where('empp.is_remain', true)
            ->sum('empp.remain_amount');

        $date = Carbon::parse($request->date); // auto-parses ISO8601

        $year = $date->year;
        $month = $date->month;
        $day = $date->day;


        $check_in_count = DB::table('attendances as att')
            ->where('att.check_in_status_id', AttendanceStatusEnum::present->value)
            ->whereMonth('att.created_at', $month)
            ->whereYear('att.created_at', $year)
            ->count();
        $check_out_count = DB::table('attendances as att')
            ->where('att.check_out_status_id', AttendanceStatusEnum::present->value)
            ->whereMonth('att.created_at', $month)
            ->whereYear('att.created_at', $year)
            ->count();




        // manual date
        // $month_day = Carbon::createFromDate(2025, 2, 1)->daysInMonth;

        $present_day = ($check_in_count + $check_out_count) / 2;

        // 4. Get number of days in current month
        $month_day = Carbon::now()->daysInMonth;


        // 5. Calculate per-day and net salary
        $per_day_salary = $employee->month_salary / $month_day;
        $net_salary = round($per_day_salary * $present_day, 2);


        return response()->json([
            'message' => __('app_translation.success'),
            'data' => [
                'profile' => $employee->profile,
                'overtime' => $employee->overtime_rate,
                'salary' => $employee->salary,
                'pay_period' => $employee->pay_period,
                'present_day' => $present_day,
                'net_salary' => $net_salary,
                'advance_payment' => $advance ?? 0
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
