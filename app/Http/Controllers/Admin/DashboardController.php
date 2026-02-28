<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $totalProducts = DB::table('products')->count();
        $totalOrders = DB::table('orders')->count();
        $totalUsers = DB::table('users')->count();
        $totalRevenue = DB::table('orders')->sum('total');
        
        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as user_name', 'users.email')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        $lowStockProducts = DB::table('products')
            ->where('stock', '<', 10)
            ->count();

        $pendingReviews = DB::table('reviews')->where('approved', false)->count();

        return View::make('admin.dashboard', [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'pendingReviews' => $pendingReviews,
        ]);
    }
}
