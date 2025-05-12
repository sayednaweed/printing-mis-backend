<?php

namespace App\Http\Controllers\api\app\hr\department;

use App\Models\Department;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\DepartmentTran;

class DepartmentController extends Controller
{
    public function departments()
    {
        $locale = App::getLocale();

        // Start building the query
        $query = DB::table('departments as dep')
            ->join('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'dep.id')
                    ->where('dept.language_name', $locale);
            })
            ->select(
                "dep.id",
                "dept.value as name",
                "dep.created_at",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function department($id)
    {
        $department = DB::table('department_trans as dept')
            ->where('dept.department_id', $id)
            ->select(
                'dept.department_id',
                DB::raw("MAX(CASE WHEN dept.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN dept.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN dept.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('dept.department_id')
            ->first();
        return response()->json(
            [
                "id" => $department->department_id,
                "english" => $department->english,
                "farsi" => $department->farsi,
                "pashto" => $department->pashto,
            ],
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
            'farsi' => 'required|string',
        ]);

        $department = Department::create([]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            DepartmentTran::create([
                "value" => $request["{$name}"],
                "department_id" => $department->id,
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
            'department' => [
                "id" => $department->id,
                "name" => $name,
                "created_at" => $department->created_at
            ]
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
        // 1. Find
        $department = Department::find($request->id);
        if (!$department) {
            return response()->json([
                'message' => __('app_translation.department_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $trans = DepartmentTran::where('department_id', $request->id)
            ->select('id', 'language_name', 'value')
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->value = $request["{$name}"];
            $tran->save();
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
            'department' => [
                "id" => $department->id,
                "name" => $name,
                "created_at" => $department->created_at
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
