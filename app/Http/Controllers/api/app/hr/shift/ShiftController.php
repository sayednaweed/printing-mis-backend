<?php

namespace App\Http\Controllers\api\app\hr\shift;

use App\Models\Shift;
use App\Models\ShiftTran;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ShiftController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        // Start building the query
        $query = DB::table('shifts as sh')
            ->join('shift_trans as sht', function ($join) use ($locale) {
                $join->on('sh.id', '=', 'sht.shift_id')
                    ->where('sht.language_name', $locale);
            })
            ->select(
                "sh.id",
                "sh.start_time",
                "sh.end_time",
                "sht.value as name",
                "sh.created_at",
                "sh.detail",
            )->get();

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
            'start_time' => 'required',
            'end_time' => 'required',
            'english' => 'required|string',
            'pashto' => 'required|string',
            'farsi' => 'required|string',
        ]);

        $shift = Shift::create([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'detail' => $request->detail,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ShiftTran::create([
                "value" => $request["{$name}"],
                "shift_id" => $shift->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->pashto;
        }
        return response()->json([
            'message' => __('app_translation.success'),
            'shift' => [
                "id" => $shift->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                "name" => $name,
                "created_at" => $shift->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function edit($id)
    {
        $shift = DB::table('shifts as s')
            ->where('s.id', $id)
            ->join('shift_trans as st', 's.id', '=', 'st.shift_id')
            ->select(
                's.id',
                's.start_time',
                's.end_time',
                's.detail',
                DB::raw("MAX(CASE WHEN st.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN st.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN st.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('s.id', 's.start_time', 's.end_time', 's.detail')
            ->first();
        return response()->json(
            [
                "id" => $shift->id,
                "english" => $shift->english,
                "farsi" => $shift->farsi,
                "pashto" => $shift->pashto,
                "start_time" => $shift->start_time,
                "end_time" => $shift->end_time,
                "detail" => $shift->detail,
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
            'start_time' => 'required',
            'end_time' => 'required',
            'english' => 'required|string',
            'pashto' => 'required|string',
            'farsi' => 'required|string',
        ]);
        $shift = Shift::where('id', $request->id)->first();
        if (!$shift) {
            return response()->json([
                'message' => __('app_translation.shift_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        DB::beginTransaction();
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->detail = $request->detail;
        $shift->save();

        $trans = ShiftTran::where('shift_id', $shift->id)->select('id', 'language_name', 'value')->get();
        //Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran = $trans->where('language_name', $code)->first();
            $tran->value = $request["{$name}"];
            $tran->save();
        }
        DB::commit();

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->pashto;
        }

        return response()->json([
            'message' => __('app_translation.success'),
            'shift' => [
                'id' => $shift->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'name' => $name,
                'detail' => $request->detail,
                'created_at' => $shift->created_at,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
