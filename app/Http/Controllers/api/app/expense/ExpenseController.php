<?php

namespace App\Http\Controllers\api\app\expense;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $locale = App::getLocale();
    //     $tr = DB::table('expenses as e')
    //         ->select(
    //             'e.id',
    //             'e.total_amount',
    //             'e.bill_no',
    //             'e.quantity',
    //             'e.date',
    //             'e.created_at',
    //         )->orderBy('e.id', 'desc')
    //         ->get();

    //     return response()->json(
    //         $tr,
    //         200,
    //         [],
    //         JSON_UNESCAPED_UNICODE
    //     );
    // }
    public function index(Request $request)
    {
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page


        $query = DB::table('expenses as e')
            ->select(
                'e.id',
                'e.total_amount',
                'e.bill_no',
                'e.quantity',
                'e.date',
                'e.created_at',
            )->orderBy('e.id', 'desc');

        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "expenses" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('e.date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('e.date', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        $allowedColumns = ['username', 'contact', 'email'];

        if ($searchColumn && $searchValue) {
            $allowedColumns = [
                'bill_no' => 'e.bill_no',
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
            'total_amount' => 'e.total_amount',
            'quantity' => 'e.quantity',
            'date' => 'e.date',
        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
