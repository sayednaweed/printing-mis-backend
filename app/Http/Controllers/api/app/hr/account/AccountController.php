<?php

namespace App\Http\Controllers\api\app\hr\account;

use App\Models\Account;
use App\Enums\LanguageEnum;
use App\Models\AccountTran;
use Illuminate\Http\Request;
use App\Models\AccountBalance;
use App\Traits\Helper\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\account\AccountStoreRequest;

class AccountController extends Controller
{
    use FilterTrait;
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        // Start building the query
        $query = DB::table('accounts as ac')
            ->join('account_trans as act', function ($join) use ($locale) {
                $join->on('ac.id', '=', 'act.account_id')
                    ->where('act.language_name', $locale);
            })
            ->join('currency_trans as ct', function ($join) use ($locale) {
                $join->on('ct.currency_id', '=', 'ac.currency_id')
                    ->where('ct.language_name', $locale);
            })
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'ac.user_id');
            })
            ->select(
                "ac.id",
                "ac.code",
                "ac.balance",
                "ct.value as currency",
                "u.username as saved_by",
                "act.value as name",
                "ac.created_at",
            );

        $this->applyDate($query, $request, 'emp.created_at', 'emp.created_at');
        $this->applyFilters($query, $request, [
            'name' => 'act.value',
            'balance' => 'ac.balance',
            'date' => 'ac.created_at',
        ]);
        $this->applySearch($query, $request, [
            'name' => 'act.value',
            'code' => 'ac.code'
        ]);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store(AccountStoreRequest $request)
    {
        $request->validated();
        $locale = App::getLocale();
        DB::beginTransaction();
        $authUser = $request->user();
        $account = Account::create([
            'code' => $request->code,
            'detail' => $request->detail,
            'user_id' => $authUser->id,
            'currency_id' => $request->currency_id,
            'balance' => $request->balance
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AccountTran::create([
                "value" => $request["name_{$name}"],
                "language_name" => $code,
                "account_id" => $account->id
            ]);
        }
        DB::commit();
        $name = $request['name_english'];
        if ($locale === 'fa') {
            $name = $request['name_farsi'];
        } else if ($locale === 'ps') {
            $name = $request['name_pashto'];
        }

        $data = [
            'id' => $account->id,
            'code' => $request->code,
            'saved_by' => $authUser->username,
            'name' => $name,
            'created_at' => $account->created_at,
            'detail' => $account->detail
        ];

        return response()->json([
            'message' => __('app_translation.success'),
            'account' => $data,
        ]);
    }

    public function edit($id)
    {
        // Get the current locale
        $locale = App::getLocale();

        // Fetch account details with left join on account_trans table
        $account = DB::table('accounts as ac')
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

        // Fetch account balance details with the correct locale
        $balance = DB::table('account_balances as acb')
            ->where('acb.account_id', $id)
            ->join('currencies as cur', function ($join) use ($locale) {
                $join->on('acb.currency_id', '=', 'cur.id')
                    ->where('language_name', $locale); // Corrected typo here
            })
            ->select(
                'acb.id',
                'acb.balance',
                'acb.currency_id',
                'cur.value as currency'
            )
            ->get();

        // Prepare the response data
        $data = [
            'id' => $account->id,
            'code' => $account->code,
            'detail' => $account->detail,
            'name_english' => $account->english,
            'name_farsi' => $account->farsi, // Corrected typo here
            'name_pashto' => $account->pashto,
            'balance' => $balance,
            'created_at' => $account->created_at,
        ];

        // Return the response as JSON
        return response()->json([
            'account' => $data
        ]);
    }
}
