<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function sales()
    {
        $total = DB::table('orders')->sum('total');
        $count = DB::table('orders')->count();
        $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total');

        $salesByMonth = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count, SUM(total) as amount')
            ->where('status', 'completed')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        return View::make('admin.reports.sales', [
            'total' => $total,
            'count' => $count,
            'totalRevenue' => $totalRevenue,
            'salesByMonth' => $salesByMonth
        ]);
    }

    public function inventory()
    {
        $lowStockCount = DB::table('products')->where('stock', '<', 10)->count();
        $outOfStockCount = DB::table('products')->where('stock', 0)->count();
        $totalProducts = DB::table('products')->count();
        $totalValue = DB::table('products')->selectRaw('SUM(price * stock) as value')->first()->value ?? 0;

        return View::make('admin.reports.inventory', [
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'totalProducts' => $totalProducts,
            'totalValue' => $totalValue
        ]);
    }

    public function customerMetrics()
    {
        $totalCustomers = DB::table('users')->where('role', 'user')->count();
        $activeCustomers = DB::table('users')->where('role', 'user')->where('active', true)->count();
        $totalOrders = DB::table('orders')->count();
        $avgOrderValue = DB::table('orders')->avg('total');

        return View::make('admin.reports.customer-metrics', [
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue
        ]);
    }
}
