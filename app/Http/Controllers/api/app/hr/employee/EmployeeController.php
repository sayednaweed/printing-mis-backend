<?php

namespace App\Http\Controllers\api\app\hr\employee;

use App\Models\Nid;
use App\Models\Email;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Employee;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use App\Models\EmployeeTran;
use Illuminate\Http\Request;
use App\Models\EmployeeDocument;
use App\Enums\Types\HireTypeEnum;
use App\Models\PositionAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Traits\Address\AddressTrait;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Models\PositionAssignmentDuration;
use App\Http\Requests\hr\employee\EmployeeStoreRequest;
use App\Models\EmployeeEducation;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class EmployeeController extends Controller
{
    use AddressTrait;
    protected $pendingTaskRepository;
    protected $storageRepository;
    protected $permissionRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        StorageRepositoryInterface $storageRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->storageRepository = $storageRepository;
    }

    public function employees(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        // Start building the query
        $query = DB::table('employees as emp')
            ->leftjoin('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->leftjoin('emails', 'emp.email_id', '=', 'emails.id')
            ->leftjoin('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->select(
                "emp.id",
                "empt.first_name",
                "empt.last_name",
                "empt.father_name",
                "emp.hr_code",
                "emp.is_current_employee",
                "emp.contact_id",
                "emp.email_id",
                "emails.value as email",
                "contacts.value as contact",
            );

        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function employee($id)
    {
        $locale = App::getLocale();

        $query = DB::table('employees as emp')
            ->leftJoin(DB::raw('(
                SELECT
                    employee_id,
                    MAX(CASE WHEN language_name = "fa" THEN first_name END) AS first_name_fa,
                    MAX(CASE WHEN language_name = "fa" THEN last_name END) AS last_name_fa,
                    MAX(CASE WHEN language_name = "fa" THEN father_name END) AS father_name_fa,
        
                    MAX(CASE WHEN language_name = "en" THEN first_name END) AS first_name_en,
                    MAX(CASE WHEN language_name = "en" THEN last_name END) AS last_name_en,
                    MAX(CASE WHEN language_name = "en" THEN father_name END) AS father_name_en,
        
                    MAX(CASE WHEN language_name = "ps" THEN first_name END) AS first_name_ps,
                    MAX(CASE WHEN language_name = "ps" THEN last_name END) AS last_name_ps,
                    MAX(CASE WHEN language_name = "ps" THEN father_name END) AS father_name_ps
                FROM employee_trans
                GROUP BY employee_id
            ) as empt'), 'emp.id', '=', 'empt.employee_id')

            ->leftJoin('department_trans as dept', function ($join) use ($locale) {
                $join->on('dept.department_id', '=', 'emp.department_id')
                    ->where('dept.language_name', $locale);
            })
            ->leftJoin('position_assignments as posa', 'emp.id', '=', 'posa.employee_id')
            ->leftJoin('position_tran as post', function ($join) use ($locale) {
                $join->on('post.position_id', '=', 'posa.position_id')
                    ->where('post.language_name', $locale);
            })
            ->leftJoin('emails', 'emp.email_id', '=', 'emails.id')
            ->leftJoin('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->leftJoin('addresses as perAdd', 'emp.permanent_address_id', '=', 'addresses.id')
            ->leftJoin('addresses as tempAdd', 'emp.current_address_id', '=', 'addresses.id')
            ->select(
                'emp.id',
                'emp.hr_code',
                'emp.contact_id',
                'emp.email_id',
                'emp.address_i',
                'emp.created_at',
                'post.id as position_id',
                'post.value as position',
                'dept.value as department',
                'dept.department_id',
                'emails.value as email',
                'contacts.value as contact',
                // Names in 3 languages
                'empt.first_name_fa',
                'empt.last_name_fa',
                'empt.father_name_fa',

                'empt.first_name_en',
                'empt.last_name_en',
                'empt.father_name_en',

                'empt.first_name_ps',
                'empt.last_name_ps',
                'empt.father_name_ps'
            )
            ->where('emp.id', $id);

        $result = $query->first();

        return response()->json([
            "id" => $result->id,


            "description" => $result->description,
            "english" => $result->english,
            "farsi" => $result->farsi,
            "pashto" => $result->pashto,
            "created_at" => $result->created_at,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(EmployeeStoreRequest $request)
    {
        $request->validated();
        // Create email
        $email = null;
        DB::beginTransaction();
        if ($request->email != null && !empty($request->email)) {
            $email = Email::where('value', '=', $request->email)->first();
            if ($email) {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
            } else {
                $email = Email::create([
                    "value" => $request->email
                ]);
            }
        }
        // 2. Check contact
        $contact = Contact::where('value', '=', $request->contact)->first();
        if ($contact) {
            return response()->json([
                'message' => __('app_translation.contact_exist'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $contact = Contact::create([
            "value" => $request->contact
        ]);

        // 2. Check family contact

        $family_contact = Contact::where('value', '=', $request->family_contact)->first();
        if ($family_contact) {
            return response()->json([
                'message' => __('app_translation.contact_exist'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $family_contact = Contact::create([
            "value" => $request->family_contact
        ]);

        $permAddress = Address::create([
            'province_id' => $request->permanent_province_id,
            'district_id' => $request->permanent_district_id,
        ]);
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AddressTran::create([
                "area" => $request->permanent_area,
                "address_id" => $permAddress->id,
                "language_name" => $code,
            ]);
        }

        $currentAddress = Address::create([
            'province_id' => $request->current_province_id,
            'district_id' => $request->current_district_id,
        ]);
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AddressTran::create([
                "area" => $request->current_area,
                "address_id" => $currentAddress->id,
                "language_name" => $code,
            ]);
        }

        $employee = Employee::create([
            'hr_code' => '',
            'contact_id' => $contact->id,
            'family_contact_id' => $family_contact->id,
            'email_id' => $email ? $email->id : null,
            'parmanent_address_id' => $permAddress->id,
            'current_address_id' => $currentAddress->id,
            'date_of_birth' => $request->date_of_birth,
            'nationality_id' => $request->nationality_id,
            'gender_id' => $request->gender_id,
            'marital_status_id' => $request->marital_status_id,
        ]);
        $employee->hr_code = "HR-" . $employee->id;
        $employee->save();


        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            EmployeeTran::create([
                'employee_id' => $employee->id,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "father_name" => $request->father_name,
                "language_name" => $code,
            ]);
        }
        // Create NID
        Nid::create([
            'employee_id' => $employee->id,
            'nid_type' => $request->nid_type_id,
            'province_id' => $request->nid_province_id,
            'number' => $request->nid_number,
            'volume' => $request->nid_volume ?? '',
            'page' => $request->nid_page ?? '',
        ]);

        // create education
        EmployeeEducation::create([
            'employee_id' => $employee->id,
            'education_id' => $request->education_id,
            'description' => $request->description ?? '',
        ]);



        $postAss = PositionAssignment::create([
            'employee_id' => $employee->id,
            'hire_type_id' => $request->hire_type_id,
            'salary' => $request->salary,
            'shift_id' => $request->shift_id,
            'position_id' => $request->position_id,
            'overtime_rate' => $request->overtime_rate,
            'currency_id' => $request->currency_id,
            'department_id' => $request->department_id,
            'hire_date' => $request->hire_date,
        ]);

        // Insert PositionAssignmentDuration in case it is not permanent
        if (HireTypeEnum::permanent->value != $request->hire_type_id) {
            $request->validate([
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            PositionAssignmentDuration::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'position_assignment_id' => $postAss->id
            ]);
        }

        if ($request->has_attachment == true) {
            $user = $request->user();
            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                CheckListTypeEnum::employee->value,
                CheckListEnum::employee_attachment->value,
                null
            );

            if (!$task) {
                return response()->json([
                    'message' => __('app_translation.task_not_found')
                ], 404);
            }
            $document_id = '';

            $this->storageRepository->documentStore(CheckListTypeEnum::employee->value, $user->id, $task->id, function ($documentData) use (&$document_id) {
                $checklist_id = $documentData['check_list_id'];
                $document = Document::create([
                    'actual_name' => $documentData['actual_name'],
                    'size' => $documentData['size'],
                    'path' => $documentData['path'],
                    'type' => $documentData['type'],
                    'check_list_id' => $checklist_id,
                ]);
                $document_id = $document->id;
            });

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_id' => $document_id,
            ]);
            $this->pendingTaskRepository->destroyPendingTask(
                $request->user(),
                CheckListTypeEnum::employee->value,
                CheckListEnum::employee_attachment->value,
                null
            );
        }


        DB::commit();
        return response()->json(
            [
                "employee" => [
                    'profile' => null,
                    "id" => $employee->id,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "father_name" => $request->father_name,
                    "hr_code" => $employee->hr_code,
                    "email" => $request->email,
                    "hire_date" => $request->hire_date,
                    "contact" => $request->contact,
                    "is_current_employee" => $employee->is_current_employee,
                ],
                "message" => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function personalDetial($id)
    {
        $locale = App::getLocale();
        $query = DB::table('employees as emp')
            ->where('emp.id', $id)
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->join('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->leftJoin('emails', 'emp.email_id', '=', 'emails.id')
            ->join('genders as gent', function ($join) {
                $join->on('gent.id', '=', 'emp.gender_id');
            })
            ->join('marital_status_trans as mrt', function ($join) use ($locale) {
                $join->on('mrt.marital_status_id', '=', 'emp.marital_status_id')
                    ->where('mrt.language_name', $locale);
            })
            ->join('nationality_trans as nit', function ($join) use ($locale) {
                $join->on('nit.nationality_id', '=', 'emp.nationality_id')
                    ->where('nit.language_name', $locale);
            });

        $query = $this->address($query, 'p_', 'emp.parmanent_address_id');
        $query = $this->address($query, 't_', 'emp.current_address_id');
        $employee = $query->select(
            'emp.id',
            'emp.hr_code',
            'empt.first_name',
            'emp.picture',
            'empt.last_name',
            'empt.father_name',
            'emp.date_of_birth',
            'emp.is_current_employee',
            'contacts.value as contact',
            'emails.value as email',
            'emp.gender_id',
            "gent.name_{$locale} as gender",
            'emp.nationality_id',
            'nit.value as nationality',
            // 'p_add.province_id as parmanent_province_id',
            // 'p_add.district_id as parmanent_district_id',
            // 'p_addt.area as parmanent_area',
            // 'p_pvt.value as parmanent_province',
            // 'p_dst.value as parmanent_district',
            't_add.province_id as temprory_province_id',
            't_add.district_id as temprory_district_id',
            't_addt.area as temprory_area',
            't_pvt.value as temprory_province',
            't_dst.value as temprory_district'
        )->first();
        return response()->json([
            'message' => __('app_translation.employee_not_found'),
            'dd' => $employee,
        ], 404, [], JSON_UNESCAPED_UNICODE);
        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
                'dd' => $employee,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $document = DB::table('employee_documents as ed')
            ->where('ed.employee_id', $employee->id)
            ->join('documents as d', 'd.id', '=', 'ed.document_id')
            ->select(
                'd.actual_name',
                'd.path',
                'd.type',
            )
            ->first();


        $result = [
            'id' => $employee->id,
            'hrcode' => $employee->hr_code,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'father_name' => $employee->father_name,
            'picture' => $employee->picture,
            'date_of_birth' => $employee->date_of_birth,
            'contact' =>  $employee->contact,
            'email' => $employee->email,
            'gender' => ['id' => $employee->gender_id, 'name' => $employee->gender],
            'nationality' => ['id' => $employee->nationality_id, 'name' => $employee->nationality],
            'is_current_employee' => $employee->is_current_employee,
            'permanent_area' => $employee->parmanent_area,
            'permanent_province' => ['id' => $employee->parmanent_province_id, 'name' => $employee->parmanent_province],
            'permanent_district' => ['id' => $employee->parmanent_district_id, 'name' => $employee->parmanent_district],
            'current_area' => $employee->temprory_area,
            'current_province' => ['id' => $employee->temprory_province_id, 'name' => $employee->temprory_province],
            'current_district' => ['id' => $employee->temprory_district_id, 'name' => $employee->temprory_district],
            'attachment' => $document ? [
                'actual_name' => $document->actual_name,
                'type' => $document->type,
                'path' => $document->path,
            ] : null,
        ];
        return response()->json([
            'employee' => $result,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }




    // personal detail update
    public function updatePersonalDetail(Request $request, $id)
    {
        $employee = DB::table('employees')->where('id', $id)->first();

        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        DB::beginTransaction();

        // Update employee

        $employee->date_of_birth = $request->date_of_birth;
        $employee->gender_id = $request->gender_id;
        $nationality_id = $request->nationality_id;
        $employee->is_current_employee = $request->is_current_employee;
        $employee->save();

        $employeeTran  = EmployeeTran::where('employee_id', $id)->first();
        // Update employee_trans (localized data)
        $employeeTran->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
        ]);
        $employeeTran->save();

        $contact = Contact::where('id', $employee->contact_id)->first();
        // Update contact
        $contact->value = $request->contact;
        $contact->save();

        $email = Email::where('id', $employee->email_id)->first();
        $email->value = $request->email;
        $email->save();



        if ($request->has_attachment == true) {
            $user = $request->user();

            $exists = EmployeeDocument::join('documents', 'documents.id', '=', 'employee_documents.document_id')
                ->join('check_lists', 'check_lists.id', '=', 'documents.check_list_id')
                ->where('employee_id', $id)
                ->where('documents.check_list_id', CheckListEnum::employee_attachment->value)
                ->where('check_list_id', CheckListEnum::employee_attachment->value)
                ->first();
            if ($exists) {
                $exists->delete();
            }

            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                CheckListTypeEnum::employee->value,
                CheckListEnum::employee_attachment->value,
                null
            );

            if (!$task) {
                return response()->json([
                    'message' => __('app_translation.task_not_found')
                ], 404);
            }
            $document_id = '';

            $this->storageRepository->documentStore(CheckListTypeEnum::employee->value, $user->id, $task->id, function ($documentData) use (&$document_id) {
                $checklist_id = $documentData['check_list_id'];
                $document = Document::create([
                    'actual_name' => $documentData['actual_name'],
                    'size' => $documentData['size'],
                    'path' => $documentData['path'],
                    'type' => $documentData['type'],
                    'check_list_id' => $checklist_id,
                ]);
                $document_id = $document->id;
            });

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_id' => $document_id,
            ]);
            $this->pendingTaskRepository->destroyPendingTask(
                $request->user(),
                CheckListTypeEnum::employee->value,
                CheckListEnum::employee_attachment->value,
                null
            );
        }




        DB::commit();

        return response()->json([
            'message' => __('app_translation.employee_updated_successfully'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }












    // 
    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('emp.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('emp.created_at', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = [

                'first_name' => 'empt.first_name',
                'last_name' => 'empt.last_name',
                'father_name' => 'empt.father_name',
                'hr_code' => 'emp.hr_code',
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
            'status' => 'emp.is_current_employee',
            'created_at' => 'emp.created_at',
            'contact' => 'emp.contact_id',
        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
    //
    public function employeesCount()
    {
        $statistics = DB::select("
            SELECT
                COUNT(*) AS employeeCount,
                (SELECT COUNT(*) FROM employees WHERE DATE(created_at) = CURDATE()) AS todayCount,
                (SELECT COUNT(*) FROM employees WHERE is_current_employee = 1) AS activeUserCount,
                (SELECT COUNT(*) FROM employees WHERE is_current_employee = 0) AS inActiveUserCount
            FROM employees
        ");
        return response()->json([
            'counts' => [
                "userCount" => $statistics[0]->employeeCount,
                "todayCount" => $statistics[0]->todayCount,
                "activeUserCount" => $statistics[0]->activeUserCount,
                "inActiveUserCount" => $statistics[0]->inActiveUserCount

            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
