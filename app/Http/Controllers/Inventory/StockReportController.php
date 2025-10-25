<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use Log;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Product;
use App\Models\AnSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\TransferOrder;
use App\Models\SaleReturnItem;
use App\Models\Setting;
use App\Models\DailyClosingStockValue;
use App\Models\PurchaseOrderItem;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        $user = Auth::user();
        $shops = $company->shops()->get();
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        $instocks = array();
        $stock_ins = null;
        if (!$settings->is_filling_station) {

            $currstatus = 'All Items';
            $statuses = array(
                ['value' => 'All Items'],
                ['value' => 'In Stock'],
                ['value' => 'Low Stock'],
                ['value' => 'Out of Stock']
            );

            $start_date = null;
            $end_date = null;
            //check if user opted for date range
            $is_post_query = false;
            $currstore = null;

            if (empty($request['status']) && empty($request['store'])) {
                $currstore = Shop::find(Session::get('shop_id'));
            } elseif (!empty($request['store'])) {
                $currstore = Shop::find($request['store']);
            }

            $locations =null;
            $location = '';
            $categories = null;
            $categoryid = '';
            $prodstocks = array();
            if (!is_null($currstore)) {
                $shop = $currstore;
                $locations = $shop->products()->whereNotNull('location')->select('location')->get();
                $categories = $shop->categories()->select('id', 'name')->get();
                $category = Category::find($request['category_id']);
                if (!is_null($category)) {
                    $categoryid = $category->id;
                    if ($category->children->count() > 0) {
                        if ($category->products()->count() > 0) {
                            $ctproducts = $category->products()->join('product_shop', 'product_id', '=', 'products.id')->where('shop_id', $shop->id)->where('is_active', true)->get([
                                \DB::raw('products.id as id'),
                                \DB::raw('slug as name'),
                                \DB::raw('in_stock as in_stock'),
                                \DB::raw('status as status')
                            ]);;
                            foreach ($ctproducts as $key => $value) {
                                array_push($prods, $value);
                            }
                        }

                        foreach ($category->catProducts() as $key => $value) {
                           $ctproduct = $shop->products()->where('id', $value['id'])->where('is_active', true)->get([
                                \DB::raw('products.id as id'),
                                \DB::raw('slug as name'),
                                \DB::raw('in_stock as in_stock'),
                                \DB::raw('status as status')
                            ]);;
                           if (!is_null($ctproduct)) {
                               array_push($prods, $ctproduct);
                           }
                        }
                        
                        $cat_products = $prods;
                    }else{
                        $cat_products = $category->products()->join('product_shop', 'product_id', '=', 'products.id')->where('shop_id', $shop->id)->where('is_active', true)->get([
                            \DB::raw('products.id as id'),
                            \DB::raw('slug as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);;
                    }

                    foreach ($cat_products as $key => $stock) {
                        array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'in_stock' => $stock->in_stock, 'status' => $stock->status]);
                    }
                }elseif (!empty($request['location'])) {
                    $location = $request['location'];
                    if (empty($request['status']) || $request['status'] == 'All Items') {
                        $products = $shop->products()->where('location', $location)->where('is_active', true)->get([
                            \DB::raw('id'),
                            \DB::raw('name as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);
                    } else {
                        $products = $shop->products()->where('location', $location)->where('is_active', true)->where('status', $request['status'])->get([
                            \DB::raw('id'),
                            \DB::raw('name as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);
                        $currstatus = $request['status'];
                    }

                    foreach ($products as $key => $stock) {
                        array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'in_stock' => $stock->in_stock, 'status' => $stock->status]);
                    }
                }else{
                    if (empty($request['status']) || $request['status'] == 'All Items') {
                        $products = $shop->products()->where('is_active', true)->get([
                            \DB::raw('id'),
                            \DB::raw('name as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);
                    } else {
                        $products = $shop->products()->where('is_active', true)->where('status', $request['status'])->get([
                            \DB::raw('id'),
                            \DB::raw('name as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);
                        $currstatus = $request['status'];
                    }

                    foreach ($products as $key => $stock) {
                        array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'in_stock' => $stock->in_stock, 'status' => $stock->status]);
                    }
                }
            } else {
                foreach ($shops as $key => $shop) {
                    if (!empty($request['status'])) {
                        if ($request['status'] == 'All Items') {
                            $products = $shop->products()->where('is_active', true)->get([
                                \DB::raw('id'),
                                \DB::raw('name as name'),
                                \DB::raw('in_stock as in_stock'),
                                \DB::raw('status as status')
                            ]);
                        } else {
                            $products = $shop->products()->where('is_active', true)->where('status', $request['status'])->get([
                                \DB::raw('id'),
                                \DB::raw('name as name'),
                                \DB::raw('in_stock as in_stock'),
                                \DB::raw('status as status')
                            ]);
                            $currstatus = $request['status'];
                        }
                    } else {
                        $products = $shop->products()->where('is_active', true)->get([
                            \DB::raw('id'),
                            \DB::raw('name as name'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('status as status')
                        ]);
                    }
                    foreach ($products as $key => $stock) {
                        array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'in_stock' => $stock->in_stock, 'status' => $stock->status]);
                    }
                }
            }


            $result = array();
            foreach ($prodstocks as $value) {
                if (isset($result[$value["name"]])) {
                    $result[$value["name"]]["in_stock"] += $value["in_stock"];
                    if ($result[$value['name']]['status'] == 'Out of Stock' && ($value['status'] == 'Low Stock' || $value['status'] == 'In Stock')) {
                        $result[$value['name']]['status'] = $value['status'];
                    }elseif ($result[$value['name']]['status'] == 'Low Stock' && $value['status'] == 'In Stock') {
                        $result[$value['name']]['status'] = $value['status'];
                        // Log::info('Status changed from '.$result[$value['name']]['status'].' to '.$value['status']);
                    }
                } else {
                    $result[$value["name"]] = $value;
                }
            }

            // return $result;

            $stockstatus = [];
            foreach ($result as $key => $value) {
                $shopsstocks = array();
                foreach ($shops as $key => $store) {
                    $sqty = 0;
                    $sq = $store->products()->where('product_id', $value['id'])->first();
                    if (!is_null($sq)) {
                        $sqty = $sq->pivot->in_stock;
                    }
                    array_push($shopsstocks, [$store->name => $sqty]);
                }
                array_push($stockstatus, array_merge($value, $shopsstocks));
            }

            // return $stockstatus;

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Reports';
            $title = 'Stock Status Report';
            $title_sw = 'Ripoti ya hali ya Stock';
            return view('reports.inventory.index', compact('page', 'title', 'title_sw', 'reporttime', 'instocks', 'shop', 'currstatus', 'statuses', 'is_post_query', 'start_date', 'end_date', 'settings', 'stockstatus', 'shops', 'currstore', 'locations', 'location', 'categories', 'categoryid'));
        } else {
            $now = Carbon::now();
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $start_date = $start->format('Y-m-d');
            $end_date = $end->format('Y-m-d');

            //check if user opted for date range
            $is_post_query = false;
            if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'] . ' 00:00:00';
                $end = $request['end_date'] . ' 23:59:59';
                $is_post_query = true;
            }

            $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
            $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


            $products = $shop->products()->where('is_active', true)->get();

            $product = null;
            $damages = null;
            if (!empty($request->product_id)) {
                $product = Product::find($request->product_id);
                $damages = ProdDamage::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('time_created', [$start, $end])->join('products', 'products.id', '=', 'prod_damages.product_id')->select('products.name as name', 'time_created', 'deph_measure', 'in_stock', 'quantity')->get();

                $stock_ins = $shop->products()->where('is_active', true)->where('product_id', $product->id)->join('stocks', 'stocks.product_id', '=', 'product_id')->where('stocks.shop_id', $shop->id)->where('stocks.is_deleted', false)->groupBy('product_id')->get([
                    \DB::raw('name as name'),
                    \DB::raw('SUM(quantity_in) as stock_in'),
                    \DB::raw('in_stock as in_stock'),
                    \DB::raw('status as status')
                ]);

                foreach ($stock_ins as $key => $stock) {
                    $returned = SaleReturnItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    $sold = AnSaleItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                    $damage = ProdDamage::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    $transfered = TransferOrderItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    array_push($instocks, array_merge($stock->toArray(), ['returned' => $returned], ['sold' => $sold], ['transfered' => $transfered], ['damage' => $damage]));
                }
            } else {
                $damages = ProdDamage::where('shop_id', $shop->id)->whereBetween('time_created', [$start, $end])->join('products', 'products.id', '=', 'prod_damages.product_id')->select('products.name as name', 'time_created', 'deph_measure', 'in_stock', 'quantity')->get();

                $stock_ins = $shop->products()->where('is_active', true)->join('stocks', 'stocks.product_id', '=', 'product_id')->where('stocks.shop_id', $shop->id)->where('stocks.is_deleted', false)->groupBy('product_id')->get([
                    \DB::raw('name as name'),
                    \DB::raw('SUM(quantity_in) as stock_in'),
                    \DB::raw('in_stock as in_stock'),
                    \DB::raw('status as status')
                ]);

                foreach ($stock_ins as $key => $stock) {
                    $returned = SaleReturnItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    $sold = AnSaleItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                    $damage = ProdDamage::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    $transfered = TransferOrderItem::where('product_id', $stock->pivot->product_id)->where('shop_id', $shop->id)->sum('quantity');
                    array_push($instocks, array_merge($stock->toArray(), ['returned' => $returned], ['sold' => $sold], ['transfered' => $transfered], ['damage' => $damage]));
                }
            }

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Reports';
            $title = 'Stock Status Report';
            $title_sw = 'Ripoti ya hali ya Stock';
            return view('reports.inventory.index-filling', compact('page', 'title', 'title_sw', 'reporttime', 'instocks', 'shop', 'is_post_query', 'start_date', 'end_date', 'settings', 'damages', 'product', 'products', 'duration', 'duration_sw'));
        }
    }

    public function transfers(Request $request)
    {
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($shop)) {

            $now = Carbon::now();
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $start_date = $start->format('Y-m-d');
            $end_date = $end->format('Y-m-d');

            //check if user opted for date range
            $is_post_query = false;
            if (!empty($request['stock_date'])) {
                $start_date = $request['stock_date'];
                $end_date = $request['stock_date'];
                $start = $request['stock_date'] . ' 00:00:00';
                $end = $request['stock_date'] . ' 23:59:59';
                $is_post_query = true;
            } else if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'] . ' 00:00:00';
                $end = $request['end_date'] . ' 23:59:59';
                $is_post_query = true;
            }
            $shops = $user->shops()->where('is_warehouse', false)->select('id', 'name')->get();

            $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
            $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';
            $transfers = collect([]);
            $currstore = null;
            if (!empty($request['store'])) {
                $currstore = Shop::find($request['store']);
                $transfers = TransferOrderItem::where('transfer_order_items.shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('transfer_orders', 'transfer_orders.id', '=', 'transfer_order_items.transfer_order_id')->where('destination_id', $currstore->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('order_no', 'transfer_order_items.created_at', 'name', 'quantity', 'source_unit_price', 'transfer_order_id', 'destination_id')->get();
            }else{
                $transfers = TransferOrderItem::where('transfer_order_items.shop_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('transfer_orders', 'transfer_orders.id', '=', 'transfer_order_items.transfer_order_id')->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('order_no', 'transfer_order_items.created_at', 'name', 'quantity', 'source_unit_price', 'transfer_order_id', 'destination_id')->get();
            }

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Reports';
            $title = 'Stock Transfer Report';
            $title_sw = 'Ripoti ya Uhamishaji wa Stock';
            return view('reports.inventory.transfer', compact('page', 'title', 'title_sw', 'reporttime', 'transfers', 'shop', 'is_post_query', 'start_date', 'end_date', 'settings', 'duration', 'duration_sw', 'shops', 'currstore'));
        }
    }

    public function transfersReceived(Request $request)
    {
        $user = Auth::user();
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        if (!is_null($shop)) {

            $now = Carbon::now();
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $start_date = $start->format('Y-m-d');
            $end_date = $end->format('Y-m-d');

            //check if user opted for date range
            $is_post_query = false;
            if (!empty($request['stock_date'])) {
                $start_date = $request['stock_date'];
                $end_date = $request['stock_date'];
                $start = $request['stock_date'] . ' 00:00:00';
                $end = $request['stock_date'] . ' 23:59:59';
                $is_post_query = true;
            } else if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'] . ' 00:00:00';
                $end = $request['end_date'] . ' 23:59:59';
                $is_post_query = true;
            }
            $shops = $user->shops()->where('is_warehouse', false)->select('id', 'name')->get();

            $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
            $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';
            $transfers = collect([]);
            $currstore = null;
            if (!empty($request['store'])) {
                $currstore = Shop::find($request['store']);
                $transfers = TransferOrderItem::where('destination_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('transfer_orders', 'transfer_orders.id', '=', 'transfer_order_items.transfer_order_id')->where('transfer_order_items.shop_id', $currstore->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('order_no', 'transfer_order_items.created_at', 'name', 'quantity', 'source_unit_price', 'transfer_order_id', 'destination_id')->get();
            }else{
                $transfers = TransferOrderItem::where('destination_id', $shop->id)->whereBetween('transfer_order_items.created_at', [$start, $end])->join('transfer_orders', 'transfer_orders.id', '=', 'transfer_order_items.transfer_order_id')->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('order_no', 'transfer_order_items.created_at', 'name', 'quantity', 'source_unit_price', 'transfer_order_id', 'destination_id')->get();
            }

            $crtime = \Carbon\Carbon::now();
            $reporttime = $crtime->toDayDateTimeString();
            $page = 'Stock Reports';
            $title = 'Stock Transfer Received Report';
            $title_sw = 'Ripoti ya Uhamishaji wa Stock Zilizopokelewa';
            return view('reports.inventory.received', compact('page', 'title', 'title_sw', 'reporttime', 'transfers', 'shop', 'is_post_query', 'start_date', 'end_date', 'settings', 'duration', 'duration_sw', 'shops', 'currstore'));
        }
    }

    public function reorderReports()
    {
        $shop = Shop::find(Session::get('shop_id'));

        $start_date = null;
        $end_date = null;
        $duration = null;
        $duration_sw = null;
        //check if user opted for date range
        $is_post_query = false;

        $products = $shop->products()->where('is_active', true)->whereRaw('in_stock <= reorder_point')->get();
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Reports';
        $title = 'Stock Reorder Report';
        $title_sw = 'Ripoti ya Kuagiza Stock';
        return view('reports.inventory.reorder', compact('page', 'title', 'title_sw', 'reporttime', 'shop', 'products',  'duration', 'duration_sw', 'is_post_query', 'start_date', 'end_date'));
    }
    
    public function stockCapital(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        $shops = $company->shops()->select('id', 'name')->get();
        $currstore = null;
        $locations =null;
        $location = '';
        $categories = null;
        $categoryid = '';
        $prodstocks = array();
        $stockvalues = array(); 
        if (!empty($request['store'])) {
            if ($request['store'] != 'All') {
                $currstore = Shop::find($request['store']);
            }
        }else{
            $currstore = Shop::find(Session::get('shop_id'));
        }

        if (!is_null($currstore)) {
            
            $shop = $currstore;
            $locations = $shop->products()->whereNotNull('location')->select('location')->get();
            $categories = $shop->categories()->select('id', 'name')->get();
            $category = Category::find($request['category_id']);
            if (!is_null($category)) {
                $categoryid = $category->id;
                if ($category->children->count() > 0) {
                    if ($category->products()->count() > 0) {
                        $ctproducts = $category->products()->join('product_shop', 'product_id', '=', 'products.id')->where('shop_id', $shop->id)->where('is_active', true)->get([
                            \DB::raw('products.id as id'),
                            \DB::raw('slug as name'),
                            \DB::raw('basic_uom'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('unit_cost'),
                            \DB::raw('retail_price'),
                            \DB::raw('wholesale_price')
                        ]);;
                        foreach ($ctproducts as $key => $value) {
                            array_push($prods, $value);
                        }
                    }

                    foreach ($category->catProducts() as $key => $value) {
                       $ctproduct = $shop->products()->where('id', $value['id'])->where('is_active', true)->get([
                            \DB::raw('products.id as id'),
                            \DB::raw('slug as name'),
                            \DB::raw('basic_uom'),
                            \DB::raw('in_stock as in_stock'),
                            \DB::raw('unit_cost'),
                            \DB::raw('retail_price'),
                            \DB::raw('wholesale_price')
                        ]);;
                       if (!is_null($ctproduct)) {
                           array_push($prods, $ctproduct);
                       }
                    }
                        
                    $cat_products = $prods;
                }else{
                    $cat_products = $category->products()->join('product_shop', 'product_id', '=', 'products.id')->where('shop_id', $shop->id)->where('is_active', true)->get([
                        \DB::raw('products.id as id'),
                        \DB::raw('slug as name'),
                        \DB::raw('basic_uom'),
                        \DB::raw('in_stock as in_stock'),
                        \DB::raw('unit_cost'),
                        \DB::raw('retail_price'),
                        \DB::raw('wholesale_price')
                    ]);;
                }

                foreach ($cat_products as $key => $stock) {
                    array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'basic_uom' => $stock->basic_uom, 'in_stock' => $stock->in_stock, 'unit_cost' => $stock->unit_cost, 'retail_price' => $stock->retail_price, 'wholesale_price' => $stock->wholesale_price]);
                }
            }elseif (!empty($request['location'])) {
                $location = $request['location'];
                $products = $shop->products()->where('location', $location)->where('is_active', true)->get([
                    \DB::raw('id'),
                    \DB::raw('slug as name'),
                    \DB::raw('basic_uom'),
                    \DB::raw('in_stock as in_stock'),
                    \DB::raw('unit_cost'),
                    \DB::raw('retail_price'),
                    \DB::raw('wholesale_price')
                ]);
                foreach ($products as $key => $stock) {
                    array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'basic_uom' => $stock->basic_uom, 'in_stock' => $stock->in_stock, 'unit_cost' => $stock->unit_cost, 'retail_price' => $stock->retail_price, 'wholesale_price' => $stock->wholesale_price]);
                }
            }else{
                $products = $shop->products()->where('is_active', true)->get([
                    \DB::raw('id'),
                    \DB::raw('slug as name'),
                    \DB::raw('basic_uom'),
                    \DB::raw('in_stock as in_stock'),
                    \DB::raw('unit_cost'),
                    \DB::raw('retail_price'),
                    \DB::raw('wholesale_price')
                ]);

                foreach ($products as $key => $stock) {
                    array_push($prodstocks, ['id' => $stock->id, 'name' => $stock->name, 'basic_uom' => $stock->basic_uom, 'in_stock' => $stock->in_stock, 'unit_cost' => $stock->unit_cost, 'retail_price' => $stock->retail_price, 'wholesale_price' => $stock->wholesale_price]);
                }
            }

            foreach ($prodstocks as $key => $value) {
                if ($value['in_stock'] > 0) {
                    $lstock = Stock::where('shop_id', $shop->id)->where('product_id', $value['id'])->where('is_deleted', false)->where('is_utilized', false)->latest()->first();
                    $unit_cost = $value['unit_cost'];
                    if (!is_null($lstock) && $lstock->unit_cost != $value['unit_cost']) {
                        $unit_cost = $lstock->unit_cost;
                    }
                    array_push($stockvalues, ['name' => $value['name'], 'basic_uom' => $value['basic_uom'], 'qty' => $value['in_stock'], 'unit_cost' => $unit_cost, 'retail_price' => $value['retail_price'], 'wholesale_price' => $value['wholesale_price']]);
                }
            }
        } else {
            foreach ($shops as $key => $shop) {
                $products = $shop->products()->where('is_active', true)->get([
                    \DB::raw('id'),
                    \DB::raw('slug as name'),
                    \DB::raw('basic_uom'),
                    \DB::raw('in_stock as in_stock'),
                    \DB::raw('unit_cost'),
                    \DB::raw('retail_price'),
                    \DB::raw('wholesale_price')
                ]);
                
                foreach ($products as $key => $product) {
                    if ($product->in_stock > 0) {
                        $lstock = Stock::where('shop_id', $shop->id)->where('product_id', $product->id)->where('is_deleted', false)->where('is_utilized', false)->latest()->first();
                        $unit_cost = $product->unit_cost;
                        if (!is_null($lstock) && $lstock->unit_cost != $product->unit_cost) {
                            $unit_cost = $lstock->unit_cost;
                        }
                        array_push($stockvalues, ['name' => $product->name, 'basic_uom' => $product->basic_uom, 'qty' => $product->in_stock, 'unit_cost' => $unit_cost, 'retail_price' => $product->retail_price, 'wholesale_price' => $product->wholesale_price]);
                    }
                }
            }
        }

        $products =  array_values($stockvalues);
        $shop = $company->shops()->first();
        if (!is_null($currstore)) {
            $shop = $currstore;
        }
        $settings = Setting::where('shop_id', $shop->id)->first();
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Capital Report';
        $title = 'Stock Capital Report';
        $title_sw = 'Ripoti ya Mtaji';
        return view('reports.inventory.capital', compact('page', 'title', 'title_sw', 'reporttime', 'products', 'shop', 'settings', 'currstore', 'shops', 'categories', 'categoryid', 'locations', 'location'));
    }

    public function initialStockCapital(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $stocks = Stock::where('stocks.shop_id', $shop->id)->where('is_deleted', false)->where('source', 'Circle Counting')->get();
        $shopproducts = [];
        foreach ($stocks as $key => $stock) {
            $product = $shop->products()->where('is_active', true)->where('product_id', $stock->product_id)->first();
            if (isset($shopproducts[$product->name]) && $shopproducts[$product->name]['unit_cost'] == $stock->unit_cost) {
                $shopproducts[$product->name]['qty'] += ($stock->quantity_in-$stock->quantity_out);
            }else{
                $shopproducts[$product->name]['name'] = $product->name;
                $shopproducts[$product->name]['basic_uom'] = $product->basic_uom;
                $shopproducts[$product->name]['qty'] = $stock->quantity_in-$stock->quantity_out;
                $shopproducts[$product->name]['unit_cost'] = $stock->unit_cost;
                $shopproducts[$product->name]['retail_price'] = $product->retail_price;
                $shopproducts[$product->name]['wholesale_price'] = $product->wholesale_price;
            }
        }

        $products =  array_values($shopproducts);
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Capital Report';
        $title = 'Initial Stock Capital Report';
        $title_sw = 'Ripoti ya Mtaji ya Awali';
        return view('reports.inventory.initial-stock-value', compact('page', 'title', 'title_sw', 'reporttime', 'products', 'shop', 'settings'));
    }

    public function stockTaking(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $products = $shop->products()->where('is_active', true)->get();

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'] . ' 00:00:00';
            $end = $request['stock_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
        $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


        $product = null;
        if (!empty($request['product_id'])) {
            $product = Product::find($request['product_id']);

            $stocks = Stock::where('shop_id', $shop->id)->where('product_id', $product->id)->where('is_deleted', false)->whereBetween('stocks.time_created', [$start, $end])->join('products', 'products.id', '=', 'stocks.product_id')->orderBy('time_created', 'desc')->get();
        } else {

            $stocks = Stock::where('shop_id', $shop->id)->where('is_deleted', false)->whereBetween('stocks.time_created', [$start, $end])->join('products', 'products.id', '=', 'stocks.product_id')->orderBy('time_created', 'desc')->get();
        }

        $total_buying = 0;

        foreach ($stocks as $key => $value) {
            $total_buying += $value->unit_cost * $value->quantity_in;
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'Production Report';
        $title_sw = 'Ripoti ya Uhamishaji';
        return view('reports.inventory.purchases', compact('page', 'title', 'title_sw', 'stocks', 'duration', 'duration_sw', 'is_post_query', 'product', 'products', 'start_date', 'end_date', 'total_buying', 'reporttime', 'shop'));
    }

    public function stockExpires(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'] . ' 00:00:00';
            $end = $request['stock_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
        $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';


        $product = null;
        $products = $shop->products()->where('is_active', true)->select('id as id', 'name as name', 'basic_uom as basic_uom', 'time_created as created_at', 'in_stock as in_stock', 'retail_price as retail_price',  'wholesale_price as wholesale_price', 'unit_cost as unit_cost')->get();

        $expstocks = array();
        foreach ($products as $key => $product) {
            $stocks = Stock::where('stocks.shop_id', $shop->id)->where('product_id', $product->id)->where('is_deleted', false)->whereNotNull('expire_date')->orderBy('created_at', 'DESC')->get();
            $instock = $product->in_stock;
            foreach ($stocks as $key => $stock) {
                $status = 'No';
                if ($stock->expire_date < Carbon::now()) {
                    $status = 'Yes';
                }

                $pdate = Carbon::parse(
$stock->stock_date);
                $edate = Carbon::parse($stock->expire_date);
                $days = $edate->diffInDays($pdate);
                if ($instock > 0) {
                    if ($instock <= $stock->quantity_in) {
                        $qty_expired = $instock;
                        $expstock = ['name' => $product->name, 'quantity_in' => $stock->quantity_in, 'qty_expired' => $qty_expired, 'purchase_date' => 
$stock->stock_date, 'expire_date' => $stock->expire_date, 'numdays' => $days, 'status' => $status, 'unit_cost' => $stock->unit_cost];
                        array_push($expstocks, $expstock);
                    } else {
                        $qty_expired = $stock->quantity_in;
                        $expstock = ['name' => $product->name, 'quantity_in' => $stock->quantity_in, 'qty_expired' => $qty_expired, 'purchase_date' => 
$stock->stock_date, 'expire_date' => $stock->expire_date, 'numdays' => $days, 'status' => $status, 'unit_cost' => $stock->unit_cost];
                        array_push($expstocks, $expstock);
                    }
                }
                $instock -= $stock->quantity_in;
            }
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Stock Reports';
        $title = 'Expiration Report';
        $title_sw = 'Ripoti ya Kumalizika Muda';
        return view('reports.inventory.expires', compact('expstocks', 'page', 'title', 'title_sw', 'reporttime', 'duration', 'duration_sw', 'is_post_query', 'product', 'products', 'start_date', 'end_date', 'shop', 'settings'));
    }

    public function dcsValues(Request $request)
    {
        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Daily Closing Stock Report';
        $title = 'Daily Closing Stock Report';
        $title_sw = 'Ripoti ya Kufunga Stocki ya Kila siku';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        
        $now = Carbon::now();
        $start = $now->subDays(1)->startOfDay();
        $end = \Carbon\Carbon::now()->subDays(1)->endOfDay();
        $is_post_query = true;
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        $duration_sw = 'Kuanzia '.date('d-m-Y', strtotime($start)).' Mpaka '.date('d-m-Y', strtotime($end)).'.';
        $products = null;
        $product = null;
        $dcsvalues = DailyClosingStockValue::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->join('products', 'products.id', '=', 'daily_closing_stock_values.product_id')->select('name', 'date', 'start_qty', 'purchase_qty', 'sold_qty', 'return_qty', 'transfer_qty', 'dam_qty', 'start_value', 'start_retail_value', 'start_wholesale_value', 'end_qty', 'end_value', 'end_retail_value', 'end_wholesale_value')->get();

        return view('reports.inventory.dcs-report', compact('page', 'title', 'title_sw', 'shop', 'settings', 'products', 'product', 'dcsvalues', 'start_date', 'end_date', 'is_post_query', 'duration', 'duration_sw', 'reporttime'));
    }


    public function poItemStatusReport(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->where('is_active', true)->get();
        $suppliers = $shop->suppliers()->where('supplier_for', 'Stock')->pluck('name', 'id');
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['stock_date'])) {
            $start_date = $request['stock_date'];
            $end_date = $request['stock_date'];
            $start = $request['stock_date'] . ' 00:00:00';
            $end = $request['stock_date'] . ' 23:59:59';
            $is_post_query = true;
        } else if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';
        $duration_sw = 'Kuanzia ' . date('d-m-Y', strtotime($start)) . ' Mpaka ' . date('d-m-Y', strtotime($end)) . '.';

        // return $request;
        $currsupp = null;
        $product = null;
        if (!empty($request['supplier_id'])) {
            $currsupp = $request['supplier_id'];
            if (!empty($request['product_id']) && $request['product_id'] > 0) {
                $product = Product::find($request['product_id']);
                $poitems = PurchaseOrderItem::where('purchase_order_items.shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('purchase_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'purchase_order_items.product_id')->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')->where('purchase_orders.supplier_id', $currsupp)->join('purchases', 'purchases.purchase_order_id', '=', 'purchase_orders.id')->where('purchases.is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')->select('purchase_order_items.created_at as date', 'suppliers.name as name', 'purchase_orders.order_no as order_no', 'invoice_no', 'delivery_note_no', 'grn_no', 'slug', 'basic_uom', 'qty', 'received_qty')->get();
            } else {
                $poitems = PurchaseOrderItem::where('purchase_order_items.shop_id', $shop->id)->whereBetween('purchase_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'purchase_order_items.product_id')->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')->where('purchase_orders.supplier_id', $currsupp)->join('purchases', 'purchases.purchase_order_id', '=', 'purchase_orders.id')->where('purchases.is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')->select('purchase_order_items.created_at as date', 'suppliers.name as name', 'purchase_orders.order_no as order_no', 'invoice_no', 'delivery_note_no', 'grn_no', 'slug', 'basic_uom', 'qty', 'received_qty')->get();
            }
        }else{
            if (!empty($request['product_id'])) {
                $product = Product::find($request['product_id']);
                $poitems = PurchaseOrderItem::where('purchase_order_items.shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('purchase_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'purchase_order_items.product_id')->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')->join('purchases', 'purchases.purchase_order_id', '=', 'purchase_orders.id')->where('purchases.is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')->select('purchase_order_items.created_at as date', 'suppliers.name as name', 'purchase_orders.order_no as order_no', 'invoice_no', 'delivery_note_no', 'grn_no', 'slug', 'basic_uom', 'qty', 'received_qty')->get();
            } else {
                $poitems = PurchaseOrderItem::where('purchase_order_items.shop_id', $shop->id)->whereBetween('purchase_order_items.created_at', [$start, $end])->join('products', 'products.id', '=', 'purchase_order_items.product_id')->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')->join('purchases', 'purchases.purchase_order_id', '=', 'purchase_orders.id')->where('purchases.is_deleted', false)->join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')->select('purchase_order_items.created_at as date', 'suppliers.name as name', 'purchase_orders.order_no as order_no', 'invoice_no', 'delivery_note_no', 'grn_no', 'slug', 'basic_uom', 'qty', 'received_qty')->get();
            }
        }

        $crtime = \Carbon\Carbon::now();
        $reporttime = $crtime->toDayDateTimeString();
        $page = 'Reports';
        $title = 'PO Receiving Report';
        $title_sw = 'Ripoti ya Uhamishaji';
        return view('reports.inventory.po-items', compact('page', 'title', 'title_sw', 'poitems', 'duration', 'duration_sw', 'is_post_query', 'product', 'products', 'start_date', 'end_date', 'reporttime', 'shop', 'suppliers', 'currsupp'));
    }
    
}
