<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Pages;

use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Models\DirectMarket;
use App\Models\DirectMarketItem;
use App\Services\Purchasing\DirectMarketService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditDirectMarket extends EditRecord
{
    protected static string $resource =
        DirectMarketResource::class;

	protected function getHeaderActions(): array
	{
	    return [

	        /*
	        |--------------------------------------------------------------------------
	        | SAVE CHANGES
	        |--------------------------------------------------------------------------
	        */

	        Action::make('save')
	            ->label('Save Changes')
	            ->icon('heroicon-o-check')
	            ->color('primary')
	            ->action('save')
	            ->visible(
	                fn (): bool =>
	                    $this->record->status ===
	                    DirectMarket::STATUS_DRAFT
	            ),

	        /*
	        |--------------------------------------------------------------------------
	        | SAVE & CLOSE
	        |--------------------------------------------------------------------------
	        */

	        Action::make('saveAndClose')
	            ->label('Save & Close')
	            ->icon('heroicon-o-check-badge')
	            ->color('success')
	            ->action(function (): void {

	                $this->save(
	                    shouldRedirect: false,
	                    shouldSendSavedNotification: true,
	                );

	                $this->redirect(
	                    static::getResource()::getUrl('index')
	                );
	            })
	            ->visible(
	                fn (): bool =>
	                    $this->record->status ===
	                    DirectMarket::STATUS_DRAFT
	            ),

	        /*
	        |--------------------------------------------------------------------------
	        | SUBMIT
	        |--------------------------------------------------------------------------
	        */

	        Action::make('submit')
	            ->label('Submit')
	            ->icon('heroicon-o-paper-airplane')
	            ->color('primary')
	            ->requiresConfirmation()
	            ->visible(
	                fn (): bool =>
	                    $this->record->status ===
	                    DirectMarket::STATUS_DRAFT
	                    &&
	                    $this->canSubmitDirectMarket()
	            )
	            ->authorize(
	                fn (): bool =>
	                    auth()->user()->can(
	                        'submit',
	                        $this->record
	                    )
	            )
	            ->action(
	                fn () =>
	                    $this->submitDirectMarket()
	            ),
	    ];
	}

    protected function getFormActions(): array
	{
	    return [];
	}

	protected function mutateFormDataBeforeFill(
	    array $data
	): array {

	    $data['items'] = $this->record
	        ->items()
	        ->orderBy('id')
	        ->get()
	        ->map(
	            fn ($item): array => [

	                'id' =>
	                    $item->id,

	                'item_id' =>
	                    $item->item_id,

	                'uom_id' =>
	                    $item->uom_id,

	                'qty' =>
	                    $item->qty,

	                'required_date' =>
	                    $item->required_date,

	                'delivery_location' =>
	                    $item->delivery_location,

	                'remarks' =>
	                    $item->remarks,

	            ]
	        )
	        ->values()
	        ->all();

	    return $data;
	}


	protected function handleRecordUpdate(
	    Model $record,
	    array $data
	): Model {

	    $items = $data['items'] ?? [];

	    unset($data['items']);

	    $data['updated_by'] = auth()->id();

	    $record->update($data);

	    $submittedItemIds = [];

	    foreach ($items as $itemData) {

	        $itemId = $itemData['id'] ?? null;

	        unset($itemData['id']);

	        $itemData = [
	            'item_id' =>
	                $itemData['item_id'] ?? null,

	            'uom_id' =>
	                $itemData['uom_id'] ?? null,

	            'qty' =>
	                $itemData['qty'] ?? 0,

				'unit_price' =>
				    $itemData['unit_price'] ?? 0,

	            'required_date' =>
	                $itemData['required_date'] ?? null,

	            'delivery_location' =>
	                $itemData['delivery_location'] ?? null,

	            'remarks' =>
	                $itemData['remarks'] ?? null,

	        ];

	        if ($itemId) {

	            $item = $record
	                ->items()
	                ->whereKey($itemId)
	                ->first();

	            if ($item) {

	                $item->update([
	                    ...$itemData,
	                    'updated_by' =>
	                        auth()->id(),
	                ]);

	                $submittedItemIds[] =
	                    $item->id;
	            }

	            continue;
	        }

			$item = $record
			    ->items()
			    ->create([
			        'uuid' =>
			            (string) Str::uuid(),

			        ...$itemData,

			        'status' =>
			            DirectMarketItem::STATUS_DRAFT,

			        'created_by' =>
			            auth()->id(),
			    ]);

			$submittedItemIds[] =
			    $item->id;
	    }

	    /*
	    |--------------------------------------------------------------------------
	    | DELETE REMOVED ITEMS
	    |--------------------------------------------------------------------------
	    */

	    $record
	        ->items()
	        ->when(
	            count($submittedItemIds) > 0,
	            fn ($query) =>
	                $query->whereNotIn(
	                    'id',
	                    $submittedItemIds
	                )
	        )
	        ->when(
	            count($submittedItemIds) === 0,
	            fn ($query) =>
	                $query
	        )
	        ->delete();

	    $record->refresh();

	    return $record;
	}


	protected function canSubmitDirectMarket(): bool
	{
	    /*
	    |--------------------------------------------------------------------------
	    | READ RAW FORM STATE
	    |--------------------------------------------------------------------------
	    |
	    | IMPORTANT:
	    | Do NOT use $this->form->getState() here.
	    |
	    | getState() may trigger schema validation and cause a
	    | ValidationException while the page is only rendering.
	    |
	    */

	    $data = $this->form->getRawState();

	    /*
	    |--------------------------------------------------------------------------
	    | HEADER
	    |--------------------------------------------------------------------------
	    */

		$requiredHeaderFields = [
		    'request_date',
		    'required_date',
		    'company_id',
		    'business_unit_id',
		    'branch_id',
		    'department_id',
		    'cost_center_id',
		    'requester_id',
		    'currency_id',
		];

	    foreach ($requiredHeaderFields as $field) {

	        if (
	            ! array_key_exists($field, $data) ||
	            blank($data[$field])
	        ) {
	            return false;
	        }
	    }

	    /*
	    |--------------------------------------------------------------------------
	    | ITEMS
	    |--------------------------------------------------------------------------
	    */

	    $items = $data['items'] ?? [];

	    if (
	        ! is_array($items) ||
	        count($items) < 1
	    ) {
	        return false;
	    }

	    /*
	    |--------------------------------------------------------------------------
	    | ITEM VALIDATION
	    |--------------------------------------------------------------------------
	    */

	    foreach ($items as $item) {

	        if (! is_array($item)) {
	            return false;
	        }

			if (
			    blank($item['item_id'] ?? null) ||
			    blank($item['uom_id'] ?? null) ||
			    blank($item['qty'] ?? null) ||
			    (float) ($item['qty'] ?? 0) <= 0 ||
			    blank($item['required_date'] ?? null)
			) {
			    return false;
			}
	    }

	    return true;
	}


	protected function submitDirectMarket(): void
	{
	    /*
	    |--------------------------------------------------------------------------
	    | FINAL SUBMIT VALIDATION
	    |--------------------------------------------------------------------------
	    */

	    if (! $this->canSubmitDirectMarket()) {

	        Notification::make()
	            ->danger()
	            ->title('Direct Market is incomplete.')
	            ->body(
	                'Please complete all required header fields and item information before submitting.'
	            )
	            ->send();

	        return;
	    }

	    /*
	    |--------------------------------------------------------------------------
	    | SUBMIT
	    |--------------------------------------------------------------------------
	    */

	    app(DirectMarketService::class)
	        ->submit($this->record->id);

	    $this->record->refresh();

	    Notification::make()
	        ->success()
	        ->title(
	            'Direct Market submitted successfully.'
	        )
	        ->send();

		$this->redirect(
		    static::getResource()::getUrl('index')
		);
	}
	
}
