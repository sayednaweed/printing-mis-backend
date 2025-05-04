
<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(['api.key', "authorized:" . 'finance:api'])->group(function () {});
