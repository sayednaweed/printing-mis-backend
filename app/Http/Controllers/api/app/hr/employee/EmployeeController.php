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
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Traits\Address\AddressTrait;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Enums\Status\StatusEnum;
use App\Enums\Types\NidTypeEnum;
use App\Models\PositionAssignmentDuration;
use App\Http\Requests\app\hr\EmployeeStoreRequest;
use App\Http\Requests\app\hr\EmployeeUpdateRequest;
use App\Models\EmployeeEducation;
use App\Models\EmployeeNid;
use App\Models\EmployeeStatus;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class EmployeeController extends Controller
{
    use AddressTrait, HelperTrait;
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
            ->join('employee_trans as empt', function ($join) use ($locale) {
                $join->on('empt.employee_id', '=', 'emp.id')
                    ->where('empt.language_name', $locale);
            })
            ->leftjoin('emails', 'emp.email_id', '=', 'emails.id')
            ->join('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->join('employee_statuses as es', 'es.employee_id', '=', 'emp.id')
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'es.status_id')
                    ->where('st.language_name', $locale);
            })
            ->select(
                "emp.id",
                "emp.picture",
                "empt.first_name",
                "es.status_id as status",
                "st.value as status_name",
                "empt.last_name",
                "empt.father_name",
                "emp.hr_code",
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

    public function store(EmployeeStoreRequest $request)
    {
        $request->validated();
        if ($request->nid_type_id == NidTypeEnum::paper_id_card->value) {
            $request->validate([
                'register' => 'required',
                'volume' => 'required',
                'page' => 'required',
            ]);
        }
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

        $family_contact = Contact::where('value', '=', $request->family_mem_contact)->first();
        if ($family_contact) {
            return response()->json([
                'message' => __('app_translation.contact_exist'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $family_contact = Contact::create([
            "value" => $request->family_mem_contact
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

        EmployeeStatus::create([
            'status_id' => StatusEnum::active->value,
            'employee_id' => $employee->id,
            'description' => '',
            'user_id' => $request->user()->id,
        ]);

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
        EmployeeNid::create([
            'employee_id' => $employee->id,
            'nid_type_id' => $request->nid_type_id,
            'register_number' => $request->register_no,
            'register' => $request->register,
            'volume' => $request->volume,
            'page' => $request->page,
        ]);
        // create education
        EmployeeEducation::create([
            'employee_id' => $employee->id,
            'education_level_id' => $request->education_level_id,
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

        $status = DB::table('status_trans as st')
            ->where('st.status_id', '=', StatusEnum::active->value)
            ->select('st.value as status')
            ->first();
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
                    "status" => StatusEnum::active->value,
                    "status" => $status ? $status->status : 'Active',
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
            ->join('employee_nids as nt', 'emp.id', '=', 'nt.employee_id')
            ->join('contacts', 'emp.contact_id', '=', 'contacts.id')
            ->leftJoin('contacts as fc', 'emp.family_contact_id', '=', 'fc.id')
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
            'mrt.marital_status_id',
            'mrt.value as marital_status',
            'empt.last_name',
            'empt.father_name',
            'emp.date_of_birth',
            'contacts.value as contact',
            'fc.value as family_mem_contact',
            'emails.value as email',
            'emp.gender_id',
            "gent.name_{$locale} as gender",
            'emp.nationality_id',
            'nit.value as nationality',
            'p_add.province_id as parmanent_province_id',
            'p_add.district_id as parmanent_district_id',
            'p_addt.area as parmanent_area',
            'p_pvt.value as parmanent_province',
            'p_dst.value as parmanent_district',
            't_add.province_id as temprory_province_id',
            't_add.district_id as temprory_district_id',
            't_addt.area as temprory_area',
            't_pvt.value as temprory_province',
            't_dst.value as temprory_district'
        )->first();

        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
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
            'marital_status' => ['id' => $employee->marital_status_id, 'name' => $employee->marital_status],
            'last_name' => $employee->last_name,
            'father_name' => $employee->father_name,
            'picture' => $employee->picture,
            'date_of_birth' => $employee->date_of_birth,
            'contact' =>  $employee->contact,
            'family_mem_contact' =>  $employee->family_mem_contact,
            'email' => $employee->email,
            'gender' => ['id' => $employee->gender_id, 'name' => $employee->gender],
            'nationality' => ['id' => $employee->nationality_id, 'name' => $employee->nationality],
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


    public function personalMoreDetial($id)
    {
        $locale = App::getLocale();
        $query = DB::table('employees as emp')
            ->where('emp.id', $id)
            ->join('employee_nids as empn', 'empn.employee_id', '=', 'emp.id')
            ->join('nid_type_trans as nit', function ($join) use ($locale) {
                $join->on('nit.nid_type_id', '=', 'empn.nid_type_id')
                    ->where('nit.language_name', $locale);
            })
            ->join('employee_education as empedu', 'empedu.employee_id', 'emp.id')
            ->join('education_level_trans as edult', function ($join) use ($locale) {
                $join->on('edult.education_level_id', '=', 'empedu.education_level_id ')
                    ->where('edult.language_name', $locale);
            });
        $employee = $query->select(
            'emp.id',
            'empn.register_number',
            'empn.register',
            'empn.volume',
            'empn.page',
            'empn.nid_type_id',
            'nit.value as nid_type',
            'edult.value as education_level',
            'edult.education_level_id'
        )->first();


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
            'register_number' => $employee->register_number,
            'register' => $employee->register,
            'volume' => $employee->volume,
            'page' => $employee->page,
            'nid_type' => ['id' => $employee->nid_type_id, 'name' => $employee->nid_type],
            'education_level' => ['id' => $employee->education_level_id, 'name' => $employee->education_level],
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
    public function updatePersonalDetail(EmployeeUpdateRequest $request)
    {
        $request->validated();
        $id = $request->id;

        $employee = Employee::where('id', $id)->first();
        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        DB::beginTransaction();
        $employee->date_of_birth = $request->date_of_birth;
        $employee->gender_id = $request->gender_id;
        $employee->nationality_id = $request->nationality_id;
        $employee->marital_status_id = $request->marital_status_id;

        $employeeTran  = EmployeeTran::where('employee_id', $id)->first();
        // Update employee_trans (localized data)
        $employeeTran->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
        ]);
        $employeeTran->save();

        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id == $employee->contact_id) {
                $contact->value = $request->contact;
                $contact->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $contact = Contact::where('id', $employee->contact_id)->first();
            $contact->value = $request->contact;
            $contact->save();
        }
        if ($request->email !== null && !empty($request->email)) {
            $email = Email::where('value', $request->email)
                ->select('id')->first();
            if ($email) {
                if ($email->id == $employee->email_id) {
                    $email->value = $request->email_id;
                    $email->save();
                } else {
                    return response()->json([
                        'message' => __('app_translation.email_exist'),
                    ], 409, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                if (isset($employee->email_id)) {
                    $email = Email::where('id', $employee->email_id)->first();
                    $email->value = $request->email;
                    $email->save();
                } else {
                    $email = Email::create(['value' => $request->email]);
                    $employee->email_id = $email->id;
                }
            }
        }

        $employee->save();
        if ($request->has_attachment == true) {
            $user = $request->user();
            $document = EmployeeDocument::join('documents', 'documents.id', '=', 'employee_documents.document_id')
                ->join('check_lists', 'check_lists.id', '=', 'documents.check_list_id')
                ->where('employee_id', $id)
                ->where('documents.check_list_id', CheckListEnum::employee_attachment->value)
                ->where('check_list_id', CheckListEnum::employee_attachment->value)
                ->select('documents.path')
                ->first();
            if ($document) {
                $this->deleteDocument(storage_path() . "/app/private/" . $document->path);
                $document->delete();
            }
            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                CheckListTypeEnum::employee->value,
                CheckListEnum::employee_attachment->value,
                $employee->id
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
                $employee->id
            );
        }


        DB::commit();

        return response()->json([
            'message' => __('app_translation.profile_changed'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'id' => 'required',
        ]);
        $employee = Employee::find($request->id);
        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.user_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $path = $this->storeProfile($request);
        if ($path != null) {
            // 1. delete old profile
            $this->deleteDocument($this->getEmployeeProfilePath($employee->profile));
            // 2. Update the profile
            $employee->picture = $path;
        }
        $employee->save();
        return response()->json([
            'message' => __('app_translation.profile_changed'),
            "profile" => $employee->picture
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteProfilePicture($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => __('app_translation.employee_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        // 1. delete old profile
        $this->deleteDocument($this->getEmployeeProfilePath($employee->picture));
        // 2. Update the profile
        $employee->picture = null;
        $employee->save();
        return response()->json([
            'message' => __('app_translation.success')
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

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
                (SELECT COUNT(*) FROM employees WHERE DATE(created_at) = CURDATE()) AS todayCount, (SELECT COUNT(*) FROM employees AS e JOIN employee_statuses AS es ON e.id = es.employee_id WHERE es.status_id = 1 AND es.active = 1) AS activeUserCount,
                (SELECT COUNT(*) FROM employees AS e JOIN employee_statuses AS es ON e.id = es.employee_id WHERE es.status_id > 2 AND es.active = 1) AS inActiveUserCount
            FROM employees;
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
