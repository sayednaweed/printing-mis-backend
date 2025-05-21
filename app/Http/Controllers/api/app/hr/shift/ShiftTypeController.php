<?php

namespace App\Http\Controllers\api\app\hr\shift;

use App\Models\Shift;
use App\Models\ShiftTran;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ShiftTypeController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        // Start building the query
        $query = DB::table('shifts as sh')
            ->leftjoin('shift_trans as sht', function ($join) use ($locale) {
                $join->on('sh.id', '=', 'sht.shift_id')
                    ->where('sht.language_name', $locale);
            })
            ->select(
                "sh.id",
                "sh.start_time",
                "sh.end_time",
                "sht.value as name",
                "sh.created_at",
            )->get();

        return response()->json(
            $query,

            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function shift($id)
    {


        $locale = App::getLocale();

        $query = DB::table('shifts as sh')
            ->leftJoin(DB::raw('(
                SELECT
                    shift_id,
                    MAX(CASE WHEN language_name = "fa" THEN value END) as farsi,
                    MAX(CASE WHEN language_name = "en" THEN value END) as english,
                    MAX(CASE WHEN language_name = "ps" THEN value END) as pashto
                FROM shifts
                GROUP BY shift_id
            ) as sht'), 'sh.id', '=', 'sht.shift_id')
            ->select(
                'sh.id',
                "sh.start_time",
                "sh.end_time",
                'sh.created_at',
                'post.farsi',
                'post.english',
                'post.pashto'
            )
            ->where('sh.id', $id);

        $result = $query->first();

        return response()->json([
            "id" => $result->id,
            "start_time" => $result->start_time,
            "end_time" => $result->end_time,
            "english" => $result->english,
            "farsi" => $result->farsi,
            "pashto" => $result->pashto,
            "created_at" => $result->created_at,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'name_english' => 'required|string',
            'name_pashto' => 'required|string',
            'name_farsi' => 'required|string',
        ]);

        $shift = Shift::create([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ShiftTran::create([
                "value" => $request["{$name}"],
                "shift_id" => $shift->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->name_farsi;
        } else {
            $name = $request->name_pashto;
        }
        return response()->json([
            'message' => __('app_translation.success'),
            'position' => [
                "id" => $shift->id,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                "name" => $name,
                "created_at" => $shift->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

//Edit Function

   public function edit($id)
{
 // First we try to find the shift
    $shift = Shift::find($id);

    if (!$shift) {
        return response()->json([
            'message' => __('app_translation.shift_not_found'),
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }
// Find translations for shift
    $translations = ShiftTran::where('shift_id', $shift->id)->get();
// Set names based on the current language
    $locale = App::getLocale();
    $name = null;
    foreach ($translations as $translation) {
        if ($translation->language_name == $locale) {
            $name = $translation->value;
        }
    }

    return response()->json([
        'shift' => [
            'id' => $shift->id,
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'name' => $name,  
            'created_at' => $shift->created_at,
            'translations' => $translations, 
        ]
    ], 200, [], JSON_UNESCAPED_UNICODE);
}


    

 //Update Function
    public function update(Request $request, $id){
        $request->validate([
            'start_time'=>'required|date_format:H:',
            'end_time'=>'required|date_format:H',
            'english'=>'required|string',
            'pashto'=>'required|string',
            'farsi'=>'required|string',
            'detail'=>'required|string'
        ]);
        $shift = Shift::find($id);
    if (!$shift) {
        return response()->json([
            'message' => __('app_translation.shift_not_found'),
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    DB::transaction();
    $shift->update([
        'start_time'=>$request->start_time,
        'end_time'=>$request->end_time,
        'description'=>$request->detail

    ]);
    $trans = ShiftTran::where('shift_id',$shift->id)->select('id','language_name','value')->get();
    //Update
    foreach(LanguageEnum::LANGUAGES as $code=>$name){
        $tran = $trans->where('language_name', $code)->first();
        $tran->value = $request["{$name}"];
        $tran->save();

    }
    DB::commit(); 

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->pashto;
        }
    
    return response()->json([
        'message' => __('app_translation.success'),
        'shift' => [
            'id' => $shift->id,
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'name' => $request->name,
            'created_at' => $shift->created_at,
        ]
    ], 200, [], JSON_UNESCAPED_UNICODE);





    }

    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('eu.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('eu.created_at', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = [
                'name' => 'sht.name',

            ];
            // Ensure that the search column is allowed
            if (in_array($searchColumn, array_keys($allowedColumns))) {
                $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
            }
        }
    }
    // filter function
    protected function applyFilters($query, $request)
    {
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 
        $allowedColumns = [
            'name' => 'sht.name',

        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
