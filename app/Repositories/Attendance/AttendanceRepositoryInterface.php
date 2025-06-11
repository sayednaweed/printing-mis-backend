<?php

namespace App\Repositories\Attendance;

interface AttendanceRepositoryInterface
{
    /**
     * Creates a attendance.
     *
     *
     * @param mixed $attendances
     * @param string $today
     * @param boolean $takeCheckIn
     * @param mixed $authUser
     * @param mixed $shift_id
     * @return void
     */
    public function store($attendances, $today, $takeCheckIn, $authUser, $shift_id);
    /**
     * returns attendancies.
     *
     *
     * @return mixed
     */
    public function attendancies();
    /**
     * returns attendance.
     *
     *
     * @return mixed
     */
    public function attendance();
    /**
     * returns list of today attendance.
     *
     * @param string $date
     * @param string $locale
     * @return mixed
     */
    public function showAttendance($date, $locale);
}
