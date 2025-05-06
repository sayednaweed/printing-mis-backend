<?php

namespace App\Http\Controllers\api\app\hr\assignment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class EmployeeAssignment extends Controller
{
    public function employeeAssignments($id)
    {
        $locale = App::getLocale();
        $tr = [];
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
