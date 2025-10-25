<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use Session;
use \Carbon\Carbon;
use App\Models\Product;
use App\Models\UnitMeasure;
use App\Models\Brand;
use App\Models\Category;
use App\Models\StorageLocation;
use App\Models\Stock;
use App\Models\ProductUnit;
use App\Models\ProductImage;

class ProductController extends Controller
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
        $page = 'Product List';
        $shopid = Session::get('shop_id');
        $units = UnitMeasure::select('unit_name')->get();
        $categories = Category::where('shop_id', $shopid)->select('id', 'name')->get();
        $slocations = StorageLocation::where('shop_id', $shopid)->select('id', 'location_name')->get();
        $currcatid = '';
        $stock_status = '';
        $products = [];
        if (!empty($request->search_key)) {
            $searchkey = $request->search_key;
            $products = Product::where('shop_id', $shopid)->where('is_active', true)->where(\DB::raw('CONCAT_WS(" ", `product_code`, `name`, `description`)'), 'like', '%' . $searchkey . '%')->get();
        }else{
            if (!empty($request['category_id']) ) {
                $currcatid = $request['category_id'];
                $category = Category::find($currcatid);
                if (!empty($request['stock_status'])) {
                    $stock_status = $request['stock_status'];
                    if($stock_status == 'In Stock') {
                        $products = $category->products()->where('is_active', true)->where('available_qty', '>', 0)->get();  
                    }else{
                        $products = $category->products()->where('is_active', true)->where('available_qty', '<=', 0)->get(); 
                    }
                }else{
                    $products = $category->products()->where('is_active', true)->get();
                }
            }else{
                if (!empty($request['stock_status'])) {
                    $stock_status = $request['stock_status'];
                    if($stock_status == 'In Stock') {
                        $products = Product::where('shop_id', $shopid)->where('is_active', true)->where('available_qty', '>', 0)->get();  
                    }else{
                        $products = Product::where('shop_id', $shopid)->where('is_active', true)->where('available_qty', '<=', 0)->get(); 
                    }
                }else{
                    $products = Product::where('shop_id', $shopid)->where('is_active', true)->get();
                }
            }
        }

        return view('shop.products.index', compact('page', 'products', 'units', 'categories', 'slocations', 'currcatid', 'stock_status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Product';
        $shopid = Session::get('shop_id');
        $units = UnitMeasure::select('unit_name')->get();
        $categories = Category::where('shop_id', $shopid)->select('id', 'name')->get();
        $slocations = StorageLocation::where('shop_id', $shopid)->select('id', 'location_name')->get();
        return view('shop.products.create', compact('page', 'units', 'categories', 'slocations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shopid = Session::get('shop_id');
        $product = Product::where('shop_id', $shopid)->where('product_code', $request['product_code'])->first();
        if (is_null($product)) {
            $product = new Product();
            $product->shop_id = $shopid;
            $product->product_code = $request['product_code'];
            $product->name = $request['name'];
            $product->basic_uom = $request['basic_uom'];
            $product->short_desc = $request['short_desc'];
            $product->description = $request['description'];
            $product->available_qty = $request['quantity_in'];
            $product->unit_cost = $request['unit_cost'];
            $product->retail_price = $request['retail_price'];
            $product->wholesale_price = $request['wholesale_price'];
            $product->storage_location_id = $request['storage_location_id'];
            $product->save();


            $location = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                    ]);

                    $img_path = storage_path('/products/'.$product->image_url);
                    if (File::exists($img_path)) {
                        unlink($img_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/products', $product->id.'_product.'.$extension);
                    $location = 'products/'.$product->id.'_product.'.$extension;
                }
            }
            $product->image_url = $location;
            $product->save();

            //Add this product to category
            $category = Category::find($request['category_id']);
            if (!is_null($category)) {
                $category->products()->attach($product);
            }

            $prod_unit = new ProductUnit();
            $prod_unit->product_id = $product->id;
            $prod_unit->unit_name = $product->basic_uom;
            $prod_unit->is_basic = true;
            $prod_unit->qty_equal_to_basic = 1;
            $prod_unit->unit_price = $request['retail_price'];
            $prod_unit->save();

            if ($request['quantity_in'] > 0) {
                $stock = new Stock();
                $stock->shop_id = $shopid;
                $stock->product_id = $product->id;
                $stock->quantity_in = $request['quantity_in'];
                $stock->unit_cost = $request['unit_cost'];
                $stock->supp_unit_cost = $request['unit_cost'];
                $stock->source = 'Circle Counting';
                $stock->stock_date = Carbon::now();
                $stock->save();
            }

            return redirect('products')->with('success', 'Product added successfully');
        }else{
            return redirect()->back()->with('error', 'Product with code '.$request['product_code'].' already exists');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Product details';
        $product = Product::find(decrypt($id));
        if (!is_null($product)) {
                
            $slocation = StorageLocation::find($product->storage_location);
            $images = ProductImage::where('product_id', $product->id)->select('id', 'img_url')->get();
            $punits = ProductUnit::where('product_id', $product->id)->select('id', 'product_id', 'unit_name', 'unit_price', 'is_basic', 'qty_equal_to_basic')->get();
            $units = UnitMeasure::select('unit_name')->get();

            return view('shop.products.show', compact('page', 'product', 'slocation', 'images', 'punits', 'units')); 
        }else {
            return redirect('products')->with('error', "Product not found");
        }
    }


    public function createBasicUnit($id)
    {
        
        $product = Product::find(decrypt($id));
        if (!is_null($product)) {
            $prod_unit = new ProductUnit();
            $prod_unit->product_id = $product->id;
            $prod_unit->unit_name = $product->basic_uom;
            $prod_unit->is_basic = true;
            $prod_unit->qty_equal_to_basic = 1;
            $prod_unit->unit_price = $product->retail_price;
            $prod_unit->save();

            return redirect()->route('products.show', encrypt($product->id))->with('success', 'Basic Unit created successfully');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Product';
        $product = Product::find(decrypt($id));
        $shopid = Session::get('shop_id');
        $units = UnitMeasure::select('unit_name')->get();
        $slocations = StorageLocation::where('shop_id', $shopid)->select('id', 'location_name')->get();

        return view('shop.products.edit', compact('page', 'product', 'units', 'slocations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find(decrypt($id));
        if (!is_null($product)) {
            $product->product_code = $request['product_code'];
            $product->name = $request['name'];
            $product->basic_uom = $request['basic_uom'];
            $product->short_desc = $request['short_desc'];
            $product->description = $request['description'];
            $product->retail_price = $request['retail_price'];
            $product->wholesale_price = $request['wholesale_price'];
            $product->storage_location_id = $request['storage_location_id'];
            $product->save();

            $location = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                    ]);

                    $img_path = storage_path('/products/'.$product->image_url);
                    if (File::exists($img_path)) {
                        unlink($img_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/products', $product->id.'_product.'.$extension);
                    $location = 'products/'.$product->id.'_product.'.$extension;
                }
            }else{
                $location = $product->image_url;
            }

            $product->image_url = $location;
            $product->save();

            return redirect('products')->with('success', 'Product details updated successfully');
        }else{
            return redirect()->back()->with('error', 'Something went wrong. Product not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find(decrypt($id));
        if (!is_null($product)) {
            $stocks = Stock::where('product_id', $product->id)->count();
            if ($stocks > 0) {
                return redirect()->back()->with('warning', 'Product with multiple Stocks, Orders, etc. cannot be deleted. You can start deleted the related items');
            }else{
                $product->delete();
                return redirect('products')->with('success', 'Product removed successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Something went wrong. Product not found');
        }
    }
}
