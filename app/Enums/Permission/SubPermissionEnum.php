<?php

namespace App\Enums\Permission;

enum SubPermissionEnum: int
{
    /*
    HR SECTION
    */
    case hr_employees_information = 1;
    case hr_employees_promotion_demotion = 2;
    case hr_employees_status = 3;
    public const HR_EMPLOYEES = [
        1 => "personal_information",
        2 => "promotion_demotion",
        3 => "status",
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
    case hr_configuration_department = 22;
    case hr_configuration_leave_type = 23;
    case hr_configuration_shifts = 24;
    case hr_configuration_hire_type = 25;
    public const HR_CONFIGURATIONS = [
        21 => "job",
        22 => "department",
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
    case expense_configuration_expense_icon = 52;
    public const EXPENSE_CONFIGURATIONS = [
        51 => "expense_type",
        52 => "expense_icon",
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
    case inventory_configuration_warehouse = 111;
    case inventory_configuration_material = 112;
    case inventory_configuration_material_type = 113;
    case inventory_configuration_size_unit = 114;
    case inventory_configuration_size = 115;
    case inventory_configuration_weight = 116;
    case inventory_configuration_weight_unit = 117;
    public const INVENTORY_CONFIGURATIONS = [
        111 => "warehouse",
        112 => "material",
        113 => "material_type",
        114 => "size_unit",
        115 => "size",
        116 => "weight",
        117 => "weight_unit",
    ];
    case inventory_reports_purchases = 121;
    case inventory_reports_sells = 122;
    case inventory_reports_warehouses = 123;
    public const INVENTORY_REPORTS = [
        121 => "reports_purchases",
        122 => "reports_sells",
        123 => "reports_warehouses",
    ];
    case inventory_accounts_detail = 161;
    case inventory_accounts_expenses = 162;
    case inventory_accounts_sales = 163;
    case inventory_accounts_purchases = 164;
    case inventory_accounts_salaries = 165;
    public const INVENTORY_ACCOUNTS = [
        161 => "detail",
        162 => "expenses",
        163 => "sales",
        164 => "purchases",
        165 => "salaries",
    ];
}
