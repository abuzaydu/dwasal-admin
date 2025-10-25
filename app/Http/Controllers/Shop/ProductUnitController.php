<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\ProductUnit;
use App\Models\UnitMeasure;
use App\Models\Product;

class ProductUnitController extends Controller
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
        $companyid = Session::get('company_id');
        $prod_unit = new ProductUnit();
        $prod_unit->company_id = $companyid;
        $prod_unit->product_id = $request['product_id'];
        $prod_unit->unit_name = $request['unit_name'];
        $prod_unit->qty_equal_to_basic = $request['qty_equal_to_basic'];
        $prod_unit->unit_price = $request['unit_price'];
        $prod_unit->save();

        return redirect()->route('products.show', encrypt($request['product_id']))->with('success', 'Product Unit added successfully');

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
        $page = 'Edit Product Unit';
        $prod_unit = ProductUnit::find(decrypt($id));
        
        $units = UnitMeasure::select('unit_name')->get();
        return view('shop.products.edit-unit', compact('page', 'prod_unit', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prod_unit = ProductUnit::find(decrypt($id));
        $prod_unit->unit_name = $request['unit_name'];
        $prod_unit->qty_equal_to_basic = $request['qty_equal_to_basic'];
        $prod_unit->unit_price = $request['unit_price'];
        $prod_unit->save();

        if ($prod_unit->is_basic) {
            $product = Product::where('company_id', Session::get('company_id'))->where('id', $prod_unit->product_id)->first();
            if (!is_null($product)) {
                $product->basic_uom = $prod_unit->unit_name;
                $product->save();
                $product->retail_price = $prod_unit->unit_price;
                $product->save();
            }
        }

        return redirect()->route('products.show', encrypt($prod_unit->product_id))->with('success', 'Product Unit updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prod_unit = ProductUnit::find(decrypt($id));
        if (!is_null($prod_unit)) {
            if (!$prod_unit->is_basic) {
                $prod_unit->delete();
                return redirect()->route('products.show', encrypt($prod_unit->product_id))->with('success', 'Product Unit deleted successfully');
            }else{
                return redirect()->back()->with('warning', 'Basic Unit cannot be deleted');
            }
        }else{
            return redirect()->back()->with('info', 'Item not Found');
        }
    }
}
