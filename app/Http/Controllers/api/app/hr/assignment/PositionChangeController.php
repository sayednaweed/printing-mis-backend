<?php

namespace App\Http\Controllers\api\app\hr\assignment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class PositionChangeController extends Controller
{
    public function positionChangeTypes()
    {
        $locale = App::getLocale();
        $tr = DB::table('position_change_types as pct')
            ->join('position_change_type_trans as pctt', function ($join) use ($locale) {
                $join->on('pctt.position_change_type_id', '=', 'pct.id')
                    ->where('pctt.language_name', $locale);
            })
            ->select('pct.id', "pctt.value as name")->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
