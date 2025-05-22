<?php

namespace App\Repositories\Party;

use App\Models\Email;
use App\Models\Party;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Document;
use App\Models\PartyTran;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use App\Enums\Types\PartyTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class PartyRepository implements PartyRepositoryInterface
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

    public function store($request, $checklistType, $checklist, $partyType)
    {
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

        $seller = Party::create([
            'party_type_id' =>  $partyType,
            'email_id' => $email ? $email->id : null,
            'contact_id' => $contact->id,
            'address_id' => $address->id,
        ]);
        $logo_path =  null;

        if ($request->has_attachment == true) {
            $user = $request->user();
            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                $checklistType,
                $checklist,
                null
            );

            if (!$task) {
                return response()->json([
                    'message' => __('app_translation.task_not_found')
                ], 404);
            }
            $document_id = '';

            $this->storageRepository->sellerDocumentStore(
                $seller->id,
                $task->id,
                function ($documentData) use (&$document_id, &$logo_path) {
                    $checklist_id = $documentData['check_list_id'];
                    $document = Document::create([
                        'actual_name' => $documentData['actual_name'],
                        'size' => $documentData['size'],
                        'path' => $documentData['path'],
                        'type' => $documentData['type'],
                        'check_list_id' => $checklist_id,
                    ]);
                    $logo_path = $documentData['path'];
                    $document_id = $document->id;
                }
            );


            $this->pendingTaskRepository->destroyPendingTask(
                $request->user(),
                $checklistType,
                $checklist,
                null
            );

            $seller->logo_document_id = $document_id;
            $seller->save();
        }

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            PartyTran::create([
                'party_id' => $seller->id,
                "name" => $request->name,
                "company_name" => $request->company_name,
                "language_name" => $code,
            ]);
        }

        DB::commit();

        return response()->json(
            [
                "seller" => [
                    'logo' => $logo_path,
                    "id" => $seller->id,
                    "name" => $request->name,
                    "company_name" => $request->company_name,
                    "email" => $request->email,
                    "contact" => $request->contact,
                ],
                "message" => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
