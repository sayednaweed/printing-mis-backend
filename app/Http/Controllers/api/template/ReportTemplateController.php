<?php

namespace App\Http\Controllers\api\template;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ReportTemplateController extends Controller
{
    public function selections()
    {
        $locale = App::getLocale();
        $tr = DB::table('report_selections as rs')
            ->join('report_selection_trans as rst', function ($join) use ($locale) {
                $join->on('rst.report_selection_id', '=', 'rs.id')
                    ->where('rst.language_name', $locale);
            })
            ->select('rs.id', "rst.value as name", 'rs.created_at')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
