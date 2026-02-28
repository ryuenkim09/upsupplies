<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $products = Product::with(['images', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return View::make('admin.products.index', ['products' => $products]);
    }

    public function create()
    {
        $categories = Category::all();
        return View::make('admin.products.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'image' => $imageName,
        ]);

        // handle additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $name = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('images'), $name);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $name,
                    'primary' => false,
                ]);
            }
        }

        return Redirect::route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::with('images')->find($id);
        if (!$product) {
            return Redirect::route('admin.products.index')->with('error', 'Product not found');
        }

        $categories = Category::all();

        return View::make('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'images' => $product->images
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return Redirect::route('admin.products.index')->with('error', 'Product not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $product->name = $validated['name'];
        $product->description = $validated['description'];
        $product->price = $validated['price'];
        $product->stock = $validated['stock'];
        $product->category_id = $validated['category_id'];

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        // additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $name = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('images'), $name);
                ProductImage::create([
                    'product_id' => $id,
                    'path' => $name,
                    'primary' => false,
                ]);
            }
        }

        return Redirect::route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::with('images')->find($id);
        if (!$product) {
            return Redirect::route('admin.products.index')->with('error', 'Product not found');
        }

        // delete images files and records
        foreach ($product->images as $img) {
            $path = public_path('images/' . $img->path);
            if (file_exists($path)) {@unlink($path);} 
            $img->delete();
        }

        // delete main image file
        if ($product->image) {
            $mainPath = public_path('images/' . $product->image);
            if (file_exists($mainPath)) {@unlink($mainPath);} 
        }

        $product->delete();

        return Redirect::route('admin.products.index')->with('success', 'Product deleted successfully');
    }

    public function destroyImage($productId, $imageId)
    {
        $img = ProductImage::where('id', $imageId)->where('product_id', $productId)->first();
        if ($img) {
            // remove file from storage
            $path = public_path('images/' . $img->path);
            if (file_exists($path)) {
                @unlink($path);
            }
            $img->delete();
        }
        return Redirect::back()->with('success', 'Image removed');
    }
}
