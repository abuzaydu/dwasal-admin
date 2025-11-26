<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\CustomerCategory;

class CustomerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Customer Categories';
        $title = 'Customer Categories';

        $categories = CustomerCategory::where('shop_id', Session::get('shop_id'))->get();
        return view('sales.customers.categories.index', compact('page', 'title', 'categories'));
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
        $cat = new CustomerCategory;
        $cat->shop_id = Session::get('shop_id');
        $cat->cat_name = $request['name'];
        $cat->description = $request['description'];
        $cat->save();

        return redirect('customer-categories')->with('success', 'Category created successfully');
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
        $page = 'Edit Customer Category';
        $title = 'Edit Customer Category';
        $cat = CustomerCategory::find(decrypt($id));
        return view('sales.customers.categories.edit', compact('page','title', 'cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cat = CustomerCategory::find(decrypt($id));
        $cat->cat_name = $request['name'];
        $cat->description = $request['description'];
        $cat->save();

        return redirect('customer-categories')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cat = CustomerCategory::find(decrypt($id));
        $customers = Customer::where('cust_cat_id')->count();
        if ($customers > 0) {
            return redirect('customer-categories')->with('info', 'Category can not be deleted');
        }else{
            $cat->delete();
            return redirect('customer-categories')->with('success', 'Category remoced successfully');
        }
    }
}
