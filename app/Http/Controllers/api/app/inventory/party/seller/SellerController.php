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
        return $this->partyRepository->parties(
            $request,
            PartyTypeEnum::sellers->value
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
        return $this->partyRepository->store(
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
        return $this->partyRepository->party(
            $id,
            PartyTypeEnum::sellers->value
        );
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
