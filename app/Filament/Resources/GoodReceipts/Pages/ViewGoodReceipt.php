<?php

namespace App\Filament\Resources\GoodReceipts\Pages;

use App\Filament\Resources\GoodReceipts\GoodReceiptResource;
use App\Models\GoodReceipt;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewGoodReceipt extends ViewRecord
{
    protected static string $resource = GoodReceiptResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Eager load items with product relationship
        $this->record->load('items.product');

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Confirm Action - untuk draft GR
            Action::make('confirm')
                ->label('Konfirmasi Penerimaan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Penerimaan Barang')
                ->modalDescription('Apakah Anda yakin ingin mengkonfirmasi penerimaan barang ini? Stock akan otomatis bertambah dan Purchase Order akan di-update.')
                ->modalSubmitActionLabel('Ya, Konfirmasi')
                ->modalCancelActionLabel('Batal')
                ->action(function (GoodReceipt $record) {
                    $record->confirm();

                    Notification::make()
                        ->success()
                        ->title('Penerimaan Dikonfirmasi')
                        ->body("Good Receipt {$record->receipt_number} berhasil dikonfirmasi. Stock sudah diperbarui.")
                        ->send();

                    // Redirect ke list atau refresh
                    return redirect()->route('filament.admin.resources.good-receipts.index');
                })
                ->visible(
                    fn(GoodReceipt $record): bool =>
                    $record->status === GoodReceipt::STATUS_DRAFT
                ),

            // Cancel Action - untuk draft GR
            Action::make('cancel')
                ->label('Batalkan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Penerimaan Barang')
                ->modalDescription('Apakah Anda yakin ingin membatalkan penerimaan barang ini?')
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->modalCancelActionLabel('Kembali')
                ->action(function (GoodReceipt $record) {
                    $record->status = GoodReceipt::STATUS_CANCELLED;
                    $record->save();

                    Notification::make()
                        ->warning()
                        ->title('Penerimaan Dibatalkan')
                        ->body("Good Receipt {$record->receipt_number} telah dibatalkan.")
                        ->send();

                    return redirect()->route('filament.admin.resources.good-receipts.index');
                })
                ->visible(
                    fn(GoodReceipt $record): bool =>
                    $record->status === GoodReceipt::STATUS_DRAFT
                ),

            // Edit - hanya untuk draft
            EditAction::make()
                ->visible(
                    fn(GoodReceipt $record): bool =>
                    $record->status === GoodReceipt::STATUS_DRAFT
                ),

            // Delete - hanya untuk draft
            DeleteAction::make()
                ->visible(
                    fn(GoodReceipt $record): bool =>
                    $record->status === GoodReceipt::STATUS_DRAFT
                ),
        ];
    }
}
