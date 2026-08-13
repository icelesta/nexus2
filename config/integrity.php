<?php

declare(strict_types=1);

use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\BusinessUnit;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CostCenter;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Department;
use App\Models\ExchangeRate;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\JournalType;
use App\Models\Manufacturer;
use App\Models\MasterCategory;
use App\Models\PaymentTerm;
use App\Models\ProfitCenter;
use App\Models\Section;
use App\Models\Supplier;
use App\Models\TaxMaster;
use App\Models\TransactionNumbering;
use App\Models\Uom;
use App\Models\Warehouse;
use App\Models\WarehouseType;

return [

    /*
    |--------------------------------------------------------------------------
    | Data Integrity Configuration
    |--------------------------------------------------------------------------
    |
    | Every master data that must be protected from deletion
    | is registered here.
    |
    */

    ChartOfAccount::class => [

        'name' => 'Chart Of Account',

        'dependencies' => [

            // Example
            // [
            //     'model' => App\Models\JournalDetail::class,
            //     'foreign_key' => 'account_id',
            //     'module' => 'Journal Entry',
            // ],

        ],

    ],

    Company::class => [

        'name' => 'Company',

        'dependencies' => [

            [
                'model' => App\Models\Branch::class,
                'foreign_key' => 'company_id',
                'module' => 'Branch',
            ],

            [
                'model' => App\Models\BusinessUnit::class,
                'foreign_key' => 'company_id',
                'module' => 'Business Unit',
            ],

            [
                'model' => App\Models\Department::class,
                'foreign_key' => 'company_id',
                'module' => 'Department',
            ],

            [
                'model' => App\Models\Section::class,
                'foreign_key' => 'company_id',
                'module' => 'Section',
            ],

            [
                'model' => App\Models\CostCenter::class,
                'foreign_key' => 'company_id',
                'module' => 'Cost Center',
            ],

            [
                'model' => App\Models\ProfitCenter::class,
                'foreign_key' => 'company_id',
                'module' => 'Profit Center',
            ],

            [
                'model' => App\Models\Warehouse::class,
                'foreign_key' => 'company_id',
                'module' => 'Warehouse',
            ],

            [
                'model' => App\Models\Supplier::class,
                'foreign_key' => 'company_id',
                'module' => 'Supplier',
            ],

            [
                'model' => App\Models\Customer::class,
                'foreign_key' => 'company_id',
                'module' => 'Customer',
            ],

            [
                'model' => App\Models\ChartOfAccount::class,
                'foreign_key' => 'company_id',
                'module' => 'Chart Of Account',
            ],

            [
                'model' => App\Models\BankAccount::class,
                'foreign_key' => 'company_id',
                'module' => 'Bank Account',
            ],

            [
                'model' => App\Models\User::class,
                'foreign_key' => 'company_id',
                'module' => 'User',
            ],

        ],

    ],

    Branch::class => [

        'name' => 'Branch',

        'dependencies' => [

            [
                'model' => App\Models\BusinessUnit::class,
                'foreign_key' => 'branch_id',
                'module' => 'Business Unit',
            ],

            [
                'model' => App\Models\Department::class,
                'foreign_key' => 'branch_id',
                'module' => 'Department',
            ],

            [
                'model' => App\Models\Section::class,
                'foreign_key' => 'branch_id',
                'module' => 'Section',
            ],

            [
                'model' => App\Models\CostCenter::class,
                'foreign_key' => 'branch_id',
                'module' => 'Cost Center',
            ],

            [
                'model' => App\Models\ProfitCenter::class,
                'foreign_key' => 'branch_id',
                'module' => 'Profit Center',
            ],

            [
                'model' => App\Models\Warehouse::class,
                'foreign_key' => 'branch_id',
                'module' => 'Warehouse',
            ],

        ],

    ],

    BusinessUnit::class => [

        'name' => 'Business Unit',

        'dependencies' => [

            [
                'model' => App\Models\Branch::class,
                'foreign_key' => 'company_id',
                'module' => 'Branch',
            ],            

            [
                'model' => App\Models\Department::class,
                'foreign_key' => 'business_unit_id',
                'module' => 'Department',
            ],

            [
                'model' => App\Models\Section::class,
                'foreign_key' => 'business_unit_id',
                'module' => 'Section',
            ],

            [
                'model' => App\Models\CostCenter::class,
                'foreign_key' => 'business_unit_id',
                'module' => 'Cost Center',
            ],

            [
                'model' => App\Models\ProfitCenter::class,
                'foreign_key' => 'business_unit_id',
                'module' => 'Profit Center',
            ],

        ],

    ],

    Department::class => [

        'name' => 'Department',

        'dependencies' => [

            [
                'model' => App\Models\Branch::class,
                'foreign_key' => 'company_id',
                'module' => 'Branch',
            ], 
            
            [
                'model' => App\Models\Department::class,
                'foreign_key' => 'business_unit_id',
                'module' => 'Department',
            ],                       

            [
                'model' => App\Models\Section::class,
                'foreign_key' => 'department_id',
                'module' => 'Section',
            ],

            [
                'model' => App\Models\CostCenter::class,
                'foreign_key' => 'department_id',
                'module' => 'Cost Center',
            ],

            [
                'model' => App\Models\ProfitCenter::class,
                'foreign_key' => 'department_id',
                'module' => 'Profit Center',
            ],

        ],

    ],

    Section::class => [

        'name' => 'Section',

        'dependencies' => [

            [
                'model' => App\Models\Branch::class,
                'foreign_key' => 'company_id',
                'module' => 'Branch',
            ],

            [
                'model' => App\Models\BusinessUnit::class,
                'foreign_key' => 'company_id',
                'module' => 'Business Unit',
            ],

            [
                'model' => App\Models\Department::class,
                'foreign_key' => 'company_id',
                'module' => 'Department',
            ],            

            [
                'model' => App\Models\CostCenter::class,
                'foreign_key' => 'department_id',
                'module' => 'Cost Center',
            ],

            [
                'model' => App\Models\ProfitCenter::class,
                'foreign_key' => 'department_id',
                'module' => 'Profit Center',
            ],

        ],

    ],

    CostCenter::class => [

        'name' => 'Cost Center',

        'dependencies' => [

            [
                'model' => App\Models\Branch::class,
                'foreign_key' => 'company_id',
                'module' => 'Branch',
            ],             

            [
                'model' => App\Models\Branch::class,
                'foreign_key' => 'company_id',
                'module' => 'Branch',
            ],

            [
                'model' => App\Models\BusinessUnit::class,
                'foreign_key' => 'company_id',
                'module' => 'Business Unit',
            ],

            [
                'model' => App\Models\Department::class,
                'foreign_key' => 'company_id',
                'module' => 'Department',
            ],  

            [
                'model' => App\Models\Section::class,
                'foreign_key' => 'department_id',
                'module' => 'Section',
            ],            

        ],

    ],

    ProfitCenter::class => [

        'name' => 'Profit Center',

        'dependencies' => [],

    ],

    Currency::class => [

        'name' => 'Currency',

        'dependencies' => [],

    ],

    ExchangeRate::class => [

        'name' => 'Exchange Rate',

        'dependencies' => [],

    ],

    FiscalYear::class => [

        'name' => 'Fiscal Year',

        'dependencies' => [],

    ],

    TaxMaster::class => [

        'name' => 'Tax Master',

        'dependencies' => [],

    ],

    PaymentTerm::class => [

        'name' => 'Payment Term',

        'dependencies' => [],

    ],

    BankAccount::class => [

        'name' => 'Bank Account',

        'dependencies' => [],

    ],

    Supplier::class => [

        'name' => 'Supplier',

        'dependencies' => [],

    ],

    Customer::class => [

        'name' => 'Customer',

        'dependencies' => [],

    ],

    WarehouseType::class => [

        'name' => 'Warehouse Type',

        'dependencies' => [],

    ],

    Warehouse::class => [

        'name' => 'Warehouse',

        'dependencies' => [],

    ],

    MasterCategory::class => [

        'name' => 'Master Category',

        'dependencies' => [],

    ],

    JournalType::class => [

        'name' => 'Journal Type',

        'dependencies' => [],

    ],

    Uom::class => [

        'name' => 'Unit Of Measure',

        'dependencies' => [],

    ],

    ItemCategory::class => [

        'name' => 'Item Category',

        'dependencies' => [],

    ],

    Brand::class => [

        'name' => 'Brand',

        'dependencies' => [],

    ],

    Manufacturer::class => [

        'name' => 'Manufacturer',

        'dependencies' => [],

    ],

    Item::class => [

        'name' => 'Item',

        'dependencies' => [],

    ],

    TransactionNumbering::class => [

        'name' => 'Transaction Numbering',

        'dependencies' => [],

    ],

];