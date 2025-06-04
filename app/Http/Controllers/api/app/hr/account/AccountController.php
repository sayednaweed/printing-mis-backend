<?php

namespace App\Http\Controllers\api\app\hr\account;

use App\Models\Account;
use App\Enums\LanguageEnum;
use App\Models\AccountTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\account\AccountStoreRequest;
use App\Models\AccountBalance;

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

    public function store(AccountStoreRequest $request)
    {

        $request->validate();
        $locale = App::getLocale();
        DB::transaction();

        $authUser = $request->user();
        $account =    Account::create([
            'code' => $request->code,
            'detail' => $request->detail,
            'user_id' => $authUser->id
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {

            AccountTran::create([
                "value" => $request["name_${name}"],
                "language_name" => $code,
                "account_id" => $account->id
            ]);
        }
        foreach ($request->account_balance as $key => $account) {
            AccountBalance::create(
                [
                    'account_id'  => $account->id,
                    'currency_id' => $account->currency_id,
                    'balance' => 0.0
                ]
            );
        }

        $name = $request['name_english'];
        if ($locale === 'fa') {
            $name = $request['name_farsi'];
        }
        if ($locale === 'ps') {
            $name = $request['name_pashto'];
        }

        $data = [
            'id' => $account->id,
            'code' => $request->code,
            'username' => $authUser->username,
            'name' => $name,
            'created_at' => $account->created_at,
            'detail' => $account->detail

        ];

        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
            'account' => $data,
        ]);
    }

    public function edit($id)
    {
        $locale = App::getLocale();
        DB::table('accounts as ac')
            ->where('ac.id', $id)
            ->leftJoin('account_trans as act', 'ac.id', '=', 'act.account_id')
            ->select(
                'ac.id',
                'ac.code',
                'ac.detail',
                'ac.created_at',
                DB::raw("MAX(CASE WHEN act.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN act.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN act.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('ac.id')
            ->first();
        DB::table('account_balances as acb')
            ->join('currencies as cur', function ($join) use ($locale) {
                $join->on('acb.currency_id', '=', 'cur.id')
                    ->where('langauge_name', $locale);
            });
    }
}
