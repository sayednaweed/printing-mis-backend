<?php

namespace App\Http\Controllers\api\app\expense\expenseType;

use App\Models\Expense;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use App\Models\ExpenseTypeTran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use App\Models\Icon;
use App\Models\IconTran;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $tr =     DB::table('expense_types as ext')
            ->join('expense_type_trans as extt', function ($join) use ($locale) {
                $join->on('ext.id', '=', 'extt.expense_type_id')
                    ->where('extt.language_name', $locale);
            })
            ->join('icons as i', 'ext.icon_id', '=', 'i.id')
            ->select(
                'ext.id',
                'extt.value as name',
                'i.path as icon'

            )->get();


        $this->applyDate($tr, $request);
        $this->applyFilters($tr, $request);
        $this->applySearch($tr, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $query = $tr->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'icon_id' => 'required|integer|exists:icons,id',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'pashto' => 'required|string',
        ]);

        $exp =  ExpenseType::create(
            ['icon_id' => $request->icon_id]
        );

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ExpenseTypeTran::create([
                "value" => $request["{$name}"],
                "expense_type_id" => $exp->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale === 'fa') {
            $name = $request->farsi;
        }
        if ($locale === 'ps') {
            $name = $request->pashto;
        }


        $data = ['id' => $exp->id, 'name' => $name];

        return response()->json(
            [
                'expense_type' => $data,
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $locale = App::getLocale();
        $exp =   ExpenseType::find($id);

        if (!$exp) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $icons = Icon::join('icons_trans as ict', function ($join) use ($locale) {
            $join->on('ict.icon_id', '=', 'icons.id')
                ->where('language_name', $locale);
        })
            ->select(
                'ict.value as icon',
                'icons.id'
            )->where('icons.id', $exp->icon_id)->first();

        // Get all translations for this status
        $translations = DB::table('expense_type_trans')
            ->where('expense_type_id', $id)
            ->pluck('value', 'language_name'); // Returns ['en' => '...', 'fa' => '...', 'ps' => '...']

        return response()->json([
            'expense_type' => [
                'id' => $exp->id,
                'icon' => ['id' => $icons->id, 'name' => $icons->icon],
                'translations' => [
                    'english' => $translations['en'] ?? null,
                    'farsi' => $translations['fa'] ?? null,
                    'pashto' => $translations['ps'] ?? null,
                ]
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //

        $request->validate([
            'id' => 'required|integer|exists:expenses,id',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'pashto' => 'required|string',
            'icon_id' => 'required|integer'
        ]);


        $exp =   ExpenseType::find($request->id);


        DB::transaction();



        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $expTran = ExpenseTypeTran::where('expense_type_id', $exp->id)
                ->where('language_name', $code);

            $expTran->value = $request["{$name}"];
            $expTran->save();
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


        $data = ['id' => $exp->id, 'name' => $name];

        return response()->json(
            ['expense_type' => $data],
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
        //
    }



    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('ext.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('ext.created_at', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = [

                'name' => 'ext.name',

            ];
            // Ensure that the search column is allowed
            if (in_array($searchColumn, array_keys($allowedColumns))) {
                $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
            }
        }
    }
    // filter function
    protected function applyFilters($query, $request)
    {
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 
        $allowedColumns = [
            'name' => 'ext.name',

        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
