<?php

namespace App\Http\Controllers\api\app\expense\party;

use App\Models\Email;
use App\Models\Party;
use App\Models\Address;
use App\Models\Contact;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Http\Requests\app\expense\PartyStoreRequest;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class PartyController extends Controller
{

    protected $pendingTaskRepository;
    protected $storageRepository;


    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        StorageRepositoryInterface $storageRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->storageRepository = $storageRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartyStoreRequest $request)
    {
        //

        $request->validate();


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



        $address = Address::create([
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
        ]);
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AddressTran::create([
                "area" => $request->area,
                "address_id" => $address->id,
                "language_name" => $code,
            ]);
        }



        if ($request->has_attachment == true) {
            $user = $request->user();
            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                CheckListTypeEnum::party->value,
                CheckListEnum::party_attachment->value,
                null
            );

            if (!$task) {
                return response()->json([
                    'message' => __('app_translation.task_not_found')
                ], 404);
            }
            $document_id = '';

            $this->storageRepository->employeeDocumentStore(
                $employee->id,
                $task->id,
                function ($documentData) use (&$document_id) {
                    $checklist_id = $documentData['check_list_id'];
                    $document = Document::create([
                        'actual_name' => $documentData['actual_name'],
                        'size' => $documentData['size'],
                        'path' => $documentData['path'],
                        'type' => $documentData['type'],
                        'check_list_id' => $checklist_id,
                    ]);
                    $document_id = $document->id;
                }
            );

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
        Party::create([
            'party_type_id' => $request->party_type_id,
            'email_id' => $email->id,
            'contact_id' => $contact->id,
            'address_id' => $address->id,
            'logo' => ''

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
