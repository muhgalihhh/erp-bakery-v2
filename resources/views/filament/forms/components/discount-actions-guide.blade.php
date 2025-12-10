<div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 p-4">
    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">🎯 Panduan Lengkap Format JSON Aksi Diskon</h3>

    <div class="space-y-4 text-xs text-gray-700 dark:text-gray-300">

        <!-- Contoh 1: Percentage Discount -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-blue-600 dark:text-blue-400 mb-2">💯 Contoh 1: Diskon Persentase (20% Off)</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "percentage",
  "discount_value": 20,
  "max_discount_amount": 100000,
  "apply_to": "order"
}</code></pre>
            <p class="mt-2 italic">➜ Diskon 20% untuk total order, maksimal potongan Rp 100.000</p>
        </div>

        <!-- Contoh 2: Fixed Amount Discount -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-green-600 dark:text-green-400 mb-2">💵 Contoh 2: Diskon Nominal Tetap (Rp 50.000
                Off)</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "fixed",
  "discount_value": 50000,
  "apply_to": "order"
}</code></pre>
            <p class="mt-2 italic">➜ Potongan langsung Rp 50.000 dari total</p>
        </div>

        <!-- Contoh 3: Free Item -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-purple-600 dark:text-purple-400 mb-2">🎁 Contoh 3: Gratis Produk (Free Item)
            </p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "free_item",
  "free_product_id": 5,
  "free_quantity": 1
}</code></pre>
            <p class="mt-2 italic">➜ Gratis 1 produk ID 5 (misal: kopi gratis)</p>
        </div>

        <!-- Contoh 4: Buy X Get Y (Cheapest Free) -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-orange-600 dark:text-orange-400 mb-2">🛒 Contoh 4: Buy 2 Get 1 Free (Termurah
                Gratis)</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "free_item",
  "free_quantity": 1,
  "apply_to": "cheapest"
}</code></pre>
            <p class="mt-2 italic">➜ Beli 2, gratis 1 item termurah</p>
        </div>

        <!-- Contoh 5: Percentage on Category -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-red-600 dark:text-red-400 mb-2">🏷️ Contoh 5: Diskon untuk Kategori Tertentu
            </p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "percentage",
  "discount_value": 15,
  "apply_to": "category",
  "target_category_ids": [2, 3]
}</code></pre>
            <p class="mt-2 italic">➜ Diskon 15% khusus untuk kategori ID 2 dan 3</p>
        </div>

        <!-- Contoh 6: Percentage on Products -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-pink-600 dark:text-pink-400 mb-2">🎯 Contoh 6: Diskon untuk Produk Tertentu</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "percentage",
  "discount_value": 25,
  "apply_to": "products",
  "target_product_ids": [1, 5, 10]
}</code></pre>
            <p class="mt-2 italic">➜ Diskon 25% untuk produk ID 1, 5, dan 10</p>
        </div>

        <!-- Contoh 7: Free Shipping -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-teal-600 dark:text-teal-400 mb-2">🚚 Contoh 7: Gratis Ongkir</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "free_shipping",
  "max_shipping_discount": 25000
}</code></pre>
            <p class="mt-2 italic">➜ Gratis ongkir, maksimal Rp 25.000</p>
        </div>

        <!-- Contoh Kompleks -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded p-3 border border-yellow-300 dark:border-yellow-700">
            <p class="font-semibold text-yellow-800 dark:text-yellow-400 mb-2">🔥 Contoh Kombinasi: Diskon + Gratis Item
            </p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "discount_type": "percentage",
  "discount_value": 10,
  "max_discount_amount": 50000,
  "apply_to": "order",
  "bonus_free_item": {
    "product_id": 8,
    "quantity": 1
  }
}</code></pre>
            <p class="mt-2 italic">➜ Diskon 10% (max Rp 50rb) + bonus gratis produk ID 8</p>
        </div>

    </div>

    <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3">
        <p class="font-semibold text-blue-900 dark:text-blue-100 text-xs mb-2">📋 Daftar Field yang Tersedia:</p>
        <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">discount_type</code> - Jenis diskon:
                "percentage", "fixed", "free_item", "free_shipping"</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">discount_value</code> - Nilai diskon (angka
                persentase atau nominal)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">max_discount_amount</code> - Maksimal potongan
                rupiah (untuk percentage)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">apply_to</code> - Target: "order", "category",
                "products", "cheapest", "most_expensive"</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">target_category_ids</code> - Array ID kategori
                untuk diskon [1, 2]</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">target_product_ids</code> - Array ID produk
                untuk diskon [1, 5, 10]</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">free_product_id</code> - ID produk yang gratis
                (integer)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">free_quantity</code> - Jumlah produk gratis
                (integer)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">max_shipping_discount</code> - Maksimal gratis
                ongkir (integer)</li>
        </ul>
    </div>

    <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded p-3">
        <p class="font-semibold text-green-900 dark:text-green-100 text-xs mb-2">💡 Tips Penting:</p>
        <ul class="text-xs text-green-800 dark:text-green-200 space-y-1 list-disc list-inside">
            <li>Untuk <strong>percentage discount</strong>, selalu set <code>max_discount_amount</code> agar tidak over
                budget</li>
            <li>Field <code>apply_to</code> menentukan scope diskon: "order" = semua item, "cheapest" = item termurah
            </li>
            <li>Untuk <strong>buy X get Y</strong>, gunakan <code>discount_type: "free_item"</code> + <code>apply_to:
                    "cheapest"</code></li>
            <li>Pastikan ID produk/kategori yang diinput benar-benar ada di database</li>
        </ul>
    </div>

</div>
