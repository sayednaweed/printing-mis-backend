<?php

namespace App\Http\Controllers\api\app\expense\expenseType;

use App\Models\Icon;
use App\Models\Expense;
use App\Models\IconTran;
use App\Enums\LanguageEnum;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Models\ExpenseTypeIcon;
use App\Models\ExpenseTypeTran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $tr = DB::table('expense_types as ext')
            ->join('expense_type_trans as extt', function ($join) use ($locale) {
                $join->on('ext.id', '=', 'extt.expense_type_id')
                    ->where('extt.language_name', $locale);
            })
            ->select(
                'ext.id',
                'extt.value as name',
                'ext.created_at',
            )->orderBy('ext.id', 'desc')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'icons.*.id' => 'required',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'pashto' => 'required|string',
        ]);
        DB::beginTransaction();

        $expenseType = ExpenseType::create();
        foreach ($request->icons as $iconData) {
            // Create the icon
            ExpenseTypeIcon::create([
                'icon_id' => $iconData['id'],
                'expense_type_id' => $expenseType->id,
            ]);
        }

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ExpenseTypeTran::create([
                "value" => $request["{$name}"],
                "expense_type_id" => $expenseType->id,
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

        return response()->json(
            [
                'expense_type' => [
                    'id' => $expenseType->id,
                    'name' => $name,
                    'created_at' =>  $expenseType->created_at,
                ],
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $locale = App::getLocale();

        $type = DB::table('expense_type_trans as ett')
            ->where('ett.expense_type_id', $id)
            ->select(
                'ett.expense_type_id as id',
                DB::raw("MAX(CASE WHEN ett.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN ett.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN ett.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('ett.expense_type_id')
            ->first();

        if (!$type)
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 400, [], JSON_UNESCAPED_UNICODE);

        $icons = DB::table('icons as i')
            ->leftJoin('expense_type_icons as eti', function ($join) use ($id) {
                $join->on('eti.icon_id', '=', 'i.id')
                    ->where('eti.expense_type_id', $id);
            })
            ->leftJoin('icon_trans as it', function ($join) use ($locale) {
                $join->on('it.icon_id', '=', 'i.id')
                    ->where('it.language_name', $locale);
            })
            ->select(
                'i.id',
                'it.value as name',
                'i.path',
                DB::raw('CASE WHEN eti.icon_id IS NOT NULL THEN true ELSE false END as selected')
            )
            ->get();

        return response()->json([
            'expense_type' => [
                'id' => $type->id,
                'english' => $type->english,
                'farsi' => $type->farsi,
                'pashto' => $type->pashto,
                'icons' => $icons,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'icons.*.id' => 'required',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'pashto' => 'required|string',
            'id' => 'required',
        ]);

        $expenseType = ExpenseType::find($request->id);
        if (!$expenseType)
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 400, [], JSON_UNESCAPED_UNICODE);

        DB::beginTransaction();

        ExpenseTypeIcon::where('expense_type_id', '=', $expenseType->id)->delete();
        foreach ($request->icons as $iconData) {
            // Create the icon
            ExpenseTypeIcon::create([
                'icon_id' => $iconData['id'],
                'expense_type_id' => $expenseType->id,
            ]);
        }

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ExpenseTypeTran::create([
                "value" => $request["{$name}"],
                "expense_type_id" => $expenseType->id,
                "language_name" => $code,
            ]);
        }

        $trans = ExpenseTypeTran::where('expense_type_id', $request->id)
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
        }
        if ($locale === 'ps') {
            $name = $request->pashto;
        }

        return response()->json(
            [
                'expense_type' => [
                    'id' => $expenseType->id,
                    'name' => $name,
                    'created_at' =>  $expenseType->created_at,
                ],
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expenseType = ExpenseType::find($id);
        if ($expenseType) {
            $expenseType->delete();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }

    public function expenseIcons($id)
    {
        $locale = App::getLocale();
        $tr = DB::table('expense_type_icons as eti')
            ->where('eti.expense_type_id', '=', $id)
            ->join('icons as i', 'i.id', '=', 'eti.icon_id')
            ->join('icon_trans as it', function ($join) use ($locale) {
                $join->on('i.id', '=', 'it.icon_id')
                    ->where('it.language_name', $locale);
            })
            ->select(
                'eti.id',
                'i.path',
                'it.value as name',
                'i.created_at',
            )
            ->orderBy('eti.id', 'desc')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
