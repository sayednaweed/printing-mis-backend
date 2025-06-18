<?php

namespace App\Traits\Hr\Salary;

use Illuminate\Support\Facades\DB;


trait TaxTrait
{

    // private function salaryTax($salary)
    // {
    //     // Get active tax ranges and sort them by start
    //     $tax_ranges = DB::table('salary_tax_ranges')
    //         ->where('is_active', true)
    //         ->orderBy('start')
    //         ->get();

    //     $tax_total = 0;

    //     foreach ($tax_ranges as $range) {
    //         $start = $range->start;
    //         $end = $range->end ?? INF; // In case of open-ended last range
    //         $tax = $range->tax; // Percentage (e.g., 10 for 10%)

    //         // If salary is less than or equal to start, no tax applies in this or any higher range
    //         if ($salary <= $start) {
    //             break;
    //         }

    //         // Calculate the taxable amount in this range
    //         $taxable_amount = min($salary, $end) - $start;


    //         // Calculate tax for this range

    //         if ($start === 5000.00 and $end === 12500.00) {
    //             $tax_total += 150;
    //         } else {
    //             $tax_total += $taxable_amount * ($tax / 100);
    //         }
    //     }

    //     return $tax_total;
    // }
    private function salaryTax($salary)
    {
        $tax_ranges = DB::table('salary_tax_ranges')
            ->where('is_active', true)
            ->orderBy('start')
            ->get();

        $tax_total = 0;

        foreach ($tax_ranges as $range) {
            $start = $range->start;
            $end = $range->end ?? INF;

            // Skip if salary is below the start of this range
            if ($salary <= $start) {
                break;
            }

            // Determine how much of the salary falls into this range
            $amount_in_range = min($salary, $end) - $start;

            // If fixed tax is defined, apply it once (as long as any amount falls in this range)
            if (!is_null($range->fixed_tax)) {
                $tax_total += $range->fixed_tax;
            }
            // If percentage tax is defined, apply it proportionally
            elseif (!is_null($range->percentage_tax)) {
                $tax_total += $amount_in_range * ($range->percentage_tax / 100);
            }
        }

        return $tax_total;
    }
}
