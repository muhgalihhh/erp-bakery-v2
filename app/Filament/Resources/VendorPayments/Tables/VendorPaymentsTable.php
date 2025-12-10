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
          ->label('No. Pembayaran')
          ->searchable()
          ->sortable()
          ->copyable()
          ->copyMessage('Nomor pembayaran disalin!')
          ->weight(FontWeight::Bold)
          ->color('primary'),

        TextColumn::make('vendor.name')
          ->label('Supplier')
          ->searchable()
          ->sortable()
          ->description(fn($record) => $record->vendor?->phone ?? 'Tidak ada telepon'),

        TextColumn::make('purchaseOrder.po_number')
          ->label('Ref. PO')
          ->sortable()
          ->searchable()
          ->placeholder('Tidak terkait PO')
          ->color('info'),

        TextColumn::make('payment_date')
          ->label('Tgl Bayar')
          ->date('d M Y')
          ->sortable()
          ->searchable(),

        TextColumn::make('amount')
          ->label('Jumlah')
          ->money('IDR')
          ->sortable()
          ->formatStateUsing(function ($state) {
            $formatted = number_format((float) $state, 2, '.', ',');
            $cleaned = rtrim(rtrim($formatted, '0'), '.');
            return 'Rp ' . $cleaned;
          })
          ->weight(FontWeight::Bold),

        TextColumn::make('payment_method')
          ->label('Metode')
          ->badge()
          ->formatStateUsing(fn($state) => match ($state) {
            'cash' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'check' => 'Cek',
            'giro' => 'Giro',
            'other' => 'Lainnya',
            default => ucwords(str_replace('_', ' ', $state)),
          })
          ->color(fn(string $state): string => match ($state) {
            VendorPayment::METHOD_CASH => 'success',
            VendorPayment::METHOD_BANK_TRANSFER => 'info',
            VendorPayment::METHOD_CHECK => 'warning',
            VendorPayment::METHOD_GIRO => 'warning',
            VendorPayment::METHOD_OTHER => 'gray',
            default => 'gray',
          }),

        TextColumn::make('reference_number')
          ->label('No. Referensi')
          ->searchable()
          ->placeholder('Tidak ada referensi')
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('bank_account')
          ->label('Rekening Bank')
          ->searchable()
          ->placeholder('N/A')
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('paidBy.name')
          ->label('Dibayar Oleh')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('status')
          ->label('Status')
          ->badge()
          ->formatStateUsing(fn($state) => match ($state) {
            'draft' => 'Draft',
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($state),
          })
          ->color(fn(string $state): string => match ($state) {
            VendorPayment::STATUS_DRAFT => 'warning',
            VendorPayment::STATUS_CONFIRMED => 'success',
            VendorPayment::STATUS_CANCELLED => 'danger',
            default => 'gray',
          })
          ->sortable(),

        TextColumn::make('created_at')
          ->label('Dibuat')
          ->dateTime('d M Y H:i')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('createdBy.name')
          ->label('Dibuat Oleh')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('payment_date', 'desc')
      ->filters([
        SelectFilter::make('status')
          ->label('Status')
          ->options([
            VendorPayment::STATUS_DRAFT => 'Draft',
            VendorPayment::STATUS_CONFIRMED => 'Dikonfirmasi',
            VendorPayment::STATUS_CANCELLED => 'Dibatalkan',
          ])
          ->multiple()
          ->searchable(),

        SelectFilter::make('payment_method')
          ->label('Metode Pembayaran')
          ->options([
            VendorPayment::METHOD_CASH => 'Tunai',
            VendorPayment::METHOD_BANK_TRANSFER => 'Transfer Bank',
            VendorPayment::METHOD_CHECK => 'Cek',
            VendorPayment::METHOD_GIRO => 'Giro',
            VendorPayment::METHOD_OTHER => 'Lainnya',
          ])
          ->multiple()
          ->searchable(),

        SelectFilter::make('vendor_id')
          ->label('Supplier')
          ->relationship('vendor', 'name')
          ->searchable()
          ->preload(),

        Filter::make('payment_date')
          ->form([
            DatePicker::make('payment_from')
              ->label('Dari Tanggal')
              ->placeholder('Pilih tanggal mulai'),
            DatePicker::make('payment_until')
              ->label('Sampai Tanggal')
              ->placeholder('Pilih tanggal akhir'),
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
              $indicators[] = 'Pembayaran dari ' . \Carbon\Carbon::parse($data['payment_from'])->format('d M Y');
            }

            if ($data['payment_until'] ?? null) {
              $indicators[] = 'Pembayaran sampai ' . \Carbon\Carbon::parse($data['payment_until'])->format('d M Y');
            }

            return $indicators;
          }),
      ])
      ->actions([
        ViewAction::make()
          ->label('Lihat')
          ->icon('heroicon-o-eye'),

        EditAction::make()
          ->label('Ubah')
          ->icon('heroicon-o-pencil')
          ->visible(fn(VendorPayment $record) => $record->isDraft()),

        Action::make('confirm')
          ->label('Konfirmasi')
          ->icon('heroicon-o-check-circle')
          ->color('success')
          ->requiresConfirmation()
          ->modalHeading('Konfirmasi Pembayaran')
          ->modalDescription(
            fn(VendorPayment $record) =>
            "Yakin ingin konfirmasi pembayaran {$record->payment_number}? Tindakan ini tidak bisa dibatalkan."
          )
          ->modalSubmitActionLabel('Ya, Konfirmasi Pembayaran')
          ->action(function (VendorPayment $record) {
            $record->confirm();
          })
          ->visible(fn(VendorPayment $record) => $record->isDraft())
          ->successNotification(
            \Filament\Notifications\Notification::make()
              ->success()
              ->title('Pembayaran Dikonfirmasi')
              ->body('Pembayaran berhasil dikonfirmasi.')
          ),

        Action::make('cancel')
          ->label('Batalkan')
          ->icon('heroicon-o-x-circle')
          ->color('danger')
          ->requiresConfirmation()
          ->modalHeading('Batalkan Pembayaran')
          ->modalDescription(
            fn(VendorPayment $record) =>
            "Yakin ingin batalkan pembayaran {$record->payment_number}? Tindakan ini tidak bisa dibatalkan."
          )
          ->modalSubmitActionLabel('Ya, Batalkan Pembayaran')
          ->action(function (VendorPayment $record) {
            $record->cancel();
          })
          ->visible(fn(VendorPayment $record) => $record->isConfirmed())
          ->successNotification(
            \Filament\Notifications\Notification::make()
              ->success()
              ->title('Pembayaran Dibatalkan')
              ->body('Pembayaran berhasil dibatalkan.')
          ),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make()
            ->label('Hapus')
            ->requiresConfirmation(),
        ]),
      ])
      ->emptyStateHeading('Belum ada pembayaran')
      ->emptyStateDescription('Buat pembayaran supplier pertama untuk memulai.')
      ->emptyStateIcon('heroicon-o-banknotes');
  }
}
