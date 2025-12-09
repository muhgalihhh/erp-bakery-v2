<?php

namespace App\Filament\Resources\VendorPayments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class VendorPaymentInfolist
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->columns(3)
      ->components([
        // Payment Header
        TextEntry::make('payment_number')
          ->label('Payment Number')
          ->weight(FontWeight::Bold)
          ->color('primary')
          ->copyable()
          ->copyMessage('Payment number copied!'),

        TextEntry::make('status')
          ->label('Status')
          ->badge()
          ->formatStateUsing(fn($state) => ucfirst($state))
          ->color(fn(string $state): string => match ($state) {
            'draft' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
          }),

        TextEntry::make('payment_date')
          ->label('Payment Date')
          ->date('d M Y'),

        // Vendor Info
        TextEntry::make('vendor.name')
          ->label('Vendor Name')
          ->weight(FontWeight::Bold)
          ->columnSpan(2),

        TextEntry::make('vendor.phone')
          ->label('Phone')
          ->placeholder('No phone')
          ->icon('heroicon-o-phone'),

        TextEntry::make('vendor.email')
          ->label('Email')
          ->placeholder('No email')
          ->icon('heroicon-o-envelope')
          ->columnSpan(2),

        TextEntry::make('vendor.address')
          ->label('Address')
          ->placeholder('No address')
          ->icon('heroicon-o-map-pin'),

        // Purchase Order Reference
        TextEntry::make('purchaseOrder.po_number')
          ->label('PO Reference')
          ->placeholder('Not linked to PO')
          ->color('info')
          ->weight(FontWeight::Bold),

        TextEntry::make('purchaseOrder.total_amount')
          ->label('PO Total Amount')
          ->placeholder('N/A')
          ->formatStateUsing(function ($state) {
            if (!$state)
              return 'N/A';
            $formatted = number_format($state, 2, '.', ',');
            $cleaned = rtrim(rtrim($formatted, '0'), '.');
            return 'Rp ' . $cleaned;
          })
          ->columnSpan(2),

        // Payment Details
        TextEntry::make('amount')
          ->label('Payment Amount')
          ->formatStateUsing(function ($state) {
            $formatted = number_format($state, 2, '.', ',');
            $cleaned = rtrim(rtrim($formatted, '0'), '.');
            return 'Rp ' . $cleaned;
          })
          ->weight(FontWeight::Bold)
          ->color('success')
          ->columnSpan(2),

        TextEntry::make('payment_method')
          ->label('Payment Method')
          ->badge()
          ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state)))
          ->color(fn(string $state): string => match ($state) {
            'cash' => 'success',
            'bank_transfer' => 'info',
            'check' => 'warning',
            'giro' => 'warning',
            'other' => 'gray',
            default => 'gray',
          }),

        TextEntry::make('paidBy.name')
          ->label('Paid By')
          ->icon('heroicon-o-user')
          ->columnSpan(2),

        TextEntry::make('reference_number')
          ->label('Reference Number')
          ->placeholder('No reference')
          ->copyable()
          ->copyMessage('Reference copied!'),

        TextEntry::make('bank_account')
          ->label('Bank Account')
          ->placeholder('N/A')
          ->copyable()
          ->copyMessage('Bank account copied!')
          ->columnSpan(2),

        TextEntry::make('notes')
          ->label('Notes')
          ->placeholder('No notes')
          ->columnSpanFull()
          ->markdown(),

        // Audit Info
        TextEntry::make('createdBy.name')
          ->label('Created By')
          ->icon('heroicon-o-user'),

        TextEntry::make('created_at')
          ->label('Created At')
          ->dateTime('d M Y H:i')
          ->columnSpan(2),

        TextEntry::make('updatedBy.name')
          ->label('Updated By')
          ->placeholder('Not updated')
          ->icon('heroicon-o-user'),

        TextEntry::make('updated_at')
          ->label('Updated At')
          ->dateTime('d M Y H:i')
          ->columnSpan(2),
      ]);
  }
}
