<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Services\DiscountEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PointOfSale extends Component
{
    // Cart & Customer
    public array $cart = [];
    public ?int $selectedCustomerId = null;
    public ?array $selectedCustomer = null;
    public string $searchProduct = '';

    // Calculations
    public float $subtotal = 0;
    public float $discountAmount = 0;
    public ?string $discountName = null;
    public float $taxPercentage = 11;
    public float $taxAmount = 0;
    public float $total = 0;

    // Payment
    public string $paymentMethod = 'cash';
    public float $paidAmount = 0;
    public float $changeAmount = 0;

    // Receipt
    public bool $showReceipt = false;
    public ?int $lastOrderId = null;

    public function render()
    {
        $products = Product::query()
            ->select('id', 'name', 'selling_price', 'purchase_price', 'current_stock', 'sku')
            ->where('is_sellable', true)
            ->where('is_active', true)
            ->when($this->searchProduct, fn($q) => $q->where('name', 'like', "%{$this->searchProduct}%"))
            ->orderBy('name')
            ->limit(50)
            ->get();

        $customers = Customer::query()->orderBy('name')->limit(50)->get();

        return view('livewire.pos.point-of-sale', compact('products', 'customers'));
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // Ambil harga selling_price (bukan sell_price)
        $price = $product->selling_price ?? 0;

        $existingIndex = collect($this->cart)->search(fn($item) => $item['product_id'] === $productId);
        if ($existingIndex !== false) {
            $this->cart[$existingIndex]['quantity']++;
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $price,
                'quantity' => 1,
                'uom' => $product->uom_stock ?? 'Pcs', // Tambahkan UOM
            ];
        }
        $this->calculateTotals();
    }    public function updateQuantity(int $index, int $quantity): void
    {
        if (!isset($this->cart[$index]))
            return;
        $this->cart[$index]['quantity'] = max(1, $quantity);
        $this->calculateTotals();
    }

    public function removeFromCart(int $index): void
    {
        if (!isset($this->cart[$index]))
            return;
        array_splice($this->cart, $index, 1);
        $this->calculateTotals();
    }

    public function selectCustomer(?int $customerId): void
    {
        $this->selectedCustomerId = $customerId;
        $this->selectedCustomer = $customerId ? Customer::find($customerId)?->toArray() : null;
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        // Discount via service (if available)
        try {
            $cartData = [
                'items' => $this->cart,
                'subtotal' => $this->subtotal,
                'customer_id' => $this->selectedCustomerId,
                'tax_percentage' => $this->taxPercentage,
            ];
            $engine = app(DiscountEngine::class);
            $best = $engine->getBestDiscount($cartData);
            if ($best) {
                $this->discountAmount = (float) $engine->calculateDiscountAmount($best, $cartData);
                $this->discountName = $best->name;
            } else {
                $this->discountAmount = 0;
                $this->discountName = null;
            }
        } catch (\Throwable $e) {
            $this->discountAmount = 0;
            $this->discountName = null;
        }
        $this->taxAmount = round(($this->subtotal - $this->discountAmount) * ($this->taxPercentage / 100), 2);
        $this->total = max(0, $this->subtotal - $this->discountAmount + $this->taxAmount);
    }

    public function processPayment(): void
    {
        if ($this->total <= 0 || empty($this->cart))
            return;

        DB::transaction(function () {
            $order = SalesOrder::create([
                'customer_id' => $this->selectedCustomerId,
                'order_date' => now(),
                'order_type' => 'pos',
                'order_channel' => 'store',
                'status' => SalesOrder::STATUS_CONFIRMED,
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'discount_source' => $this->discountName,
                'tax_percentage' => $this->taxPercentage,
                'tax_amount' => $this->taxAmount,
                'total' => $this->total,
                'payment_status' => SalesOrder::PAYMENT_PAID,
                'payment_method' => $this->paymentMethod,
                'served_by_user_id' => Auth::id(),
            ]);

            foreach ($this->cart as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'uom' => $item['uom'] ?? 'Pcs', // Tambahkan UOM
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            $this->lastOrderId = $order->id;
            $this->showReceipt = true;
        });

        $this->resetCart();
    }

    public function resetCart(): void
    {
        $this->cart = [];
        $this->subtotal = 0;
        $this->discountAmount = 0;
        $this->discountName = null;
        $this->taxAmount = 0;
        $this->total = 0;
        $this->paidAmount = 0;
        $this->changeAmount = 0;
    }
}
