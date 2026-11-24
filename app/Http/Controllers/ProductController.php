<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('hotel')->latest();

        // Filter by hotel
        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products   = $query->paginate(15)->withQueryString();
        $hotels     = Hotel::orderBy('name')->get();
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('products.index', compact('products', 'hotels', 'categories'));
    }

    public function create()
    {
        $hotels     = Hotel::orderBy('name')->get();
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('products.create', compact('hotels', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id'            => ['required', 'exists:hotels,id'],
            'name'                => [
                'required', 'string', 'max:255',
                Rule::unique('products')->where(function ($q) use ($request) {
                    return $q->where('hotel_id', $request->hotel_id)
                             ->whereNull('deleted_at');
                }),
            ],
            'category'            => ['nullable', 'string', 'max:100'],
            'description'         => ['nullable', 'string', 'max:2000'],
            'price'               => ['required', 'numeric', 'min:0'],
            'is_vegetarian'       => ['boolean'],
            'is_available'        => ['boolean'],
            'stock_quantity'      => ['nullable', 'integer', 'min:0'],
            'preparation_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'sort_order'          => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_vegetarian'] = $request->boolean('is_vegetarian');
        $validated['is_available']  = $request->boolean('is_available', true);
        $validated['sort_order']    = $validated['sort_order'] ?? 0;

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load('hotel', 'preOrderItems');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $hotels     = Hotel::orderBy('name')->get();
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('products.edit', compact('product', 'hotels', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'hotel_id'            => ['required', 'exists:hotels,id'],
            'name'                => [
                'required', 'string', 'max:255',
                Rule::unique('products')->where(function ($q) use ($request, $product) {
                    return $q->where('hotel_id', $request->hotel_id)
                             ->whereNull('deleted_at')
                             ->where('id', '!=', $product->id);
                }),
            ],
            'category'            => ['nullable', 'string', 'max:100'],
            'description'         => ['nullable', 'string', 'max:2000'],
            'price'               => ['required', 'numeric', 'min:0'],
            'is_vegetarian'       => ['boolean'],
            'is_available'        => ['boolean'],
            'stock_quantity'      => ['nullable', 'integer', 'min:0'],
            'preparation_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'sort_order'          => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_vegetarian'] = $request->boolean('is_vegetarian');
        $validated['is_available']  = $request->boolean('is_available', true);
        $validated['sort_order']    = $validated['sort_order'] ?? 0;

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
