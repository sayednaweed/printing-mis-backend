<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AttendanceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\hr\attendance\StoreAttendanceRequest;
use App\Repositories\Attendance\AttendanceRepositoryInterface;

class AttendanceController extends Controller
{
    protected $attendanceRepository;
    public function __construct(
        AttendanceRepositoryInterface $attendanceRepository,
    ) {
        $this->attendanceRepository = $attendanceRepository;
    }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $summary = $this->attendanceRepository->attendancies();
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

    public function showAttendance(Request $request)
    {
        $locale = App::getLocale();
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s'); // 24-hour format
        $shiftId = $request->query('shift_id');
        $viewAttendance = $request->query('created_at') ? true : false;
        $date = $viewAttendance ? Carbon::parse($request->query('created_at')) : Carbon::today();
        $shift = DB::table('shifts as s')
            ->where('s.id', $shiftId)
            ->join('shift_trans as st', function ($join) use ($locale) {
                $join->on('st.shift_id', '=', 's.id')
                    ->where('st.language_name', $locale);
            })
            ->select(
                's.id',
                's.check_in_start',
                's.check_in_end',
                's.check_out_start',
                's.check_out_end',
                'st.value',
            )->first();

        if (!$shift) {
            return response()->json([
                "message" => __('app_translation.shift_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        if ($viewAttendance) {
            return response()->json([
                'data' => $this->attendanceRepository->showAttendance($date, $locale),
                'shift' => ['id' => $shift->id, 'name' => $shift->value],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        $check_in_start = Carbon::createFromTimeString($shift->check_in_start)->format('H:i:s');
        $check_in_end = Carbon::createFromTimeString($shift->check_in_end)->format('H:i:s');
        $check_out_start = Carbon::createFromTimeString($shift->check_out_start)->format('H:i:s');
        $check_out_end = Carbon::createFromTimeString($shift->check_out_end)->format('H:i:s');

        $attendance = DB::table('attendances as a')
            ->whereDate('a.created_at', $date)
            ->where('a.shift_id', $shiftId)
            ->select(
                'a.id',
                'a.check_in_time',
                'a.check_out_time',
                'a.created_at',
            )->first();

        if ($attendance) {
            if ($attendance->check_in_time == null) {
                if (!($currentTime >= $check_in_start && $currentTime <= $check_in_end)) {
                    return response()->json([
                        "message" => __('app_translation.checkin_must_be') . ' ' . $check_in_start . '-' . $check_in_end,
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
            } else if ($attendance->check_out_time == null) {
                if (!($currentTime >= $check_out_start && $currentTime <= $check_out_end)) {
                    return response()->json([
                        "message" => __('app_translation.checkout_must_be') . ' ' . $check_out_start . '-' . $check_out_end,
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json([
                    "message" => __('app_translation.already_attendance_taken'),
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        }
        return response()->json([
            'data' => $this->attendanceRepository->showAttendance($date, $locale),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreAttendanceRequest $request)
    {
        $authUser = $request->user();
        $request->validated();
        $today = Carbon::today();
        $now = Carbon::now();
        $tr = [];
        $currentTime = $now->format('H:i:s'); // 24-hour format
        $shiftId = $request->shift_id;
        $shift = DB::table('shifts as s')
            ->where('s.id', $shiftId)
            ->select(
                's.id',
                's.check_in_start',
                's.check_in_end',
                's.check_out_start',
                's.check_out_end',
            )->first();

        if (!$shift) {
            return response()->json([
                "message" => __('app_translation.shift_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $check_in_start = Carbon::createFromTimeString($shift->check_in_start)->format('H:i:s');
        $check_in_end = Carbon::createFromTimeString($shift->check_in_end)->format('H:i:s');
        $check_out_start = Carbon::createFromTimeString($shift->check_out_start)->format('H:i:s');
        $check_out_end = Carbon::createFromTimeString($shift->check_out_end)->format('H:i:s');

        $attendance = DB::table('attendances as a')
            ->whereDate('a.created_at', $today)
            ->where('a.shift_id', $shiftId)
            ->select(
                'a.id',
                'a.check_in_time',
                'a.check_out_time',
                'a.created_at',
            )->first();

        if ($attendance) {
            if ($attendance->check_out_time == null) {
                if (!($currentTime >= $check_out_start && $currentTime <= $check_out_end)) {
                    return response()->json([
                        "message" => __('app_translation.att_dealine_exp'),
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
                $tr = $this->attendanceRepository->store($request->attendances, $today, false, $authUser, $shiftId);
            } else {
                return response()->json([
                    "message" => __('app_translation.already_attendance_taken'),
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            if (!($currentTime >= $check_in_start && $currentTime <= $check_in_end)) {
                return response()->json([
                    "message" => __('app_translation.att_dealine_exp'),
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
            $tr = $this->attendanceRepository->store($request->attendances, $today, true, $authUser, $shiftId);
        }

        return response()->json([
            'message' => __('app_translation.success'),
            'attendance' => $tr,
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
            $query->where('att.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('att.created_at', '<=', $endDate);
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
