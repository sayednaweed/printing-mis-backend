<?php

namespace App\Http\Controllers\api\app\hr\Education;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class EducationController extends Controller
{
    public function educationLevels()
    {
        $locale = App::getLocale();

        // Start building the query
        $query = DB::table('education_levels as el')
            ->join('education_level_trans as elt', function ($join) use ($locale) {
                $join->on('elt.education_level_id', '=', 'el.id')
                    ->where('elt.language_name', $locale);
            })
            ->select(
                "el.id",
                "elt.value as name",
                "el.created_at",
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
