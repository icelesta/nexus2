<?php

declare(strict_types=1);

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Currency;
use App\Models\MasterCategory;
use App\Models\PaymentTerm;
use App\Models\TaxMaster;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            /*
            |--------------------------------------------------------------------------
            | General Information
            |--------------------------------------------------------------------------
            */

            Section::make('General Information')

                ->description('Basic supplier master information.')

                ->icon('heroicon-o-building-office')

                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | Company
                    |--------------------------------------------------------------------------
                    */

                    Select::make('company_id')

                        ->label('Company')

                        ->relationship(
                            'company',
                            'company_name'
                        )

                        ->searchable()

                        ->preload()

                        ->native(false)

                        ->required()

                        ->helperText(
                            'Company that owns this supplier.'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | Supplier Code
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make('supplier_code')

                        ->label('Supplier Code')

                        ->required()

                        ->unique(ignoreRecord: true)

                        ->maxLength(50)

                        ->placeholder('SUP-000001')

                        ->disabledOn('edit')

                        ->helperText(
                            'Unique supplier code.'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | Supplier Name
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make('supplier_name')

                        ->label('Supplier Name')

                        ->required()

                        ->maxLength(200)

                        ->placeholder('PT Schlumberger Indonesia')

                        ->helperText(
                            'Official supplier name.'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | Category
                    |--------------------------------------------------------------------------
                    */

                    Select::make('category_id')

                        ->label('Category')

                        ->options(

                            MasterCategory::query()

                                ->orderBy('category_name')

                                ->pluck(
                                    'category_name',
                                    'id'
                                )

                        )

                        ->searchable()

                        ->preload()

                        ->native(false)

                        ->helperText(
                            'Supplier business category.'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | Company Type
                    |--------------------------------------------------------------------------
                    */

                    Select::make('company_type')

                        ->label('Company Type')

                        ->options([

                            'Supplier'     => 'Supplier',

                            'Contractor'   => 'Contractor',

                            'Service'      => 'Service',

                            'Manufacturer' => 'Manufacturer',

                        ])

                        ->required()

                        ->searchable()

                        ->native(false)

                        ->helperText(
                            'Business classification.'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | Contact Person
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make('contact_person')

                        ->label('Contact Person')

                        ->maxLength(150)

                        ->placeholder('John Doe')

                        ->helperText(
                            'Primary supplier contact.'
                        ),

                ])

                ->columns(3),

                /*
                |--------------------------------------------------------------------------
                | Tax Information
                |--------------------------------------------------------------------------
                */

                Section::make('Tax Information')

                    ->description('Supplier tax registration information.')

                    ->icon('heroicon-o-receipt-percent')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Tax Number
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('tax_number')

                            ->label('Tax Number (NPWP)')

                            ->maxLength(100)

                            ->placeholder('01.234.567.8-901.000')

                            ->helperText(
                                'Registered supplier tax number.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Tax Master
                        |--------------------------------------------------------------------------
                        */

                        Select::make('tax_id')

                            ->label('Tax Profile')

                            ->relationship(
                                'tax',
                                'tax_name'
                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->placeholder('Select Tax Profile')

                            ->helperText(
                                'Default tax profile used for purchasing transactions.'
                            ),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                Section::make('Contact Information')

                    ->description('Supplier communication and contact details.')

                    ->icon('heroicon-o-phone')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Email
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('email')

                            ->label('Email')

                            ->email()

                            ->maxLength(150)

                            ->placeholder('supplier@company.com')

                            ->autocomplete(false)

                            ->helperText(
                                'Primary email address for Purchase Orders and notifications.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Phone
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('phone')

                            ->label('Office Phone')

                            ->tel()

                            ->maxLength(50)

                            ->placeholder('+62 21 5555555')

                            ->helperText(
                                'Main office telephone number.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Mobile
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('mobile')

                            ->label('Mobile Phone')

                            ->tel()

                            ->maxLength(50)

                            ->placeholder('+62 812 3456 7890')

                            ->helperText(
                                'Primary contact mobile number.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Website
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('website')

                            ->label('Website')

                            ->url()

                            ->maxLength(150)

                            ->placeholder('https://www.company.com')

                            ->helperText(
                                'Official supplier website.'
                            ),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Address Information
                |--------------------------------------------------------------------------
                */

                Section::make('Address Information')

                    ->description('Supplier business address information.')

                    ->icon('heroicon-o-map-pin')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Address
                        |--------------------------------------------------------------------------
                        */

                        Textarea::make('address')

                            ->label('Street Address')

                            ->rows(4)

                            ->columnSpanFull()

                            ->placeholder('Jl. Jenderal Sudirman No. 88')

                            ->helperText(
                                'Complete business street address.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | City
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('city')

                            ->label('City')

                            ->maxLength(100)

                            ->placeholder('Jakarta')

                            ->helperText(
                                'City or municipality.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Province
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('province')

                            ->label('Province')

                            ->maxLength(100)

                            ->placeholder('DKI Jakarta')

                            ->helperText(
                                'Province or state.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Country
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('country')

                            ->label('Country')

                            ->default('Indonesia')

                            ->maxLength(100)

                            ->placeholder('Indonesia')

                            ->helperText(
                                'Country of supplier.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Postal Code
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('postal_code')

                            ->label('Postal Code')

                            ->maxLength(20)

                            ->placeholder('12345')

                            ->helperText(
                                'ZIP or postal code.'
                            ),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Financial Information
                |--------------------------------------------------------------------------
                */

                Section::make('Financial Information')

                    ->description('Financial configuration for purchasing and accounts payable.')

                    ->icon('heroicon-o-banknotes')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Currency
                        |--------------------------------------------------------------------------
                        */

                        Select::make('currency_id')

                            ->label('Currency')

                            ->relationship(
                                'currency',
                                'currency_code'
                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->required()

                            ->helperText(
                                'Default transaction currency.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Payment Term
                        |--------------------------------------------------------------------------
                        */

                        Select::make('payment_term_id')

                            ->label('Payment Term')

                            ->relationship(
                                'paymentTerm',
                                'term_name'
                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->helperText(
                                'Default payment term for Purchase Orders.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Accounts Payable Account
                        |--------------------------------------------------------------------------
                        */

                        Select::make('ap_account_id')

                            ->label('Accounts Payable Account')

                            ->options(

                                ChartOfAccount::query()

                                    ->where('account_type', 'Liability')

                                    ->orderBy('account_code')

                                    ->get()

                                    ->mapWithKeys(fn (ChartOfAccount $account) => [

                                        $account->id =>

                                        "{$account->account_code} - {$account->account_name}",

                                    ])

                            )

                            ->searchable()

                            ->preload()

                            ->native(false)

                            ->required()

                            ->helperText(
                                'Default Accounts Payable ledger account.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Credit Limit
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('credit_limit')

                            ->label('Credit Limit')

                            ->numeric()

                            ->default(0)

                            ->prefix('Rp')

                            ->helperText(
                                'Maximum credit limit granted to this supplier.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Opening Balance
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('opening_balance')

                            ->label('Opening Balance')

                            ->numeric()

                            ->default(0)

                            ->prefix('Rp')

                            ->helperText(
                                'Opening balance during ERP implementation.'
                            ),

                    ])

                    ->columns(2),


                /*
                |--------------------------------------------------------------------------
                | Bank Information
                |--------------------------------------------------------------------------
                */

                Section::make('Bank Information')

                    ->description('Supplier banking information for payment transactions.')

                    ->icon('heroicon-o-building-library')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Bank Name
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('bank_name')

                            ->label('Bank Name')

                            ->maxLength(150)

                            ->placeholder('PT Bank Central Asia Tbk')

                            ->helperText(
                                'Primary bank used by the supplier.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Bank Account Number
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('bank_account_no')

                            ->label('Bank Account Number')

                            ->maxLength(100)

                            ->placeholder('1234567890')

                            ->helperText(
                                'Supplier bank account number.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Bank Account Name
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('bank_account_name')

                            ->label('Bank Account Name')

                            ->maxLength(200)

                            ->placeholder('PT Schlumberger Indonesia')

                            ->helperText(
                                'Registered account holder name.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | SWIFT Code
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('swift_code')

                            ->label('SWIFT / BIC Code')

                            ->maxLength(50)

                            ->placeholder('CENAIDJA')

                            ->helperText(
                                'Required for international bank transfers.'
                            ),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Procurement Information
                |--------------------------------------------------------------------------
                */

                Section::make('Procurement Information')

                    ->description('Purchasing configuration for this supplier.')

                    ->icon('heroicon-o-shopping-cart')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Lead Time
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('lead_time')

                            ->label('Lead Time (Days)')

                            ->numeric()

                            ->default(0)

                            ->minValue(0)

                            ->suffix('Days')

                            ->helperText(
                                'Estimated delivery lead time.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Vendor Rating
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make('vendor_rating')

                            ->label('Vendor Rating')

                            ->numeric()

                            ->default(0)

                            ->minValue(0)

                            ->maxValue(5)

                            ->step(0.01)

                            ->suffix('/ 5')

                            ->helperText(
                                'Internal supplier performance rating.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Purchase Permission
                        |--------------------------------------------------------------------------
                        */

                        Toggle::make('allow_purchase')

                            ->label('Allow Purchase')

                            ->default(true)

                            ->inline(false)

                            ->helperText(
                                'Allow this supplier to be selected in purchasing transactions.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Service Permission
                        |--------------------------------------------------------------------------
                        */

                        Toggle::make('allow_service')

                            ->label('Allow Service')

                            ->default(true)

                            ->inline(false)

                            ->helperText(
                                'Allow this supplier to provide service-based procurement.'
                            ),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Status Information
                |--------------------------------------------------------------------------
                */

                Section::make('Status Information')

                    ->description('Supplier operational status.')

                    ->icon('heroicon-o-shield-check')

                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Preferred Supplier
                        |--------------------------------------------------------------------------
                        */

                        Toggle::make('is_preferred')

                            ->label('Preferred Supplier')

                            ->default(false)

                            ->inline(false)

                            ->helperText(
                                'Mark this supplier as a preferred purchasing partner.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Blacklisted Supplier
                        |--------------------------------------------------------------------------
                        */

                        Toggle::make('is_blacklisted')

                            ->label('Blacklisted')

                            ->live()

                            ->inline(false)

                            ->helperText(
                                'Prevent this supplier from being used in future transactions.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Blacklist Date
                        |--------------------------------------------------------------------------
                        */

                        DatePicker::make('blacklist_date')

                            ->label('Blacklist Date')

                            ->visible(fn ($get) => $get('is_blacklisted'))

                            ->helperText(
                                'Date when the supplier was blacklisted.'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Active
                        |--------------------------------------------------------------------------
                        */

                        Toggle::make('is_active')

                            ->label('Active')

                            ->default(true)

                            ->inline(false)

                            ->helperText(
                                'Inactive suppliers cannot be selected in transactions.'
                            ),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Remarks
                |--------------------------------------------------------------------------
                */

                Section::make('Remarks')

                    ->description('Additional notes and internal remarks.')

                    ->icon('heroicon-o-document-text')

                    ->schema([

                        Textarea::make('remarks')

                            ->label('Internal Remarks')

                            ->rows(5)

                            ->columnSpanFull()

                            ->placeholder(
                                'Enter additional supplier information, purchasing notes, payment instructions, or internal remarks.'
                            )

                            ->helperText(
                                'This information is intended for internal use only and will not appear on customer-facing documents.'
                            )

                            ->maxLength(5000),

                    ])

                    ->columns(1),


            ]);
    }
}