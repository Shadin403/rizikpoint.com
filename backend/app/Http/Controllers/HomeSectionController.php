<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function index()
    {
        $homeSections = HomeSection::with('category')->orderBy('sort_order', 'asc')->get();
        $categories   = Category::orderBy('name', 'asc')->get();
        $products     = Product::where('published', 1)->select('id', 'name')->latest()->get();

        return view('home_sections.index', compact('homeSections', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'type'        => 'nullable|string',
            'category_id' => 'nullable',
            'product_ids' => 'nullable',
            'limit'       => 'nullable|integer|min:1|max:50',
            'sort_order'  => 'nullable|integer',
        ]);

        $productIds = $request->input('product_ids');
        if (is_array($productIds)) {
            $productIds = implode(',', array_filter($productIds));
        }
        if (empty($productIds)) {
            $productIds = null;
        }

        $categoryId = $request->input('category_id');
        if (empty($categoryId)) {
            $categoryId = null;
        }

        $type = $request->input('type', 'new_arrivals');
        if (!empty($productIds)) {
            $type = 'custom';
        } elseif (!empty($categoryId)) {
            $type = 'category';
        }

        HomeSection::create([
            'title'       => $request->input('title'),
            'subtitle'    => $request->input('subtitle'),
            'type'        => $type,
            'category_id' => $categoryId,
            'product_ids' => $productIds,
            'limit'       => $request->input('limit', 12),
            'sort_order'  => $request->input('sort_order', 0),
            'status'      => 1,
        ]);

        flash(translate('Home Section created successfully'))->success();
        return redirect()->route('home-sections.index');
    }

    public function edit($id)
    {
        $section      = HomeSection::findOrFail($id);
        $homeSections = HomeSection::with('category')->orderBy('sort_order', 'asc')->get();
        $categories   = Category::orderBy('name', 'asc')->get();
        $products     = Product::where('published', 1)->select('id', 'name')->latest()->get();

        return view('home_sections.index', compact('section', 'homeSections', 'categories', 'products'));
    }

    public function update(Request $request, $id)
    {
        $section = HomeSection::findOrFail($id);

        // Status-only toggle check
        if ($request->has('status') && !$request->has('title')) {
            $section->status = (int) $request->input('status');
            $section->save();
            return '1';
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'type'        => 'nullable|string',
            'category_id' => 'nullable',
            'product_ids' => 'nullable',
            'limit'       => 'nullable|integer|min:1|max:50',
            'sort_order'  => 'nullable|integer',
        ]);

        $productIds = $request->input('product_ids');
        if (is_array($productIds)) {
            $productIds = implode(',', array_filter($productIds));
        }
        if (empty($productIds)) {
            $productIds = null;
        }

        $categoryId = $request->input('category_id');
        if (empty($categoryId)) {
            $categoryId = null;
        }

        $type = $request->input('type', 'new_arrivals');
        if (!empty($productIds)) {
            $type = 'custom';
        } elseif (!empty($categoryId)) {
            $type = 'category';
        }

        $section->update([
            'title'       => $request->input('title'),
            'subtitle'    => $request->input('subtitle'),
            'type'        => $type,
            'category_id' => $categoryId,
            'product_ids' => $productIds,
            'limit'       => $request->input('limit', 12),
            'sort_order'  => $request->input('sort_order', 0),
            'status'      => (int) $request->input('status', 1),
        ]);

        flash(translate('Home Section updated successfully'))->success();
        return redirect()->route('home-sections.index');
    }

    public function destroy($id)
    {
        HomeSection::destroy($id);
        flash(translate('Home Section deleted successfully'))->success();
        return redirect()->route('home-sections.index');
    }
}
