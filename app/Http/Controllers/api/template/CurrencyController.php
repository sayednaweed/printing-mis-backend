<?php

namespace App\Http\Controllers\api\template;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    public function currencies()
    {
        $locale = App::getLocale();
        $query = DB::table('currencies as cur')
            ->leftjoin('currency_trans as curt', function ($join) use ($locale) {
                $join->on('cur.id', '=', 'curt.currency_id')
                    ->where('curt.language_name', $locale);
            })
            ->select(
                "cur.id",
                'cur.abbr',
                'cur.symbol',
                "curt.value as name"
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
