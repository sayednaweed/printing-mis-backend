<?php

namespace App\Repositories\Attendance;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceStatusTran;
use App\Enums\Attendance\AttendanceStatusEnum;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function store($attendances, $today, $takeCheckIn, $authUser, $shift_id)
    {
        $now = Carbon::now()->format('H:i:s');
        DB::beginTransaction();

        if ($takeCheckIn) {
            // For check-in, just insert all records at once (batch insert)
            $insertData = [];
            foreach ($attendances as $entry) {
                $insertData[] = [
                    'employee_id' => $entry['employee_id'],
                    'check_in_time' => $now,
                    'description' => $entry['description'],
                    'check_in_status_id' => $entry['status_type_id'],
                    'check_in_taken_by' => $authUser->id,
                    'shift_id' => $shift_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Attendance::insert($insertData);
        } else {
            // For check-out, fetch all today's attendances for involved employees at once
            $employeeIds = collect($attendances)->pluck('employee_id')->all();

            $attendancesToday = Attendance::whereIn('employee_id', $employeeIds)
                ->whereDate('created_at', $today)
                ->get()
                ->keyBy('employee_id');

            foreach ($attendances as $entry) {
                $employeeId = $entry['employee_id'];

                if (isset($attendancesToday[$employeeId])) {
                    $attendance = $attendancesToday[$employeeId];

                    $attendance->update([
                        'check_out_time' => $now,
                        'check_out_taken_by' => $authUser->id,
                        'description' => $entry['description'] ?? $attendance->description,
                        'check_out_status_id' => $entry['status_type_id'],
                        'updated_at' => now(),
                    ]);
                }
                // Optional: handle case when attendance record does not exist for check-out
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
            att.shift_id ,
            usci.username as check_in_taken_by,
            usco.username as check_out_taken_by,
            SUM(CASE WHEN att.check_in_status_id = ? AND (att.check_out_status_id = ? OR att.check_out_status_id IS NULL) THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN att.check_in_status_id = ? AND (att.check_out_status_id = ?) THEN 1 ELSE 0 END) AS absent,
            SUM(CASE WHEN att.check_in_status_id = ? OR att.check_out_status_id = ? THEN 1 ELSE 0 END) AS `leave`,
            SUM(CASE WHEN att.check_in_status_id NOT IN (?, ?, ?) OR att.check_out_status_id NOT IN (?, ?, ?) THEN 1 ELSE 0 END) AS other
          FROM attendances att
        JOIN users usci ON usci.id = att.check_in_taken_by
        LEFT JOIN users usco ON usco.id = att.check_out_taken_by
        GROUP BY att.check_in_time, att.check_out_time, usci.username, usco.username, att.shift_id, att.created_at
    ", [$presentId, $presentId, $absentId, $absentId, $leaveId, $leaveId, $presentId, $absentId, $leaveId, $presentId, $absentId, $leaveId]);

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
            SUM(CASE WHEN att.check_in_status_id = ? AND (att.check_out_status_id = ? OR att.check_out_status_id IS NULL) THEN 1 ELSE 0 END) AS present,
            SUM(CASE WHEN att.check_in_status_id = ? OR att.check_out_status_id = ? THEN 1 ELSE 0 END) AS absent,
            SUM(CASE WHEN att.check_in_status_id = ? OR att.check_out_status_id = ? THEN 1 ELSE 0 END) AS `leave`,
            SUM(CASE WHEN att.check_in_status_id NOT IN (?, ?, ?) OR att.check_out_status_id NOT IN (?, ?, ?) THEN 1 ELSE 0 END) AS other
        FROM attendances att
        JOIN users usci ON usci.id = att.check_in_taken_by
        LEFT JOIN users usco ON usco.id = att.check_out_taken_by
        WHERE DATE(att.created_at) = CURDATE()
        GROUP BY att.check_in_time, att.check_out_time, usci.username, usco.username, att.created_at
        LIMIT 1
    ", [$presentId, $presentId, $absentId, $absentId, $leaveId, $leaveId, $presentId, $absentId, $leaveId, $presentId, $absentId, $leaveId]);

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
            ->leftJoin('attendances as att', function ($join) use ($date) {
                $join->on('att.employee_id', '=', 'emp.id')
                    ->whereDate('att.created_at', $date);
            })
            ->leftJoin('users as us_chk_in', 'us_chk_in.id', '=', 'att.check_in_taken_by')
            ->leftJoin('users as us_chk_out', 'us_chk_out.id', '=', 'att.check_out_taken_by')
            ->select(
                'emp.id',
                'emp.picture',
                'emp.hr_code',
                'empt.first_name',
                'empt.last_name',
                'att.description',
                'att.check_in_time',
                'att.check_out_time',
                'us_chk_in.username as chk_in_username',
                'us_chk_out.username as chk_out_username',
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
                'check_in_time' => $emp->check_in_time,
                'check_out_time' => $emp->check_out_time,
                'detail' => $emp->description ?? '',
                'check_in_taken_by' => $emp->chk_in_username,
                'check_out_taken_by' => $emp->chk_out_username,
                'status' => $statuses->values(),
            ];
        });
    }
}
