<?php

namespace App\Filament\Resources\DiscountRules\Pages;

use App\Filament\Resources\DiscountRules\DiscountRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDiscountRule extends EditRecord
{
    protected static string $resource = DiscountRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // ========== POPULATE USER-FRIENDLY FIELDS FROM CONDITIONS JSON ==========
        $conditions = $data['conditions'] ?? [];

        if (!empty($conditions['min_subtotal'])) {
            $data['condition_min_subtotal'] = $conditions['min_subtotal'];
        }

        if (!empty($conditions['max_subtotal'])) {
            $data['condition_max_subtotal'] = $conditions['max_subtotal'];
        }

        if (!empty($conditions['min_quantity'])) {
            $data['condition_min_quantity'] = $conditions['min_quantity'];
        }

        if (!empty($conditions['customer_tier_codes'])) {
            $data['condition_customer_tiers'] = $conditions['customer_tier_codes'];
        }

        if (!empty($conditions['required_product_ids'])) {
            $data['condition_required_products'] = $conditions['required_product_ids'];
        }

        if (!empty($conditions['day_of_week'])) {
            $data['condition_day_of_week'] = $conditions['day_of_week'];
        }

        if (!empty($conditions['is_birthday'])) {
            $data['condition_is_birthday'] = true;
        }

        if (!empty($conditions['is_first_purchase'])) {
            $data['condition_is_first_purchase'] = true;
        }

        // ========== POPULATE USER-FRIENDLY FIELDS FROM ACTIONS JSON ==========
        $actions = $data['actions'] ?? [];

        if (!empty($actions['discount_type'])) {
            $data['action_discount_type'] = $actions['discount_type'];
        }

        if (!empty($actions['discount_value'])) {
            $data['action_discount_value'] = $actions['discount_value'];
        }

        if (!empty($actions['max_discount_amount'])) {
            $data['action_max_discount'] = $actions['max_discount_amount'];
        }

        if (!empty($actions['apply_to'])) {
            $data['action_apply_to'] = $actions['apply_to'];
        }

        if (!empty($actions['free_product_id'])) {
            $data['action_free_product'] = $actions['free_product_id'];
        }

        if (!empty($actions['free_quantity'])) {
            $data['action_free_quantity'] = $actions['free_quantity'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set updated_by
        $data['updated_by'] = auth()->id();

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

        // Remove temporary form fields
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
