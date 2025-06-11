<?php

namespace App\Http\Controllers\api\app\hr\payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        $query = DB::table('payment_types as pt')
            ->join('payment_type_trans as ptt', function ($join) use ($locale) {
                $join->on('pt.id', '=', 'ptt.payment_type_id')
                    ->where('ptt.language_name', $locale);
            })
            ->select(
                "pt.id",
                "pt.detail",
                "ptt.value as name",
                "pt.created_at",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function names()
    {
        $locale = App::getLocale();
        $query = DB::table('payment_types as pt')
            ->join('payment_type_trans as ptt', function ($join) use ($locale) {
                $join->on('pt.id', '=', 'ptt.payment_type_id')
                    ->where('ptt.language_name', $locale);
            })
            ->select(
                "pt.id",
                "ptt.value as name",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
