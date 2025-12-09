<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Product;
use Carbon\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get vendors and products
        $bogasari = Vendor::where('vendor_code', 'SUP-001')->first();
        $mitraTelur = Vendor::where('vendor_code', 'SUP-002')->first();
        $frisianFlag = Vendor::where('vendor_code', 'SUP-003')->first();

        $tepung = Product::where('sku', 'TP-SEGITIGA-001')->first();
        $telur = Product::where('sku', 'TELUR-NEG-001')->first();
        $susu = Product::where('sku', 'SUSU-FF-UHT-001')->first();
        $mentega = Product::where('sku', 'MTG-ANCHOR-001')->first();
        $gula = Product::where('sku', 'GULA-PASIR-001')->first();

        if (!$bogasari || !$mitraTelur || !$frisianFlag) {
            $this->command->error('❌ Vendors not found! Please run VendorSeeder first.');
            return;
        }

        if (!$tepung) {
            $this->command->error('❌ Products not found! Please run ProductSeeder first.');
            return;
        }

        // PO #1 - Bogasari (Approved, ready for GR)
        $po1 = PurchaseOrder::create([
            'po_number' => 'PO/2025/12/001',
            'vendor_id' => $bogasari->id,
            'order_date' => Carbon::now()->subDays(3),
            'expected_delivery_date' => Carbon::now()->addDays(2),
            'status' => 'approved',
            'notes' => 'Order bulanan tepung terigu',
            'subtotal' => 0,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'shipping_cost' => 100000,
            'total' => 0,
        ]);

        // Items for PO #1
        $items1 = [
            [
                'product_id' => $tepung->id,
                'quantity' => 10, // 10 Sak
                'unit_price' => 500000,
                'discount_amount' => 0,
                'tax_percentage' => 11,
            ],
        ];

        $subtotal = 0;
        foreach ($items1 as $itemData) {
            $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
            $taxAmount = ($itemSubtotal - $itemData['discount_amount']) * ($itemData['tax_percentage'] / 100);
            $total = $itemSubtotal - $itemData['discount_amount'] + $taxAmount;

            PurchaseOrderItem::create([
                'purchase_order_id' => $po1->id,
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'discount_amount' => $itemData['discount_amount'],
                'tax_percentage' => $itemData['tax_percentage'],
                'tax_amount' => $taxAmount,
                'subtotal' => $itemSubtotal,
                'total' => $total,
            ]);

            $subtotal += $itemSubtotal;
        }

        $po1->update([
            'subtotal' => $subtotal,
            'tax_amount' => $subtotal * 0.11,
            'total' => $subtotal + ($subtotal * 0.11) + 100000,
        ]);

        // PO #2 - Mitra Telur (hanya jika ada produk telur)
        if ($telur) {
            $po2 = PurchaseOrder::create([
                'po_number' => 'PO/2025/12/002',
                'vendor_id' => $mitraTelur->id,
                'order_date' => Carbon::now()->subDays(2),
                'expected_delivery_date' => Carbon::now()->addDay(),
                'status' => 'approved',
                'notes' => 'Order telur ayam negeri fresh',
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'shipping_cost' => 50000,
                'total' => 0,
            ]);

            $items2 = [
                [
                    'product_id' => $telur->id,
                    'quantity' => 50, // 50 Kg
                    'unit_price' => 32000,
                    'discount_amount' => 50000,
                    'tax_percentage' => 11,
                ],
            ];

            $subtotal = 0;
            foreach ($items2 as $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                $taxAmount = ($itemSubtotal - $itemData['discount_amount']) * ($itemData['tax_percentage'] / 100);
                $total = $itemSubtotal - $itemData['discount_amount'] + $taxAmount;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po2->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_amount' => $itemData['discount_amount'],
                    'tax_percentage' => $itemData['tax_percentage'],
                    'tax_amount' => $taxAmount,
                    'subtotal' => $itemSubtotal,
                    'total' => $total,
                ]);

                $subtotal += $itemSubtotal;
            }

            $po2->update([
                'subtotal' => $subtotal,
                'tax_amount' => $subtotal * 0.11,
                'total' => $subtotal + ($subtotal * 0.11) + 50000,
            ]);
        }

        // PO #3 - Frisian Flag (Pending, belum approved) - hanya jika produk ada
        if ($susu && $mentega) {
            $po3 = PurchaseOrder::create([
                'po_number' => 'PO/2025/12/003',
                'vendor_id' => $frisianFlag->id,
                'order_date' => Carbon::now(),
                'expected_delivery_date' => Carbon::now()->addDays(5),
                'status' => 'pending',
                'notes' => 'Order bulanan susu dan produk dairy',
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'shipping_cost' => 75000,
                'total' => 0,
            ]);

            $items3 = [
                [
                    'product_id' => $susu->id,
                    'quantity' => 100, // 100 Liter
                    'unit_price' => 18000,
                    'discount_amount' => 100000,
                    'tax_percentage' => 11,
                ],
                [
                    'product_id' => $mentega->id,
                    'quantity' => 20, // 20 Kg
                    'unit_price' => 120000,
                    'discount_amount' => 0,
                    'tax_percentage' => 11,
                ],
            ];

            $subtotal = 0;
            foreach ($items3 as $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                $taxAmount = ($itemSubtotal - $itemData['discount_amount']) * ($itemData['tax_percentage'] / 100);
                $total = $itemSubtotal - $itemData['discount_amount'] + $taxAmount;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po3->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_amount' => $itemData['discount_amount'],
                    'tax_percentage' => $itemData['tax_percentage'],
                    'tax_amount' => $taxAmount,
                    'subtotal' => $itemSubtotal,
                    'total' => $total,
                ]);

                $subtotal += $itemSubtotal;
            }

            $po3->update([
                'subtotal' => $subtotal,
                'tax_amount' => $subtotal * 0.11,
                'total' => $subtotal + ($subtotal * 0.11) + 75000,
            ]);
        }

        $this->command->info('✅ Sample Purchase Orders created successfully!');
        $this->command->info('   - PO/2025/12/001: Bogasari (Approved) - Rp ' . number_format((float) $po1->total, 0));
        if (isset($po2)) {
            $this->command->info('   - PO/2025/12/002: Mitra Telur (Approved) - Rp ' . number_format((float) $po2->total, 0));
        }
        if (isset($po3)) {
            $this->command->info('   - PO/2025/12/003: Frisian Flag (Pending) - Rp ' . number_format((float) $po3->total, 0));
        }
    }
}
