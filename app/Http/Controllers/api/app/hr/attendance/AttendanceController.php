<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AttendanceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\AttendanceStatusTran;
use App\Enums\Attendance\AttendanceStatusEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\hr\attendance\StoreAttendanceRequest;

class AttendanceController extends Controller
{


    public function index(Request $request)
    {
        $locale = App::getLocale();
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $absentId = AttendanceStatusEnum::absent->value;
        $presentId = AttendanceStatusEnum::present->value;
        $leaveId = AttendanceStatusEnum::leave->value;

        // Execute raw SQL query
        $rawData = DB::select("
        SELECT
            att.created_at,
            us.username as taken_by,
            DATE(att.created_at) AS date,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS absent,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS `leave`,
            SUM(CASE WHEN att.attendance_status_id NOT IN (?, ?, ?) THEN 1 ELSE 0 END) AS other
        FROM attendances att
        JOIN users us ON us.id = att.taken_by_id
        GROUP BY att.created_at, us.username, DATE(att.created_at)
        ORDER BY DATE(att.created_at) DESC, us.username ASC
    ", [$presentId, $absentId, $leaveId, $presentId, $absentId, $leaveId]);

        // Convert to collection
        $summary = collect($rawData);

        // Apply filters
        $this->applyDate($summary, $request);
        $this->applyFilters($summary, $request);
        $this->applySearch($summary, $request);

        // Manual pagination
        $total = $summary->count();
        $paginated = new LengthAwarePaginator(
            $summary->slice(($page - 1) * $perPage, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );


        return response()->json([
            "attendance" => $paginated,
        ], 200, [], JSON_UNESCAPED_UNICODE);

    }




    // 

    public function employeeAttendance()
    {
        $locale = App::getLocale();
        $currentDate = Carbon::now()->toDateString();

        $attendance = Attendance::whereDate('created_at', Carbon::today())->first();
        if ($attendance && $attendance->check_in_time && $attendance->check_out_time) {
            return response()->json([
                'message' => __('app_translation.attendance_taken'),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        $employees = DB::table('employees as emp')
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('emp.id', '=', 'empt.employee_id')
                    ->where('empt.language_name', $locale);
            })
            ->leftJoin('leaves as lv', function ($join) use ($currentDate) {
                $join->on('emp.id', '=', 'lv.employee_id')
                    ->whereDate('lv.start_date', '<=', $currentDate)
                    ->whereDate('lv.end_date', '>=', $currentDate);
            })
            ->select(
                'emp.id',
                'emp.picture',
                'emp.hr_code',
                'empt.first_name',
                'empt.last_name',
                DB::raw('CASE WHEN lv.id IS NOT NULL THEN 1 ELSE 0 END as has_leave')
            )
            ->get();

        // Attendance statuses without leave
        $leaveStatusValue = AttendanceStatusEnum::leave->value;
        $absentStatusValue = AttendanceStatusEnum::absent->value;

        $statuses = AttendanceStatusTran::where('language_name', $locale)
            ->select('value as status', 'attendance_status_id as status_id')
            ->get();



        $data = $employees->map(function ($emp) use ($statuses, $leaveStatusValue, $absentStatusValue) {

            $statuses = $statuses->map(function ($status) use ($emp, $leaveStatusValue, $absentStatusValue) {

                $selected = $emp->has_leave
                    ? ($status->status_id === $leaveStatusValue)
                    : ($status->status_id === $absentStatusValue);

                return [
                    'name' => $status->status,
                    'id' => $status->status_id,
                    'selected' => $selected,
                ];
            });

            return [
                'id' => $emp->id,
                'hr_code' => $emp->hr_code,
                'picture' => $emp->picture,
                'first_name' => $emp->first_name,
                'last_name' => $emp->last_name,
                'detail' => '',
                'status' => $statuses->values(),
            ];
        });

        return response()->json([
            'data' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreAttendanceRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $today = Carbon::today();

        DB::beginTransaction();

        foreach ($data['attendances'] as $entry) {
            $employeeId = $entry['employee_id'];

            // Find today's attendance record for this employee
            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('created_at', $today)
                ->first();

            // CASE 1: Already both check-in and check-out recorded
            if ($attendance && $attendance->check_in_time && $attendance->check_out_time) {

                return response()->json([
                    'message' => __('app_translation.already_attendance_taken')
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            // CASE 2: Check-in exists, but no check-out — update same row
            if ($attendance && $attendance->check_in_time && !$attendance->check_out_time) {
                $attendance->update([
                    'check_out_time' => $entry['status_type_id'] === AttendanceStatusEnum::present->value ? now() : '',
                    'taken_by_id' => $user->id,
                    'description' => $entry['description'] ?? $attendance->description,
                    'attendance_status_id' => $entry['status_type_id'],
                ]);
            }

            // CASE 3: No record or no check-in — create a new check-in
            if (!$attendance) {
                Attendance::create([
                    'employee_id' => $employeeId,
                    'check_in_time' => $entry['status_type_id'] === AttendanceStatusEnum::present->value ? now() : '',
                    'description' => $entry['description'] ?? null,
                    'attendance_status_id' => $entry['status_type_id'],
                    'taken_by_id' => $user->id,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
        ]);
    }

    public function statuses()
    {
        $locale = App::getLocale();
        $tr = DB::table('attendance_statuses as as')
            ->join('attendance_status_trans as ast', function ($join) use ($locale) {
                $join->on('ast.attendance_status_id', '=', 'as.id')
                    ->where('ast.language_name', $locale);
            })
            ->select('as.id', "ast.value as name", 'as.created_at')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function show() {}
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
