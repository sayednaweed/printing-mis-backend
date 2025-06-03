<?php

namespace App\Http\Controllers\api\app\hr\hire;

use App\Models\HireType;
use App\Enums\LanguageEnum;
use App\Models\HireTypeTran;
use Illuminate\Http\Request;
use App\Enums\Status\StatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class HireController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        // Start building the query
        $query = DB::table('hire_types as ht')
            ->leftjoin('hire_type_trans as htt', function ($join) use ($locale) {
                $join->on('htt.hire_type_id', '=', 'ht.id')
                    ->where('htt.language_name', $locale);
            })
            ->select(
                "ht.id",
                "ht.detail",
                "htt.value as name",
                "ht.created_at",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function edit($id)
    {
        $hiretype = DB::table('hire_types as ht')
            ->where('ht.id', $id)
            ->join('hire_type_trans as htt', 'ht.id', '=', 'htt.hire_type_id')
            ->select(
                'ht.id',
                'ht.detail',
                DB::raw("MAX(CASE WHEN htt.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN htt.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN htt.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('ht.id', 'ht.detail')
            ->first();
        return response()->json(
            [
                "id" => $hiretype->id,
                "english" => $hiretype->english,
                "farsi" => $hiretype->farsi,
                "pashto" => $hiretype->pashto,
                "detail" => $hiretype->detail,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail' => 'string',
            'english' => 'required|string',
            'pashto' => 'required|string',
            'farsi' => 'required|string',
        ]);

        DB::beginTransaction();
        $hiretype = HireType::create(['detail' => $request->detail]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            HireTypeTran::create([
                "value" => $request["{$name}"],
                "hire_type_id" => $hiretype->id,
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
            'hire_type' => [
                'id' => $hiretype->id,
                'name' => $name,
                'detail' => $request->detail,
                'created_at' => $hiretype->created_at,
            ],
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'english' => 'required|string',
            'pashto' => 'required|string',
            'farsi' => 'required|string',
        ]);
        $hireType = HireType::where('id', $request->id)->first();
        if (!$hireType) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        DB::beginTransaction();
        $hireType->detail = $request->detail;
        $hireType->save();

        $trans = HireTypeTran::where('hire_type_id', $hireType->id)->select('id', 'language_name', 'value')->get();
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
            'hire_type' => [
                'id' => $hireType->id,
                'name' => $name,
                'detail' => $request->detail,
                'created_at' => $hireType->created_at,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function hrCodes()
    {
        $statuses =  [StatusEnum::working->value, StatusEnum::hired->value];
        $tr = DB::table('employees as emp')
            ->join('employee_statuses as es', function ($join) use ($statuses) {
                $join->on('es.employee_id', '=', 'emp.id')
                    ->where('es.active', 1)
                    ->whereIn('es.status_id', $statuses);
            })
            ->select(
                "emp.id",
                "emp.picture",
                "emp.hr_code as name",
            )->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
