<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Session;
use App;
use Auth;
use Validator;
use File;
use Carbon\Carbon;
use App\Models\Company;
use App\Imports\ProductsImport;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Shop;
use App\Models\Payment;
use App\Models\BarcodeSetting;
use App\Models\categories;
use App\Models\Setting;
use App\Models\UnitMeasure;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\Category;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\Invoice;
use App\Models\CustomerAccount;
use App\Models\CustomerTransaction;
use App\Models\ShopCurrency;
use App\Models\PriceChange;
use App\Models\StockCorrection;
use Log;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Products';
        $title = 'My products';
        $title_sw = 'Bidhaa Zangu';

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {

            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
            if ($shop->is_warehouse) {
                $payment = Payment::where('shop_id', $shop->parent_shop_id)->where('is_expired', 0)->where('is_for_module', false)->first();
            }

            if (!is_null($payment)) {
                $units = UnitMeasure::select('unit_name')->get();
                $brands = Brand::where('shop_id', $shop->id)->select('id', 'name')->get();
                $currency = '';
                $shopcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
                if (!is_null($shopcurr)) {
                    $currency = $shopcurr->code;
                }
                $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
                if (is_null($bsetting)) {
                    $bsetting = new BarcodeSetting();
                    $bsetting->shop_id = $shop->id;
                    $bsetting->save();

                    return redirect('products');
                }

                $settings = Setting::where('shop_id', $shop->id)->first();
                if (is_null($settings)) {
                    $settings = new Setting();
                    $settings->shop_id = $shop->id;
                    $settings->tax_rate = 18;
                    $settings->inv_no_type = 'Automatic';
                    $settings->save();
                }

                $now = Carbon::now();
                $shidlen = 0;
                $code = '';
                if ($bsetting->code_type === 'EAN8') {
                    $shidlen = strlen($shop->id);
                    $code = $shop->id . str_pad(1, $bsetting->code_length - $shidlen, '0', STR_PAD_LEFT);
                } else {
                    $shidlen = strlen($shop->id);
                    $code = $shop->id . str_pad(1, $bsetting->code_length - $shidlen, '0', STR_PAD_LEFT);
                }

                $pnos = $shop->products()->where('is_active', true)->whereNotNull('product_code')->count();
                $pls = $shop->products()->where('is_active', true)->whereNotNull('location')->count();

                if (!empty($request['category_id'])) {
                    $searchcat = Category::find($request['category_id']);
                    $childrens = $searchcat->children()->get();
                    $products = [];
                    if ($searchcat->children->count() > 0) {
                        if ($searchcat->products()->count() > 0) {
                            foreach ($searchcat->products()->get() as $catprod) {
                                $shopprod = $shop->products()->where('id', $catprod->id)->first();
                                array_push($products, ['id' => $catprod->id, 'name' => $catprod->name, 'basic_uom' => $catprod->basic_uom, 'in_stock' => $shopprod->in_stock, 'retail_price' => $shopprod->retail_price, 'created_at' => $shopprod->created_at]);
                            }
                        }
                        Log::info($searchcat->catProducts());
                        if (count($searchcat->catProducts()) > 0) {
                            array_push($products, $searchcat->catProducts);
                        }
                    }else{
                        foreach ($searchcat->products()->get() as $catprod) {
                            $shopprod = $shop->products()->where('id', $catprod->id)->first();
                            array_push($products, ['id' =>  $catprod->id, 'name' => $catprod->name, 'basic_uom' => $catprod->basic_uom, 'in_stock' => $shopprod->in_stock, 'retail_price' => $shopprod->retail_price, 'created_at' => $shopprod->created_at]);
                        }
                    }
                    
                    $isSearched = true;
                    $categories = $shop->categories()->get();

                    return view('products.index1', compact('page', 'title', 'title_sw', 'products', 'units', 'brands', 'childrens', 'categories', 'searchcat', 'isSearched', 'bsetting', 'code', 'settings', 'shop', 'pnos', 'pls', 'currency'));
                }else {
                    $products = $shop->products()->where('is_active', true)->get();
                    $isSearched = false;
                    $categories = $shop->categories()->get();
                    return view('products.index', compact('page', 'title', 'title_sw', 'products', 'units', 'brands', 'categories', 'isSearched', 'bsetting', 'code', 'shop', 'pnos', 'settings', 'pls', 'currency'));
                }
            }else{
                $info = 'Dear customer your account is not activated please make payment and activate now.';
                // Alert::info("Payment Expired", $info);
                return redirect('verify-payment')->with('error', $info);
            }
        }else{
            $user = Auth::user();
            if (is_null($user->first_name) && is_null($user->phone)) {
                return redirect()->intended('signup-complete');
            }else{
                return redirect('user-profile')->with('info', 'Please setup your Businesses');
            }
        }
    }

    public function getShopProducts(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = $shop->products()->where('is_active', true)->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $shop->products()->where('is_active', true)->select('count(*) as allcount')->where(\DB::raw('CONCAT_WS(" ", `name`, `barcode`, `product_code`)'), 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $shop->products()->where('is_active', true)->orderBy('name', 'asc')->where(\DB::raw('CONCAT_WS(" ", `name`, `barcode`, `product_code`)'), 'like', '%' . $searchValue . '%')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $key => $record) {
            $id = $record->id;
            $product_code = $record->product_code;
            $name = '';
            if (Auth::user()->can('view-product')) {
                $name = "<a href='" . route('products.show', encrypt($record->id)) . "'>".$record->slug . "</a>";
            }else{
                $name = "<a href='" . url('product-sale-history/'.encrypt($record->id)) . "'>".$record->slug . "</a>";
            }
            $basic_uom = $record->basic_uom;
            $instock = $record->in_stock+0;
            $price = $record->retail_price;
            $wholesale_price = $record->wholesale_price;
            $date = date('Y-m-d H:i:s', strtotime($record->created_at));
            if (Auth::user()->can('edit-product')) {
                $editbtn = "<a href='" . route('products.edit', encrypt($record->id)) . "'><i class='fa fa-edit' style='color: blue;''></i></a>";
            } else {
                $editbtn = "";
            }
            if (Auth::user()->can('delete-product')) {
                $deletebtn = "<form id='delete-form-" . $record->id . "' method='POST' action='" . route('products.destroy', encrypt($record->id)) . "' style='display: inline;'> 
                   " . csrf_field() . "
                    <input type='hidden' name='_method' value='DELETE'>
                <a href='javascript:;' onclick=' return confirmDelete(" . $record->id . ")'><span class='fa fa-trash' aria-hidden='true' style='color: red'></span></a>
                </form>";
            } else {
                $deletebtn = '';
            }
            $action = $editbtn . ' ' . $deletebtn;

            if ($settings->retail_with_wholesale) {
                $data_arr[] = array(
                    "id" => $id,
                    'product_code' => $product_code,
                    "name" => $name,
                    "basic_uom" => $basic_uom,
                    "in_stock" => $instock,
                    "price" => $price,
                    'wholesale_price' => $wholesale_price,
                    'date' => $date,
                    'action' => $action
                );
            }else{
                $data_arr[] = array(
                    "id" => $id,
                    'product_code' => $product_code,
                    "name" => $name,
                    "basic_uom" => $basic_uom,
                    "in_stock" => $instock,
                    "price" => $price,
                    'date' => $date,
                    'action' => $action
                );
            }
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        $slug = $request['name'];
        if (!empty($request['brand'])) {
            $slug .= ' - '.$request['brand'];
        }
        if (!empty($request['model'])) {
            $slug .= ' - '.$request['model'];
        }
        if (!empty($request['type'])) {
            $slug .= ' - '.$request['type'];
        }
        if (!empty($request['color'])) {
            $slug .= ' - '.$request['color'];
        }
        if (!empty($request['size'])) {
           $slug .= ' - '.$request['size'];
        }
        if (!empty($request['thick'])) {
            $slug .= ' - '.$request['thick'];
        }
        if (!empty($request['length'])) {
            $slug .= ' - '.$request['length'];
        }
        if (!empty($request['width'])) {
            $slug .= ' - '.$request['width'];
        }
        if (!empty($request['height'])) {
            $slug .= ' - '.$request['height'];
        }
        if (!empty($request['volume'])) {
            $slug .= ' - '.$request['volume'];
        }
        if (!empty($request['weight'])) {
            $slug .= ' - '.$request['weight'];
        }
        Log::info($slug);
        $product = Product::where('slug', $slug)->where('shop_id', $shop->id)->first();
        if (is_null($product)) {
            $product = new Product();
            $product->shop_id = $shop->id;
            $product->name = $request['name'];
            $product->basic_uom = $request['basic_uom'];
            $product->slug = $slug;
            $product->save();
            
            //Upload Product Image
            $image_path = null;
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    Log::info('Valid image');
                    $validated = $request->validate([
                        'image' => 'mimes:jpg,png,jpeg,webp,gif,svg|max:1024',
                    ]);

                    $extension = $request->image->extension();
                    $request->image->storeAs('/products', $product->id.'_img.'.$extension);
                    $image_path = $product->id.'_img.'.$extension;
                }
            }

            $unit_cost = 0;
            if (!empty($request['unit_cost'])) {
                $unit_cost = $request['unit_cost'];
            }

            if (!empty($request['quantity_in'])) {
                $stock = new Stock();
                $stock->shop_id = $shop->id;
                $stock->product_id = $product->id;
                $stock->quantity_in = $request['quantity_in'];
                $stock->unit_cost = $unit_cost;
                $stock->stock_date = $now;
                $stock->source = 'Circle Counting';
                $stock->expire_date =  empty($request->expire_date) ? null : $request->expire_date;
                $stock->save();
            }

            //Add this product to category
            $category = Category::where('id', $request['category_id'])->where('shop_id', $shop->id)->first();
            if (!is_null($category)) {
                $category->products()->attach($product);
            }
            $newbarcode = null;
            $settings = Setting::where('shop_id', $shop->id)->first();
            if ($settings->generate_barcode) {
                $codes = array();
                $bcodes = $shop->products()->select('barcode')->get();
                foreach ($bcodes as $key => $bcode) {
                    array_push($codes, $bcode->barcode);
                }
                if (count($codes) > 0) {
                    $max_code = max($codes);
                    $newbarcode = (int)$max_code + 1;
                }
            } else {
                $newbarcode = $request['barcode'];
            }

            $instock = $request['quantity_in'];
            $product->in_stock = $instock;
            $product->location = $request['location'];
            $product->product_code = $request['product_code'];
            $product->unit_cost = $unit_cost;
            $product->retail_price = $request['retail_price'];
            $product->image_url = $image_path;
            $product->wholesale_price = $request['wholesale_price'];
            $product->barcode = $newbarcode;
            $product->description = $request['description'];
            $product->time_created = $now;
            $product->brand = $request['brand'];
            $product->model = $request['model'];
            $product->type = $request['type'];
            $product->size = $request['size'];
            $product->color = $request['color'];
            $product->length = $request['length'];
            $product->width = $request['width'];
            $product->thick = $request['thick'];
            $product->height = $request['height'];
            $product->volume = $request['volume'];
            $product->weight = $request['weight'];
            $product->save();

            $prod_unit = new ProductUnit();
            $prod_unit->product_id = $product->id;
            $prod_unit->unit_name = $product->basic_uom;
            $prod_unit->is_basic = true;
            $prod_unit->qty_equal_to_basic = 1;
            $prod_unit->unit_price = $request['retail_price'];
            $prod_unit->save();

            Log::info('Product added to '.$shop->name);
            $message = 'Your product was added successfully!';
            return redirect()->back()->with('success', $message);
        } else {
            $message = 'This product already exists in your shop product list';
            Log::info($message.' in '.$shop->name);
            return redirect()->back()->with('info', $message);
        }
    }

    public function checkProductNo(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $checkprodno = $shop->products()->where('product_code', $request['product_code'])->first();
        if (!is_null($checkprodno)) {
            return response()->json(['exists' => 1]);
        }else{
            return response()->json(['exists' => 0]);
        }
    }


    public function import(Request $request)
    {
        set_time_limit(1800);
        $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) {
            return \Redirect::to('products')->withErrors($validator);
        } else {
            $import = new ProductsImport;
            Excel::import($import, request()->file('file'));
            $insertedRows = $import->getRowCount();
            // if ($insertedRows > 0) {
                $message = 'Products uploaded Finished';
                return redirect('products')->with('info', $message);
            // }else{
            //     $message = 'No Product uploaded. Please check Your file, Make sure your column titles match the sample file provided';
            //     return redirect('products')->with('info', $message);
            // }
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
        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find(decrypt($id));
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        $settings = Setting::where('shop_id', $shop->id)->first();

        if (is_null($product)) {
            return redirect('products')->with('info', 'Sorry! The item your trying to view does not exist.');
        } else {
            $category = $product->categories()->where('shop_id', $shop->id)->first();
            // Log::info($category);
            $stocks = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('stocks.shop_id', $shop->id)->where('quantity_in', '>', 0)->select('stocks.id as id', 'purchase_id', 'source', 'stocks.quantity_in as quantity_in', 'is_utilized', 'quantity_out', 'stocks.unit_cost as unit_cost', 'stocks.stock_date as created_at', 'stocks.expire_date as exp_date')->orderBy('stock_date', 'desc')->get();
            $damages = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->get();

            $transfers = TransferOrder::where('transfer_orders.shop_id', $shop->id)->join('transfer_order_items', 'transfer_order_items.transfer_order_id', '=', 'transfer_orders.id')->where('transfer_order_items.product_id', $product->id)->select('transfer_orders.order_no as order_no', 'transfer_orders.order_date as order_date', 'transfer_orders.destination_id as destination_id', 'transfer_orders.reason as reason', 'transfer_orders.user_id as user_id', 'transfer_order_items.quantity as quantity', 'transfer_order_items.created_at as created_at')->orderBy('transfer_order_items.created_at', 'desc')->get();

            $t_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
            $t_out = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
            $t_dam = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
            $t_transfer = TransferOrder::where('transfer_orders.shop_id', $shop->id)->join('transfer_order_items', 'transfer_order_items.transfer_order_id', '=', 'transfer_orders.id')->where('transfer_order_items.product_id', $product->id)->sum('quantity');

            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
            $productunits = ProductUnit::where('product_id', $product->id)->get();

            $diff_qty = StockCorrection::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('diff_qty');

            $stockcorrections = StockCorrection::where('stock_corrections.shop_id', $shop->id)->where('product_id', $product->id)->join('users', 'users.id', '=', 'stock_corrections.user_id')->join('products', 'products.id','=', 'stock_corrections.product_id')->select('stock_corrections.id as id', 'first_name', 'last_name', 'name', 'correction_qty', 'stock_corrections.in_stock as in_stock', 'diff_qty', 'stock_corrections.time_created as time_created', 'reason')->get();

            $pchanges = PriceChange::where('product_id', $product->id)->where('shop_id', $shop->id)->join('users', 'users.id', '=', 'price_changes.user_id')->select('price_changes.id as id', 'price_changes.created_at as created_at', 'first_name', 'retail_price','wholesale_price')->get();

            $units = UnitMeasure::select('unit_name')->get();
            $page = 'Product details';
            $title = $product->slug;
            $title_sw = $product->slug;

            return view('products.show', compact('page', 'title', 'title_sw', 'product', 'stocks', 'damages', 'transfers', 't_transfer', 't_in', 't_out', 't_dam', 'returned', 'shop', 'bsetting', 'settings', 'productunits', 'units', 'pchanges', 'diff_qty', 'stockcorrections', 'category'));
        }
    }

    public function activateDeactivateProduct($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find(decrypt($id));
        if (!is_null($product)) {
            if ($product->is_active) {
                $product->is_active = false;
                $product->save();
                return redirect()->route('products.show', encrypt($product->id))->with('success', 'Product '.$product->name.' DEACTIVATED successfully');
            }else{
                $product->is_active = true;
                $product->save();
                return redirect()->route('products.show', encrypt($product->id))->with('success', 'Product '.$product->name.' ACTIVATED successfully');
            }
        }else{
            return redirect('products')->with('error', 'Product not Found');
        }
    }

    public function inActiveProducts()
    {
        $page = 'Deactivated Products';
        $title = 'Deactivated Products';
        $shop = Shop::find(Session::get('shop_id'));
        $currency = '';
        $shopcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (!is_null($shopcurr)) {
            $currency = $shopcurr->code;
        }
        $products = $shop->products()->where('is_active', false)->get();

        return view('products.deactivated', compact('page', 'title', 'shop', 'currency', 'products'));
    }

    public function productSalesHistory($id, Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $product = Product::find(decrypt($id));

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
          
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'].' 00:00:00';
            $end = $request['sale_date'].' 23:59:59';
            $is_post_query = true;
        }else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        if (!is_null($product)) {
            $sale_items = AnSaleItem::where('product_id', $product->id)->where('an_sale_items.is_deleted', false)->where('an_sale_items.shop_id', $shop->id)->whereBetween('an_sale_items.time_created', [$start, $end])->join('products','products.id', '=','an_sale_items.product_id')->join('an_sales', 'an_sales.id', '=', 'an_sale_items.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('users', 'users.id', '=', 'an_sales.user_id')->select('products.id as p_id', 'products.name as name', 'products.basic_uom as basic_uom', 'an_sale_items.product_id as product_id', 'an_sale_items.id as id', 'an_sale_items.quantity_sold as quantity_sold', 'an_sale_items.unit_cost as unit_cost', 'an_sale_items.buying_price as buying_price', 'an_sale_items.retail_price as retail_price', 'an_sale_items.discount as discount', 'an_sale_items.price as price', 'an_sale_items.total_discount as total_discount', 'an_sale_items.tax_amount as tax_amount', 'an_sale_items.time_created as created_at', 'customers.name as customer', 'users.first_name as first_name')->orderBy('an_sale_items.time_created', 'desc')->get();

            $t_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_in');
            $t_out = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->sum('quantity_sold');
            $t_dam = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
            $t_transfer = TransferOrder::where('transfer_orders.shop_id', $shop->id)->join('transfer_order_items', 'transfer_order_items.transfer_order_id', '=', 'transfer_orders.id')->where('transfer_order_items.product_id', $product->id)->sum('quantity');

            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');

            $diff_qty = StockCorrection::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('diff_qty');

            $page = 'Product Sales History';
            $title = $product->name;
            $title_sw = $product->name;
            
            return view('products.product-sale-history', compact('page', 'title', 'title_sw', 'product', 'sale_items', 't_transfer', 't_in', 't_out', 't_dam', 'returned', 'diff_qty', 'shop', 'settings', 'is_post_query', 'start_date', 'end_date'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $product = $shop->products()->where('id', decrypt($id))->first();
        if (is_null($product)) {
            return redirect('forbiden');
        } else {

            $page = 'Edit product';
            $title = 'Edit product';
            $title_sw = 'Hariri Bidhaa';

            $units = UnitMeasure::select('unit_name')->get();
            $brands = Brand::where('shop_id', $shop->id)->select('name')->get();
            $settings = Setting::where('shop_id', $shop->id)->first();
            return view('products.edit', compact('page', 'title', 'title_sw', 'shop', 'units', 'product', 'brands', 'settings'));
        }
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
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find(decrypt($id));
        if (!is_null($product)) {
                
            $slug = $request['name'];
            if (!empty($request['brand'])) {
                $slug .= ' - '.$request['brand'];
            }
            if (!empty($request['model'])) {
                $slug .= ' - '.$request['model'];
            }
            if (!empty($request['color'])) {
                $slug .= ' - '.$request['color'];
            }
            if (!empty($request['size'])) {
               $slug .= ' - '.$request['size'];
            }
            if (!empty($request['thick'])) {
                $slug .= ' - '.$request['thick'];
            }
            if (!empty($request['length'])) {
                $slug .= ' - '.$request['length'];
            }
            if (!empty($request['width'])) {
                $slug .= ' - '.$request['width'];
            }
            if (!empty($request['height'])) {
                $slug .= ' - '.$request['height'];
            }
            if (!empty($request['volume'])) {
                $slug .= ' - '.$request['volume'];
            }
            if (!empty($request['weight'])) {
                $slug .= ' - '.$request['weight'];
            }
            $product->name = $request['name'];
            $product->basic_uom = $request['basic_uom'];
            $product->slug = $slug;
            $product->save();
            
            $image_path = $product->image_url;
            //Upload Product Image
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {
                    Log::info('Valid image');
                    $validated = $request->validate([
                        'image' => 'mimes:jpg,png,jpeg,webp,gif,svg|max:1024',
                    ]);

                    $old_path = storage_path('/products/'.$image_path);
                    if (File::exists($old_path)) {
                        unlink($old_path);
                    }

                    $extension = $request->image->extension();
                    $request->image->storeAs('/products', $product->id.'_img.'.$extension);
                    $image_path = $product->id.'_img.'.$extension;
                }
            }

            $newbarcode = null;
            $settings = Setting::where('shop_id', $shop->id)->first();
            if ($settings->use_barcode && empty($request['barcode'])) {
                $codes = array();
                $bcodes = $shop->products()->select('barcode')->get();
                foreach ($bcodes as $key => $bcode) {
                    array_push($codes, $bcode->barcode);
                }
                $max_code = max($codes);
                if (is_numeric($max_code)) {
                    $newbarcode = $max_code + 1;
                }
            } else {
                $newbarcode = $request['barcode'];
            }

            $product->barcode = $newbarcode;
            $product->description = $request['description'];
            $product->product_code  = $request['product_code'];
            $product->location  = $request['location'];
            $product->brand = $request['brand'];
            $product->model = $request['model'];
            $product->type = $request['type'];
            $product->size = $request['size'];
            $product->color = $request['color'];
            $product->length = $request['length'];
            $product->width = $request['width'];
            $product->thick = $request['thick'];
            $product->height = $request['height'];
            $product->volume = $request['volume'];
            $product->weight = $request['weight'];
            $product->image_url = $image_path;
            $product->is_by_product = $request['is_by_product'];
            $product->save();
            
            $prod_unit = ProductUnit::where('product_id', $product->id)->first();
            if (is_null($prod_unit)) {
                $prod_unit = new ProductUnit();
                $prod_unit->product_id = $product->id;
                $prod_unit->unit_name = $product->basic_uom;
                $prod_unit->is_basic = true;
                $prod_unit->qty_equal_to_basic = 1;
                $prod_unit->unit_price = $product->retail_price;
                $prod_unit->save();
            }

            $message = 'Your product was updated successfully!';

            return redirect('/products')->with('success', $message);
        }else{

            $message = 'Product not Found!';
            return redirect('/products')->with('error', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find(decrypt($id));
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        $sales = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $product->id)->count();
        $transfers = TransferOrderItem::where('shop_id', $shop->id)->where('product_id', $product->id)->count();
        $stocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
        if ($sales > 0 || $transfers > 0 || $stocks->count() > 2) {
            $message = 'Item '.$product->name.' for '.$shop->name.' can not be deleted';
            Log::info($message);
            return redirect()->back()->with('info', $message);
        }else{ 
            $stocks = Stock::where('product_id', $product->id)->get();
            foreach ($stocks as $key => $value) {
                $value->delete();
            }
            foreach ($shop->categories()->get() as $key => $category) {
                $catprod = $category->products()->where('product_id', $product->id)->first();
                if (!is_null($catprod)) {
                    $category->products()->detach($catprod);
                }
            }
            $product->delete();
            $message = 'You have successfully removed this product from your product list!';
            return redirect()->back()->with('success', $message);
        }
    }

    public function deleteMultiple(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));

        $user = Auth::user();
        if (!empty($request->input('id'))) {

            foreach ($request->input('id') as $key => $id) {
                $product = Product::find($id);

                $sales = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $product->id)->count();
                $transfers = TransferOrderItem::where('shop_id', $shop->id)->where('product_id', $product->id)->count();
                $stocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
                if ($sales > 0 || $transfers > 0 || $stocks->count() > 2) {
                    $message = 'Item '.$product->name.' for '.$shop->name.' can not be deleted';
                    Log::info($message);
                }else{
                    $stocks = Stock::where('product_id', $product->id)->get();
                    foreach ($stocks as $key => $value) {
                        $value->delete();
                    }
                    foreach ($shop->categories()->get() as $key => $category) {
                        $catprod = $category->products()->where('product_id', $product->id)->first();
                        if (!is_null($catprod)) {
                            $category->products()->detach($catprod);
                        }
                    }
                    $product->delete();
                }
            }
            $success = 'Products were  successfully removed from your product list!';
            return redirect('products')->with('success', $success);
        } else {

            $warning = 'No items selected. Please select at least one item';
            return redirect('products')->with('warning', $warning);
        }
    }

    public function postPrice(Request $request)
    {
        $product = Product::find($request['product_id']);
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
       // $shops = $user->shops()->get();
        // foreach ($shops as $key => $shop) {
            $product = $shop->products()->where('id', $product->id)->first();
            
            if (!is_null($product)) {
                $product->retail_price = $request['new_unit_price'];
                $product->wholesale_price = $request['wholesale_price'];
                $product->save();

                $pchange = new PriceChange();
                $pchange->product_id = $product->id;
                $pchange->shop_id = $shop->id;
                $pchange->user_id = $user->id;
                $pchange->retail_price = $product->retail_price;
                $pchange->wholesale_price = $product->wholesale_price;
                $pchange->save();

                $prod_unit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                if (!is_null($prod_unit)) {
                    $prod_unit->unit_price = $request['new_unit_price'];
                    $prod_unit->save();
                }
            }
        // }
        $message = 'Price was successfully updated';

        return redirect()->route('products.show', encrypt($product->id))->with('message', $message);
    }

    public function newBuyPrice(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find($request['product_id']);
        $product = $shop->products()->where('id', $product->id)->first();
        $product->unit_cost = $request['unit_cost'];
        $product->save();

        $message = 'Price was successfully updated';

        return redirect()->route('products.show', encrypt($product->id))->with('message', $message);
    }


    public function newReorderPoint(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $product = Product::find($request['product_id']);
        $product = $shop->products()->where('id', $product->id)->first();
        $product->reorder_point = $request['reorder_point'];
        $product->save();

        $message = 'Re-order Point was successfully updated';

        return redirect()->route('products.show', encrypt($product->id))->with('message', $message);
    }

    public function priceList(Request $request)
    {
        $page = 'Price List';
        $title = 'Price list';
        $title_sw = 'Orodha ya Bei';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        $prices = $shop->products()->get();
        $serv_prices = $shop->services()->get(); 
        $pnos = $shop->products()->whereNotNull('product_code')->count();
        $pls = $shop->products()->whereNotNull('location')->count();

        return view('products.pricing', compact('page', 'title', 'title_sw', 'shop', 'prices', 'pnos', 'pls', 'serv_prices', 'settings'));
    }

    //Auto generate barcodes
    public function generateBarcode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        $products = $shop->products()->orderBy('name', 'asc')->get();

        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $shidlen = 0;
        $code = '';
        $codes = array();
        if ($bsetting->code_type === 'EAN8') {
            $shidlen = strlen($shop->id);
            foreach ($products as $key => $product) {
                $code = $shop->id . str_pad($key + 1, 7 - $shidlen, '0', STR_PAD_LEFT);
                array_push($codes, $code);
                $product->barcode = $code;
                $product->save();
            }
        } else {
            $shidlen = strlen($shop->id);
            foreach ($products as $key => $product) {
                $code = $shop->id . str_pad($key + 1, $bsetting->code_length - $shidlen, '0', STR_PAD_LEFT);
                array_push($codes, $code);
                $product->barcode = $code;
                $product->save();
            }
        }

        $success = 'Barcodes were generate successfully';
        return redirect('products')->with('success', $success);
    }

    public function changeLocation(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $product = $shop->products()->where('id', $request['product_id'])->first();

        if (!is_null($product)) {
            $product->location = $request['location'];
            $product->save();
        }

        return redirect()->back()->with('success', 'Product location updated successfully');
    }

    public function setActualPrices($id)
    {

        $product = Product::find(decrypt($id));

        $shop = Shop::find(Session::get('shop_id'));
        $product = $shop->products()->where('id', $product->id)->first();
        if (!is_null($product)) {
            $items = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $shop->id)->get();
            foreach ($items as $key => $item) {
                $item->unit_cost = $product->unit_cost;
                $item->buying_price = $item->quantity_sold * $item->unit_cost;
                $item->retail_price = $product->retail_price;
                $item->price = $item->retail_price * $item->quantity_sold;
                $item->tax_amount = ($product->price_with_vat - $product->retail_price) * $item->quantity_sold;
                $item->save();

                $sale = AnSale::find($item->an_sale_id);
                $amountp = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                $discountp = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $amounts = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                $discounts = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                $taxp = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                $taxs = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');

                $sale->sale_amount = ($amountp+$amounts);
                $sale->sale_discount = ($discountp+$discounts);
                $sale->tax_amount = ($taxp+$taxs);
                $sale->save();

                $this->updateSaleStatus($sale);

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->amount = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $acctrans->save();
                }
            }
        }
        $success = 'Items updated successfully';
        return redirect()->route('products.show', encrypt($product->id))->with('success', $success);
    }

    public function updateSaleStatus($sale)
    {
        $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
        $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
        $netsales_amount = $tnetsales-$tnetreturn;
        if ($netsales_amount == $sale->sale_amount_paid) {
            $sale->status = 'Paid';
            $sale->is_paid = true;
            $sale->time_paid = \Carbon\Carbon::now();
            $sale->save();
        }elseif ($netsales_amount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
            $sale->status = 'Partially Paid';
            $sale->time_paid = null;
            $sale->is_paid = false;
            $sale->save();
        }elseif ($netsales_amount < $sale->sale_amount_paid) {
            $sale->status = 'Excess Paid';
            $sale->is_paid = true;
            $sale->time_paid = \Carbon\Carbon::now();
            $sale->save();
        }else{
            $sale->status = 'Unpaid';
            $sale->time_paid = null;
            $sale->is_paid = false;
            $sale->save();
        }
    }
    
    public function download()
    {
        if (File::exists(public_path('sample-products.xlsx'))) {
            return response()->download(public_path('sample-products.xlsx'));
        } else {
            return redirect()->back()->witherrormessage('NO such File Exists');
        }
    }
}