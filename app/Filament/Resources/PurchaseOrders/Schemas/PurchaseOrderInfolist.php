<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Header - PO Info
                TextEntry::make('po_number')
                    ->label('PO Number')
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->color('primary'),

                TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                        'partially_received' => 'info',
                        'received' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => strtoupper(str_replace('_', ' ', $state))),

                TextEntry::make('total')
                    ->label('Total Amount')
                    ->money('IDR')
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                // Vendor Info
                TextEntry::make('vendor.name')
                    ->label('Supplier')
                    ->weight(FontWeight::Bold)
                    ->columnSpan(2),

                TextEntry::make('vendor.vendor_code')
                    ->label('Supplier Code'),

                TextEntry::make('vendor.phone')
                    ->label('Phone')
                    ->placeholder('-'),

                TextEntry::make('vendor.email')
                    ->label('Email')
                    ->copyable()
                    ->placeholder('-'),

                TextEntry::make('vendor.contact_person')
                    ->label('Contact Person')
                    ->placeholder('-'),

                // Order Dates
                TextEntry::make('order_date')
                    ->label('Order Date')
                    ->date('d M Y'),

                TextEntry::make('expected_delivery_date')
                    ->label('Expected Delivery')
                    ->date('d M Y')
                    ->placeholder('-'),

                TextEntry::make('vendor.payment_terms_days')
                    ->label('Payment Terms')
                    ->formatStateUsing(fn($state) => $state ? "NET {$state} days" : '-'),

                // Financial Summary
                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR'),

                TextEntry::make('tax_amount')
                    ->label('Tax (11%)')
                    ->money('IDR'),

                TextEntry::make('shipping_cost')
                    ->label('Shipping Cost')
                    ->money('IDR')
                    ->visible(fn($record) => $record->shipping_cost > 0),

                TextEntry::make('discount_amount')
                    ->label('Discount')
                    ->money('IDR')
                    ->color('warning')
                    ->visible(fn($record) => $record->discount_amount > 0),

                // Approval
                TextEntry::make('approvedBy.name')
                    ->label('Approved By')
                    ->placeholder('Not approved yet')
                    ->visible(fn($record) => $record->approved_at || $record->approved_by),

                TextEntry::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->visible(fn($record) => $record->approved_at || $record->approved_by),

                // Notes
                TextEntry::make('notes')
                    ->label('Notes')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->visible(fn($record) => !empty($record->notes)),

                TextEntry::make('terms_and_conditions')
                    ->label('Terms & Conditions')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->visible(fn($record) => !empty($record->terms_and_conditions)),

                // Timestamps
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, H:i'),
            ]);
    }
}
