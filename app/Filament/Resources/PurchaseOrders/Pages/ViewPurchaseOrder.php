<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Approve Action - untuk pending PO
            Action::make('approve')
                ->label('Approve PO')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Approve Purchase Order')
                ->modalDescription(
                    fn(PurchaseOrder $record) =>
                    "Apakah Anda yakin ingin menyetujui Purchase Order {$record->po_number}? Setelah di-approve, PO dapat dibuatkan Good Receipt."
                )
                ->modalSubmitActionLabel('Ya, Approve')
                ->modalCancelActionLabel('Batal')
                ->action(function (PurchaseOrder $record) {
                    $record->status = PurchaseOrder::STATUS_APPROVED;
                    $record->approved_by = Auth::id();
                    $record->approved_at = now();
                    $record->save();

                    Notification::make()
                        ->success()
                        ->title('PO Disetujui')
                        ->body("Purchase Order {$record->po_number} telah disetujui dan dapat diproses.")
                        ->send();

                    // Refresh page to show updated status
                })
                ->visible(
                    fn(PurchaseOrder $record): bool =>
                    $record->status === PurchaseOrder::STATUS_PENDING
                ),

            // Submit for Approval - untuk draft PO
            Action::make('submit')
                ->label('Ajukan Persetujuan')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Ajukan untuk Persetujuan')
                ->modalDescription(
                    fn(PurchaseOrder $record) =>
                    "Ajukan Purchase Order {$record->po_number} untuk persetujuan? Status akan berubah menjadi Pending."
                )
                ->modalSubmitActionLabel('Ya, Ajukan')
                ->modalCancelActionLabel('Batal')
                ->action(function (PurchaseOrder $record) {
                    $record->status = PurchaseOrder::STATUS_PENDING;
                    $record->save();

                    Notification::make()
                        ->success()
                        ->title('PO Diajukan')
                        ->body("Purchase Order {$record->po_number} telah diajukan untuk persetujuan.")
                        ->send();
                })
                ->visible(
                    fn(PurchaseOrder $record): bool =>
                    $record->status === PurchaseOrder::STATUS_DRAFT
                ),

            // Reject Action - untuk pending PO
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Purchase Order')
                ->modalDescription(
                    fn(PurchaseOrder $record) =>
                    "Apakah Anda yakin ingin menolak Purchase Order {$record->po_number}? Status akan kembali ke Draft."
                )
                ->modalSubmitActionLabel('Ya, Tolak')
                ->modalCancelActionLabel('Batal')
                ->action(function (PurchaseOrder $record) {
                    $record->status = PurchaseOrder::STATUS_DRAFT;
                    $record->approved_by = null;
                    $record->approved_at = null;
                    $record->save();

                    Notification::make()
                        ->warning()
                        ->title('PO Ditolak')
                        ->body("Purchase Order {$record->po_number} telah ditolak dan dikembalikan ke Draft.")
                        ->send();
                })
                ->visible(
                    fn(PurchaseOrder $record): bool =>
                    $record->status === PurchaseOrder::STATUS_PENDING
                ),

            // Cancel Action - untuk draft/pending PO
            Action::make('cancel')
                ->label('Batalkan PO')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Purchase Order')
                ->modalDescription(
                    fn(PurchaseOrder $record) =>
                    "Apakah Anda yakin ingin membatalkan Purchase Order {$record->po_number}?"
                )
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->modalCancelActionLabel('Kembali')
                ->action(function (PurchaseOrder $record) {
                    $record->status = PurchaseOrder::STATUS_CANCELLED;
                    $record->save();

                    Notification::make()
                        ->warning()
                        ->title('PO Dibatalkan')
                        ->body("Purchase Order {$record->po_number} telah dibatalkan.")
                        ->send();

                    return redirect()->route('filament.admin.resources.purchase-orders.index');
                })
                ->visible(
                    fn(PurchaseOrder $record): bool =>
                    in_array($record->status, [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_PENDING])
                ),

            // Edit - hanya untuk draft
            EditAction::make()
                ->visible(
                    fn(PurchaseOrder $record): bool =>
                    $record->status === PurchaseOrder::STATUS_DRAFT
                ),

            // Delete - hanya untuk draft
            DeleteAction::make()
                ->visible(
                    fn(PurchaseOrder $record): bool =>
                    $record->status === PurchaseOrder::STATUS_DRAFT
                ),
        ];
    }
}
