<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Mail\OrderProcessedMail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class ItemController extends Controller
{
    public function getItems(Request $request)
    {
        $query = DB::table('products');

        // Search functionality
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        // Category filter
        if ($request->has('category') && $request->category !== null) {
            $query->where('category_id', $request->category);
        }

        $items = $query->paginate(12);
        // attach average rating to each item
        foreach ($items as $item) {
            $item->avg_rating = DB::table('reviews')
                ->where('product_id', $item->id)
                ->where('approved', 1)
                ->avg('rating');
        }

        $categories = DB::table('categories')->get();

        return View::make('shop.index', [
            'items' => $items,
            'categories' => $categories,
            'search' => $request->search,
            'selectedCategory' => $request->category
        ]);
    }

    public function addToCart(Request $request, $id)
    {
        if (!Auth::check()) {
            return Redirect::route('login')->with('error', 'Please log in to add items to cart');
        }
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return Redirect::back()->with('error', 'Administrators are not allowed to use the cart');
        }
        $product = Product::find($id);
        if (!$product) {
            return Redirect::back()->with('error', 'Product not found');
        }

        $quantity = $request->input('quantity', 1);
        $action = $request->input('action', 'add');

        $cartItem = CartItem::firstOrNew([
            'user_id' => $user->id,
            'product_id' => $id,
        ]);
        // increment quantity
        $cartItem->quantity = ($cartItem->quantity ?? 0) + $quantity;
        $cartItem->save();

        // if "Buy Now" redirect to checkout, otherwise to cart
        if ($action === 'buyNow') {
            return Redirect::route('getCart');
        }

        return Redirect::route('getItems')->with('success', 'Item added to cart');
    }

    public function getCart()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return Redirect::route('admin.dashboard.index')->with('error', 'Administrators do not have a shopping cart');
        }
        $cartItems = $user->cartItems()->with('product')->get();

        $totalPrice = $cartItems->sum(function ($ci) {
            return $ci->product->price * $ci->quantity;
        });

        // convert into simple objects that the existing view expects
        $cart = $cartItems->map(function ($ci) {
            $prod = $ci->product;
            return (object)[
                'image' => $prod->image,
                'name' => $prod->name,
                'price' => $prod->price,
                'quantity' => $ci->quantity,
                'product_id' => $prod->id,
            ];
        });

        return View::make('shop.shopping-cart', ['products' => $cart, 'totalPrice' => $totalPrice]);
    }

    public function getReduceByOne($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->where('product_id', $id)->first();
        if ($cartItem) {
            $cartItem->quantity = max(0, $cartItem->quantity - 1);
            if ($cartItem->quantity === 0) {
                $cartItem->delete();
            } else {
                $cartItem->save();
            }
        }
        return Redirect::route('getCart');
    }

    public function getRemoveItem($id)
    {
        CartItem::where('user_id', Auth::id())->where('product_id', $id)->delete();
        return Redirect::route('getCart');
    }

    public function postCheckout(Request $request)
    {
        if (!Auth::check()) {
            return \redirect()->route('login');
        }
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return Redirect::route('admin.dashboard.index')->with('error', 'Administrators cannot checkout');
        }
        $request->validate([
            'address_id' => ['nullable','integer','exists:user_addresses,id'],
            'shipping_address' => ['required_without:address_id', 'string', 'max:2000'],
            'shipping_phone' => ['required_without:address_id','string','max:50'],
            'payment_method' => ['required', 'in:cod,online'],
            'save_address' => ['nullable','boolean'],
        ]);

        $oldCart = CartItem::where('user_id', Auth::id())->get();
        if ($oldCart->isEmpty()) {
            return \redirect()->route('getCart');
        }

        try {
            DB::beginTransaction();

            $paymentMethod = $request->input('payment_method');
            
            // For this mock system:
            // - Online payments are marked as "paid" immediately
            // - COD payment stays "pending" until collected
            // - Order status always starts as "pending" (payment ≠ order fulfillment)
            $orderStatus = 'pending';
            $paymentStatus = ($paymentMethod === 'online') ? 'paid' : 'pending';
            $transactionId = null;

            // Determine shipping data: prefer saved address when provided
            $addressId = $request->input('address_id');
            if ($addressId) {
                $addr = DB::table('user_addresses')->where('id', $addressId)->where('user_id', Auth::id())->first();
                $shippingAddress = $addr->address ?? $request->input('shipping_address');
                $shippingPhone = $addr->phone ?? $request->input('shipping_phone');
            } else {
                $shippingAddress = $request->input('shipping_address');
                $shippingPhone = $request->input('shipping_phone');
            }

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => Auth::id(),
                'total' => 0,
                'status' => $orderStatus,
                'shipping_address' => $shippingAddress,
                'shipping_phone' => $shippingPhone,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'transaction_id' => $transactionId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Optionally save the provided address for the user
            if (!$addressId && $request->boolean('save_address')) {
                DB::table('user_addresses')->insert([
                    'user_id' => Auth::id(),
                    'label' => null,
                    'address' => $shippingAddress,
                    'phone' => $shippingPhone,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $total = 0;
            foreach ($oldCart as $item) {
                $product = Product::find($item->product_id);
                $line = $product->price * $item->quantity;
                $total += $line;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $product->decrement('stock', $item->quantity);
            }

            DB::table('orders')->where('id', $orderId)->update(['total' => $total, 'updated_at' => Carbon::now()]);
            DB::table('cart_items')->where('user_id', Auth::id())->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return \redirect()->route('getCart')->with('error', $e->getMessage());
        }

        DB::commit();
        
        // Send order confirmation email
        $order = Order::find($orderId);
        if ($order) {
            Mail::to($user->email)->send(new OrderProcessedMail($order));
        }
        
        return \redirect()->route('getItems')->with('checkout_success', [
            'message' => 'Successfully Purchased Your Products!!!',
            'order_id' => $orderId,
            'total' => $total,
            'payment_method' => $paymentMethod,
        ]);
    }

    public function orderHistory()
    {
        if (!Auth::check()) {
            return Redirect::route('login');
        }

        $orders = DB::table('orders')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return View::make('orders.history', ['orders' => $orders]);
    }

    public function show($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return Redirect::route('getItems')->with('error', 'Product not found');
        }

        $images = DB::table('product_images')->where('product_id', $id)->get();

        $reviews = DB::table('reviews')
            ->join('users', 'reviews.user_id', '=', 'users.id')
            ->where('reviews.product_id', $id)
            ->where('reviews.approved', 1)
            ->select('reviews.*', 'users.name as reviewer_name')
            ->orderBy('reviews.created_at', 'desc')
            ->get();

        $averageRating = DB::table('reviews')
            ->where('product_id', $id)
            ->where('approved', 1)
            ->avg('rating');

        $userReviewed = false;
        if (Auth::check()) {
            $userReviewed = DB::table('reviews')
                ->where('product_id', $id)
                ->where('user_id', Auth::id())
                ->exists();
        }

        return View::make('shop.product', [
            'product' => $product,
            'images' => $images,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'userReviewed' => $userReviewed
        ]);
    }

    public function orderDetails($id)
    {
        if (!Auth::check()) {
            return Redirect::route('login');
        }

        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return Redirect::route('orderHistory')->with('error', 'Order not found');
        }

        $orderItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'products.name', 'products.price', 'products.image')
            ->where('order_items.order_id', $id)
            ->get();

        return View::make('orders.details', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }
}
