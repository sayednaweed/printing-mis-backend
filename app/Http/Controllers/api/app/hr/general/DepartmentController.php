<?php

namespace App\Http\Controllers\api\app\hr\general;

use App\Models\Department;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\DepartmentTran;

class DepartmentController extends Controller
{
    //

    public function departments(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page


        // Start building the query
        $query = DB::table('departments as dep')
            ->leftjoin('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'dep.id')
                    ->where('dept.language_name', $locale);
            })
            ->select(
                "dep.id",
                "dept.value as name",
                "dep.created_at",
            );

        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "users" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }



    public function department($id)
    {


        $locale = App::getLocale();

        $query = DB::table('departments as dep')
            ->leftJoin(DB::raw('(
                SELECT
                    department_id,
                    MAX(CASE WHEN language_name = "fa" THEN value END) as farsi,
                    MAX(CASE WHEN language_name = "en" THEN value END) as english,
                    MAX(CASE WHEN language_name = "ps" THEN value END) as pashto
                FROM department_trans
                GROUP BY department_id
            ) as dept'), 'dep.id', '=', 'dept.department_id')
            ->select(
                'dep.id',
                'dep.created_at',
                'dept.farsi',
                'dept.english',
                'dept.pashto'
            )
            ->where('dept.id', $id);

        $result = $query->first();

        return response()->json([
            "id" => $result->id,
            "english" => $result->english,
            "farsi" => $result->farsi,
            "pashto" => $result->pashto,
            "created_at" => $result->created_at,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name_english' => 'required|string',
            'name_pashto' => 'required|string',
            'name_farsi' => 'required|string',
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
        $name = $request->name_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->name_farsi;
        } else {
            $name = $request->name_pashto;
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

                'name' => 'htt.name',

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
            'name' => 'htt.name',


        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
