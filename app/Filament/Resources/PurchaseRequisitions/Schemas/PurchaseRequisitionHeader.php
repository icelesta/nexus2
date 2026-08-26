<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;


use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use App\Support\Timezone\UserTimezone;

class PurchaseRequisitionHeader
{
    public static function configure(
        Schema $schema,
    ): Schema {

    return $schema
        ->components([

        /*
        |--------------------------------------------------------------------------
        | Document Information
        |--------------------------------------------------------------------------
        */

        Section::make('Document Information')
            ->description('General information of the Material Requisition document.')
            ->icon('heroicon-o-document-text')
            ->collapsible()
            ->columns(12)
            ->schema([

                TextInput::make('pr_number')
                    ->label('PR Number')
                    ->placeholder('Auto Generated')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(3),

                DatePicker::make('pr_date')
                    ->label('PR Date')
                    ->required()
                    ->native(false)
                    ->default(now())
                    ->columnSpan(3),

                DatePicker::make('required_date')
                    ->label('Required Date')
                    ->required()
                    ->native(false)
                    ->columnSpan(3),

                Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->default('medium')
                    ->native(false)
                    ->required()
                    ->columnSpan(3),

                TextInput::make('document_status')
                    ->label('Document Status')
                    ->default('Draft')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(3),

                TextInput::make('workflow_status')
                    ->label('Workflow Status')
                    ->default('Waiting Submission')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(3),

                TextInput::make('reference_number')
                    ->label('Reference Number')
                    ->maxLength(100)
                    ->placeholder('Quotation / WO / Reference')
                    ->columnSpan(3),

                TextInput::make('revision_no')
                    ->label('Revision')
                    ->default('0')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(3),

            ])
            ->columnSpanFull(),

        /*
        |--------------------------------------------------------------------------
        | Organization
        |--------------------------------------------------------------------------
        */

        Section::make('Organization')
            ->description('Company and organizational assignment.')
            ->icon('heroicon-o-building-office-2')
            ->collapsible()
            ->columns(12)
            ->schema([

                Select::make('company_id')
                    ->label('Company')
                    ->relationship(
                        'company',
                        'company_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

                Select::make('business_unit_id')
                    ->label('Business Unit')
                    ->relationship(
                        'businessUnit',
                        'business_unit_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

                Select::make('department_id')
                    ->label('Department')
                    ->relationship(
                        'department',
                        'department_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

                Select::make('section_id')
                    ->label('Section')
                    ->relationship(
                        'section',
                        'section_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(4),

                Select::make('cost_center_id')
                    ->label('Cost Center')
                    ->relationship(
                        'costCenter',
                        'cost_center_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship(
                        'warehouse',
                        'warehouse_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

            ])
            ->columnSpanFull(),

            
        /*
        |--------------------------------------------------------------------------
        | Purchasing
        |--------------------------------------------------------------------------
        */

        Section::make('Purchasing')
            ->description('Purchasing information and procurement settings.')
            ->icon('heroicon-o-shopping-cart')
            ->collapsible()
            ->columns(12)
            ->schema([

                Select::make('purchase_type')
                    ->label('Purchase Type')
                    ->options([
                        'local'      => 'Local Purchase',
                        'import'     => 'Import Purchase',
                        'service'    => 'Service',
                        'asset'      => 'Asset',
                        'project'    => 'Project',
                        'emergency'  => 'Emergency',
                    ])
                    ->required()
                    ->native(false)
                    ->default('local')
                    ->columnSpan(4),

                Select::make('supplier_id')
                    ->label('Preferred Supplier')
                    ->relationship(
                        'supplier',
                        'supplier_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->placeholder('Select supplier (optional)')
                    ->columnSpan(4),

                Select::make('currency_id')
                    ->label('Currency')
                    ->relationship(
                        'currency',
                        'currency_code',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

                TextInput::make('exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->suffix('Rate')
                    ->columnSpan(4),

                Select::make('payment_term_id')
                    ->label('Payment Term')
                    ->relationship(
                        'paymentTerm',
                        'payment_term_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(4),

                Select::make('incoterm_id')
                    ->label('Incoterm')
                    ->relationship(
                        'incoterm',
                        'incoterm_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(4),

            ])
            ->columnSpanFull(),

        /*
        |--------------------------------------------------------------------------
        | Request Information
        |--------------------------------------------------------------------------
        */

        Section::make('Request Information')
            ->description('Requester information and business references.')
            ->icon('heroicon-o-user')
            ->collapsible()
            ->columns(12)
            ->schema([

                Select::make('requested_by')
                    ->label('Requested By')
                    ->relationship(
                        'requestedBy',
                        'employee_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->columnSpan(4),

                Select::make('requested_for')
                    ->label('Requested For')
                    ->relationship(
                        'requestedFor',
                        'employee_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(4),

                Select::make('project_id')
                    ->label('Project')
                    ->relationship(
                        'project',
                        'project_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(4),

                TextInput::make('work_order_no')
                    ->label('Work Order')
                    ->maxLength(100)
                    ->placeholder('WO-000001')
                    ->columnSpan(4),

                TextInput::make('reference_document')
                    ->label('Reference Document')
                    ->maxLength(100)
                    ->placeholder('MR / WO / RFQ / Others')
                    ->columnSpan(4),

                Textarea::make('request_purpose')
                    ->label('Business Justification')
                    ->rows(3)
                    ->maxLength(1000)
                    ->placeholder('Describe the purpose of this purchase request...')
                    ->columnSpan(12),

            ])
            ->columnSpanFull(),

        /*
        |--------------------------------------------------------------------------
        | Financial
        |--------------------------------------------------------------------------
        */

        Section::make('Financial')
            ->description('Budget information and financial estimation.')
            ->icon('heroicon-o-banknotes')
            ->collapsible()
            ->columns(12)
            ->schema([

                Select::make('budget_year')
                    ->label('Budget Year')
                    ->options([
                        now()->year - 1 => (string) (now()->year - 1),
                        now()->year     => (string) now()->year,
                        now()->year + 1 => (string) (now()->year + 1),
                    ])
                    ->required()
                    ->native(false)
                    ->default(now()->year)
                    ->columnSpan(3),

                Select::make('budget_id')
                    ->label('Budget')
                    ->relationship(
                        'budget',
                        'budget_name',
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(5),

                Placeholder::make('budget_remaining')
                    ->label('Budget Remaining')
                    ->content('Calculated automatically')
                    ->columnSpan(4),

                Placeholder::make('estimated_amount')
                    ->label('Estimated Amount')
                    ->content('Calculated from item details')
                    ->columnSpan(4),

                Placeholder::make('tax_amount')
                    ->label('Estimated Tax')
                    ->content('Calculated automatically')
                    ->columnSpan(4),

                Placeholder::make('grand_total')
                    ->label('Estimated Grand Total')
                    ->content('Calculated automatically')
                    ->columnSpan(4),

            ])
            ->columnSpanFull(),

        /*
        |--------------------------------------------------------------------------
        | Notes
        |--------------------------------------------------------------------------
        */

        Section::make('Notes')
            ->description('Additional remarks and purchasing instructions.')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->collapsible()
            ->columns(12)
            ->schema([

                Textarea::make('internal_notes')
                    ->label('Internal Notes')
                    ->placeholder('Visible only to internal users...')
                    ->rows(4)
                    ->maxLength(2000)
                    ->columnSpan(6),

                Textarea::make('supplier_notes')
                    ->label('Supplier Notes')
                    ->placeholder('Notes that may be forwarded to the supplier...')
                    ->rows(4)
                    ->maxLength(2000)
                    ->columnSpan(6),

            ])
            ->columnSpanFull(),

        /*
        |--------------------------------------------------------------------------
        | Attachment
        |--------------------------------------------------------------------------
        */

        Section::make('Attachment')
            ->description('Supporting documents for this Material Requisition.')
            ->icon('heroicon-o-paper-clip')
            ->collapsible()
            ->columns(12)
            ->schema([

                FileUpload::make('attachments')
                    ->label('Supporting Documents')
                    ->multiple()
                    ->reorderable()
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->directory('purchase-requisitions')
                    ->visibility('public')
                    ->preserveFilenames()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxFiles(10)
                    ->maxSize(10240)
                    ->columnSpanFull(),

            ])
            ->columnSpanFull(),

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        Section::make('Audit Information')
            ->description('System generated audit information.')
            ->icon('heroicon-o-shield-check')
            ->collapsible()
            ->collapsed()
            ->columns(12)
            ->schema([

                Placeholder::make('created_by')
                    ->label('Created By')
                    ->content(fn ($record) => $record?->creator?->name ?? '-')
                    ->columnSpan(3),

                Placeholder::make('created_at')
                    ->label('Created At')
                    ->content(
                        fn ($record) => $record?->created_at
                            ? UserTimezone::format(
                                $record->created_at,
                                'd M Y H:i:s'
                            )
                            : '-'
                    )
                    ->columnSpan(3),

                Placeholder::make('updated_at')
                    ->label('Last Updated At')
                    ->content(
                        fn ($record) => $record?->updated_at
                            ? UserTimezone::format(
                                $record->updated_at,
                                'd M Y H:i:s'
                            )
                            : '-'
                    )
                    ->columnSpan(3),

            ])
            ->columnSpanFull(),

        ]);

    }
}