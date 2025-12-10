<div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 p-4">
    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">📚 Panduan Lengkap Format JSON Kondisi</h3>

    <div class="space-y-4 text-xs text-gray-700 dark:text-gray-300">

        <!-- Contoh 1 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-blue-600 dark:text-blue-400 mb-2">💰 Contoh 1: Diskon untuk Pembelian Minimum
            </p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "min_subtotal": 200000,
  "max_subtotal": 1000000
}</code></pre>
            <p class="mt-2 italic">➜ Berlaku untuk pembelian Rp 200.000 - Rp 1.000.000</p>
        </div>

        <!-- Contoh 2 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-green-600 dark:text-green-400 mb-2">🛍️ Contoh 2: Diskon untuk Produk Tertentu
            </p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "required_product_ids": [1, 5, 10],
  "min_quantity": 2
}</code></pre>
            <p class="mt-2 italic">➜ Berlaku jika beli minimal 2 dari produk ID 1, 5, atau 10</p>
        </div>

        <!-- Contoh 3 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-purple-600 dark:text-purple-400 mb-2">⭐ Contoh 3: Diskon untuk Member Gold ke
                Atas</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "customer_tier_codes": ["GOLD", "PLATINUM", "VIP"],
  "min_subtotal": 150000
}</code></pre>
            <p class="mt-2 italic">➜ Hanya untuk member Gold/Platinum/VIP dengan belanja min Rp 150.000</p>
        </div>

        <!-- Contoh 4 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-orange-600 dark:text-orange-400 mb-2">📅 Contoh 4: Diskon Weekend
                (Sabtu-Minggu)</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "day_of_week": [6, 0],
  "min_subtotal": 100000
}</code></pre>
            <p class="mt-2 italic">➜ Berlaku Sabtu (6) & Minggu (0), min pembelian Rp 100.000</p>
            <p class="text-xs text-gray-500 mt-1">Catatan: 0=Minggu, 1=Senin, 2=Selasa, dst.</p>
        </div>

        <!-- Contoh 5 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-red-600 dark:text-red-400 mb-2">⏰ Contoh 5: Diskon Happy Hour (Jam Tertentu)
            </p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "time_range": ["14:00", "17:00"],
  "min_subtotal": 80000
}</code></pre>
            <p class="mt-2 italic">➜ Berlaku jam 14:00-17:00 (sore hari)</p>
        </div>

        <!-- Contoh 6 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-pink-600 dark:text-pink-400 mb-2">🎂 Contoh 6: Diskon Ulang Tahun</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "is_birthday": true,
  "min_subtotal": 50000
}</code></pre>
            <p class="mt-2 italic">➜ Hanya untuk customer yang sedang ulang tahun hari ini</p>
        </div>

        <!-- Contoh 7 -->
        <div class="bg-white dark:bg-gray-900 rounded p-3 border border-gray-200 dark:border-gray-700">
            <p class="font-semibold text-teal-600 dark:text-teal-400 mb-2">🎉 Contoh 7: Diskon First Purchase</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "is_first_purchase": true,
  "min_subtotal": 300000
}</code></pre>
            <p class="mt-2 italic">➜ Hanya untuk customer yang belanja pertama kali</p>
        </div>

        <!-- Contoh Kombinasi -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded p-3 border border-yellow-300 dark:border-yellow-700">
            <p class="font-semibold text-yellow-800 dark:text-yellow-400 mb-2">🔥 Contoh Kombinasi Kompleks</p>
            <pre class="bg-gray-100 dark:bg-gray-950 p-2 rounded text-xs overflow-x-auto"><code>{
  "min_subtotal": 500000,
  "customer_tier_codes": ["PLATINUM", "VIP"],
  "day_of_week": [6, 0],
  "required_category_ids": [2]
}</code></pre>
            <p class="mt-2 italic">➜ Member Platinum/VIP yang beli kategori ID 2 min Rp 500.000 di weekend</p>
        </div>

    </div>

    <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3">
        <p class="font-semibold text-blue-900 dark:text-blue-100 text-xs mb-2">📋 Daftar Field yang Tersedia:</p>
        <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">min_subtotal</code> - Minimal total belanja
                (integer)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">max_subtotal</code> - Maksimal total belanja
                (integer)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">required_product_ids</code> - Array ID produk
                yang harus dibeli [1, 2, 3]</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">required_category_ids</code> - Array ID kategori
                [1, 2]</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">customer_tier_codes</code> - Array kode tier
                ["GOLD", "PLATINUM"]</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">day_of_week</code> - Array hari [0-6] (0=Minggu,
                6=Sabtu)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">time_range</code> - Array jam ["HH:MM", "HH:MM"]
            </li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">min_quantity</code> - Minimal jumlah item
                (integer)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">is_first_purchase</code> - Pembelian pertama?
                (true/false)</li>
            <li><code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">is_birthday</code> - Sedang ulang tahun?
                (true/false)</li>
        </ul>
    </div>

</div>
