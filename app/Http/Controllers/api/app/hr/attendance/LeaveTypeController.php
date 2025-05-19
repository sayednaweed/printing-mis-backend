<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Enums\Types\StatusTypeEnum;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        $query =  Status::join('status_trans as stt', function ($join) use ($locale) {
            $join->on('stt.status_id', '=', 'statuses.id')
                ->where('stt.language_name', $locale);
        })
            ->select('stt.status_id as id', 'stt.value as name')
            ->where('statuses.status_type_id', StatusTypeEnum::leave_type->value)
            ->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
