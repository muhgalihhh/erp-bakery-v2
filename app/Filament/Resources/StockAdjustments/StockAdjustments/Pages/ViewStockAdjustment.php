<?php

namespace App\Filament\Resources\StockAdjustments\StockAdjustments\Pages;

use App\Filament\Resources\StockAdjustments\StockAdjustments\StockAdjustmentResource;
use App\Models\StockAdjustment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewStockAdjustment extends ViewRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Ubah')
                ->icon('heroicon-o-pencil')
                ->visible(fn(StockAdjustment $record) => $record->isDraft()),

            Action::make('approve')
                ->label('Setujui Adjustment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Penyesuaian Stok?')
                ->modalDescription(function (StockAdjustment $record) {
                    $diff = (float) $record->difference_quantity;
                    $action = $diff > 0 ? 'menambah' : 'mengurangi';
                    $absQty = number_format(abs($diff), 2);
                    return "Stok produk '{$record->product->name}' akan {$action} sebanyak {$absQty} {$record->uom}. Pastikan data sudah benar!";
                })
                ->modalSubmitActionLabel('Ya, Setujui')
                ->action(function (StockAdjustment $record) {
                    try {
                        $record->approve();

                        Notification::make()
                            ->title('Adjustment Berhasil Disetujui')
                            ->body("Stok produk '{$record->product->name}' telah diupdate.")
                            ->success()
                            ->send();

                        return redirect()->to(StockAdjustmentResource::getUrl('view', ['record' => $record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Menyetujui Adjustment')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn(StockAdjustment $record) => $record->isDraft()),

            Action::make('cancel')
                ->label('Batalkan Adjustment')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Penyesuaian Stok?')
                ->modalDescription('Adjustment ini akan dibatalkan dan tidak bisa digunakan lagi.')
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->action(function (StockAdjustment $record) {
                    try {
                        $record->cancel();

                        Notification::make()
                            ->title('Adjustment Dibatalkan')
                            ->body("Adjustment {$record->adjustment_number} telah dibatalkan.")
                            ->success()
                            ->send();

                        return redirect()->to(StockAdjustmentResource::getUrl('view', ['record' => $record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Membatalkan Adjustment')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn(StockAdjustment $record) => $record->isDraft()),
        ];
    }
}
