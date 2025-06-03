<?php

namespace App\Repositories\Attendance;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceStatusTran;
use App\Enums\Attendance\AttendanceStatusEnum;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function store($attendances, $today, $takeCheckIn, $authUser)
    {
        $now = Carbon::now();
        DB::beginTransaction();
        foreach ($attendances as $entry) {
            $employeeId = $entry['employee_id'];

            // Find today's attendance record for this employee
            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('created_at', $today)
                ->first();
            if ($takeCheckIn) {
                Attendance::create([
                    'employee_id' => $employeeId,
                    'check_in_time' => $now->format('H:i:s'),
                    'description' => $entry['description'],
                    'attendance_status_id' => $entry['status_type_id'],
                    'check_in_taken_by' => $authUser->id,
                ]);
            } else {
                $attendance->update([
                    'check_out_time' => $now->format('H:i:s'),
                    'check_out_taken_by' => $authUser->id,
                    'description' => $entry['description'] ? $entry['description'] : $attendance->description,
                    'attendance_status_id' => $entry['status_type_id'],
                ]);
            }
        }

        DB::commit();

        return $this->attendance();
    }
    public function attendancies()
    {
        $absentId = AttendanceStatusEnum::absent->value;
        $presentId = AttendanceStatusEnum::present->value;
        $leaveId = AttendanceStatusEnum::leave->value;

        // Execute raw SQL query
        $rawData = DB::select("
        SELECT
            att.created_at,
            att.check_in_time,
            att.check_out_time,
            usci.username as check_in_taken_by,
            usco.username as check_out_taken_by,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS absent,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS `leave`,
            SUM(CASE WHEN att.attendance_status_id NOT IN (?, ?, ?) THEN 1 ELSE 0 END) AS other
        FROM attendances att
        JOIN users usci ON usci.id = att.check_in_taken_by
        LEFT JOIN users usco ON usco.id = att.check_out_taken_by
        GROUP BY att.check_in_time, att.check_out_time, usci.username, usco.username, att.created_at
    ", [$presentId, $absentId, $leaveId, $presentId, $absentId, $leaveId]);

        // Convert to collection
        return collect($rawData);
    }
    public function attendance()
    {
        $absentId = AttendanceStatusEnum::absent->value;
        $presentId = AttendanceStatusEnum::present->value;
        $leaveId = AttendanceStatusEnum::leave->value;

        // Execute raw SQL query
        $rawData = DB::select("
        SELECT
            att.created_at,
            att.check_in_time,
            att.check_out_time,
            usci.username as check_in_taken_by,
            usco.username as check_out_taken_by,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS absent,
            SUM(CASE WHEN att.attendance_status_id = ? THEN 1 ELSE 0 END) AS `leave`,
            SUM(CASE WHEN att.attendance_status_id NOT IN (?, ?, ?) THEN 1 ELSE 0 END) AS other
        FROM attendances att
        JOIN users usci ON usci.id = att.check_in_taken_by
        LEFT JOIN users usco ON usco.id = att.check_out_taken_by
        WHERE DATE(att.created_at) = CURDATE()
        GROUP BY att.check_in_time, att.check_out_time, usci.username, usco.username, att.created_at
        LIMIT 1
    ", [$presentId, $absentId, $leaveId, $presentId, $absentId, $leaveId]);

        // Convert to collection
        return $rawData[0] ?? [];
    }
    public function showAttendance($date, $locale)
    {
        $employees = DB::table('employees as emp')
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('emp.id', '=', 'empt.employee_id')
                    ->where('empt.language_name', $locale);
            })
            ->leftJoin('leaves as lv', function ($join) use ($date) {
                $join->on('emp.id', '=', 'lv.employee_id')
                    ->whereDate('lv.start_date', '<=', $date)
                    ->whereDate('lv.end_date', '>=', $date);
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

        return $employees->map(function ($emp) use ($statuses, $leaveStatusValue, $absentStatusValue) {
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
    }
}
