<?php

namespace App\Repositories\Party;

interface PartyRepositoryInterface
{
    /**
     * Creates a party.
     *
     *
     * @param Illuminate\Http\Request $request
     * @param string $checklistType
     * @param string $checklist
     * @param string $partyType
     * @return mixed
     */
    public function store($request, $checklistType, $checklist, $partyType);
    /**
     * returns parties.
     *
     *
     * @param Illuminate\Http\Request $request
     * @param string $partyType
     * @return mixed
     */
    public function parties($request, $partyType);
    /**
     * returns party.
     *
     *
     * @param string $Party   
     * @param string $partyType
     * @return mixed
     */
    public function party($id, $partyType);
    /**
     * returns updated party.
     *
     * @param Illuminate\Http\Request $request
     * @param string $partyType
     * @return mixed
     */
    public function updateParty($request, $partyType);
}
