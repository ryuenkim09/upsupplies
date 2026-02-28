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
            // include shipped since it appears in the dropdown
            'status' => 'required|in:pending,processing,shipped,completed,cancelled'
        ]);
        $oldStatus = $order->status;
        $newStatus = $validated['status'];
        $order->status = $newStatus;
        
        // If marking a COD order as completed, auto-mark payment as paid
        if ($newStatus === 'completed' && $order->payment_method === 'cod') {
            $order->payment_status = 'paid';
        }

        // decrement stock once when the order is first shipped/completed
        if (!in_array($oldStatus, ['shipped','completed']) && in_array($newStatus, ['shipped','completed'])) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product && $item->quantity > 0) {
                    $product->stock = max(0, $product->stock - $item->quantity);
                    $product->save();
                }
            }
        }
        
        $order->save();

        return Redirect::route('admin.orders.show', $id)->with('success', 'Order status updated successfully');
    }
}
