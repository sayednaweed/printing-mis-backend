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
use App\Traits\Helper\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Traits\Address\AddressTrait;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class PartyRepository implements PartyRepositoryInterface
{
    use FilterTrait, AddressTrait;

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

            $method = $partyType === PartyTypeEnum::sellers ? 'sellerDocumentStore' : 'buyerDocumentStore';

            $this->storageRepository->$method(
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
                "party" => [
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
    public function parties($request, $partyType)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = DB::table('parties as pr')
            ->where('party_type_id', $partyType)
            ->join('party_trans as prt', function ($join) use ($locale) {
                $join->on('pr.id', '=', 'prt.party_id')
                    ->where('language_name', $locale);
            })
            ->leftJoin('documents as doc', 'doc.id', '=', 'pr.logo_document_id')
            ->leftJoin('emails as em', 'em.id', '=', 'pr.email_id')
            ->join('contacts as con', 'con.id', '=', 'pr.contact_id')
            ->select(
                'pr.id',
                'prt.name',
                'prt.company_name',
                'em.value as email',
                'con.value as contact',
                'doc.path as logo',
                'pr.created_at',
            );

        $this->applyDate($query, $request, 'pr.created_at', 'pr.created_at');
        $allowedColumns = [
            'name' => 'prt.name',
            'company_name' => 'prt.company_name',
        ];
        $this->applyFilters($query, $request, $allowedColumns);
        $allowedColumns = [
            'created_at' => 'pr.created_at',
            'id' => 'pr.id',
        ];
        $this->applySearch($query, $request, $allowedColumns);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function party($id, $partyType)
    {
        $locale = App::getLocale();
        $tr = [];
        $query = DB::table('parties as pr')
            ->where('party_type_id', $partyType)
            ->where('pr.id', $id)
            ->join('party_trans as prt', function ($join) use ($locale) {
                $join->on('pr.id', '=', 'prt.party_id')
                    ->where('language_name', $locale);
            })
            ->leftJoin('documents as doc', 'doc.id', '=', 'pr.logo_document_id')
            ->leftJoin('emails as em', 'em.id', '=', 'pr.email_id')
            ->join('contacts as con', 'con.id', '=', 'pr.contact_id');
        $query = $this->address($query, 'p_', 'pr.address_id');

        $query->select(
            'pr.id',
            'prt.name',
            'prt.company_name',
            'em.value as email',
            'con.value as contact',
            'doc.path as logo',
            'pr.created_at',
            'p_add.province_id as province_id',
            'p_add.district_id as district_id',
            'p_addt.area as area',
            'p_pvt.value as province',
            'p_dst.value as district',
        )->first();

        return response()->json(
            [
                "party" => [
                    "id" => $query->id,
                    "name" => $query->name,
                    "company_name" => $query->company_name,
                    "email" => $query->email,
                    "contact" => $query->contact,
                    "province" => ['id' => $query->province_id, 'name' => $query->province],
                    "district" => ['id' => $query->district_id, 'name' => $query->district],
                    "area" => $query->area,
                    "logo" => $query->logo,
                    "created_at" => $query->created_at
                ],
                "message" => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function updateParty($request, $partyType)
    {


        DB::beginTransaction();
        $locale = App::getLocale();

        $party = Party::findOrFail($request->id);




        $contact = Contact::where('value', $request->contact)
            ->select('id')->first();
        if ($contact) {
            if ($contact->id == $party->contact_id) {
                $contact->value = $request->contact;
                $contact->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $contact = Contact::where('id', $party->contact_id)->first();
            $contact->value = $request->contact;
            $contact->save();
        }
        if ($request->email !== null && !empty($request->email)) {
            $email = Email::where('value', $request->email)
                ->select('id')->first();
            if ($email) {
                if ($email->id == $party->email_id) {
                    $email->value = $request->email_id;
                    $email->save();
                } else {
                    return response()->json([
                        'message' => __('app_translation.email_exist'),
                    ], 409, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                if (isset($party->email_id)) {
                    $email = Email::where('id', $party->email_id)->first();
                    $email->value = $request->email;
                    $email->save();
                } else {
                    $email = Email::create(['value' => $request->email]);
                    $party->email_id = $email->id;
                }
            }
        }

        // Update address
        $party->address->update([
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
        ]);

        // Update address translations
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AddressTran::updateOrCreate(
                ['address_id' => $party->address->id, 'language_name' => $code],
                ['area' => $request->area]
            );
        }


        // Update party translations
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            PartyTran::updateOrCreate(
                ['party_id' => $party->id, 'language_name' => $code],
                ['name' => $request->name, 'company_name' => $request->company_name]
            );
        }

        // Handle attachment/logo update
        if ($request->has_attachment == true) {
            $document_id = '';
            $logo_path = '';

            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                $request->checklist_type,
                $request->checklist,
                null
            );

            if (!$task) {
                return response()->json([
                    'message' => __('app_translation.task_not_found')
                ], 404);
            }

            $this->storageRepository->sellerDocumentStore(
                $party->id,
                $task->id,
                function ($documentData) use (&$document_id, &$logo_path) {
                    $document = Document::create([
                        'actual_name' => $documentData['actual_name'],
                        'size' => $documentData['size'],
                        'path' => $documentData['path'],
                        'type' => $documentData['type'],
                        'check_list_id' => $documentData['check_list_id'],
                    ]);
                    $logo_path = $documentData['path'];
                    $document_id = $document->id;
                }
            );

            $this->pendingTaskRepository->destroyPendingTask(
                $request->user(),
                $request->checklist_type,
                $request->checklist,
                null
            );

            $party->logo_document_id = $document_id;
        }

        $party->save();
        DB::commit();

        return response()->json([
            "party" => [
                "id" => $party->id,
                "name" => $request->name,
                "company_name" => $request->company_name,
                "email" => $request->email,
                "contact" => $request->contact,
                "province_id" => $request->province_id,
                "district_id" => $request->district_id,
                "area" => $request->area,
                "logo" => $logo_path ?? optional($party->document)->path,
            ],
            "message" => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
