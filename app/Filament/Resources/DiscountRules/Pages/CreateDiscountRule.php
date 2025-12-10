<?php

namespace App\Filament\Resources\DiscountRules\Pages;

use App\Filament\Resources\DiscountRules\DiscountRuleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDiscountRule extends CreateRecord
{
    protected static string $resource = DiscountRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Disabled untuk sekarang - bisa diaktifkan jika membuat halaman guide terpisah
            // \Filament\Actions\Action::make('viewGuide')
            //     ->label('📖 Lihat Panduan Lengkap')
            //     ->icon('heroicon-o-book-open')
            //     ->color('info')
            //     ->url('/docs/DISCOUNT_RULES_GUIDE.md', shouldOpenInNewTab: true),
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('✅ Discount Rule Berhasil Dibuat!')
            ->body('Promosi sudah aktif dan siap digunakan di sales order.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate code if empty
        if (empty($data['code'])) {
            $lastDiscount = \App\Models\DiscountRule::latest('id')->first();
            $nextNumber = $lastDiscount ? (int) substr($lastDiscount->code, 5) + 1 : 1;
            $data['code'] = 'DISC-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // Set created_by
        $data['created_by'] = auth()->id();

        // ========== BUILD CONDITIONS JSON FROM USER-FRIENDLY FIELDS ==========
        $conditions = [];

        if (!empty($data['condition_min_subtotal'])) {
            $conditions['min_subtotal'] = (int) $data['condition_min_subtotal'];
        }

        if (!empty($data['condition_max_subtotal'])) {
            $conditions['max_subtotal'] = (int) $data['condition_max_subtotal'];
        }

        if (!empty($data['condition_min_quantity'])) {
            $conditions['min_quantity'] = (int) $data['condition_min_quantity'];
        }

        if (!empty($data['condition_customer_tiers'])) {
            $conditions['customer_tier_codes'] = $data['condition_customer_tiers'];
        }

        if (!empty($data['condition_required_products'])) {
            $conditions['required_product_ids'] = array_map('intval', $data['condition_required_products']);
        }

        if (!empty($data['condition_day_of_week'])) {
            $conditions['day_of_week'] = array_map('intval', $data['condition_day_of_week']);
        }

        if (!empty($data['condition_is_birthday'])) {
            $conditions['is_birthday'] = true;
        }

        if (!empty($data['condition_is_first_purchase'])) {
            $conditions['is_first_purchase'] = true;
        }

        $data['conditions'] = !empty($conditions) ? $conditions : null;

        // ========== BUILD ACTIONS JSON FROM USER-FRIENDLY FIELDS ==========
        $actions = [
            'discount_type' => $data['action_discount_type'] ?? 'percentage',
            'apply_to' => $data['action_apply_to'] ?? 'order',
        ];

        if ($data['action_discount_type'] === 'free_item') {
            if (!empty($data['action_free_product'])) {
                $actions['free_product_id'] = (int) $data['action_free_product'];
            }
            if (!empty($data['action_free_quantity'])) {
                $actions['free_quantity'] = (int) $data['action_free_quantity'];
            }
        } else {
            if (!empty($data['action_discount_value'])) {
                $actions['discount_value'] = (float) $data['action_discount_value'];
            }
            if (!empty($data['action_max_discount']) && $data['action_discount_type'] === 'percentage') {
                $actions['max_discount_amount'] = (int) $data['action_max_discount'];
            }
        }

        $data['actions'] = $actions;

        // Remove temporary form fields (tidak perlu disimpan ke database)
        unset(
            $data['condition_min_subtotal'],
            $data['condition_max_subtotal'],
            $data['condition_min_quantity'],
            $data['condition_customer_tiers'],
            $data['condition_required_products'],
            $data['condition_day_of_week'],
            $data['condition_is_birthday'],
            $data['condition_is_first_purchase'],
            $data['action_discount_type'],
            $data['action_discount_value'],
            $data['action_max_discount'],
            $data['action_apply_to'],
            $data['action_free_product'],
            $data['action_free_quantity']
        );

        return $data;
    }
}
