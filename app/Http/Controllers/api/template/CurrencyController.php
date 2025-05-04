<?php

namespace App\Http\Controllers\api\template;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    //

    public function currencies(Request $request)
    {
        $locale = App::getLocale();
        // $tr = [];
        // $perPage = $request->input('per_page', 10); // Number of records per page
        // $page = $request->input('page', 1); // Current page


        // Start building the query
        $query = DB::table('currencies as cur')
            ->leftjoin('currency_tran as curt', function ($join) use ($locale) {
                $join->on('cur.id', '=', 'curt.currency_id')
                    ->where('curt.language_name', $locale);
            })
            ->select(
                "cur.id",
                'cur.abbr',
                'cur.symbol',
                "curt.value as name"
            )->get();


        // Apply pagination (ensure you're paginating after sorting and filtering)
        return response()->json(
            [
                "currency" => $query,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
