<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\Actions\ApprovePurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\Actions\RejectPurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Filament\Resources\PurchaseRequisitions\Schemas\PurchaseRequisitionApproval;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPurchaseRequisition extends ViewRecord
{
    protected static string $resource =
        PurchaseRequisitionResource::class;

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return 'Material Requisition';
    }

    /*
    |--------------------------------------------------------------------------
    | Heading
    |--------------------------------------------------------------------------
    */

    public function getHeading(): string
    {
        return 'Material Requisition';
    }

    /*
    |--------------------------------------------------------------------------
    | Subheading
    |--------------------------------------------------------------------------
    */

    public function getSubheading(): ?string
    {
        return $this->record->pr_no;
    }

    /*
    |--------------------------------------------------------------------------
    | Breadcrumb
    |--------------------------------------------------------------------------
    */

    public function getBreadcrumb(): string
    {
        return 'View';
    }

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Document Status
            |--------------------------------------------------------------------------
            */

            Action::make('status')
                ->label(
                    strtoupper(
                        (string) $this->record->status
                    )
                )
                ->color(
                    match (
                        $this->record->status
                    ) {

                        'Draft' =>
                            'warning',

                        'Submitted' =>
                            'info',

                        'Pending Approval' =>
                            'warning',

                        'Approved' =>
                            'success',

                        'Rejected' =>
                            'danger',

                        'Cancelled',
                        'Closed' =>
                            'gray',

                        default =>
                            'gray',
                    }
                )
                ->disabled(),

            /*
            |--------------------------------------------------------------------------
            | Reject
            |--------------------------------------------------------------------------
            */

            RejectPurchaseRequisition::make(),

            /*
            |--------------------------------------------------------------------------
            | Approve
            |--------------------------------------------------------------------------
            */

            ApprovePurchaseRequisition::make(),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Approval Infolist
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We pass the existing Schema directly into configure().
    |
    | DO NOT do:
    |
    | $schema->components([
    |     PurchaseRequisitionApproval::configure($schema)
    | ]);
    |
    | That creates recursive Schema construction.
    |
    */

    public function infolist(
        Schema $schema
    ): Schema {

        return PurchaseRequisitionApproval::configure(
            $schema,
            $this->record,
        );
    }
}