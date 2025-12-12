<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RecalculateProductCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:recalculate-cost {--type= : Product type to recalculate} {--all : Recalculate all products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate standard cost for products (from BOM for finished goods, from purchase price for others)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting product cost recalculation...');
        $this->newLine();

        // Build query
        $query = Product::query()->where('is_active', true);

        if ($this->option('type')) {
            $query->where('type', $this->option('type'));
        }

        if (!$this->option('all')) {
            // Only products with zero or null standard_cost
            $query->where(function ($q) {
                $q->whereNull('standard_cost')
                    ->orWhere('standard_cost', 0);
            });
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            $this->warn('No products found to recalculate.');
            return 0;
        }

        $this->info("Found {$products->count()} products to recalculate.");
        $this->newLine();

        $updated = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $oldCost = $product->standard_cost ?? 0;

            // Ensure standard cost
            $product->ensureStandardCost();
            $product->refresh();

            $newCost = $product->standard_cost ?? 0;

            if ($newCost > 0 && $newCost != $oldCost) {
                $updated++;
                $this->newLine();
                $this->line(sprintf(
                    "  ✓ %s - %s: Rp %s → Rp %s",
                    $product->sku,
                    $product->name,
                    number_format($oldCost, 0, ',', '.'),
                    number_format($newCost, 0, ',', '.')
                ));
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✅ Recalculation completed!');
        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            [
                ['Total products', $products->count()],
                ['Updated', $updated],
                ['Skipped (no change)', $skipped],
            ]
        );

        if ($updated > 0) {
            $this->newLine();
            $this->info('💡 Tip: Products with BOM will use calculated cost from BOM.');
            $this->info('💡 Raw materials and packaging use purchase_price as standard_cost.');
            $this->info('💡 Finished goods without BOM use 60% of selling_price as fallback.');
        }

        return 0;
    }
}
