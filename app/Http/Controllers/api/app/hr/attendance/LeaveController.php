<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $tr =  Leave::join('employees as emp', 'leaves.employee_id', '=', 'emp.id')
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->join('users as us', 'us.id', '=', 'leaves.user_id')
            ->join('status_trans as stt', function ($join) use ($locale) {
                $join->on('stt.status_id', '=', 'leaves.status_id')
                    ->where('stt.language_name', $locale);
            })
            ->select(
                'leaves.id as id',
                'emp.picture',
                'us.full_name as user',
                'emp.hr_code',
                DB::raw("CONCAT(empt.first_name, ' ', empt.last_name) as employee_name"),
                'stt.value as leave_type',
                'leaves.reason',
                'leaves.start_date',
                'leaves.end_date',
                'leaves.created_at'
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

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'status_id' => 'required|exists:statuses,id',
            'reason' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $locale = App::getLocale();
        $authUser = $request->user();
        $employee = DB::table('employees as e')
            ->where('e.id', $request->employee_id)
            ->join('employee_trans as et', function ($join) use (&$locale) {
                $join->on('et.employee_id', '=', 'e.id')
                    ->where('et.language_name', $locale);
            })
            ->select(
                'et.first_name',
                'et.last_name',
                'e.hr_code',
                'e.picture',
                'e.id'
            )
            ->first();
        if (!$employee) {
            return response()->json(
                [
                    'message' => __('app_translation.employee_not_found'),
                ],
                404,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        $leave = Leave::create([
            'user_id' => $authUser->id,
            'employee_id' => $request->employee_id,
            'status_id' => $request->status_id,
            'reason' => $request->reason,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
        return response()->json(
            [
                'message' => __('app_translation.success'),
                'leave' => [
                    'id' => $employee->id,
                    'profile' => $employee->picture,
                    'hr_code' => $employee->hr_code,
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'leave_type' => $request->status,
                    'saved_by' => $authUser->username,
                    'created_at' => $leave->created_at,
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function edit($id)
    {
        $locale = App::getLocale();

        // Find the leave with joined status
        $leave = DB::table('leaves as l')
            ->where('l.id', $id)
            ->join('statuses as s', 's.id', '=', 'l.status_id')
            ->select(
                'l.id',
                'l.employee_id',
                'l.status_id',
                's.name as status',
                'l.reason',
                'l.start_date',
                'l.end_date',
                'l.created_at'
            )
            ->first();

        if (!$leave) {
            return response()->json([
                'message' => __('app_translation.leave_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // Get employee translation
        $employee = DB::table('employees as e')
            ->where('e.id', $leave->employee_id)
            ->join('employee_trans as et', function ($join) use ($locale) {
                $join->on('et.employee_id', '=', 'e.id')
                    ->where('et.language_name', $locale);
            })
            ->select('et.first_name', 'et.last_name', 'e.hr_code', 'e.picture')
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'leave' => [
                'id' => $leave->id,
                'employee_id' => $leave->employee_id,
                'status' => ['id' => $leave->status_id, 'status' => $leave->status],
                'reason' => $leave->reason,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'profile' => $employee->picture,
                'hr_code' => $employee->hr_code,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'created_at' => $leave->created_at,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function update(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|exists:leaves,id',
            'employee_id' => 'required|exists:employees,id',
            'status_id' => 'required|exists:statuses,id',
            'reason' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $locale = App::getLocale();

        // Get employee translation
        $employee = DB::table('employees as e')
            ->where('e.id', $request->employee_id)
            ->join('employee_trans as et', function ($join) use ($locale) {
                $join->on('et.employee_id', '=', 'e.id')
                    ->where('et.language_name', $locale);
            })
            ->select('et.first_name', 'et.last_name', 'e.hr_code', 'e.picture')
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // Find the existing leave record
        $leave = Leave::find($request->leave_id);
        if (!$leave) {
            return response()->json([
                'message' => __('app_translation.leave_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // Update the leave
        $leave->update([
            'employee_id' => $request->employee_id,
            'status_id' => $request->status_id,
            'reason' => $request->reason,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        return response()->json([
            'message' => __('app_translation.success'),
            'leave' => [
                'profile' => $employee->picture,
                'hr_code' => $employee->hr_code,
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'leave_type' => $request->status, // You may want to get the actual status name here
                'saved_by' => $request->user()->username,
                'updated_at' => $leave->updated_at,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    // 
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
                'leave_type' => 'lett.value',
                'hr_code' => 'emp.hr_code',
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
            'leave_type' => 'lett.value',
            'hr_code' => 'emp.hr_code',
        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
