<?php

namespace App\Http\Controllers\api\app\hr\attendance;

use App\Models\Status;
use App\Models\StatusTran;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\StatusTypeEnum;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        $query =  DB::table('statuses as s')
            ->where('s.status_type_id', StatusTypeEnum::leave_type->value)
            ->join('status_trans as stt', function ($join) use ($locale) {
                $join->on('stt.status_id', '=', 's.id')
                    ->where('stt.language_name', $locale);
            })
            ->select('s.id', 'stt.value as name', 's.created_at')
            ->get();

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
            'english' => 'required|string',
            'pashto' => 'required|string',
            'farsi' => 'required|string'
        ]);

        DB::beginTransaction();

        $status =  Status::create([
            'status_type_id' => StatusTypeEnum::leave_type->value,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            StatusTran::create([
                "value" => $request["{$name}"],
                "status_id" => $status->id,
                "language_name" => $code,
            ]);
        }
        DB::commit();
        $locale = App::getLocale();
        $name = $request->english;
        if ($locale === 'fa') {
            $name = $request->farsi;
        }
        if ($locale === 'ps') {
            $name = $request->pashto;
        }

        return response()->json([
            'leave_type' => [
                'id' => $status->id,
                'name' => $name,
                'created_at' => $status->created_at,
            ],
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function edit($id)
    {
        $status = Status::find($id);
        if (!$status) {
            return response()->json([
                'message' => __('app_translation.leave_type_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = DB::table('status_trans as st')
            ->where('st.status_id', $id)
            ->select(
                'st.status_id',
                DB::raw("MAX(CASE WHEN st.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN st.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN st.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('st.status_id')
            ->first();
        return response()->json(
            [
                "id" => $status->status_id,
                "english" => $status->english,
                "farsi" => $status->farsi,
                "pashto" => $status->pashto,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'pashto' => 'required|string',
        ]);

        $status = Status::where('id', $request->id)->first();
        if (!$status)
            return response()->json([
                'message' => __('app_translation.leave_type_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);

        DB::beginTransaction();
        $trans = StatusTran::where('status_id', $status->id)
            ->select('id', 'language_name', 'value')
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->value = $request["{$name}"];
            $tran->save();
        }
        DB::commit();

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale === 'fa') {
            $name = $request->farsi;
        } else if ($locale === 'ps') {
            $name = $request->pashto;
        }

        return response()->json([
            'leave_type' => [
                'id' => $status->id,
                'name' => $name,
                'created_at' => $status->created_at,
            ],
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
