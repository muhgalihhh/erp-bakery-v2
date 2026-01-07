<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use App\Models\Product;
use Illuminate\Http\Request;

class LandingController extends Controller
{
  /**
   * Display the landing page
   */
  public function index()
  {
    // Get shop settings
    $settings = ShopSetting::getSettings();

    // Get ONLY finished products (barang jadi) that are active and in stock
    $products = Product::where('type', 'finished')
      ->where('is_active', true)
      ->where('is_sellable', true)
      ->where('current_stock', '>', 0)
      ->orderBy('name')
      ->get();

    return view('landing.index', compact('settings', 'products'));
  }

  /**
   * Display all products (catalog page)
   */
  public function products(Request $request)
  {
    $settings = ShopSetting::getSettings();

    // Only show finished products (barang jadi)
    $query = Product::where('type', 'finished')
      ->where('is_sellable', true);

    // Search filter
    if ($request->has('search') && $request->search) {
      $query->where('name', 'like', '%' . $request->search . '%')
        ->orWhere('description', 'like', '%' . $request->search . '%');
    }

    // Stock filter
    if ($request->has('in_stock') && $request->in_stock) {
      $query->where('current_stock', '>', 0);
    }

    // Active filter
    if (!$request->has('show_all')) {
      $query->where('is_active', true);
    }

    // Sort
    $sortBy = $request->get('sort', 'name');
    $sortOrder = $request->get('order', 'asc');

    $query->orderBy($sortBy, $sortOrder);

    $products = $query->paginate(12);

    return view('landing.products', compact('settings', 'products'));
  }
}
