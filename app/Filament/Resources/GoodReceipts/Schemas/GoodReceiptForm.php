<?php

namespace App\Filament\Resources\GoodReceipts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;

class GoodReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Good Receipt')
                    ->tabs([
                        // Tab 1: Receipt Information
                        Tabs\Tab::make('Receipt Info')
                            ->schema([
                                Fieldset::make('Purchase Order')
                                    ->schema([
                                        Select::make('purchase_order_id')
                                            ->label('Select Purchase Order')
                                            ->options(
                                                PurchaseOrder::whereIn('status', ['approved', 'partially_received'])
                                                    ->with('vendor')
                                                    ->get()
                                                    ->mapWithKeys(fn($po) => [
                                                        $po->id => $po->po_number . ' - ' . $po->vendor->name .
                                                            ' | Status: ' . strtoupper(str_replace('_', ' ', $po->status)) .
                                                            ' | Total: Rp ' . number_format($po->total, 0, ',', '.')
                                                    ])
                                            )
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                if ($state) {
                                                    $po = PurchaseOrder::with(['items.product', 'items.goodReceiptItems'])->find($state);
                                                    if ($po) {
                                                        // Auto-populate items from PO
                                                        $items = $po->items->map(function ($item) {
                                                            // Calculate already received quantity
                                                            $alreadyReceived = $item->goodReceiptItems()
                                                                ->whereHas('goodReceipt', function ($q) {
                                                                $q->where('status', 'confirmed');
                                                            })
                                                                ->sum('received_quantity');

                                                            $outstanding = $item->quantity - $alreadyReceived;

                                                            return [
                                                                'purchase_order_item_id' => $item->id,
                                                                'product_id' => $item->product_id,
                                                                'ordered_quantity' => $item->quantity,
                                                                'received_quantity' => max(0, $outstanding), // Default = outstanding
                                                                'rejected_quantity' => 0,
                                                                '_already_received' => $alreadyReceived,
                                                                '_outstanding' => $outstanding,
                                                            ];
                                                        })->toArray();
                                                        $set('items', $items);

                                                        // Set PO details for display
                                                        $set('_po_vendor', $po->vendor->name);
                                                        $set('_po_date', $po->order_date->format('d M Y'));
                                                        $set('_po_items_count', $po->items->count());
                                                        $set('_po_total', 'Rp ' . number_format($po->total, 0, ',', '.'));
                                                        $set('_po_status', strtoupper(str_replace('_', ' ', $po->status)));
                                                    }
                                                } else {
                                                    $set('items', []);
                                                    $set('_po_vendor', null);
                                                    $set('_po_date', null);
                                                    $set('_po_items_count', null);
                                                    $set('_po_total', null);
                                                    $set('_po_status', null);
                                                }
                                            })
                                            ->helperText('✅ PO dengan status APPROVED atau PARTIALLY RECEIVED')
                                            ->columnSpan(2),

                                        // Display PO Details
                                        Placeholder::make('_po_details')
                                            ->label('PO Details')
                                            ->content(
                                                fn($get) =>
                                                $get('_po_vendor')
                                                ? "📦 Supplier: {$get('_po_vendor')}\n📅 Order Date: {$get('_po_date')}\n📋 Items: {$get('_po_items_count')} item(s)\n💰 Total: {$get('_po_total')}\n📊 Status: {$get('_po_status')}"
                                                : '← Pilih Purchase Order terlebih dahulu'
                                            )
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Receipt Details')
                                    ->schema([
                                        Placeholder::make('receipt_number')
                                            ->label('Receipt Number')
                                            ->content(fn($record) => $record?->receipt_number ?? 'Auto-generated after save')
                                            ->helperText('📝 Generated automatically: GR-YYYYMM-XXXX')
                                            ->hidden(fn($context) => $context === 'create'),

                                        DatePicker::make('receipt_date')
                                            ->label('Receipt Date')
                                            ->required()
                                            ->default(now())
                                            ->native(false),

                                        Select::make('received_by')
                                            ->label('Received By')
                                            ->relationship('receivedBy', 'name')
                                            ->default(Auth::id())
                                            ->required()
                                            ->helperText('User who received the goods'),

                                        TextInput::make('delivery_note_number')
                                            ->label('No. Surat Jalan (Delivery Note)')
                                            ->placeholder('Contoh: SJ-VENDOR-12345')
                                            ->helperText('📄 Opsional: Nomor surat jalan dari vendor/supplier')
                                            ->maxLength(100)
                                            ->suffixIcon('heroicon-o-document-text')
                                            ->columnSpan(2),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'confirmed' => 'Confirmed',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default('draft')
                                            ->required(),

                                        Textarea::make('notes')
                                            ->label('Notes')
                                            ->rows(3)
                                            ->placeholder('Any notes about this receipt...')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        // Tab 2: Items
                        Tabs\Tab::make('Items Received')
                            ->schema([
                                Repeater::make('items')
                                    ->schema([
                                        // Hidden field for PO item ID - MUST BE FIRST!
                                        TextInput::make('purchase_order_item_id')
                                            ->hidden()
                                            ->dehydrated()
                                            ->required(),

                                        // Product Info - Full Width
                                        Select::make('product_id')
                                            ->label('Product')
                                            // Not using relationship here because repeater no longer auto-saves via relationship.
                                            // Provide static options and keep disabled; value comes from PO item mapping.
                                            ->options(\App\Models\Product::query()->pluck('name', 'id'))
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpanFull(),

                                        // Row 1: Quantities - 4 columns
                                        TextInput::make('ordered_quantity')
                                            ->label('Ordered Qty')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated()
                                            ->suffix(
                                                fn($get) =>
                                                PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit'
                                            )
                                            ->columnSpan(1),

                                        Placeholder::make('_already_received_display')
                                            ->label('Already Received')
                                            ->content(
                                                fn($get) =>
                                                ($get('_already_received') ?? 0) . ' ' .
                                                (PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit')
                                            )
                                            ->extraAttributes(['class' => 'text-warning-600 font-semibold'])
                                            ->columnSpan(1),

                                        Placeholder::make('_outstanding_display')
                                            ->label('Outstanding')
                                            ->content(
                                                fn($get) =>
                                                ($get('_outstanding') ?? 0) . ' ' .
                                                (PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit')
                                            )
                                            ->extraAttributes(['class' => 'text-info-600 font-semibold'])
                                            ->columnSpan(1),

                                        Placeholder::make('accepted_display')
                                            ->label('Accepted Now')
                                            ->content(
                                                fn($get) =>
                                                (($get('received_quantity') ?? 0) - ($get('rejected_quantity') ?? 0)) . ' ' .
                                                (PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit')
                                            )
                                            ->extraAttributes(['class' => 'text-success-600 font-bold'])
                                            ->columnSpan(1),

                                        // Row 2: Input quantities - 2 columns
                                        TextInput::make('received_quantity')
                                            ->label('Received Qty Now ✍️')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix(fn($get) => PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit')
                                            ->helperText(fn($get) => static::getConversionHelperText($get))
                                            ->live()
                                            ->columnSpan(1),

                                        TextInput::make('rejected_quantity')
                                            ->label('Rejected Qty ❌')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Damaged/rejected')
                                            ->suffix(
                                                fn($get) =>
                                                PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit'
                                            )
                                            ->columnSpan(1),

                                        // Row 3: Notes - Full Width
                                        Textarea::make('notes')
                                            ->label('Item Notes')
                                            ->rows(2)
                                            ->placeholder('Notes for this item...')
                                            ->columnSpanFull(),

                                        Textarea::make('rejection_reason')
                                            ->label('Rejection Reason')
                                            ->rows(2)
                                            ->placeholder('Why rejected? (quality issue, damage, etc.)')
                                            ->hidden(fn($get) => ($get('rejected_quantity') ?? 0) <= 0)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->cloneable(false)
                                    ->itemLabel(function (array $state): ?string {
                                        if (!$state['product_id'])
                                            return '🆕 New Item';
                                        $product = \App\Models\Product::find($state['product_id']);
                                        $ordered = $state['ordered_quantity'] ?? 0;
                                        $alreadyReceived = $state['_already_received'] ?? 0;
                                        $outstanding = $state['_outstanding'] ?? 0;
                                        $receivingNow = $state['received_quantity'] ?? 0;
                                        $accepted = $receivingNow - ($state['rejected_quantity'] ?? 0);

                                        return '📦 ' . $product?->name .
                                            ' • Ordered: ' . $ordered .
                                            ' • Outstanding: ' . $outstanding .
                                            ' • Receiving: ' . $receivingNow .
                                            ' • ✅ Accepted: ' . $accepted;
                                    })
                                    ->columnSpanFull()
                                    ->helperText('💡 Items auto-populated from PO. Click to expand each item. "Already Received" = confirmed from previous receipts. Enter quantities for this receipt.')
                                    ->live(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Get helper text for UoM conversion display
     */
    private static function getConversionHelperText(callable $get): string
    {
        $poi = PurchaseOrderItem::find($get('purchase_order_item_id'));
        $product = $poi?->product;
        $receivedQty = $get('received_quantity') ?? 0;

        if ($product && $product->conversion_purchase_to_stock && $receivedQty > 0) {
            $stockQty = $receivedQty * $product->conversion_purchase_to_stock;
            $formattedStockQty = static::formatNumber($stockQty);
            $formattedConversion = static::formatNumber($product->conversion_purchase_to_stock);
            return "📦 Will add {$formattedStockQty} {$product->uom_stock} to stock (1 {$product->uom_purchase} = {$formattedConversion} {$product->uom_stock})";
        }

        return 'Enter quantity received in this delivery';
    }

    /**
     * Format number by removing trailing zeros
     */
    private static function formatNumber(float $number): string
    {
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}

