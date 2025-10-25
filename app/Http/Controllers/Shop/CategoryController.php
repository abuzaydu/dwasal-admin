<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use File;
use Session;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Product Categories';
        $shopid = Session::get('shop_id');
        if (!empty($request->search_key)) {
            $searchkey = $request->search_key;
            $categories = Category::where('shop_id', $shopid)->where(\DB::raw('CONCAT_WS(" ", `name`, `description`)'), 'like', '%' . $searchkey . '%')->get();
        }else{
            $categories = Category::where('shop_id', $shopid)->get();
        }

        return view('shop.products.categories.index', compact('page', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = new Category();
        $category->shop_id = Session::get('shop_id');
        $category->parent_id = $request->parent_id;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        $location = null;
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                ]);

                $img_path = storage_path('/categories/'.$category->img_url);
                if (File::exists($img_path)) {
                    unlink($img_path);
                }

                $extension = $request->image->extension();
                $request->image->storeAs('/categories', $category->id.'_cat.'.$extension);
                $location = 'categories/'.$category->id.'_cat.'.$extension;
            }
        }
        $category->img_url = $location;
        $category->save();

        return redirect('categories')->with('success', 'Product Category was added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Category Details';
        $category = Category::find(decrypt($id));
        if (!is_null($category)) {
            $parentcategories = Category::where('shop_id', Session::get('shop_id'))->whereNull('parent_id')->select('id', 'name')->get();
            $products = $category->products()->get();
            return view('shop.products.categories.show', compact('page', 'category', 'parentcategories', 'products'));
        }else{
            return redirect()->back()->with('error', 'Category not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Category';
        $category = Category::find(decrypt($id));
        $parentcategories = Category::where('shop_id', Session::get('shop_id'))->whereNull('parent_id')->select('id', 'name')->get();
        return view('products.categories.edit', compact('page', 'category', 'parentcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find(decrypt($id));
        $category->parent_id = $request->parent_id;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        $location = null;
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                ]);

                $img_path = storage_path('/categories/'.$category->img_url);
                if (File::exists($img_path)) {
                    unlink($img_path);
                }

                $extension = $request->image->extension();
                $request->image->storeAs('/categories', $category->id.'_cat.'.$extension);
                $location = 'categories/'.$category->id.'_cat.'.$extension;
            }
        }else{
            $location = $category->img_url;
        }
        $category->img_url = $location;
        $category->save();

        return redirect('categories')->with('success', 'Product Category was added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find(decrypt($id));
        if (!is_null($category)) {
            $products = $category->products()->count();
            if ($products > 0) {
                return redirect()->back()->with('info', 'This Category can not be deleted');
            }else{
                $category->delete();
                return redirect('categories')->with('success', 'Category deleted successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Category not found');
        }
    }
}
