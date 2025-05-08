<?php

namespace App\Http\Controllers\api\app\hr\salary;

use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Models\PayrollPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SalaryController extends Controller
{
    //


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
}
