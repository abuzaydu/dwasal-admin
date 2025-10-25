<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use \DB;
use File;
use App\Models\Shop;
use App\Models\Category;
use App\Models\AnSaleItem;
use Log;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Categories';
        $title = 'Product Categories';
        $title_sw = 'Jamii za Bidhaa';
        $shop = Shop::find(Session::get('shop_id'));
        $categories = Category::where('shop_id', $shop->id)->get();
        
        return view('products.categories.index', compact('page', 'title', 'title_sw', 'categories', 'shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = Category::where('name', $request['name'])->where('shop_id', $shop->id)->first();
        if (is_null($category)) {
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
        }
        $success = 'Category was successfuly created';
        if (!empty($request['in_products'])) {
            return redirect('products')->with('success', $success);
        }else{
            return redirect('categories')->with('success', $success);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Category Details';
        $title = 'Category Details';
        $title_sw = 'Maelezo ya Jamii ya Bidhaa';
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->select('id', 'product_code', 'name', 'slug')->get();

        $category = Category::find(decrypt($id));
        $cat_products = null;
        $currProducts =array();

        $prods = [];
        if ($category->children->count() > 0) {
            if ($category->products()->count() > 0) {
                $ctproducts = $category->products()->select('products.id as id', 'product_code', 'name', 'basic_uom', 'in_stock')->get();
                foreach ($ctproducts as $key => $value) {
                    array_push($prods, $value);
                }
            }

            foreach ($category->catProducts() as $key => $value) {
               $ctproduct = $shop->products()->where('id', $value['id'])->select('products.id as id', 'product_code', 'name', 'basic_uom', 'in_stock')->first();
               if (!is_null($ctproduct)) {
                   array_push($prods, $ctproduct);
               }
            }
            
            $cat_products = $prods;
        }else{
            $cat_products = $category->products()->select('products.id as id', 'product_code', 'name', 'basic_uom', 'in_stock')->get();
        }
        
        // return $cat_products;

        foreach ($cat_products as $key => $value) {
            array_push($currProducts, $value->id);
        }

        return view('products.categories.show', compact('page', 'title', 'title_sw', 'category', 'products', 'cat_products', 'currProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Category';
        $title = 'Edit Category';
        $title_sw = 'Hariri Jamii';
        $shop = Shop::find(Session::get('shop_id'));
        $categories = $shop->categories()->get();
        $category = Category::find(decrypt($id));
        return view('products.categories.edit', compact('page', 'title', 'title_sw', 'category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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

        $success = 'Category was successfuly updated';
        return redirect('categories')->with('success', $success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $shop = Shop::find(Session::get('shop_id'));
        $category = Category::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($category) && !($category->name == 'Uncategorized')) {
            foreach ($category->products()->get() as $key => $product) {
                $category->products()->detach($product);
            }
            $category->delete();
        }else{
            return redirect('categories')->with('warning', 'This Category is default can not be deleted.');
        }

        $success = 'Category was successfuly removed';
        return redirect('categories')->with('success', $success);
    }

    public function deleteMultiple(Request $request)
    {
        foreach ($request->input('id') as $key => $id) {
            $category = Category::find($id);
            if (!is_null($category) && !($category->name == 'Uncategorized')) {
                foreach ($category->products()->get() as $key => $product) {
                    $category->products()->detach($product);
                }
                $category->delete();
            }
        }

        $success = 'Category was successfuly removed';
        return redirect('categories')->with('success', $success);
    }

    public function categoryProducts($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = Category::find($id);

        //Check if Category has chilren
        if ($category->children->count() > 0) {
            $products = $category->catProducts();

            $prods = [];
            foreach ($products as $key => $value) {
                if (isset($value[$key])) {
                    array_push($prods, $value);
                }
            }
            return response()->json(['products' => $prods]);
        }else{
            $products = $category->products()->get();
            return response()->json(['products' => $products]);
        }

    }

    
    
    public function addProductToCategory(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = Category::find($request['category_id']);
        if (!empty($request['product_id'])) {
            foreach ($request['product_id'] as $prod) {
                $uncats = Category::where('shop_id', $shop->id)->get();
                foreach ($uncats as $key => $value) {
                    if ($value->products()->where('product_id', $prod)->count() > 0) {
                        $value->products()->detach($prod);
                    }
                }
                $category->products()->attach($prod);
                $saleitems = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $prod)->get();
                // Log::info($saleitems);
                foreach ($saleitems as $key => $item) {
                    $item->category_id = $category->id;
                    $item->save();
                }
            }

            $success = 'Products were successfuly added';
            return redirect()->route('categories.show' , encrypt($category->id))->with('success', $success);
        }else{
            return redirect()->back()->with('error', 'No Product selected. Please select at least one Product to continue.');
        }
    }

    public function removeProductFromCategory(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = Category::find($request['category_id']);

        if (!is_null($category)) {
            if (!empty($request['product_id'])) {
                foreach ($request['product_id'] as $prod) {
                    $category->products()->detach($prod);
                    $saleitems = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $prod)->get();
                    foreach ($saleitems as $key => $item) {
                        $item->category_id = null;
                        $item->save();
                    }
                    $uncat = Category::where('shop_id', $shop->id)->where('name', 'Uncategorized')->first();
                    if (!is_null($uncat)) {
                        $uncat->products()->attach($prod);
                    }
                }

                $success = 'Products were successfuly removed';
                return redirect()->route('categories.show' , encrypt($category->id))->with('success', $success);
            }else{
                return redirect()->back()->with('error', 'No Product selected. Please select at least one Product to continue.');
            }
        }else{
            return redirect()->back()->with('error', 'category not Found.');
        }
    }

    //Remove all products from a category
    public function removeAll($id)
    {
        $category = Category::find($id);
        $cat_products = $category->products()->get();

        foreach ($cat_products as $key => $product) {
            $category->products()->detach($product);
        }
        
        $success = 'Products were successfuly removed';
        return redirect()->route('categories.show' , encrypt($category->id))->with('success', $success);
    }

    public function array_flatten($array) { 
        if (!is_array($array)) { 
            return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, array_flatten($value)); 
            } 
            else { 
                $result[$key] = $value; 
            } 
        } 
        return $result; 
    }
}
