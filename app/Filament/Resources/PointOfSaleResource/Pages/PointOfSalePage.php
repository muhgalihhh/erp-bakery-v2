<?php

namespace App\Filament\Resources\PointOfSaleResource\Pages;

use App\Filament\Resources\PointOfSaleResource;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Services\DiscountEngine;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class PointOfSalePage extends Page
{
  protected static string $resource = PointOfSaleResource::class;

  protected static string $view = 'filament.resources.point-of-sale-resource.pages.point-of-sale-page';

  protected static ?string $title = 'Point of Sale';

  // Cart data
  public array $cart = [];
  public ?int $selectedCustomerId = null;
  public ?array $selectedCustomer = null;
  public string $searchProduct = '';
  public ?string $selectedCategory = null;

  // Calculations
  public float $subtotal = 0;
  public float $discountAmount = 0;
  public ?string $discountSource = null;
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

  /**
   * Livewire listeners for property updates
   */
  protected $listeners = ['refreshCart' => '$refresh'];

  /**
   * Mount the component
   */
  public function mount(): void
  {
    $this->resetCart();
  }

  /**
   * Watch for cart changes
   */
  public function updatedCart(): void
  {
    $this->calculateTotals();
  }

  /**
   * Watch for customer changes
   */
  public function updatedSelectedCustomerId(): void
  {
    if ($this->selectedCustomerId) {
      $this->selectCustomer($this->selectedCustomerId);
    }
  }

  /**
   * Watch for paid amount changes
   */
  public function updatedPaidAmount(): void
  {
    $this->calculateChange();
  }

  /**
   * Watch for payment method changes
   */
  public function updatedPaymentMethod(): void
  {
    // Reset paid amount when payment method changes
    $this->paidAmount = $this->total;
    $this->calculateChange();
  }

  /**
   * Add product to cart
   */
  public function addToCart(int $productId): void
  {
    $product = Product::find($productId);

    if (!$product) {
      Notification::make()
        ->title('Produk tidak ditemukan')
        ->danger()
        ->send();
      return;
    }

    if (!$product->is_sellable) {
      Notification::make()
        ->title('Produk tidak bisa dijual')
        ->warning()
        ->send();
      return;
    }

    if ($product->current_stock <= 0) {
      Notification::make()
        ->title('Stok habis')
        ->warning()
        ->send();
      return;
    }

    // Check if product already in cart
    $existingIndex = collect($this->cart)->search(function ($item) use ($productId) {
      return $item['product_id'] === $productId;
    });

    if ($existingIndex !== false) {
      // Increase quantity
      $this->cart[$existingIndex]['quantity']++;
    } else {
      // Add new item
      $this->cart[] = [
        'product_id' => $product->id,
        'name' => $product->name,
        'sku' => $product->sku,
        'price' => $product->selling_price,
        'cost' => $product->standard_cost ?? 0,
        'quantity' => 1,
        'uom' => $product->uom_stock,
        'stock' => $product->current_stock,
      ];
    }

    $this->calculateTotals();

    Notification::make()
      ->title('Ditambahkan ke keranjang')
      ->success()
      ->send();
  }

  /**
   * Update cart item quantity
   */
  public function updateQuantity(int $index, int $quantity): void
  {
    if ($quantity <= 0) {
      $this->removeFromCart($index);
      return;
    }

    if (isset($this->cart[$index])) {
      // Check stock
      if ($quantity > $this->cart[$index]['stock']) {
        Notification::make()
          ->title('Stok tidak cukup')
          ->warning()
          ->send();
        return;
      }

      $this->cart[$index]['quantity'] = $quantity;
      $this->calculateTotals();
    }
  }

  /**
   * Remove item from cart
   */
  public function removeFromCart(int $index): void
  {
    if (isset($this->cart[$index])) {
      unset($this->cart[$index]);
      $this->cart = array_values($this->cart); // Re-index
      $this->calculateTotals();

      Notification::make()
        ->title('Item dihapus dari keranjang')
        ->success()
        ->send();
    }
  }

  /**
   * Select customer
   */
  public function selectCustomer(?int $customerId): void
  {
    $this->selectedCustomerId = $customerId;

    if ($customerId) {
      $customer = Customer::with('tier')->find($customerId);
      $this->selectedCustomer = [
        'id' => $customer->id,
        'name' => $customer->name,
        'code' => $customer->customer_code,
        'tier' => $customer->tier?->name,
        'points' => $customer->total_points,
      ];
    } else {
      $this->selectedCustomer = null;
    }

    // Recalculate with customer-specific discounts
    $this->calculateTotals();
  }

  /**
   * Calculate totals and auto-apply discount
   */
  public function calculateTotals(): void
  {
    // Calculate subtotal
    $this->subtotal = collect($this->cart)->sum(function ($item) {
      return $item['price'] * $item['quantity'];
    });

    // Prepare cart data for discount engine
    $cartData = [
      'subtotal' => $this->subtotal,
      'items' => $this->cart,
      'customer_id' => $this->selectedCustomerId,
      'tax_percentage' => $this->taxPercentage,
    ];

    // Auto-apply best discount
    $discountEngine = app(DiscountEngine::class);
    $bestDiscount = $discountEngine->getBestDiscount($cartData);

    if ($bestDiscount) {
      $cartData = $discountEngine->applyDiscount($bestDiscount, $cartData);

      $this->discountAmount = $cartData['discount_amount'];
      $this->discountSource = $cartData['discount_source'];
      $this->discountName = $cartData['discount_name'];
    } else {
      $this->discountAmount = 0;
      $this->discountSource = null;
      $this->discountName = null;
    }

    // Calculate tax and total
    $afterDiscount = $this->subtotal - $this->discountAmount;
    $this->taxAmount = $afterDiscount * ($this->taxPercentage / 100);
    $this->total = $afterDiscount + $this->taxAmount;
  }

  /**
   * Calculate change amount
   */
  public function calculateChange(): void
  {
    $this->changeAmount = max(0, $this->paidAmount - $this->total);
  }

  /**
   * Process payment and create sales order
   */
  public function processPayment(): void
  {
    // Validate cart
    if (empty($this->cart)) {
      Notification::make()
        ->title('Keranjang kosong')
        ->body('Silakan tambahkan produk terlebih dahulu')
        ->warning()
        ->send();
      return;
    }

    // Validate payment amount
    if ($this->paidAmount < $this->total) {
      Notification::make()
        ->title('Jumlah bayar kurang')
        ->body('Jumlah bayar harus minimal Rp ' . number_format($this->total, 0, ',', '.'))
        ->warning()
        ->send();
      return;
    }

    try {
      DB::beginTransaction();

      // Create Sales Order
      $salesOrder = SalesOrder::create([
        'customer_id' => $this->selectedCustomerId,
        'order_date' => now(),
        'order_type' => 'pos',
        'order_channel' => 'store',
        'status' => SalesOrder::STATUS_CONFIRMED,
        'subtotal' => $this->subtotal,
        'discount_amount' => $this->discountAmount,
        'discount_source' => $this->discountSource,
        'tax_percentage' => $this->taxPercentage,
        'tax_amount' => $this->taxAmount,
        'total' => $this->total,
        'payment_status' => SalesOrder::PAYMENT_PAID,
        'payment_method' => $this->paymentMethod,
        'paid_amount' => $this->paidAmount,
        'change_amount' => $this->changeAmount,
        'served_by' => Auth::id(),
        'created_by' => Auth::id(),
      ]);

      // Create Sales Order Items
      foreach ($this->cart as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $itemTax = $itemTotal * ($this->taxPercentage / 100);

        SalesOrderItem::create([
          'sales_order_id' => $salesOrder->id,
          'product_id' => $item['product_id'],
          'quantity' => $item['quantity'],
          'uom' => $item['uom'],
          'unit_price' => $item['price'],
          'cost_price' => $item['cost'],
          'discount_percentage' => 0,
          'discount_amount' => 0,
          'tax_percentage' => $this->taxPercentage,
          'tax_amount' => $itemTax,
          'subtotal' => $itemTotal,
          'total' => $itemTotal + $itemTax,
          'profit' => ($item['price'] - $item['cost']) * $item['quantity'],
        ]);
      }

      // Complete the order (trigger observers for stock updates)
      $salesOrder->update(['status' => SalesOrder::STATUS_COMPLETED]);

      DB::commit();

      // Show receipt
      $this->lastOrderId = $salesOrder->id;
      $this->showReceipt = true;

      Notification::make()
        ->title('Transaksi berhasil! 🎉')
        ->body('Pesanan ' . $salesOrder->order_number . ' telah diproses')
        ->success()
        ->seconds(5)
        ->send();

    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('POS Transaction Error: ' . $e->getMessage(), [
        'cart' => $this->cart,
        'total' => $this->total,
        'trace' => $e->getTraceAsString()
      ]);

      Notification::make()
        ->title('Transaksi gagal! ❌')
        ->body('Error: ' . $e->getMessage())
        ->danger()
        ->seconds(10)
        ->send();
    }
  }

  /**
   * Reset cart
   */
  public function resetCart(): void
  {
    $this->cart = [];
    $this->selectedCustomerId = null;
    $this->selectedCustomer = null;
    $this->searchProduct = '';
    $this->subtotal = 0;
    $this->discountAmount = 0;
    $this->discountSource = null;
    $this->discountName = null;
    $this->taxAmount = 0;
    $this->total = 0;
    $this->paidAmount = 0;
    $this->changeAmount = 0;
    $this->showReceipt = false;
  }

  /**
   * Print receipt and reset
   */
  public function finishTransaction(): void
  {
    $this->resetCart();

    Notification::make()
      ->title('Siap untuk transaksi berikutnya')
      ->success()
      ->send();
  }
}
