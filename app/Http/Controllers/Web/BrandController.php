<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Brand;
use App\Models\Product;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $shop = Shop::find(Session::get('shop_id'));
        $brand = Brand::where('name', $request['name'])->first();
        if (is_null($brand)) {
            $brand = new Brand();
            $brand->shop_id = $shop->id;
            $brand->name = $request['name'];
            $brand->save();
        }

        return redirect('products')->with('success', 'Brand Created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::find(decrypt($id));
        if (!is_null($brand)) {
            $samebrand = Brand::where('name', $request['name'])->first();
            if (is_null($samebrand)) {
                $brand->name = $request['name'];
                $brand->save();
                return redirect('products')->with('success', 'Brand updated successfully');
            }else{
                return redirect('products')->with('info', 'Brand with same name Exists');
            }
        }else{
            return redirect('products')->with('error', 'Brand Not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = Brand::find(decrypt($id));
        if (!is_null($brand)) {
            $products = Product::where('brand', $brand->name)->count();
            if ($products > 0) {
                return redirect('products')->with('error', 'Brand has Products');
            }else{
                $brand->delete();
                return redirect('products')->with('success', 'Brand removed successfully');
            }
        }else{
            return redirect('products')->with('error', 'Brand Not found');
        }
    }
}
