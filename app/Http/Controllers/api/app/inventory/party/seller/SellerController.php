<?php

namespace App\Http\Controllers\api\app\inventory\party\seller;

use App\Models\Email;
use App\Models\Party;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Document;
use App\Models\PartyTran;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use Illuminate\Http\Request;
use App\Enums\Types\PartyTypeEnum;
use App\Traits\Helper\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use App\Http\Requests\app\expense\PartyStoreRequest;
use App\Repositories\Party\PartyRepositoryInterface;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class SellerController extends Controller
{

    use FilterTrait;
    protected $pendingTaskRepository;
    protected $storageRepository;
    protected $partyRepository;


    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        StorageRepositoryInterface $storageRepository,
        PartyRepositoryInterface $partyRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->storageRepository = $storageRepository;
        $this->partyRepository = $partyRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = DB::table('parties as pr')
            ->where('party_type_id', PartyTypeEnum::sellers->value)
            ->join('party_trans as prt', function ($join) use ($locale) {
                $join->on('pr.id', '=', 'prt.party_id')
                    ->where('language_name', $locale);
            })
            ->leftJoin('documents as doc', 'doc.id', '=', 'pr.logo_document_id')
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
        $request->validated();
        $this->partyRepository->store(
            $request,
            CheckListTypeEnum::sellers->value,
            CheckListEnum::sellers_logo->value,
            PartyTypeEnum::sellers->value
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
