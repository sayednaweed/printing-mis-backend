<?php

namespace App\Http\Controllers\api\app\hr\account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        // Start building the query
        $query = DB::table('accounts as ac')
            ->join('account_trans as act', function ($join) use ($locale) {
                $join->on('ac.id', '=', 'act.account_id')
                    ->where('act.language_name', $locale);
            })
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'ac.user_id');
            })
            ->select(
                "ac.id",
                "ac.balance",
                "ac.code",
                "u.username",
                "act.value as name",
                "ac.created_at",
                "ac.detail",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
