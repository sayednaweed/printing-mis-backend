<?php

namespace App\Http\Controllers\api\app\hr\employee\contract;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Traits\Address\AddressTrait;
use App\Traits\Report\PdfGeneratorTrait;

class ContractController extends Controller
{
    //
    use PdfGeneratorTrait, AddressTrait;

    public function generateContract($id)
    {
        // $languages = ['en', 'ps', 'fa'];
        $pdfFiles = [];
        $lang = 'fa';
        $mpdf = $this->generatePdf();
        // $this->setWatermark($mpdf);
        $data = $this->data($lang, $id);

        // return "ngo.registeration.{$lang}.registeration";
        // Generate PDF content
        $this->pdfFilePart($mpdf, "hr.employee.contract", $data);
        // $mpdf->view('hr.employee.contract')
        // $this->pdfFilePart($mpdf, "ngo.registeration.{$lang}.registeration", $data);
        $mpdf->SetProtection(['print']);

        // Store the PDF temporarily

        $fileName = "{employee_registration_contract.pdf";
        $outputPath = storage_path("app/private/temp/");
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }
        $filePath = $outputPath . $fileName;

        // return $filePath;
        $mpdf->Output($filePath, 'F'); //  F Save to file


        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    protected function data($lang, $id)
    {
        $locale = App::getLocale();

        // Subquery to get latest position assignment per employee
        $latestPositionAssignmentId = DB::table('position_assignments')
            ->where('employee_id', $id)
            ->orderByDesc('id')
            ->limit(1);

        $emp = DB::table('employees as emp')
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->join('marital_status_trans as mrt', function ($join) use ($locale) {
                $join->on('mrt.marital_status_id', '=', 'emp.marital_status_id')
                    ->where('mrt.language_name', $locale);
            })
            ->join('genders as gent', 'gent.id', '=', 'emp.gender_id')
            ->join('employee_nids as nt', 'emp.id', '=', 'nt.employee_id')
            ->join('employee_education as empedu', 'emp.id', '=', 'empedu.employee_id')
            ->join('education_level_trans as edult', function ($join) use ($locale) {
                $join->on('edult.education_level_id', '=', 'empedu.education_level_id')
                    ->where('edult.language_name', $locale);
            })
            ->joinSub($latestPositionAssignmentId, 'posasi', function ($join) {
                $join->on('posasi.employee_id', '=', 'emp.id');
            })
            ->join('currency_trans as curt', function ($join) use ($locale) {
                $join->on('curt.currency_id', '=', 'posasi.currency_id')
                    ->where('curt.language_name', $locale);
            })
            ->join('positions as pos', 'pos.id', '=', 'posasi.position_id')
            ->join('position_trans as post', function ($join) use ($locale) {
                $join->on('post.position_id', '=', 'posasi.position_id')
                    ->where('post.language_name', $locale);
            })
            ->join('position_assignment_durations as posdur', 'posdur.position_assignment_id', '=', 'posasi.id')
            ->join('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'pos.department_id')
                    ->where('dept.language_name', $locale);
            })
            ->join('shift_trans as shft', function ($join) use ($locale) {
                $join->on('shft.shift_id', '=', 'posasi.shift_id')
                    ->where('shft.language_name', $locale);
            });

        // Include address joins
        $emp = $this->address($emp, 'p_', 'emp.parmanent_address_id');
        $emp = $this->address($emp, 't_', 'emp.current_address_id');

        // Select final fields
        $emp = $emp->where('emp.id', $id)->select(
            'empt.first_name',
            'empt.last_name',
            'emp.hr_code',
            'empt.father_name',
            'emp.date_of_birth',
            'gent.name_en as gender',
            'mrt.value as marital',
            'nt.register_number as nid_no',
            'nt.register',
            'nt.volume',
            'nt.page',
            'edult.value as education',
            'post.value as position',
            'posdur.start_date',
            'posdur.end_date',
            'dept.value as department',
            'shft.value as shift',
            'p_addt.area as parmanent_area',
            'p_pvt.value as parmanent_province',
            'p_dst.value as parmanent_district',
            't_addt.area as temprory_area',
            't_pvt.value as temprory_province',
            't_dst.value as temprory_district',
            'posasi.salary',
            'posasi.overtime_rate',
            'curt.value as currency',

        )->first();

        // Build response data
        $data = [
            'company_name' => 'مطبعه فردای نوین',
            'full_name' => $emp->first_name . ' ' . $emp->last_name,
            'hr_code' => $emp->hr_code,
            'f_name' => $emp->father_name,
            'birth_day' => date('d', strtotime($emp->date_of_birth)),
            'birth_month' => date('m', strtotime($emp->date_of_birth)),
            'birth_year' => date('Y', strtotime($emp->date_of_birth)),
            'org_province' => $emp->parmanent_province,
            'cur_province' => $emp->temprory_province,
            'nid_no' => $emp->nid_no,
            'regestration' => $emp->register,
            'page' => $emp->page,
            'volume' => $emp->volume,
            'gender' => $emp->gender,
            'marital_status' => $emp->marital,
            'education' => $emp->education,
            'department' => $emp->department,
            'position' => $emp->position,
            'shift' => $emp->shift,
            'start_date' => date('Y-d-d', strtotime($emp->start_date)),
            'end_date' =>  date('Y-d-d', strtotime($emp->end_date)),
            'salary' => $emp->salary,
            'overtime' => $emp->overtime_rate,
            'currency' => $emp->currency
        ];


        return ['data' => $data];
    }
}
