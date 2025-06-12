<?php

namespace App\Http\Controllers\api\app\hr\salary;

use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Models\PayrollPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
    public function salaryPayment($id)
    {
        $employee = DB::table('employees as emp')
            ->where('emp.id', $id)
            ->join('position_assignments as posa', function ($join) {
                $join->on('emp.id', '=', 'posa.employee_id')
                    ->whereRaw('posa.id = (
                 SELECT MAX(id) FROM position_assignments WHERE employee_id = emp.id
             )');
            })
            ->join('currencies as cur', 'cur.id', '=', 'posa.currency_id')
            ->select(
                'emp.picture as profile',
                'posa.overtime_rate',
                DB::raw("CONCAT(posa.salary, ' ', cur.symbol) as salary")
            )
            ->first();

        return response()->json([
            'message' => __('app_translation.success'),
            'data' => [
                'profile' => $employee->profile,
                'overtime' => $employee->overtime_rate,
                'salary' => $employee->salary
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
