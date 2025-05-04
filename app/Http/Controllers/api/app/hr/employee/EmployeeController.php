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
            ->leftjoin('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'emp.department_id')
                    ->where('empt.language_name', $locale);
            })
            ->leftJoin('position_assignments as posa', 'emp.id', '=', 'posa.employee_id')
            ->leftjoin('position_tran as post', function ($join) use ($locale) {
                $join->on('post.position_id', '=', 'posa.position_id')
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
                "emp.department_id",
                "post.value as position",

                "dept.value as department",
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
            [
                "employees" => $tr,
            ],
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
                    employee_id,
                    MAX(CASE WHEN language_name = "fa" THEN first_name END) AS first_name_fa,
                    MAX(CASE WHEN language_name = "fa" THEN last_name END) AS last_name_fa,
                    MAX(CASE WHEN language_name = "fa" THEN father_name END) AS father_name_fa,
        
                    MAX(CASE WHEN language_name = "en" THEN first_name END) AS first_name_en,
                    MAX(CASE WHEN language_name = "en" THEN last_name END) AS last_name_en,
                    MAX(CASE WHEN language_name = "en" THEN father_name END) AS father_name_en,
        
                    MAX(CASE WHEN language_name = "ps" THEN first_name END) AS first_name_ps,
                    MAX(CASE WHEN language_name = "ps" THEN last_name END) AS last_name_ps,
                    MAX(CASE WHEN language_name = "ps" THEN father_name END) AS father_name_ps
                FROM employee_trans
                GROUP BY employee_id
            ) as empt'), 'emp.id', '=', 'empt.employee_id')

            ->leftJoin('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'emp.department_id')
                    ->where('dept.language_name', $locale);
            })
            ->leftJoin('position_assignments as posa', 'emp.id', '=', 'posa.employee_id')
            ->leftJoin('position_tran as post', function ($join) use ($locale) {
                $join->on('post.position_id', '=', 'posa.position_id')
                    ->where('post.language_name', $locale);
            })
            ->leftJoin('emails', 'emp.email_id', '=', 'emails.id')
            ->leftJoin('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->leftJoin('addresses as perAdd', 'emp.permanent_address_id', '=', 'addresses.id')
            ->leftJoin('addresses as tempAdd', 'emp.temporary_address_id', '=', 'addresses.id')
            ->select(
                'emp.id',
                'emp.hr_code',
                'emp.contact_id',
                'emp.email_id',
                'emp.address_i',
                'emp.created_at',
                'post.id as position_id',
                'post.value as position',
                'dept.value as department',
                'dept.department_id',
                'emails.value as email',
                'contacts.value as contact',



                // Names in 3 languages
                'empt.first_name_fa',
                'empt.last_name_fa',
                'empt.father_name_fa',

                'empt.first_name_en',
                'empt.last_name_en',
                'empt.father_name_en',

                'empt.first_name_ps',
                'empt.last_name_ps',
                'empt.father_name_ps'
            )
            ->where('emp.id', $id);

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


        $request->validate();

        $locale = App::getLocale();













        // kdls
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
}
