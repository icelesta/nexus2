<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

use App\Models\WarehouseType;
use App\Models\Warehouse;
use App\Models\BusinessUnit;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\CostCenter;
use App\Models\Department;
use App\Models\ExchangeRate;
use App\Models\FiscalYear;
use App\Models\MasterCategory;
use App\Models\PaymentTerm;
use App\Models\ProfitCenter;
use App\Models\Section;
use App\Models\TaxMaster;
use App\Models\TransactionNumbering;
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\JournalType;
use App\Models\Uom;
use App\Models\ItemCategory;
use App\Models\Brand;
use App\Models\Manufacturer;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\RequestForQuotation;
use App\Models\RequestForQuotationItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;

    /**
     * Items services.
     */
use App\Models\Item;
use App\Models\ItemAttachment;
use App\Models\ItemBarcode;
use App\Models\ItemBatch;
use App\Models\ItemImage;
use App\Models\ItemPrice;
use App\Models\ItemSerial;
use App\Models\ItemSpecification;
use App\Models\ItemStock;
use App\Models\ItemSupplier;
use App\Models\AssignmentMaterialRequisition;
use App\Models\ShippingAddress;



use App\Observers\WarehouseTypeObserver;
use App\Observers\WarehouseObserver;
use App\Observers\BusinessUnitObserver;
use App\Observers\CompanyObserver;
use App\Observers\BranchObserver;
use App\Observers\SupplierObserver;
use App\Observers\CustomerObserver;
use App\Observers\CurrencyObserver;
use App\Observers\BankAccountObserver;
use App\Observers\CostCenterObserver;
use App\Observers\DepartmentObserver;
use App\Observers\ExchangeRateObserver;
use App\Observers\FiscalYearObserver;
use App\Observers\MasterCategoryObserver;
use App\Observers\PaymentTermObserver;
use App\Observers\ProfitCenterObserver;
use App\Observers\SectionObserver;
use App\Observers\TaxMasterObserver;
use App\Observers\TransactionNumberingObserver;
use App\Observers\UserObserver;
use App\Observers\ChartOfAccountObserver;
use App\Observers\JournalTypeObserver;
use App\Observers\UomObserver;
use App\Observers\ItemCategoryObserver;
use App\Observers\BrandObserver;
use App\Observers\ManufacturerObserver;
use App\Observers\PurchaseRequisitionObserver;
use App\Observers\PurchaseRequisitionItemObserver;
use App\Observers\RequestForQuotationObserver;
use App\Observers\RequestForQuotationItemObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\PurchaseOrderItemObserver;
use App\Observers\GoodsReceiptObserver;
use App\Observers\GoodsReceiptItemObserver;
use App\Observers\AssignmentMaterialRequisitionObserver;
use App\Observers\ShippingAddressObserver;



    /**
     * Items services.
     */
use App\Observers\ItemObserver;
use App\Observers\ItemAttachmentObserver;
use App\Observers\ItemBarcodeObserver;
use App\Observers\ItemBatchObserver;
use App\Observers\ItemImageObserver;
use App\Observers\ItemPriceObserver;
use App\Observers\ItemSerialObserver;
use App\Observers\ItemSpecificationObserver;
use App\Observers\ItemStockObserver;
use App\Observers\ItemSupplierObserver;





class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Filament Shield - Generate Purchase Order permission identity
        |--------------------------------------------------------------------------
        |
        | GeneratePurchaseOrderResource intentionally uses the
        | AssignmentMaterialRequisition model as its data source. Shield's
        | default resource subject is "model", so without this override the
        | Generate Purchase Order resource collides with the
        | Assignment Material Requisition permission identity.
        |
        | Keep all other resources on their existing permission identities.
        |
        */
        FilamentShield::buildPermissionKeyUsing(
            function (
                string $entity,
                ?string $affix,
                string $subject,
                string $case,
                string $separator
            ): ?string {
                if (
                    $entity === \App\Filament\Resources\GeneratePurchaseOrderResource\GeneratePurchaseOrderResource::class
                ) {
                    $subject = 'GeneratePurchaseOrder';
                }

                return FilamentShield::defaultPermissionKeyBuilder(
                    affix: $affix,
                    separator: $separator,
                    subject: $subject,
                    case: $case,
                );
            }
        );

        Company::observe(CompanyObserver::class);

        Branch::observe(BranchObserver::class);

        BusinessUnit::observe(BusinessUnitObserver::class);

        Department::observe(DepartmentObserver::class);

        Section::observe(SectionObserver::class);

        CostCenter::observe(CostCenterObserver::class);

        ProfitCenter::observe(ProfitCenterObserver::class);

        Currency::observe(CurrencyObserver::class);

        ExchangeRate::observe(ExchangeRateObserver::class);

        FiscalYear::observe(FiscalYearObserver::class);

        ChartOfAccount::observe(ChartOfAccountObserver::class);

        BankAccount::observe(BankAccountObserver::class);

        PaymentTerm::observe(PaymentTermObserver::class);

        TaxMaster::observe(TaxMasterObserver::class);

        TransactionNumbering::observe(TransactionNumberingObserver::class);

        MasterCategory::observe(MasterCategoryObserver::class);

        Supplier::observe(SupplierObserver::class);

        Customer::observe(CustomerObserver::class);

        WarehouseType::observe(WarehouseTypeObserver::class);

        Warehouse::observe(WarehouseObserver::class);

        User::observe(UserObserver::class);

        JournalType::observe(JournalTypeObserver::class);

        Uom::observe(UomObserver::class);

        ItemCategory::observe(ItemCategoryObserver::class);

        Brand::observe(BrandObserver::class);

        Manufacturer::observe(ManufacturerObserver::class);


        /*
        |--------------------------------------------------------------------------
        | Inventory Observers
        |--------------------------------------------------------------------------
        */

        Item::observe(ItemObserver::class);

        ItemAttachment::observe(ItemAttachmentObserver::class);

        ItemBarcode::observe(ItemBarcodeObserver::class);

        ItemBatch::observe(ItemBatchObserver::class);

        ItemImage::observe(ItemImageObserver::class);

        ItemPrice::observe(ItemPriceObserver::class);

        ItemSerial::observe(ItemSerialObserver::class);

        ItemSpecification::observe(ItemSpecificationObserver::class);

        ItemStock::observe(ItemStockObserver::class);

        ItemSupplier::observe(ItemSupplierObserver::class);


        /*
        |--------------------------------------------------------------------------
        | Procurement
        |--------------------------------------------------------------------------
        */

        PurchaseRequisition::observe(PurchaseRequisitionObserver::class);
        PurchaseRequisitionItem::observe(PurchaseRequisitionItemObserver::class);

        RequestForQuotation::observe(RequestForQuotationObserver::class);
        RequestForQuotationItem::observe(RequestForQuotationItemObserver::class);

        PurchaseOrder::observe(PurchaseOrderObserver::class);
        PurchaseOrderItem::observe(PurchaseOrderItemObserver::class);

        GoodsReceipt::observe(GoodsReceiptObserver::class);
        GoodsReceiptItem::observe(GoodsReceiptItemObserver::class);

        AssignmentMaterialRequisition::observe(AssignmentMaterialRequisitionObserver::class);

        ShippingAddress::observe(ShippingAddressObserver::class);

    }
}