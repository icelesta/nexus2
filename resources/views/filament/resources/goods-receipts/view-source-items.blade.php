@livewire(
    'purchasing.goods-receipt-items-grid',
    [
        'goodsReceipt' => $record,
        'viewOnly' => true,
    ],
    key('goods-receipt-view-' . $record->getKey())
)