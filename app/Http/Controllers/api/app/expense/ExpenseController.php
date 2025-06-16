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
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $tr = DB::table('expenses as e')
            ->leftJoin('icon_trans as it', function ($join) use ($locale) {
                $join->on('it.icon_id', '=', 'i.id')
                    ->where('it.language_name', $locale);
            })
            ->select(
                'e.id',
                'e.total_amount',
                'e.bill_no',
                'e.quantity',
                'e.date',
                'e.created_at',
            )->orderBy('e.id', 'desc')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
