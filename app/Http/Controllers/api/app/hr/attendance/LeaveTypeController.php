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

    public function store(Request $request)
    {

        $request->validate([
            'name_english' => 'required|string',
            'name_pashto' => 'required|string',
            'name_farsi' => 'required|string'
        ]);


        DB::transaction();

        $status =  Status::create([
            'status_type_id' => StatusTypeEnum::leave_type->value,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            StatusTran::create([
                "value" => $request["name_{$name}"],
                "status_id" => $status->id,
                "language_name" => $code,
            ]);
        }
        DB::commit();
        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale === 'fa') {
            $name = $request->name_farsi;
        }
        if ($locale === 'ps') {
            $name = $request->name_pashto;
        }


        $data = ['id' => $status->id, 'name' => $name];

        return response()->json(
            ['leave_type' => $data],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function edit($id)
    {
        // Find the status by ID
        $status = Status::find($id);
        if (!$status) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // Get all translations for this status
        $translations = DB::table('status_trans')
            ->where('status_id', $id)
            ->pluck('value', 'language_name'); // Returns ['en' => '...', 'fa' => '...', 'ps' => '...']

        return response()->json([
            'leave_type' => [
                'id' => $status->id,
                'translations' => [
                    'name_english' => $translations['en'] ?? null,
                    'name_farsi' => $translations['fa'] ?? null,
                    'name_pashto' => $translations['ps'] ?? null,
                ]
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function update(Request $request)
    {

        $request->validate([
            'leave_type_id' => 'required|integer|exists:statuses,id',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'name_pashto' => 'required|string'
        ]);


        $status =   Status::find($request->leave_type_id);


        DB::transaction();

        $status =  Status::create([
            'status_type_id' => StatusTypeEnum::leave_type->value,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $statusTran = StatusTran::where('status_id', $status->id)
                ->where('language_name', $code);

            $statusTran->value = $request["name_{$name}"];
            $statusTran->save();
        }
        DB::commit();

        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale === 'fa') {
            $name = $request->name_farsi;
        }
        if ($locale === 'ps') {
            $name = $request->name_pashto;
        }


        $data = ['id' => $status->id, 'name' => $name];

        return response()->json(
            ['leave_type' => $data],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
