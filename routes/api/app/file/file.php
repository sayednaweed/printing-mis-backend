
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\file\FileController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'epi:api,finance:api'])->group(function () {
  Route::post('no/identifier/file/upload', [FileController::class, 'noIdentifierFileUpload'])->withoutMiddleware('throttle');
  Route::post('file/upload', [FileController::class, 'fileUpload'])->withoutMiddleware('throttle');
});
