<?php

namespace App\Http\Controllers\api\app\hr\employee;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{

    public function employees(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page


        // Start building the query
        $query = DB::table('employees as emp')
            ->leftjoin('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->leftjoin('emails', 'emp.email_id', '=', 'emails.id')
            ->leftjoin('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->select(
                "emp.id",
                "empt.first_name",
                "empt.last_name",
                "empt.father_name",
                "emp.hr_code",
                "emp.contact_id",
                "emp.email_id",
                "emails.value as email",
                "contacts.value as contact",
                "emp.created_at",
            );

        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function employee($id)
    {
        $locale = App::getLocale();

        $query = DB::table('employees as emp')
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

                'first_name' => 'empt.first_name',
                'last_name' => 'empt.last_name',
                'father_name' => 'empt.father_name',
                'hr_code' => 'emp.hr_code',
                'contact_id' => 'emp.contact_id',
                'email_id' => 'emp.email_id',
                'department_id' => 'emp.department_id',



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
            'first_name' => 'empt.first_name',
            'last_name' => 'empt.last_name',
            'father_name' => 'empt.father_name',
            'hr_code' => 'emp.hr_code',
            'contact_id' => 'emp.contact_id',
            'email_id' => 'emp.email_id',
            'department_id' => 'emp.department_id',


        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
    //
    public function employeesCount()
    {
        // $statistics = DB::select("
        //     SELECT
        //         COUNT(*) AS userCount,
        //         (SELECT COUNT(*) FROM employees WHERE DATE(created_at) = CURDATE()) AS todayCount,
        //         (SELECT COUNT(*) FROM employees WHERE status = 1) AS activeUserCount,
        //         (SELECT COUNT(*) FROM employees WHERE status = 0) AS inActiveUserCount
        //     FROM employees
        // ");
        return response()->json([
            'counts' => [
                "userCount" => 0,
                "todayCount" => 0,
                "activeUserCount" => 0,
                "inActiveUserCount" =>  0
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
