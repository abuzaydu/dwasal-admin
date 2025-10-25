<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\ServCategory;
use App\Models\ServiceSaleItem;

class ServCategoryController extends Controller
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
        $title = 'Service Categories';
        $title_sw = 'Kategoria za Huduma';
        $shop = Shop::find(Session::get('shop_id'));
        $servcategories = ServCategory::where('shop_id', $shop->id)->get();
        return view('services.categories.index', compact('page', 'title', 'title_sw', 'servcategories', 'shop'));
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
        $category = ServCategory::where('name', $request['name'])->where('shop_id', $shop->id)->first();
        if (is_null($category)) {
            $category = new ServCategory();
            $category->shop_id = $shop->id;
            $category->name = $request['name'];
            $category->description = $request['description'];
            $category->save();
        }
        $success = 'Category was successfuly created';
        if (!empty($request['in_services'])) {
            return redirect('services')->with('success', $success);
        }else{
            return redirect('serv-categories')->with('success', $success);
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
        $shop = Shop::find(Session::get('shop_id'));
        $services = $shop->services()->select('services.id as id', 'code', 'name')->get();

        $category = ServCategory::find(decrypt($id));
        $cat_services = $category->services()->select('services.id as id', 'code', 'name')->get();
        
        // return $cat_services;
        $currservices = [];
        foreach ($cat_services as $key => $value) {
            array_push($currservices, $value->id);
        }

        return view('services.categories.show', compact('page', 'title', 'category', 'services', 'cat_services', 'currservices'));
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
        $categories = ServCategory::where('shop_id', $shop->id)->get();
        $category = ServCategory::find(decrypt($id));
        return view('services.categories.edit', compact('page', 'title', 'title_sw', 'category', 'categories'));
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
        $category = ServCategory::find(decrypt($id));
        $category->name  = $request['name'];
        $category->description = $request['description'];
        $category->save();

        $success = 'Category was successfuly updated';
        return redirect('serv-categories')->with('success', $success);
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
        $category = ServCategory::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($category) && !($category->name == 'Uncategorized')) {
            foreach ($category->services()->get() as $key => $service) {
                $category->services()->detach($service);
            }
            $category->delete();
        }else{
            return redirect('serv-categories')->with('warning', 'This Category is default can not be deleted.');
        }

        $success = 'Category was successfuly removed';
        return redirect('serv-categories')->with('success', $success);
    }

    public function deleteMultiple(Request $request)
    {
        foreach ($request->input('id') as $key => $id) {
            $category = ServCategory::find($id);
            if (!is_null($category) && !($category->name == 'Uncategorized')) {
                foreach ($category->services()->get() as $key => $service) {
                    $category->services()->detach($service);
                }
                $category->delete();
            }
        }

        $success = 'Category was successfuly removed';
        return redirect('serv-categories')->with('success', $success);
    }

    public function categoryservices($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = ServCategory::find($id);

        $services = $category->services()->get();
        return response()->json(['services' => $services]);
    }

    
    
    public function addserviceToCategory(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = ServCategory::find($request['serv_category_id']);
        if (!empty($request['service_id'])) {
            foreach ($request['service_id'] as $prod) {
                $uncats = ServCategory::where('shop_id', $shop->id)->get();
                foreach ($uncats as $key => $value) {
                    if ($value->services()->where('service_id', $prod)->count() > 0) {
                        $value->services()->detach($prod);
                    }
                }
                $category->services()->attach($prod);
                $saleitems = ServiceSaleItem::where('service_id', $prod)->where('shop_id', $shop->id)->get();
                // Log::info($saleitems);
                foreach ($saleitems as $key => $item) {
                    $item->serv_category_id = $category->id;
                    $item->save();
                }
            }

            $success = 'services were successfuly added';
            return redirect()->route('serv-categories.show' , encrypt($category->id))->with('success', $success);
        }else{
            return redirect()->back()->with('error', 'No service selected. Please select at least one service to continue.');
        }
    }

    public function removeserviceFromCategory(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $category = ServCategory::find($request['serv_category_id']);

        if (!is_null($category)) {
            if (!empty($request['service_id'])) {
                foreach ($request['service_id'] as $prod) {
                    $category->services()->detach($prod);
                    $saleitems = ServiceSaleItem::where('service_id', $prod)->where('shop_id', $shop->id)->get();
                    foreach ($saleitems as $key => $item) {
                        $item->serv_category_id = null;
                        $item->save();
                    }
                    $uncat = ServCategory::where('shop_id', $shop->id)->where('name', 'Uncategorized')->first();
                    if (!is_null($uncat)) {
                        $uncat->services()->attach($prod);
                    }
                }

                $success = 'services were successfuly removed';
                return redirect()->route('serv-categories.show' , encrypt($category->id))->with('success', $success);
            }else{
                return redirect()->back()->with('error', 'No service selected. Please select at least one service to continue.');
            }
        }else{
            return redirect()->back()->with('error', 'category not Found.');
        }
    }

    //Remove all services from a category
    public function removeAll($id)
    {
        $category = ServCategory::find($id);
        $cat_services = $category->services()->get();

        foreach ($cat_services as $key => $service) {
            $category->services()->detach($service);
        }
        
        $success = 'services were successfuly removed';
        return redirect()->route('serv-categories.show' , encrypt($category->id))->with('success', $success);
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
