<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return View::make('admin.orders.index', ['orders' => $orders]);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->find($id);

        if (!$order) {
            return Redirect::route('admin.orders.index')->with('error', 'Order not found');
        }

        $orderItems = $order->items;

        return View::make('admin.orders.show', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return Redirect::route('admin.orders.index')->with('error', 'Order not found');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);
        $order->status = $validated['status'];
        
        // If marking a COD order as completed, auto-mark payment as paid
        if ($validated['status'] === 'completed' && $order->payment_method === 'cod') {
            $order->payment_status = 'paid';
        }
        
        $order->save();

        return Redirect::route('admin.orders.show', $id)->with('success', 'Order status updated successfully');
    }
}
