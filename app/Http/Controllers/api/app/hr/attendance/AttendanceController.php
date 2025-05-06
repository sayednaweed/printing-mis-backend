<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\AttendanceStatusTran;
use App\Enums\Attendance\AttendanceStatusEnum;
use App\Http\Requests\hr\attendance\StoreAttendanceRequest;

class AttendanceController extends Controller
{

    public function attendaceList(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $tr =  Attendance::join('employees as emp', 'attendances.employee_id', '=', 'emp.id')
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->join(
                'attendance_status_trans as astt',
                function ($join) use ($locale) {
                    $join->on('astt.attendance_status_id', '=', 'attendances.attendance_status_id')
                        ->where('astt.language_name', $locale);
                }
            )
            ->select(
                'emp.id as employee_id',
                'emp.hr_code',
                'empt.first_name',
                'empt.last_name',
                'astt.value as attendance_status',
                'check_in_time',
                'check_out_time' ?? '',

            );



        $this->applyDate($tr, $request);
        $this->applyFilters($tr, $request);
        $this->applySearch($tr, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $query = $tr->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

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


    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreAttendanceRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $today = Carbon::today();

        foreach ($data['attendances'] as $entry) {
            // Check if an attendance record exists for the employee today
            $existing = Attendance::where('employees_id', $entry['employee_id'])
                ->whereDate('created_at', $today)
                ->first();

            if ($existing) {
                // If already checked in today, update the check-out time
                $existing->update([
                    'check_out_time' => now(),
                    'taken_by_id' => $user->id,
                    'description' => $entry['description'] ?? $existing->description,
                    'attendance_status_type_id' => $entry['attendance_status_type_id'],
                ]);
            } else {
                // No attendance yet today — create new and set check-in time
                Attendance::create([
                    'employees_id' => $entry['employee_id'],
                    'check_in_time' => now(),
                    'description' => $entry['description'] ?? null,
                    'attendance_status_type_id' => $entry['attendance_status_type_id'],
                    'taken_by_id' => $user->id,
                ]);
            }
        }

        return response()->json([
            'message' => __('app_translation.success'),
        ]);
    }


    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('emp.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('emp.created_at', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = [

                'first_name' => 'empt.first_name',
                'last_name' => 'empt.last_name',
                'hr_code' => 'emp.hr_code',
                'attendance_status' => 'astt.value',
            ];
            // Ensure that the search column is allowed
            if (in_array($searchColumn, array_keys($allowedColumns))) {
                $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
            }
        }
    }
    // filter function
    protected function applyFilters($query, $request)
    {
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 
        $allowedColumns = [
            'first_name' => 'empt.first_name',
            'last_name' => 'empt.last_name',
            'hr_code' => 'emp.hr_code',
            'attendance_status' => 'astt.value',
        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
