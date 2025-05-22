<?php

namespace App\Repositories\Party;

interface PartyRepositoryInterface
{
    /**
     * Creates a party.
     *
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function store($request, $checklistType, $checklist, $partyType);
}
