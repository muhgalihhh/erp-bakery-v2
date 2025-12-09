<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return PurchaseOrderResource::table($table)
            ->recordActions([
                \Filament\Actions\ViewAction::make(),

                // Quick Approve Action
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Purchase Order')
                    ->modalDescription(
                        fn(PurchaseOrder $record) =>
                        "Setujui Purchase Order {$record->po_number}? Setelah disetujui, PO dapat dibuatkan Good Receipt."
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
                            ->body("Purchase Order {$record->po_number} telah disetujui.")
                            ->send();
                    })
                    ->visible(
                        fn(PurchaseOrder $record): bool =>
                        $record->status === PurchaseOrder::STATUS_PENDING
                    ),

                // Quick Submit Action
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan untuk Persetujuan')
                    ->modalDescription(
                        fn(PurchaseOrder $record) =>
                        "Ajukan Purchase Order {$record->po_number} untuk persetujuan?"
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

                \Filament\Actions\EditAction::make()
                    ->size('sm')
                    ->visible(
                        fn(PurchaseOrder $record): bool =>
                        $record->status === PurchaseOrder::STATUS_DRAFT
                    ),
            ]);
    }
}
