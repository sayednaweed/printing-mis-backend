<?php

namespace App\Http\Controllers\api\app\hr\report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Types\HrReportTypeEnum;

class ReportController extends Controller
{
    //

    public function reportTypes()
    {


        $data = collect(HrReportTypeEnum::cases())->map(function ($case) {

            return [
                'id' => $case->id(),         // This is the string backing value
                'name' => __('app_translation.' . $case->value),     // Custom method for a human-readable name
            ];
        });

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function salaryReport(Request $request)
    {

        $request->validate([
            'selection' => 'required|integer',
            'hr_code' => 'string',
            'employee_status' => 'required|integer'

        ]);


        //  

    }
}
