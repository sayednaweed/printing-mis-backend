<?php

namespace App\Enums\Permission;

enum SubPermissionEnum: int
{
    /*
    HR SECTION
    */
    case hr_employees_information = 1;
    case hr_employees_promotion_demotion = 2;
    public const HR_EMPLOYEES = [
        1 => "personal_information",
        2 => "promotion_demotion",
    ];
    case hr_user_information = 11;
    case hr_user_password = 12;
    case hr_user_permission = 13;
    public const HR_USERS = [
        11 => "account_information",
        12 => "update_account_password",
        13 => "permissions",
    ];
    case hr_configuration_job = 21;
    case hr_configuration_destination = 22;
    case hr_configuration_leave_type = 23;
    case hr_configuration_shifts = 24;
    case hr_configuration_hire_type = 25;
    public const HR_CONFIGURATIONS = [
        21 => "job",
        22 => "destination",
        23 => "leave_type",
        24 => "shifts",
        25 => "hire_type",
    ];
    case hr_reports_salaries = 31;
    case hr_reports_attendance = 32;
    public const HR_REPORTS = [
        31 => "reports_salaries",
        32 => "reports_dattendance",
    ];
        /*
    EXPENSE SECTION
    */
    case expense_configuration_expense_type = 51;
    public const EXPENSE_CONFIGURATIONS = [
        51 => "expense_type",
    ];
        /*
    INVENTORY SECTION
    */
    case inventory_sellers_personal_information = 61;
    case inventory_sellers_transactions = 62;
    public const INVENTORY_SELLERS = [
        61 => "personal_information",
        62 => "transactions",
    ];
    case inventory_buyers_personal_information = 71;
    case inventory_buyers_transactions = 72;
    public const INVENTORY_BUYERS = [
        71 => "personal_information",
        72 => "transactions",
    ];
    case inventory_sells_details = 81;
    case inventory_sells_payments = 82;
    public const INVENTORY_SELLS = [
        81 => "details",
        82 => "payments",
    ];
    case inventory_purchase_details = 91;
    case inventory_purchase_payments = 92;
    public const INVENTORY_PURCHASE = [
        91 => "details",
        92 => "payments",
    ];
    case inventory_configuration_accounts = 111;
    case inventory_configuration_warehouse = 112;
    case inventory_configuration_material = 113;
    case inventory_configuration_material_type = 114;
    case inventory_configuration_size_unit = 115;
    case inventory_configuration_size = 116;
    case inventory_configuration_weight = 117;
    case inventory_configuration_weight_unit = 118;
    public const INVENTORY_CONFIGURATIONS = [
        111 => "accounts",
        112 => "warehouse",
        113 => "material",
        114 => "material_type",
        115 => "size_unit",
        116 => "size",
        117 => "weight",
        118 => "weight_unit",
    ];
    case inventory_reports_purchases = 121;
    case inventory_reports_sells = 122;
    case inventory_reports_warehouses = 123;
    public const INVENTORY_REPORTS = [
        121 => "reports_purchases",
        122 => "reports_sells",
        123 => "reports_warehouses",
    ];
}
