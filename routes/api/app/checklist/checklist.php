<?php

use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api'])->group(function () {});

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {});
