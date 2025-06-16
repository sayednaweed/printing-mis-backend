<?php

namespace App\Http\Controllers\api\app\expense\icon;

use App\Models\Icon;
use App\Models\IconTran;
use App\Enums\LanguageEnum;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class IconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $tr = DB::table('icons as i')
            ->join('icon_trans as it', function ($join) use ($locale) {
                $join->on('i.id', '=', 'it.icon_id')
                    ->where('it.language_name', $locale);
            })
            ->select(
                'i.id',
                'i.path',
                'it.value as name',
                'i.created_at',
            )
            ->orderBy('i.id', 'desc')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function names(Request $request)
    {
        $locale = App::getLocale();
        $tr = DB::table('icons as i')
            ->join('icon_trans as it', function ($join) use ($locale) {
                $join->on('i.id', '=', 'it.icon_id')
                    ->where('it.language_name', $locale);
            })
            ->select(
                'i.id',
                'it.value as name',
                'i.path',
            )->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'icon' => 'required|file|mimes:svg',
            'english' => 'required|string',
            'farsi' => 'required|string',
            'pashto' => 'required|string',
        ]);
        DB::beginTransaction();
        $file = $request->file('icon');

        $extension = "." . $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $newFileName = Str::uuid() . $extension;
        // Store the file with the new file name in the folder 'icons/expense-icons' on 'public' disk
        $file->storeAs('icons/expense-icons', $newFileName, 'public');
        $path = 'icons/expense-icons/' . $newFileName;

        $icon = Icon::create(
            [
                'path' => $path,
                'type' => $mimeType,
                'extension' => $extension,
            ]
        );
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            IconTran::create([
                "value" => $request["{$name}"],
                "icon_id" => $icon->id,
                "language_name" => $code,
            ]);
        }
        DB::commit();

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale === 'fa') {
            $name = $request->farsi;
        }
        if ($locale === 'ps') {
            $name = $request->pashto;
        }

        return response()->json(
            [
                'icon' => [
                    'id' => $icon->id,
                    'name' => $name,
                    'path' => $path,
                    'created_at' =>  $icon->created_at,
                ],
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $icon =
            DB::table('icons as i')
            ->where('i.id', $id)
            ->join('icon_trans as it', function ($join) {
                $join->on('i.id', '=', 'it.icon_id');
            })
            ->select(
                'i.id',
                'i.extension',
                'i.type',
                'i.path',
                DB::raw("MAX(CASE WHEN it.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN it.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN it.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('i.id', 'i.path', 'i.extension', 'i.type')
            ->first();

        $locale = App::getLocale();
        $name = $icon->english;
        if ($locale === 'fa') {
            $name = $icon->farsi;
        }
        if ($locale === 'ps') {
            $name = $icon->pashto;
        }

        return response()->json(
            [
                "id" => $icon->id,
                "english" => $icon->english,
                "farsi" => $icon->farsi,
                "pashto" => $icon->pashto,
                "icon" => [
                    'id' => $icon->id,
                    'path' => $icon->path,
                    'type' => $icon->type,
                    'name' => $icon->type,
                    'extension' => $icon->extension,
                ],
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
