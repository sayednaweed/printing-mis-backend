<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Enums\Attendance\AttendanceStatusEnum;
use Illuminate\Http\Request;
use App\Models\AttendanceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceStatusTran;

class AttendaceController extends Controller
{
    public function employeeList()
    {
        $locale = App::getLocale();

        // Fetch the employees and join with the employee_trans and leaves tables
        $query = DB::table('employees as emp')
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('emp.id', '=', 'empt.employee_id')
                    ->where('empt.language_name', $locale);
            })
            ->leftJoin('leaves as lv', 'emp.id', '=', 'lv.employee_id') // Left join leaves table to check if employee has leave
            ->select(
                "emp.id",
                "empt.first_name",
                "empt.last_name",
                "lv.start_date as leave_start_date",
                "lv.end_date as leave_end_date"
            )
            ->get(); // Execute the query


        // Fetch all the statuses from the attendance_status_tran table
        $status = AttendanceStatusTran::where('language_name', $locale)
            ->select('value as status', 'attendance_status_id as status_id')
            ->get();

        // Define the current date to check for leave
        $currentDate = now()->format('Y-m-d'); // You can replace this with any specific date

        // Filter out the "Leave" status from the status array
        $statusWithoutLeave = $status->filter(function ($statusItem) {
            return $statusItem->status !== AttendanceStatusEnum::leave->value;
        });

        // Create an array to hold the result
        $arr = [];

        foreach ($query as $item) {
            // Check if the employee has leave and if the current date falls within the leave period
            if ($item->leave_start_date && $item->leave_end_date) {
                // Check if the current date falls within the leave period
                if ($currentDate >= $item->leave_start_date && $currentDate <= $item->leave_end_date) {
                    // Employee is on leave for the current date
                    $leaveStatus = $status->firstWhere('status', AttendanceStatusEnum::leave->value); // Fetch the "Leave" status
                    $arr[] = [
                        'id' => $item->id,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'status' => $leaveStatus ? $leaveStatus->status : 'Leave' // Default to "Leave" if status is not found
                    ];
                    continue; // Skip the attendance status check since the employee is on leave
                }
            }

            // Add employee data with attendance status (if they are not on leave)
            $arr[] = [
                'id' => $item->id,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'status' => $statusWithoutLeave // Add all the attendance statuses that are not "Leave"
            ];
        }

        // Return or process the array
        return $arr;
    }
    public function store(Request $request) {}
}
