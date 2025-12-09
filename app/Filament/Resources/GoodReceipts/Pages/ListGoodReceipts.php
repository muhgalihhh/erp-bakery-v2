<?php

namespace App\Filament\Resources\GoodReceipts\Pages;

use App\Filament\Resources\GoodReceipts\GoodReceiptResource;
use App\Models\GoodReceipt;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListGoodReceipts extends ListRecords
{
    protected static string $resource = GoodReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return GoodReceiptResource::table($table)
            ->recordActions([
                \Filament\Actions\ViewAction::make(),

                // Quick Confirm Action
                Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penerimaan Barang')
                    ->modalDescription(
                        fn(GoodReceipt $record) =>
                        "Konfirmasi penerimaan {$record->receipt_number}? Stock akan otomatis bertambah dan Purchase Order akan di-update."
                    )
                    ->modalSubmitActionLabel('Ya, Konfirmasi')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (GoodReceipt $record) {
                        $record->confirm();

                        Notification::make()
                            ->success()
                            ->title('Penerimaan Dikonfirmasi')
                            ->body("Good Receipt {$record->receipt_number} berhasil dikonfirmasi. Stock telah diperbarui.")
                            ->send();
                    })
                    ->visible(
                        fn(GoodReceipt $record): bool =>
                        $record->status === GoodReceipt::STATUS_DRAFT
                    ),

                \Filament\Actions\EditAction::make()
                    ->size('sm')
                    ->visible(
                        fn(GoodReceipt $record): bool =>
                        $record->status === GoodReceipt::STATUS_DRAFT
                    ),
            ]);
    }
}
