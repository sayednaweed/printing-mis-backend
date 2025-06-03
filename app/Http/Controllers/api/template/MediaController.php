<?php

namespace App\Http\Controllers\api\template;

use App\Http\Controllers\Controller;
use App\Traits\Helper\HelperTrait;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    use HelperTrait;
    public function downloadProfile(Request $request)
    {

        $filename = $request->query('path');
        $fullPath = storage_path('app/private/profile/employee/' . $filename);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, [
            'Content-Type' => mime_content_type($fullPath)
        ]);


        $filePath = $request->input('path');
        $path = $this->getProfilePath($filePath);
        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
    public function tempMediadownload(Request $request)
    {
        $filePath = $request->input('path');
        $path = $this->getPrivatePath($filePath);
        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
    public function downloadPublicFile(Request $request)
    {
        $filePath = $request->input('path');
        $path = $this->getPublicPath($filePath);

        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
    public function downloadPublic(Request $request)
    {
        $filePath = $request->input('path');
        $path = public_path() . '/' . $filePath;
        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.not_found'),
                'ss' => $path,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
}
