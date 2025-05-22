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

class BuyerController extends Controller
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
     * Display a listing of the buyers.
     */
    public function index(Request $request)
    {
        return $this->partyRepository->parties(
            $request,
            PartyTypeEnum::buyers->value
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
            CheckListTypeEnum::buyers->value,
            CheckListEnum::buyers_logo->value,
            PartyTypeEnum::buyers->value
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
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
