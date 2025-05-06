<?php

namespace App\Traits\Address;

use App\Models\AddressTran;

trait AddressTrait
{
    private function getAddressArea($address_id, $lang)
    {
        return AddressTran::where('address_id', $address_id)
            ->where('language_name', $lang)
            ->value('area');
    }

    private function getAddressAreaTran($address_id)
    {
        $translations = AddressTran::where('address_id', $address_id)
            ->select('language_name', 'area')
            ->get()
            ->keyBy('language_name');
        return $translations;
    }

    private function address($query, $as = '', $joinTable)
    {
        $locale = App()->getLocale();
        $query->join("addresses as {$as}add", $joinTable, '=', "{$as}add.id")
            ->join("address_trans as {$as}addt", function ($join) use ($locale, $as) {
                $join->on("{$as}addt.address_id", '=', "{$as}add.id")
                    ->where("{$as}addt.language_name", $locale);
            })
            ->join("province_trans as {$as}pvt", function ($join) use ($locale, $as) {
                $join->on("{$as}pvt.province_id", '=', "{$as}add.province_id")
                    ->where("{$as}pvt.language_name", $locale);
            })
            ->join("district_trans as {$as}dst", function ($join) use ($locale, $as) {
                $join->on("{$as}dst.district_id", '=', "{$as}add.district_id")
                    ->where("{$as}dst.language_name", $locale);
            });

        return $query;
    }
}
