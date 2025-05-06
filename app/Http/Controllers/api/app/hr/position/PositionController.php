<?php

namespace App\Http\Controllers\api\app\hr\position;


use App\Models\Position;
use App\Enums\LanguageEnum;
use App\Models\PositionTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    public function positions()
    {
        $locale = App::getLocale();
        $query = DB::table('positions as pos')
            ->join('position_trans as post', function ($join) use ($locale) {
                $join->on('pos.id', '=', 'post.position_id')
                    ->where('post.language_name', $locale);
            })
            ->select(
                "pos.id",
                "post.value as name",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }



    public function position($id)
    {


        $locale = App::getLocale();

        $query = DB::table('positions as pos')
            ->leftJoin('department_trans as dept', function ($join) use ($locale) {
                $join->on('pos.department_id', '=', 'dept.department_id')
                    ->where('dept.language_name', '=', $locale);
            })
            ->leftJoin(DB::raw('(
                SELECT
                    position_id,
                    MAX(CASE WHEN language_name = "fa" THEN value END) as farsi,
                    MAX(CASE WHEN language_name = "en" THEN value END) as english,
                    MAX(CASE WHEN language_name = "ps" THEN value END) as pashto
                FROM position_trans
                GROUP BY position_id
            ) as post'), 'pos.id', '=', 'post.position_id')
            ->select(
                'pos.id',
                'pos.created_at',
                'dept.value as department',
                'pos.department_id',
                'post.farsi',
                'post.english',
                'post.pashto'
            )
            ->where('pos.id', $id);

        $result = $query->first();

        return response()->json([
            "id" => $result->id,
            "english" => $result->english,
            "farsi" => $result->farsi,
            "pashto" => $result->pashto,
            "deprtment" => ["id" => $result->deprtment_id, "name" =>  $result->deprtment],
            "created_at" => $result->created_at,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {

        $request->validate([
            'department_id' => 'required|integer|exists:department,id',
            'name_english' => 'required|string',
            'name_pashto' => 'required|string',
            'name_farsi' => 'required|string',
        ]);

        $position = Position::create(['department_id' => $request->department_id]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            PositionTran::create([
                "value" => $request["{$name}"],
                "position_id" => $position->id,
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
            'position' => [
                "id" => $position->id,
                "name" => $name,
                "created_at" => $position->created_at
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
                'department' => 'dept.department',
                'name' => 'pos.name',

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
            'department' => 'dept.department',
            'name' => 'pos.name',


        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
