<?php

namespace App\Http\Controllers\api\app\hr\hire;

use App\Models\HireType;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\HireTypeTran;

class HireController extends Controller
{
    //

    public function hireTypes(Request $request)
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
                "ht.description",
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



    public function hireType($id)
    {


        $locale = App::getLocale();

        $query = DB::table('hire_types as ht')
            ->leftJoin(DB::raw('(
                SELECT
                    hire_type_id,
                    MAX(CASE WHEN language_name = "fa" THEN value END) as farsi,
                    MAX(CASE WHEN language_name = "en" THEN value END) as english,
                    MAX(CASE WHEN language_name = "ps" THEN value END) as pashto
                FROM hire_type_trans
                GROUP BY hire_type_id
            ) as htt'), 'ht.id', '=', 'htt.hire_type_id')
            ->select(
                'ht.id',
                'ht.created_at',
                "ht.description",
                'htt.farsi',
                'htt.english',
                'htt.pashto'
            )
            ->where('ht.id', $id);

        $result = $query->first();

        return response()->json([
            "id" => $result->id,


            "description" => $result->description,
            "english" => $result->english,
            "farsi" => $result->farsi,
            "pashto" => $result->pashto,
            "created_at" => $result->created_at,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {

        $request->validate([
            'description' => 'string',
            'name_english' => 'required|string',
            'name_pashto' => 'required|string',
            'name_farsi' => 'required|string',
        ]);

        $hiretype = HireType::create(['description' => $request->description]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            HireTypeTran::create([
                "value" => $request["{$name}"],
                "hire_type_id" => $hiretype->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->name_farsi;
        } else {
            $name = $request->name_pashto;
        }
        return response()->json([
            'message' => __('app_translation.success'),
            'hiretype' => [
                "id" => $hiretype->id,
                "name" => $name,
                "created_at" => $hiretype->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('eu.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('eu.created_at', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = [

                'name' => 'dept.name',

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
            'name' => 'dept.name',


        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
