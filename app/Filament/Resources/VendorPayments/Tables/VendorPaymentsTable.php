<?php

namespace App\Filament\Resources\VendorPayments\Tables;

use Filament\Support\Enums\FontWeight;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use App\Models\VendorPayment;

class VendorPaymentsTable
{
  public static function make(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('payment_number')
          ->label('Payment No.')
          ->searchable()
          ->sortable()
          ->copyable()
          ->copyMessage('Payment number copied!')
          ->weight(FontWeight::Bold)
          ->color('primary'),

        TextColumn::make('vendor.name')
          ->label('Vendor')
          ->searchable()
          ->sortable()
          ->description(fn($record) => $record->vendor?->phone ?? 'No phone'),

        TextColumn::make('purchaseOrder.po_number')
          ->label('PO Reference')
          ->sortable()
          ->searchable()
          ->placeholder('Not linked to PO')
          ->color('info'),

        TextColumn::make('payment_date')
          ->label('Payment Date')
          ->date('d M Y')
          ->sortable()
          ->searchable(),

        TextColumn::make('amount')
          ->label('Amount')
          ->money('IDR')
          ->sortable()
          ->formatStateUsing(function ($state) {
            $formatted = number_format($state, 2, '.', ',');
            $cleaned = rtrim(rtrim($formatted, '0'), '.');
            return 'Rp ' . $cleaned;
          })
          ->weight(FontWeight::Bold),

        TextColumn::make('payment_method')
          ->label('Method')
          ->badge()
          ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state)))
          ->color(fn(string $state): string => match ($state) {
            VendorPayment::METHOD_CASH => 'success',
            VendorPayment::METHOD_BANK_TRANSFER => 'info',
            VendorPayment::METHOD_CHECK => 'warning',
            VendorPayment::METHOD_GIRO => 'warning',
            VendorPayment::METHOD_OTHER => 'gray',
            default => 'gray',
          }),

        TextColumn::make('reference_number')
          ->label('Reference')
          ->searchable()
          ->placeholder('No reference')
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('bank_account')
          ->label('Bank Account')
          ->searchable()
          ->placeholder('N/A')
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('paidBy.name')
          ->label('Paid By')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('status')
          ->label('Status')
          ->badge()
          ->formatStateUsing(fn($state) => ucfirst($state))
          ->color(fn(string $state): string => match ($state) {
            VendorPayment::STATUS_DRAFT => 'warning',
            VendorPayment::STATUS_CONFIRMED => 'success',
            VendorPayment::STATUS_CANCELLED => 'danger',
            default => 'gray',
          })
          ->sortable(),

        TextColumn::make('created_at')
          ->label('Created')
          ->dateTime('d M Y H:i')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('createdBy.name')
          ->label('Created By')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('payment_date', 'desc')
      ->filters([
        SelectFilter::make('status')
          ->label('Status')
          ->options([
            VendorPayment::STATUS_DRAFT => 'Draft',
            VendorPayment::STATUS_CONFIRMED => 'Confirmed',
            VendorPayment::STATUS_CANCELLED => 'Cancelled',
          ])
          ->multiple()
          ->searchable(),

        SelectFilter::make('payment_method')
          ->label('Payment Method')
          ->options([
            VendorPayment::METHOD_CASH => 'Cash',
            VendorPayment::METHOD_BANK_TRANSFER => 'Bank Transfer',
            VendorPayment::METHOD_CHECK => 'Check',
            VendorPayment::METHOD_GIRO => 'Giro',
            VendorPayment::METHOD_OTHER => 'Other',
          ])
          ->multiple()
          ->searchable(),

        SelectFilter::make('vendor_id')
          ->label('Vendor')
          ->relationship('vendor', 'name')
          ->searchable()
          ->preload(),

        Filter::make('payment_date')
          ->form([
            DatePicker::make('payment_from')
              ->label('Payment From')
              ->placeholder('Select start date'),
            DatePicker::make('payment_until')
              ->label('Payment Until')
              ->placeholder('Select end date'),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['payment_from'],
                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
              )
              ->when(
                $data['payment_until'],
                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
              );
          })
          ->indicateUsing(function (array $data): array {
            $indicators = [];

            if ($data['payment_from'] ?? null) {
              $indicators[] = 'Payment from ' . \Carbon\Carbon::parse($data['payment_from'])->format('d M Y');
            }

            if ($data['payment_until'] ?? null) {
              $indicators[] = 'Payment until ' . \Carbon\Carbon::parse($data['payment_until'])->format('d M Y');
            }

            return $indicators;
          }),
      ])
      ->actions([
        ViewAction::make()
          ->icon('heroicon-o-eye'),

        EditAction::make()
          ->icon('heroicon-o-pencil')
          ->visible(fn(VendorPayment $record) => $record->isDraft()),

        Action::make('confirm')
          ->label('Confirm')
          ->icon('heroicon-o-check-circle')
          ->color('success')
          ->requiresConfirmation()
          ->modalHeading('Confirm Payment')
          ->modalDescription(
            fn(VendorPayment $record) =>
            "Are you sure you want to confirm payment {$record->payment_number}? This action cannot be undone."
          )
          ->modalSubmitActionLabel('Yes, Confirm Payment')
          ->action(function (VendorPayment $record) {
            $record->confirm();
          })
          ->visible(fn(VendorPayment $record) => $record->isDraft())
          ->successNotification(
            \Filament\Notifications\Notification::make()
              ->success()
              ->title('Payment Confirmed')
              ->body('The payment has been confirmed successfully.')
          ),

        Action::make('cancel')
          ->label('Cancel')
          ->icon('heroicon-o-x-circle')
          ->color('danger')
          ->requiresConfirmation()
          ->modalHeading('Cancel Payment')
          ->modalDescription(
            fn(VendorPayment $record) =>
            "Are you sure you want to cancel payment {$record->payment_number}? This action cannot be undone."
          )
          ->modalSubmitActionLabel('Yes, Cancel Payment')
          ->action(function (VendorPayment $record) {
            $record->cancel();
          })
          ->visible(fn(VendorPayment $record) => $record->isConfirmed())
          ->successNotification(
            \Filament\Notifications\Notification::make()
              ->success()
              ->title('Payment Cancelled')
              ->body('The payment has been cancelled.')
          ),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make()
            ->requiresConfirmation(),
        ]),
      ])
      ->emptyStateHeading('No payments yet')
      ->emptyStateDescription('Create your first vendor payment to get started.')
      ->emptyStateIcon('heroicon-o-banknotes');
  }
}
