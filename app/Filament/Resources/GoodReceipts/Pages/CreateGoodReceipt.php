<?php

namespace App\Filament\Resources\GoodReceipts\Pages;

use App\Filament\Resources\GoodReceipts\GoodReceiptResource;
use App\Models\GoodReceiptItem;
use Filament\Resources\Pages\CreateRecord;

class CreateGoodReceipt extends CreateRecord
{
    protected static string $resource = GoodReceiptResource::class;

    // Full width for better GR creation
    protected static string $formMaxWidth = 'full';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove items from data - we'll handle them after create
        $this->cachedItems = $data['items'] ?? [];
        unset($data['items']);

        return $data;
    }

    protected array $cachedItems = [];

    protected function afterCreate(): void
    {
        // Save items manually after the Good Receipt is created
        $goodReceipt = $this->record;

        foreach ($this->cachedItems as $itemData) {
            // Clean up temporary fields (those starting with _)
            $cleanData = collect($itemData)
                ->filter(fn($value, $key) => !str_starts_with($key, '_'))
                ->toArray();

            // Ensure purchase_order_item_id exists; if not, try to resolve from PO + product
            if (empty($cleanData['purchase_order_item_id']) && !empty($cleanData['product_id']) && !empty($goodReceipt->purchase_order_id)) {
                $poi = \App\Models\PurchaseOrderItem::where('purchase_order_id', $goodReceipt->purchase_order_id)
                    ->where('product_id', $cleanData['product_id'])
                    ->first();
                if ($poi) {
                    $cleanData['purchase_order_item_id'] = $poi->id;
                }
            }

            // Final guard: skip item if still missing required linkage
            if (empty($cleanData['purchase_order_item_id'])) {
                // Log and skip rather than crashing
                \Log::error('GR save: Missing purchase_order_item_id, skipped item', ['gr' => $goodReceipt->id, 'item' => $cleanData]);
                continue;
            }

            // Create the item
            GoodReceiptItem::create([
                'good_receipt_id' => $goodReceipt->id,
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
}
