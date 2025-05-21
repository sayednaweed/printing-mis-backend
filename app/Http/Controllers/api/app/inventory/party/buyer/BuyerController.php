<?php

namespace App\Http\Controllers\api\app\inventory\party\buyer;

use App\Models\Email;
use App\Models\Party;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Document;
use App\Models\PartyTran;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Enums\Types\PartyTypeEnum;
use App\Http\Requests\app\expense\PartyStoreRequest;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Traits\Helper\FilterTrait;

class BuyerController extends Controller
{

    use FilterTrait;
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
     * Display a listing of the buyers.
     */
    public function index(Request $request)
    {

        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = DB::table('parties as pr')
            ->where('party_type_id', PartyTypeEnum::buyers->value)
            ->join('party_trans as prt', function ($join) use ($locale) {
                $join->on('pr.id', '=', 'prt.party_id')
                    ->where('language_name', $locale);
            })
            ->join('documents as doc', 'doc.id', '=', 'pr.logo_document_id')
            ->join('emails as em', 'em.id', '=', 'pr.email_id')
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

        $buyer = Party::create([
            'party_type_id' => $request->party_type_id,
            'email_id' => $email->id,
            'contact_id' => $contact->id,
            'address_id' => $address->id,
            'logo_document_id' => ''

        ]);

        if ($request->has_attachment == true) {
            $user = $request->user();
            $task = $this->pendingTaskRepository->pendingTaskExist(
                $request->user(),
                CheckListTypeEnum::buyers->value,
                CheckListEnum::buyers_logo->value,
                null
            );

            if (!$task) {
                return response()->json([
                    'message' => __('app_translation.task_not_found')
                ], 404);
            }
            $document_id = '';
            $logo_path = '';

            $this->storageRepository->buyerDocumentStore(
                $buyer->id,
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
                    $logo_path = $documentData['path'];
                    $document_id = $document->id;
                }
            );


            $this->pendingTaskRepository->destroyPendingTask(
                $request->user(),
                CheckListTypeEnum::buyers->value,
                CheckListEnum::buyers_logo->value,
                null
            );

            $buyer->logo_document_id = $document_id;
            $buyer->save();
        }

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            PartyTran::create([
                'employee_id' => $buyer->id,
                "name" => $request->name,
                "company_name" => $request->company_name,
                "language_name" => $code,
            ]);
        }

        DB::commit();

        return response()->json(
            [
                "buyer" => [
                    'logo' => $logo_path,
                    "id" => $buyer->id,
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified buyer.
     */
    public function edit(string $id)
    {
        $buyer = Party::findOrFail($id);
        return response()->json(['buyer' => $buyer], 200);
    }

    /**
     * Update the specified buyer in storage.
     */
    public function update(Request $request, string $id)
    {
        $buyer = Party::findOrFail($id);
        $buyer->update($request->only([
            'party_type_id',
            'logo_document_id',
            // add other updatable fields as needed
        ]));
        // Optionally update related models (email, contact, address) here
        return response()->json(['buyer' => $buyer, 'message' => __('app_translation.success')], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
