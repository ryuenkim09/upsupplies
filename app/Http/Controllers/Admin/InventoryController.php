<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function lowStock()
    {
        $products = Product::with('images', 'category')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->paginate(20);

        $lowStockCount = Product::where('stock', '<', 10)->count();
        $outOfStockCount = Product::where('stock', 0)->count();
        $criticalCount = Product::where('stock', '<', 3)->count();

        return View::make('admin.inventory.low-stock', [
            'products' => $products,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'criticalCount' => $criticalCount
        ]);
    }

    public function summary()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $lowStock = Product::where('stock', '<', 10)->count();
        $outOfStock = Product::where('stock', 0)->count();

        $topSelling = Product::select('products.id', 'products.name')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as sold')
            ->groupBy('products.id', 'products.name')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit(5)
            ->get();

        return View::make('admin.inventory.summary', [
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'topSelling' => $topSelling
        ]);
    }
}
