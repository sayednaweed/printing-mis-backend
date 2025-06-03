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
use App\Models\ApplicationConfiguration;
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
        $date = $request->query('created_at') ? Carbon::parse($request->query('created_at')) : Carbon::today();
        $now = Carbon::now();

        $attendanceTime = DB::table('application_configurations as ac')
            ->where('ac.id', 1)
            ->select(
                'ac.id',
                'ac.attendance_check_in_time',
                'ac.attendance_check_out_time',
            )->first();
        $attendance = Attendance::whereDate('created_at', $date)
            ->first();
        if ($attendance) {
            if ($attendance->check_out_time != null) {
                return response()->json([
                    "message" => __('app_translation.already_attendance_taken'),
                ], 500, [], JSON_UNESCAPED_UNICODE);
            } else {
                // 2. Check check_out_time attendance
                $checkoutTime  = Carbon::createFromTimeString($attendanceTime->attendance_check_out_time);
                if ($now->format('H:i:s') < $checkoutTime->format('H:i:s')) {
                    return response()->json([
                        "message" => __('app_translation.checkout_must_be_before') . ' ' . $attendanceTime->attendance_check_out_time,
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
            }
        } else {
            // 3. Check check_in_time attendance
            $checkInTime  = Carbon::createFromTimeString($attendanceTime->attendance_check_in_time);
            if ($now->format('H:i:s') > $checkInTime->format('H:i:s')) {
                return response()->json([
                    "message" => __('app_translation.checkin_must_be_before') . ' ' . $attendanceTime->attendance_check_in_time,
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

        $attendanceTime = DB::table('application_configurations as ac')
            ->where('ac.id', 1)
            ->select(
                'ac.id',
                'ac.attendance_check_in_time',
                'ac.attendance_check_out_time',
            )->first();

        // 1. Validate for attendance
        $attendance = Attendance::whereDate('created_at', $today)
            ->first();
        if ($attendance) {
            if ($attendance->check_out_time != null) {
                return response()->json([
                    "message" => __('app_translation.already_attendance_taken'),
                ], 500, [], JSON_UNESCAPED_UNICODE);
            } else {
                // 2. Take check_out_time attendance
                $checkoutTime  = Carbon::createFromTimeString($attendanceTime->attendance_check_out_time);
                if ($now->format('H:i:s') < $checkoutTime->format('H:i:s')) {
                    return response()->json([
                        "message" => __('app_translation.checkin_must_be_before') . ' ' . $attendanceTime->attendance_check_out_time,
                    ], 500, [], JSON_UNESCAPED_UNICODE);
                }
                // 2.1 Take attendance
                $tr = $this->attendanceRepository->store($request->attendances, $today, false, $authUser);
            }
        } else {
            // 3. Take check_in_time attendance
            $checkInTime  = Carbon::createFromTimeString($attendanceTime->attendance_check_in_time);
            if ($now->format('H:i:s') > $checkInTime->format('H:i:s')) {
                return response()->json([
                    "message" => __('app_translation.checkout_must_be_before') . ' ' . $attendanceTime->attendance_check_in_time,
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
            // 2.1 Take attendance
            $tr = $this->attendanceRepository->store($request->attendances, $today, true, $authUser);
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
