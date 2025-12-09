<?php

namespace App\Filament\Resources\GoodReceipts\Pages;

use App\Filament\Resources\GoodReceipts\GoodReceiptResource;
use App\Models\GoodReceiptItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditGoodReceipt extends EditRecord
{
    protected static string $resource = GoodReceiptResource::class;

    // Full width for better GR editing
    protected static string $formMaxWidth = 'full';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Eager load items with product relationship
        $this->record->load('items.product');

        // Load items into form data
        $data['items'] = $this->record->items->map(function ($item) {
            return [
                'purchase_order_item_id' => $item->purchase_order_item_id,
                'product_id' => $item->product_id,
                'ordered_quantity' => $item->ordered_quantity,
                'received_quantity' => $item->received_quantity,
                'rejected_quantity' => $item->rejected_quantity,
                'notes' => $item->notes,
                'rejection_reason' => $item->rejection_reason,
                // Calculate temporary fields
                '_already_received' => 0, // Will be recalculated
                '_outstanding' => 0, // Will be recalculated
            ];
        })->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove items from data - we'll handle them after save
        $this->cachedItems = $data['items'] ?? [];
        unset($data['items']);

        return $data;
    }

    protected array $cachedItems = [];

    protected function afterSave(): void
    {
        // Delete existing items and recreate
        $this->record->items()->delete();

        foreach ($this->cachedItems as $itemData) {
            // Clean up temporary fields (those starting with _)
            $cleanData = collect($itemData)
                ->filter(fn($value, $key) => !str_starts_with($key, '_'))
                ->toArray();

            // Ensure purchase_order_item_id exists; if not, resolve from PO + product
            if (empty($cleanData['purchase_order_item_id']) && !empty($cleanData['product_id']) && !empty($this->record->purchase_order_id)) {
                $poi = \App\Models\PurchaseOrderItem::where('purchase_order_id', $this->record->purchase_order_id)
                    ->where('product_id', $cleanData['product_id'])
                    ->first();
                if ($poi) {
                    $cleanData['purchase_order_item_id'] = $poi->id;
                }
            }

            if (empty($cleanData['purchase_order_item_id'])) {
                \Log::error('GR edit save: Missing purchase_order_item_id, skipped item', ['gr' => $this->record->id, 'item' => $cleanData]);
                continue;
            }

            // Create the item
            GoodReceiptItem::create([
                'good_receipt_id' => $this->record->id,
                'purchase_order_item_id' => $cleanData['purchase_order_item_id'],
                'product_id' => $cleanData['product_id'],
                'ordered_quantity' => $cleanData['ordered_quantity'],
                'received_quantity' => $cleanData['received_quantity'] ?? 0,
                'rejected_quantity' => $cleanData['rejected_quantity'] ?? 0,
                'notes' => $cleanData['notes'] ?? null,
                'rejection_reason' => $cleanData['rejection_reason'] ?? null,
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm')
                ->label('Confirm Receipt')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm Good Receipt?')
                ->modalDescription('This will update inventory stock and PO status. This action cannot be undone.')
                ->action(function () {
                    $this->record->confirm();

                    Notification::make()
                        ->success()
                        ->title('Receipt Confirmed')
                        ->body('Inventory stock has been updated.')
                        ->send();
                })
                ->visible(fn() => $this->record->status === 'draft'),

            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
