<?php

namespace App\Http\Controllers\Inventory;

use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Jobs\PMUpdaterJob;
use App\Jobs\StockUpdaterJob;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Company;
use App\Models\Product;
use App\Models\RmDamage;
use App\Models\RmItem;
use App\Models\RmUse;
use App\Models\SaleReturnItem;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\TpmItem;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\TransferOrderItemTemp;
use App\Models\TransferOrderTemp;
use App\Models\TransformationTransferItem;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Log;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\ImagickEscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Spatie\Permission\Models\Role;

class TransferOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Stock Transfer Orders';
        $title = 'Stock Transfer Orders';
        $title_sw = 'Oda za Kuhamisha Stock';
        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $role =  Auth::user()->roles()->first();

        $orders = null; $sto_returns = null;
        if (!empty($request['search_key'])) {
            $searchkey = ltrim($request['search_key'], '0');
            $orders = TransferOrder::where('shop_id', $shop->id)->where(\DB::raw('CONCAT_WS(" ", `order_no`)'), 'like', '%' . $searchkey . '%')->get();
            $sto_returns = TransferOrder::where('destination_id', $shop->id)->where(\DB::raw('CONCAT_WS(" ", `order_no`)'), 'like', '%' . $searchkey . '%')->get();
        }else{
            $orders = TransferOrder::where('shop_id', $shop->id)->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();
            $sto_returns = TransferOrder::where('destination_id', $shop->id)->where('shop_id', '!=', $shop->id)->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();
        }

        return view('products.transfers.index', compact('page', 'title', 'title_sw', 'orders', 'sto_returns', 'shop', 'settings', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $page = 'New Stock Transfer';
        $title = 'New Stock Transfer';
        $title_sw = 'Stock mpy ya kuhamisha';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $owner = $shop->users()->where('user_id', $user->id)->first();
        // $destinations = $owner->shops()->where('shop_id', '!=', $shop->id)->get();
        $ordertemp = null;
        if (!empty($request['temp_id'])) {
            $ordertemp = TransferOrderTemp::find($request['temp_id']);
        }else{
            $ordertemp = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('an_sale_id')->whereNull('destination_id')->first();
            if (is_null($ordertemp)) {
                $ordertemp = new TransferOrderTemp();
                $ordertemp->shop_id = $shop->id;
                $ordertemp->user_id = $user->id;
                $ordertemp->save();
            }
        }

        $pendingtemps = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('an_sale_id')->get();
        return view('products.transfers.create', compact('page', 'title', 'title_sw', 'shop', 'settings', 'ordertemp', 'pendingtemps'));
    }

    public function createStockTransferOrder(Request $request)
    {
        // return $request;
        $page = 'New STO Request';
        $title= 'New STO Request';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $orderdate = Carbon::now();
        $ordertemp = null;
        $reason = null;
        if (!empty($request['temp_id'])) {
            $ordertemp = TransferOrderTemp::find($request['temp_id']);
            if (!is_null($ordertemp)) {
                $reason = $ordertemp->reason;
            }
            $pendingtemps = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            return view('products.transfers.st-requests.create', compact('page', 'title', 'shop', 'settings', 'reason', 'ordertemp','pendingtemps'));
        }else{
            $sale = AnSale::find($request['id']);
            if (!is_null($sale)) {
                $reason = 'Stock request for sales order : '.$sale->invoice_no;
                $ordertemp = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('an_sale_id', $sale->id)->first();
                if (is_null($ordertemp)) {
                    $ordertemp = new TransferOrderTemp();
                    $ordertemp->shop_id = $shop->id;
                    $ordertemp->user_id = $user->id;
                    $ordertemp->an_sale_id = $sale->id;
                    $ordertemp->reason = $reason;
                    $ordertemp->save();

                    $items = AnSaleItem::where('an_sale_id', $sale->id)->groupBy('product_id')->orderBy('an_sale_items.time_created', 'desc')->get([
                        DB::raw('an_sale_items.product_id as product_id'),
                        DB::raw('an_sale_items.product_unit_id as product_unit_id'),
                        DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold')
                    ]);

                    foreach ($items as $key => $item) {
                        $orderItemTemp = TransferOrderItemTemp::where('product_id', $item->product_id)->where('user_id', $user->id)->where('transfer_order_temp_id', $ordertemp->id)->first();
                        if (is_null($orderItemTemp)) {

                            $orderItemTemp = new TransferOrderItemTemp;
                            $orderItemTemp->shop_id = $shop->id;
                            $orderItemTemp->user_id = $user->id;
                            $orderItemTemp->transfer_order_temp_id = $ordertemp->id;
                            $orderItemTemp->product_id = $item->product_id;
                            $orderItemTemp->an_sale_id = $sale->id;
                            $orderItemTemp->quantity = $item->quantity_sold;
                            $orderItemTemp->save();
                        }
                    }
                }

                $pendingtemps = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                return view('products.transfers.st-requests.create', compact('page', 'title', 'shop', 'settings', 'reason', 'ordertemp', 'pendingtemps'));
            }else{
               $ordertemp = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('an_sale_id')->whereNull('destination_id')->first();
                if (is_null($ordertemp)) {
                    $ordertemp = new TransferOrderTemp();
                    $ordertemp->shop_id = $shop->id;
                    $ordertemp->user_id = $user->id;
                    $ordertemp->save();
                } 
                $pendingtemps = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
                return view('products.transfers.st-requests.create', compact('page', 'title', 'shop', 'settings', 'reason', 'ordertemp', 'pendingtemps'));
            }
        }
    }

    public function transferToItem()
    {
        $page = 'New Stock Transfer';
        $title = 'New Stock Transfer Item to Item';
        $title_sw = 'Stock mpy ya kuhamisha';
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        $orderdate = $now->format('Y-m-d');
        $products = $shop->products()->where('is_active', true)->select('id', 'name')->get();
        $pmaterials = $shop->packingMaterials()->where('is_deleted' , false)->get();
        return view('products.transfers.item-to-item', compact('page', 'title', 'title_sw', 'shop', 'orderdate', 'products', 'pmaterials'));
    }

    public function transferItems(Request $request)
    {
        $user = Auth::user();        
        $shop = Shop::find(Session::get('shop_id'));
        
        $now = Carbon::now();
        $orderdate = $now->format('Y-m-d');
        if (!empty($request['order_date'])) {
            $orderdate = $request['order_date'];
        }

        $sproduct = $shop->products()->where('id', $request['source_product_id'])->first();
        $dproduct = $shop->products()->where('id', $request['destin_product_id'])->first();
        if (!is_null($sproduct) && !is_null($dproduct)) {
                
            $max_no = TransferOrder::where('shop_id', $shop->id)->orderByRaw('CONVERT(order_no, SIGNED) desc')->first();
            $order_no = 0;
            if (!is_null($max_no)) {
                $order_no = $max_no->order_no+1;
            }else{
                $order_no = 1;
            }

            $confirm_time = Carbon::now();
            $transorder = new TransferOrder();
            $transorder->user_id = $user->id;
            $transorder->confirm_time = $confirm_time;
            $transorder->requester_id = $user->id;
            $transorder->shop_id = $shop->id;
            $transorder->destination_id = $shop->id;
            $transorder->order_no = $order_no;
            $transorder->order_date = $orderdate;
            $transorder->reason = $request['reason'];
            $transorder->save();

            $ordertime = $orderdate.' '.$now->format('H:i:s');
            
            $orderItem = new TransferOrderItem;
            $orderItem->shop_id = $shop->id;
            $orderItem->transfer_order_id = $transorder->id;
            $orderItem->product_id = $sproduct->id;
            $orderItem->source_stock = $sproduct->in_stock;
            if (is_null($dproduct->in_stock)) {
                $orderItem->destin_stock = 0;
            }else{
                $orderItem->destin_stock = $dproduct->in_stock;
            }
            $orderItem->source_unit_cost = $sproduct->unit_cost;
            $orderItem->quantity = $request['quantity'];
            $orderItem->save();
            $orderItem->created_at = $ordertime;
            $orderItem->save();

            dispatch(new StockUpdaterJob($shop, $orderItem->product_id));

            $destin_unit_cost = 0;
            if ($request['quantity'] < $request['quantity_in']) {
                $destin_unit_cost = ($orderItem->source_unit_cost/$request['quantity_in'])*$request['quantity'];
            }else{
                $destin_unit_cost = $orderItem->source_unit_cost*$request['quantity'];
            }

            $stock = new Stock();
            $stock->product_id = $dproduct->id;
            $stock->shop_id = $shop->id;
            $stock->quantity_in = $request['quantity_in'];
            $stock->unit_cost = $destin_unit_cost;
            $stock->source = 'Transfered (From: '.$sproduct->name.')';
            
            $stock->stock_date = $ordertime;
            $stock->transfer_order_id = $transorder->id;
            $stock->save();

            dispatch(new StockUpdaterJob($shop, $dproduct->id));

            if (!empty($request['source_pm_id']) || !empty($request['destin_pm_id'])) {
                $tpmitem = new TpmItem();
                $tpmitem->transfer_order_id = $transorder->id;
                $tpmitem->source_product_id = $sproduct->id;
                $tpmitem->destin_product_id = $dproduct->id;
                $tpmitem->source_pm_id = $request['source_pm_id'];
                $tpmitem->destin_pm_id = $request['destin_pm_id'];
                $tpmitem->source_pm_qty = $request['quantity'];
                $tpmitem->destin_pm_qty = $request['quantity_in'];
                $tpmitem->save();

                dispatch(new PMUpdaterJob($tpmitem->source_pm_id, $shop));

                dispatch(new PMUpdaterJob($tpmitem->destin_pm_id, $shop));
            }

            return redirect('transfer-orders')->with('success', 'Transfer done successfully');
        }else{
            return redirect()->back()->with('error', 'Product selected not found');
        }   
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
        $now = Carbon::now();
        $shop = Shop::find($request['shop_id']);
        $ordertemp = TransferOrderTemp::find($request['order_temp_id']);
        if (!is_null($ordertemp)) {
            $destinshop = Shop::find($ordertemp->destination_id);
            $orderdate = $now->format('Y-m-d');
            if (!empty($request['order_date'])) {
                $orderdate = $request['order_date'];
            }
            $confirm_time = null;
            $userid = null;
            $requesterid = null;
            $is_request = false;
            $is_return = false;
            if ($ordertemp->transfer_type == 1) {
                $shop = Shop::find($ordertemp->destination_id);
                $destinshop = Shop::find($ordertemp->shop_id);
                $requesterid = $user->id;
                $is_request = true;
            }elseif ($ordertemp->transfer_type == 2) {
                $shop = Shop::find($ordertemp->shop_id);
                $destinshop = Shop::find($ordertemp->destination_id);
                $userid = $user->id;
                $is_return = true;
            }else{
                $userid = $user->id;
                $confirm_time = Carbon::now();
            }

            if (!is_null($shop)) {
                $itemtemps = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->get();
                $saleid = $ordertemp->an_sale_id;
                if (!is_null($itemtemps)) {
                    $temps = array();
                    foreach ($itemtemps as $key => $value) {
                        if ($value->quantity == 0) {
                            array_push($temps, $value->quantity);
                        }
                    }

                    if (!empty($temps)) {
                        $warning = 'Please update the quantity of each item to continue';
                        return redirect()->back()->with('warning', $warning);
                    }else{
                        $max_no = TransferOrder::where('shop_id', $shop->id)->orderByRaw('CONVERT(order_no, SIGNED) desc')->first();
                        $order_no = 0;
                        if (!is_null($max_no)) {
                            $order_no = $max_no->order_no+1;
                        }else{
                            $order_no = 1;
                        }

                        $transorder = new TransferOrder();
                        $transorder->user_id = $userid;
                        $transorder->confirm_time = $confirm_time;
                        $transorder->requester_id = $requesterid;
                        $transorder->shop_id = $shop->id;
                        $transorder->destination_id = $destinshop->id;
                        $transorder->an_sale_id = $saleid;
                        $transorder->order_no = $order_no;
                        $transorder->is_request = $is_request;
                        $transorder->is_return = $is_return;
                        $transorder->order_date = $orderdate;
                        $transorder->reason = $request['reason'];
                        $transorder->add_vat = $request['add_vat'];
                        $transorder->save();

                        $ordertime = $orderdate.' '.$now->format('H:i:s');
                        foreach ($itemtemps as $key => $item) {

                            $product = $shop->products()->where('is_active', true)->where('id', $item->product_id)->first();
                            
                             if (!$product) {
                                \Log::warning("Missing product ID: ".$item->product_id);
                                continue;
                            }
                            $destinproduct = $destinshop->products()->where('is_active', true)->where('slug', $product->slug)->first();
                            
                            if (!$destinproduct) {
                                continue;
                            }

                            if(!is_null($product) && !is_null($destinproduct)) {
                                $orderItem = new TransferOrderItem;
                                $orderItem->shop_id = $shop->id;
                                $orderItem->transfer_order_id = $transorder->id;
                                $orderItem->product_id = $item->product_id;
                                if ($transorder->is_request) {
                                    $orderItem->req_qty = $item->quantity;
                                    if (!is_null($product->in_stock)) {
                                        $orderItem->source_stock = $product->in_stock;
                                    }else{
                                        $orderItem->source_stock = 0;
                                    }
                                    if (!is_null($destinproduct->in_stock)) {
                                        $orderItem->destin_stock = $destinproduct->in_stock;
                                    }else{
                                        $orderItem->destin_stock = 0;
                                    }
                                    $orderItem->source_unit_cost = $product->unit_cost;
                                    if (!is_null($destinproduct->unit_cost) && $destinproduct->unit_cost > 0) {
                                        $orderItem->destin_unit_cost = $destinproduct->unit_cost;
                                    }else{
                                        $orderItem->destin_unit_cost = $product->unit_cost;
                                    }
                                }else{
                                    $orderItem->quantity = $item->quantity;
                                    $orderItem->source_stock = $item->source_stock;
                                    $orderItem->destin_stock = $item->destin_stock;
                                    $orderItem->source_unit_cost = $item->source_unit_cost;
                                    $orderItem->destin_unit_cost = $item->destin_unit_cost;
                                }

                                $orderItem->source_unit_price = $product->retail_price;
                                $orderItem->save();
                                $orderItem->created_at = $ordertime;
                                $orderItem->save();
                            }else{
                                $itemnotfound = Product::find($item->product_id);
                                if (!is_null($itemnotfound)) {
                                    Log::info($itemnotfound->name.' is not registered in Either '.$shop->name.' or '.$destinshop->name);
                                }else{
                                    Log::info('Item not exist');
                                }
                            }
                        }
                    }


                    foreach ($itemtemps as $key => $value) {    
                        $value->delete();
                    }

                    $ordertemp->delete();
                    
                    if (!is_null($saleid)) {
                        $sale = AnSale::find($saleid);
                        $sale->is_stock_requested = true;
                        $sale->save();
                        return redirect()->route('transfer-orders.show', encrypt($transorder->id));
                    }else{
                        $success = 'Transfer Order was created successfully';
                        return redirect('transfer-orders')->with('success', $success);
                    }
                }else{
                    return redirect()->back()->with('warning', 'Please Select at least one Product to continue!.');
                }
            }else{
                return redirect()->back()->with('error', 'Warehouse or Destination not selected. Please select to continue');
            }
        }else{
            return redirect()->back()->with('info', 'Order temp not initialized');
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
        $page = 'Stock Transfer Order';
        $title = 'STO';
        $title_sw = 'STO';
        $transorder = TransferOrder::find(decrypt($id));
        $company = Company::find(Session::get('company_id'));
        if ($transorder->is_transfomation_transfer == 1) {
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $orderitems = TransformationTransferItem::where('transfer_order_id', $transorder->id)->with('product')->get();

            $transorder =  $transorder->join('products' , 'products.id' , 'transfer_orders.source_product_id')->where('transfer_orders.id' , decrypt($id))->first();

            return view('products.transfers.transformation.show', compact('page', 'title', 'title_sw', 'company', 'transorder', 'source', 'destin', 'user', 'orderitems'));
        }elseif($transorder->is_mix_transfer) {
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $requester = User::find($transorder->requester_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('product_code', 'name', 'basic_uom', 'req_qty', 'source_stock', 'destin_stock', 'quantity', 'source_unit_cost')->get();
            $endproduct = Stock::where('shop_id', $source->id)->where('transfer_order_id', $transorder->id)->join('products', 'products.id', 'stocks.product_id')->select('name', 'quantity_in', 'unit_cost')->get();

            return view('products.transfers.show', compact('page', 'title', 'title_sw', 'company', 'transorder', 'source', 'destin', 'user', 'requester', 'orderitems', 'endproduct'));
        }elseif ($transorder->is_transfer_to_rm) {
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $requester = User::find($transorder->requester_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('product_code', 'name', 'basic_uom', 'req_qty', 'source_stock', 'destin_stock', 'quantity', 'source_unit_cost')->get();
            $rmitem = RmItem::where('shop_id', $source->id)->where('transfer_order_id', $transorder->id)->join('raw_materials', 'raw_materials.id', 'rm_items.raw_material_id')->select('name', 'qty', 'unit_cost')->get();

            return view('products.transfers.rm-show', compact('page', 'title', 'title_sw', 'company', 'transorder', 'source', 'destin', 'user', 'requester', 'orderitems', 'rmitem'));
        } else{
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $requester = User::find($transorder->requester_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('product_code', 'name', 'basic_uom', 'req_qty', 'source_stock', 'destin_stock', 'quantity', 'rec_qty', 'source_unit_price')->get();
            if ($transorder->is_request || $transorder->is_return) {
                return view('products.transfers.st-requests.show', compact('page', 'title', 'title_sw', 'transorder', 'source', 'destin', 'user', 'requester', 'orderitems'));
            }else{
                return view('products.transfers.show', compact('page', 'title', 'title_sw', 'company', 'transorder', 'source', 'destin', 'user', 'requester', 'orderitems'));
            }
        }
    }

    public function stoValue($id)
    {
        $page = 'Stock Transfer Order';
        $title = 'STO';
        $title_sw = 'STO';
        $transorder = TransferOrder::find(decrypt($id));

        if ($transorder->is_transfomation_transfer == 1) {
            $source = Shop::find($transorder->shop_id);
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $orderitems = TransformationTransferItem::where('transfer_order_id', $transorder->id)->with('product')->get();

            $transorder =  $transorder->join('products' , 'products.id' , 'transfer_orders.source_product_id')->where('transfer_orders.id' , decrypt($id))->first();

            return view('products.transfers.transformation.order-value', compact('page', 'title', 'title_sw', 'transorder', 'source', 'destin', 'user', 'orderitems'));
        }else{
            $source = Shop::find($transorder->shop_id);
            $settings = Setting::where('shop_id', $source->id)->first();
            $destin = Shop::find($transorder->destination_id);
            $user = User::find($transorder->user_id);
            $requester = User::find($transorder->requester_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('product_code', 'name', 'basic_uom', 'quantity', 'source_unit_price')->get();
            if ($transorder->is_request || $transorder->is_return) {
                return view('products.transfers.st-requests.order-value', compact('page', 'title', 'title_sw', 'transorder', 'source', 'destin', 'user', 'requester', 'orderitems', 'settings'));
            }else{
                return view('products.transfers.order-value', compact('page', 'title', 'title_sw', 'transorder', 'source', 'destin', 'user', 'requester', 'orderitems', 'settings'));
            }
        }
    }

    function addSpaces($string = '', $valid_string_length = 0) {
        if (strlen($string) < $valid_string_length) {
            $spaces = $valid_string_length - strlen($string);
            for ($index1 = 1; $index1 <= $spaces; $index1++) {
                $string = $string . ' ';
            }
        }

        return $string;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Stock Transfer Order';
        $title = 'Edit Stock Transfer Order';
        $title_sw = 'Hariri Oda ya kuhamisha Stock';

        $transorder = TransferOrder::find(decrypt($id));
        $shop = Shop::find($transorder->shop_id);
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $destinations = $user->shops()->where('shop_id', '!=', $shop->id)->get();
        $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('transfer_order_items.id as id', 'product_code', 'name', 'slug', 'basic_uom', 'source_stock', 'destin_stock', 'req_qty', 'quantity', 'source_unit_price')->get();
        $products = $shop->products()->where('is_active', true)->select('id', 'product_code', 'name', 'slug')->get();
        if ($transorder->is_request) {
            if ($user->can('confirm-stock-transfer')) {
                return view('products.transfers.st-requests.edit', compact('page', 'title', 'title_sw', 'shop', 'settings', 'destinations', 'transorder', 'orderitems', 'products'));
            }else{
                return view('products.transfers.st-requests.edit-request', compact('page', 'title', 'title_sw', 'shop', 'settings', 'destinations', 'transorder', 'orderitems', 'products'));
            }
        } elseif ($transorder->is_mix_transfer) {

            $endproduct = Stock::where('shop_id', $shop->id)->where('transfer_order_id', $transorder->id)->join('products', 'products.id', 'stocks.product_id')->select('stocks.id as id', 'product_id', 'name', 'quantity_in', 'unit_cost')->first();
            return view('products.transfers.edit-mix', compact('page', 'title', 'title_sw', 'shop', 'settings', 'transorder', 'orderitems', 'endproduct'));
        } else{
            if (Session::get('shop_id') == $transorder->shop_id) {
                return view('products.transfers.edit', compact('page', 'title', 'title_sw', 'shop', 'settings', 'destinations', 'transorder', 'orderitems', 'products'));
            }else{
                return redirect()->back()->with('info', 'Updates can only be done from Source Shop');
            }
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
        $user = Auth::user();
        $transorder = TransferOrder::find(decrypt($id));
        if ($transorder->is_request && $request['status'] == 'Confirmed') {
            $items = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
            $unconfirmed = 0;
            foreach ($items as $key => $value) {
                if (is_null($value->quantity) || $value->quantity <= 0) {
                    $unconfirmed++;
                }
            }

            if ($unconfirmed > 0) {
                return redirect()->back()->with('error', 'Please Confirm all Items quantity to submit');
            }else{
                $transorder->user_id = $user->id;
                $transorder->confirm_time = Carbon::now();
                $transorder->status = $request['status'];
                $transorder->on_confirm_remarks = $request['on_confirm_remarks'];
            }
        }

        $transorder->order_date = $request['order_date'];
        $transorder->destination_id = $request['destin_id'];
        $transorder->reason = $request['reason'];
        $transorder->add_vat = $request['add_vat'];
        $transorder->save();

        $success = 'Transfer Order updated successfully';
        return redirect('transfer-orders/'.encrypt($transorder->id))->with('success', $success);
    }

    public function updateStocks($transorder)
    {   
        $now = Carbon::now();
        $shop = Shop::find($transorder->shop_id);
        $destinshop = Shop::find($transorder->destination_id);
        $items = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();

        foreach ($items as $key => $item) {

            if (!is_null($item->rec_qty)) {
                $sourceproduct = Product::find($item->product_id);
                $product = Product::where('slug', $sourceproduct->slug)->where('shop_id', $destinshop->id)->first();
                $dstock = Stock::where('product_id', $product->id)->where('shop_id', $destinshop->id)->where('transfer_order_id', $transorder->id)->first();
                if (is_null($dstock)) {
                    $dstock = new Stock();
                    $dstock->product_id = $product->id;
                    $dstock->shop_id = $destinshop->id;
                    $dstock->transfer_order_id = $transorder->id;
                    $dstock->quantity_in = $item->rec_qty;
                    $dstock->unit_cost = $item->destin_unit_cost;
                    $dstock->source = 'Transfered (From: '.$shop->name.')';
                    $dstock->stock_date = $now;
                    $dstock->save();
                }else{
                    $dstock->quantity_in = $item->rec_qty;
                    $dstock->save();
                    Log::info('Stock Transfer already updated in Destination shop');
                }

                dispatch(new StockUpdaterJob($shop, $item->product_id));
           

                $saleitem = AnSaleItem::where('an_sale_id', $transorder->an_sale_id)->where('product_id', $item->product_id)->first();

                if (!is_null($saleitem)) {
                    $saleitem->stock_id = $dstock->id;
                    $saleitem->unit_cost = $dstock->unit_cost;
                    $saleitem->buying_price = $saleitem->quantity_sold*$saleitem->unit_cost;
                    $saleitem->save();

                    if ($dstock->quantity_in <= $saleitem->quantity_sold) {
                        $dstock->quantity_out = $dstock->quantity_in;
                        $dstock->is_utilized = true;
                        $dstock->save();
                    }else{
                        $dstock->quantity_out = $saleitem->quantity_sold;
                        $dstock->save();
                    }

                    $remqtysold = $saleitem->quantity_sold-$dstock->quantity_out;
                    if ($remqtysold > 0) {
                        $astocks = Stock::where('product_id', $product->id)->where('shop_id', $destinshop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
                        foreach ($astocks as $key => $stock) {
                            $remqty = ($stock->quantity_in-$stock->quantity_out);
                            $stock->quantity_out = $stock->quantity_out+$remqty;
                            if ($stock->quantity_in == $stock->quantity_out) {
                                $stock->is_utilized = true;
                            }
                            $stock->save();

                            $remqtysold -= $remqty;
                        }
                    }
                }
                dispatch(new StockUpdaterJob($destinshop, $product->id));
                // $product = Product::find($item->product_id);
            }else{
                Log::info('Received quantity can not be null');
            }
        }
    }

    public function updateSaleItems($product, $shop)
    {
        // Check if there are sales done with low stock
        $psitems = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->whereNull('stock_id')->where('is_deleted', false)->get();
        // foreach ($psitems as $key => $value) {
        //     $astocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
        //     $qtysold = $value->quantity_sold;
        //     foreach ($astocks as $key => $stock) {
        //         $remqty = ($stock->quantity_in-$stock->quantity_out);
        //         if ($qtysold > 0) {
        //             if ($qtysold <= $remqty) {
        //                 $saleitemData = new AnSaleItem;
        //                 $saleitemData->shop_id = $shop->id;
        //                 $saleitemData->an_sale_id = $value->an_sale_id;
        //                 $saleitemData->product_id = $value->product_id;
        //                 $saleitemData->stock_id = $stock->id;
        //                 $saleitemData->product_unit_id = $value->product_unit_id;
        //                 $saleitemData->quantity_sold = $qtysold;
        //                 $saleitemData->unit_cost = $stock->unit_cost;
        //                 $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
        //                 $saleitemData->retail_price = $value->retail_price;
        //                 $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
        //                 $saleitemData->disc_percent = $value->disc_percent;
        //                 $saleitemData->discount = $value->discount;
        //                 $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
        //                 $saleitemData->time_created = $value->time_created;
        //                 if ($value->vat_amount > 0) {
        //                     $saleitemData->tax_amount = $value->vat_amount;
        //                     $saleitemData->input_tax = $saleitemData->buying_price*(($settings->tax_rate/100)/(1+($settings->tax_rate/100)));
        //                 }
        //                 $saleitemData->sold_in = $value->sold_in;
        //                 $saleitemData->save();

        //                 $stock->quantity_out = $stock->quantity_out+$qtysold;
        //                 if ($stock->quantity_in == $stock->quantity_out) {
        //                     $stock->is_utilized = true;
        //                 }
        //                 $stock->save();
        //             }else{
        //                 $saleitemData = new AnSaleItem;
        //                 $saleitemData->shop_id = $shop->id;
        //                 $saleitemData->an_sale_id = $value->an_sale_id;
        //                 $saleitemData->product_id = $value->product_id;
        //                 $saleitemData->stock_id = $stock->id;
        //                 $saleitemData->product_unit_id = $value->product_unit_id;
        //                 $saleitemData->quantity_sold = $remqty;
        //                 $saleitemData->unit_cost = $stock->unit_cost;
        //                 $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
        //                 $saleitemData->retail_price = $value->retail_price;
        //                 $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
        //                 $saleitemData->disc_percent = $value->disc_percent;
        //                 $saleitemData->discount = $value->discount;
        //                 $saleitemData->total_discount = $saleitemData->discount*$saleitemData->quantity_sold;
        //                 $saleitemData->time_created = $value->time_created;
        //                 if ($value->vat_amount > 0) {
        //                     $saleitemData->tax_amount = $value->vat_amount;
        //                     $saleitemData->input_tax = $saleitemData->buying_price*(($settings->tax_rate/100)/(1+($settings->tax_rate/100)));
        //                 }
        //                 $saleitemData->sold_in = $value->sold_in;
        //                 $saleitemData->save();

        //                 $stock->quantity_out = $stock->quantity_out+$remqty;
        //                 if ($stock->quantity_in == $stock->quantity_out) {
        //                     $stock->is_utilized = true;
        //                 }
        //                 $stock->save();
        //             }
        //         }
        //         $qtysold -= $remqty;
        //     }
        // }
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
        $transorder = TransferOrder::find(decrypt($id));
        if (!is_null($transorder)) {
            $destinshop = Shop::find($transorder->destination_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
            foreach ($orderitems as $key => $item) {
                $item->delete();
                dispatch(new StockUpdaterJob($shop, $item->product_id));
            }

            $transorderstocks = Stock::where('shop_id', $destinshop->id)->where('transfer_order_id', $transorder->id)->get();
            foreach ($transorderstocks as $key => $orderstock) {
                $orderstock->delete();
                dispatch(new StockUpdaterJob($destinshop, $orderstock->product_id));
            }

            if($transorder->is_transfomation_transfer == 1){
                $transform =TransformationTransferItem::where('transfer_order_id', $transorder->id)->get();
                foreach($transform as $value){
                    $value->delete();

                }
            }
             
            $rmitem = RmItem::where('shop_id', $shop->id)->where('transfer_order_id', $transorder->id)->first();
            if (!is_null($rmitem)) {
                $rmitem->delete();
                $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $rmitem->raw_material_id)->first();
                $purchased = RmItem::where('raw_material_id', $rmitem->raw_material_id)->where('is_deleted' , false)->where('shop_id', $shop->id)->sum('qty');           
                $used = RmUse::where('raw_material_id', $rmitem->raw_material_id)->where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('rm_use_items' , 'rm_use_items.rm_use_id' , '=' , 'rm_uses.id')->sum('quantity');
                $damaged = RmDamage::where('raw_material_id', $rmitem->raw_material_id)->where('shop_id', $shop->id)->sum('quantity');

                if (!is_null($shop_raw_material)) {
                    $instore = $purchased-($used + $damaged); 
                    $shop_raw_material->in_store = $instore;
                    $shop_raw_material->save();
                }
            }

            $transorder->delete();
        }  

        $success = 'Transfer Order deleted successfully';
        return redirect('transfer-orders')->with('success', $success);
    }

    public function cancelSTO($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $transorder = TransferOrder::find(decrypt($id));
        if (!is_null($transorder)) {
            $destinshop = Shop::find($transorder->destination_id);
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
            foreach ($orderitems as $key => $item) {
                if ($item->is_cancelled) {
                    $item->is_cancelled = false;
                }else{
                    $item->is_cancelled = true;
                }
                $item->save();
                dispatch(new StockUpdaterJob($shop, $item->product_id));
            }

            $transorderstocks = Stock::where('shop_id', $destinshop->id)->where('transfer_order_id', $transorder->id)->get();
            foreach ($transorderstocks as $key => $orderstock) {
                $orderstock->delete();
                dispatch(new StockUpdaterJob($destinshop, $orderstock->product_id));
            }

            if($transorder->is_transfomation_transfer == 1){
                $transform =TransformationTransferItem::where('transfer_order_id', $transorder->id)->get();
                foreach($transform as $value){
                    $value->delete();

                }
            }
            if ($transorder->is_cancelled) {
                $transorder->is_cancelled = false;
                $transorder->status = 'Restored from Cancel';
            }else{
                $transorder->is_cancelled = true;
                $transorder->status = 'Cancelled';
            }
            $transorder->save();
        }  

        $success = 'Transfer Order cancelled successfully';
        return redirect('transfer-orders')->with('success', $success);
    }

    public function updateTransorderItem(Request $request)
    {
        $orderItem = TransferOrderItem::find($request['id']);

        if (!is_null($orderItem)) {
            $transorder = TransferOrder::find($orderItem->transfer_order_id);
            $shop = Shop::find($transorder->shop_id);
            $product = $shop->products()->where('is_active', true)->where('product_id', $orderItem->product_id)->first();
            if (!is_null($product->in_stock)) {
                $orderItem->source_stock = $product->in_stock;
                $orderItem->save();
            }else{
                $orderItem->source_stock = 0;
            }
            // return $transorder;
            if ($transorder->is_request && $request['quantity'] > $orderItem->source_stock) {
                return response()->json(['success' => 0, 'msg' => 'Confirmed quantity should be less than or equal Current Stock quantity']);
            }elseif ($transorder->is_request && $request['quantity'] > $orderItem->req_qty) {
                return response()->json(['success' => 0, 'msg' => 'Confirmed quantity should be less than or equal Request quantity']);
            }else{
                //Update Item
                $orderItem->quantity = $request['quantity'];
                $orderItem->save();
                // if (!$transorder->is_request) {
                        
                    $destinshop = Shop::find($transorder->destination_id);
                    $orderstock = Stock::where('shop_id', $destinshop->id)->where('transfer_order_id', $transorder->id)->where('product_id', $orderItem->product_id)->first();
                    if (!is_null($orderstock)) {
                        //Update destination stock
                        $orderstock->quantity_in = $request['quantity'];
                        $orderstock->save();

                        dispatch(new StockUpdaterJob($destinshop, $orderstock->product_id));
                    }

                    //Update Source shop
                    if ($orderItem->source_unit_price == 0 || is_null($orderItem->source_unit_price)) {
                        $sourceproduct = $shop->products()->where('is_active', true)->where('product_id', $orderItem->product_id)->first();
                        $orderItem->source_unit_price = $sourceproduct->retail_price;
                        $orderItem->save();
                        // Log::info($orderItem);
                    }
                    dispatch(new StockUpdaterJob($shop, $orderItem->product_id));
                // }

                return response()->json(['success' => 1, 'msg' => 'Updated successfully']);
            }
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
        }
    }

    public function deleteTransorderItem($id)
    {
        $orderItem = TransferOrderItem::find(decrypt($id));

        if (!is_null($orderItem)) {
            $transorder = TransferOrder::find($orderItem->transfer_order_id);
            $shop = Shop::find($transorder->shop_id);
            $destinshop = Shop::find($transorder->destination_id);
            $orderstock = Stock::where('shop_id', $destinshop->id)->where('order_id', $transorder->id)->where('product_id', $orderItem->product_id)->first();
            if (!is_null($orderstock)) {
                    
                $orderstock->delete();
                dispatch(new StockUpdaterJob($destinshop, $orderstock->product_id));
            }

            $orderItem->delete();
            dispatch(new StockUpdaterJob($shop, $orderItem->product_id));
        }

        return redirect()->back();
    }

    public function receiveTransfer($id)
    {
        $page = 'Confirm Receive STO';
        $title = 'Confirm Receive STO';
        $title_sw = '';

        $transorder = TransferOrder::find(decrypt($id));
        if (!is_null($transorder)) {
            $user = Auth::user();
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->join('products', 'products.id', '=', 'transfer_order_items.product_id')->select('transfer_order_items.id as id', 'product_code', 'name', 'basic_uom', 'source_stock', 'destin_stock', 'req_qty', 'quantity', 'rec_qty', 'source_unit_price')->get();
                return view('products.transfers.receive', compact('page', 'title', 'title_sw', 'transorder', 'orderitems'));
        }else{
            return redirect()->back()->with('info', 'STO not Found');
        }   
    }

    public function updateReceivedItem(Request $request)
    {
        $orderItem = TransferOrderItem::find($request['id']);

        if (!is_null($orderItem)) {
            //Update Item
            $orderItem->rec_qty = $request['rec_qty'];
            $orderItem->save();
            return response()->json(['success' => 1, 'msg' => 'Updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
        }
    }

    public function confirmReceived(Request $request)
    {
        $transorder = TransferOrder::find($request['id']);
        if (!is_null($transorder)) {
            $user = Auth::user();
            $items = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
            $unconfirmed = 0;
            foreach ($items as $key => $value) {
                if (is_null($value->rec_qty) || $value->rec_qty <= 0) {
                    $unconfirmed++;
                }
            }

            if ($unconfirmed > 0) {
                return redirect()->back()->with('error', 'Please Confirm the Quantity of all items Received.');
            }
            if ($transorder->is_request) {
                $transorder->receive_time = Carbon::now();
                $transorder->received_by = $user->first_name.' '.$user->last_name;
                $transorder->status = 'Received';
                $transorder->on_receive_remarks = $request['on_receive_remarks'];
                $transorder->save();
                $this->updateStocks($transorder);
            }elseif ($transorder->is_return) {
                $transorder->user_id = $user->id;
                $transorder->received_by = $user->first_name.' '.$user->last_name;
                $transorder->receive_time = Carbon::now();
                $transorder->status = 'Received';
                $transorder->on_receive_remarks = $request['on_receive_remarks'];
                $transorder->save();
                $this->updateStocks($transorder);
            }else {
                $transorder->requester_id = Auth::user()->id;
                $transorder->received_by = $user->first_name.' '.$user->last_name;
                $transorder->receive_time = Carbon::now();
                $transorder->status = 'Received';
                $transorder->on_receive_remarks = $request['on_receive_remarks'];
                $transorder->save();
                $this->updateStocks($transorder);
            }
            return redirect()->route('transfer-orders.show', encrypt($transorder->id))->with('success', 'Stock Transfer Order(STO) received successfully');
        }else{
            return redirect()->back()->with('error', 'Transfer Order was not Found');
        }
    }

    public function modifyReceivedSTO($id)
    {
        $transorder = TransferOrder::find(decrypt($id));
        if (!is_null($transorder)) {
            $transorder->receive_time = null;
            $transorder->status = 'Modified';
            $transorder->save();
            
            return redirect()->route('transfer-orders.show', encrypt($transorder->id))->with('success', 'Stock Transfer Order(STO) updated successfully');
        }else{
            return redirect()->back()->with('error', 'Transfer Order was not Found');
        }
    }

    public function cancelOrder($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $ordertemp = TransferOrderTemp::find(decrypt($id));
        if (!is_null($ordertemp)) {
            $saleid = $ordertemp->an_sale_id;
            $itemtemps = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->get();
            foreach ($itemtemps as $key => $value) {
                $value->delete();
            }
            $ordertemp->delete();
            $success = 'Your Order was cancelled successfully';

            if (!is_null($saleid)) {
                $sale = AnSale::find($saleid);
                if (!is_null($sale)) {
                    $sale->is_stock_requested = false;
                    $sale->save();
                }
                return redirect()->route('invoices.show', encrypt($saleid))->with('success', $success);
            }else{
                return redirect('transfer-orders')->with('success', $success);
            }
        }else{
            return redirect('transfer-orders')->with('error', 'Order temp not Found');
        }
    }

    public function mixItems()
    {
        $page = 'New Stock Transfer';
        $title = 'Mix Items to to Single Item';
        $title_sw = 'Stock mpy ya kuhamisha';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();
        $orderdate = $now->format('Y-m-d');
        $ordertemp = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('is_mix_transfer', true)->first();
        if (is_null($ordertemp)) {
            $ordertemp = new TransferOrderTemp();
            $ordertemp->shop_id = $shop->id;
            $ordertemp->user_id = $user->id;
            $ordertemp->destination_id = $shop->id;
            $ordertemp->is_mix_transfer = true;
            $ordertemp->save();
        }

        $temps = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->join('products', 'products.id', '=', 'transfer_order_item_temps.product_id')->select('transfer_order_item_temps.id as id', 'product_code', 'name', 'quantity', 'source_stock', 'source_unit_cost')->get();
        return view('products.transfers.mix-items', compact('page', 'title', 'title_sw', 'shop', 'orderdate', 'ordertemp', 'temps'));
    }

    public function addMixItem(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $product = $shop->products()->where('id', $request->product_id)->select('id', 'name', 'in_stock', 'unit_cost', 'retail_price')->first();
        if (!is_null($product)) {
            $ordertemp = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('is_mix_transfer', true)->first();
            if (!is_null($ordertemp)) {
                $orderItemTemp = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->where('product_id', $product->id)->first();
                if (is_null($orderItemTemp)) {
                    $orderItemTemp = new TransferOrderItemTemp;
                    $orderItemTemp->transfer_order_temp_id = $ordertemp->id;
                    $orderItemTemp->shop_id = $shop->id;
                    $orderItemTemp->user_id = $user->id;
                    $orderItemTemp->product_id = $product->id;
                    $orderItemTemp->quantity = 1;
                    if (!is_null($product->in_stock)) {
                        $orderItemTemp->source_stock = $product->in_stock;
                    }else{
                        $orderItemTemp->source_stock = 0;
                    }
                    $orderItemTemp->destin_stock = 0;
                    $orderItemTemp->source_unit_cost = $product->unit_cost;
                    $orderItemTemp->destin_unit_cost = 0;
                    $orderItemTemp->save();

                    return response()->json(['result' => 1, 'msg' => 'Success']);
                }else{
                    Log::info('Item already selected');
                    return response()->json(['result' => 0, 'msg' => 'Item already selected']);
                }
            }else{
                Log::info('Order temp not Found');
                return response()->json(['result' => 0, 'msg' => 'Order temp not Found']);
            }
        }else{
            Log::info('Item not Found');
            return response()->with(['result' => 0, 'msg' => 'Item not Found']);
        }
    }

    public function updateMixItem(Request $request)
    {
        $orderItemTemp = TransferOrderItemTemp::find($request['id']);
        if (!is_null($orderItemTemp)) {
            $orderItemTemp->quantity = $request['quantity'];
            $orderItemTemp->save();

            return response()->json(['success' => 1, 'msg' => 'Item updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
        }
    }

    public function removeMixItem($id)
    {   
        TransferOrderItemTemp::destroy(decrypt($id));
        return redirect()->back();
    }

    public function transferMixItems(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $ordertemp = TransferOrderTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('is_mix_transfer', true)->first();
        if (!is_null($ordertemp)) {
            $orderItemTemps = TransferOrderItemTemp::where('transfer_order_temp_id', $ordertemp->id)->get();
            if ($orderItemTemps->count() >= 2) {
                $max_no = TransferOrder::where('shop_id', $shop->id)->orderByRaw('CONVERT(order_no, SIGNED) desc')->first();
                $order_no = 0;
                if (!is_null($max_no)) {
                    $order_no = $max_no->order_no+1;
                }else{
                    $order_no = 1;
                }
                $now = Carbon::now();
                $orderdate = $now->format('Y-m-d');
                if (!empty($request['order_date'])) {
                    $orderdate = $request['order_date'];
                }

                $confirm_time = Carbon::now();
                $transorder = new TransferOrder();
                $transorder->user_id = $user->id;
                $transorder->confirm_time = $confirm_time;
                $transorder->requester_id = $user->id;
                $transorder->shop_id = $shop->id;
                $transorder->destination_id = $shop->id;
                $transorder->order_no = $order_no;
                $transorder->order_date = $orderdate;
                $transorder->reason = $request['reason'];
                $transorder->status = 'Transfered';
                $transorder->is_mix_transfer = true;
                $transorder->receive_time = Carbon::now();
                $transorder->save();

                $ordertime = $orderdate.' '.$now->format('H:i:s');
                $tt_cost = 0;
                foreach ($orderItemTemps as $key => $temp) {
                    $orderItem = new TransferOrderItem;
                    $orderItem->shop_id = $shop->id;
                    $orderItem->transfer_order_id = $transorder->id;
                    $orderItem->product_id = $temp->product_id;
                    $orderItem->source_stock = $temp->source_stock;
                    $orderItem->destin_stock = 0;
                    $orderItem->source_unit_cost = $temp->source_unit_cost;
                    $orderItem->quantity = $temp->quantity;
                    $orderItem->save();
                    $orderItem->created_at = $ordertime;
                    $orderItem->save();

                    $tt_cost += $orderItem->quantity*$orderItem->source_unit_cost;
                    dispatch(new StockUpdaterJob($shop, $orderItem->product_id));

                    $temp->delete();
                }
                $request->validate([
                'quantity_in' => 'required|numeric|min:1',
                ]);

                $qty = (float) $request->quantity_in;

                if ($qty <= 0) {
                return back()->with('error', 'Invalid quantity');
                }

                $destin_unit_cost = round($tt_cost / $qty, 2);
                //$destin_unit_cost = round($tt_cost/$request['quantity_in'], 2);
                $stock = new Stock();
                $stock->product_id = $request['product_id'];
                $stock->shop_id = $shop->id;
                $stock->quantity_in = $request['quantity_in'];
                $stock->unit_cost = $destin_unit_cost;
                $stock->source = 'Transfered (From: Mixed Items)';
                
                $stock->stock_date = $ordertime;
                $stock->transfer_order_id = $transorder->id;
                $stock->save();

                dispatch(new StockUpdaterJob($shop, $request['product_id']));
                return redirect('transfer-orders')->with('success', 'Transfer created successfully');
            }else{
                return redirect()->back()->with('warning', 'Please select at least two Items to mix');
            }
        }else{
            return redirect()->back()->with('error', 'Temp order not found');
        }
    }

    public function addSTOMixItem(Request $request)
    {
        $transorder = TransferOrder::find($request['transfer_order_id']);
        if (!is_null($transorder)) {
            $shop = Shop::find($transorder->shop_id);
            $product = $shop->products()->where('id', $request['product_id'])->first();
            if (!is_null($product)) {
                $orderItem = TransferOrderItem::where('transfer_order_id', $transorder->id)->where('product_id', $product->id)->first();
                if (is_null($orderItem)) {    
                    $now = Carbon::now();                        
                    $ordertime = $transorder->order_date.' '.$now->format('H:i:s');
                    $orderItem = new TransferOrderItem;
                    $orderItem->shop_id = $shop->id;
                    $orderItem->transfer_order_id = $transorder->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->source_stock = $product->in_stock;
                    $orderItem->destin_stock = 0;
                    $orderItem->source_unit_cost = $product->unit_cost;
                    $orderItem->quantity = 1;
                    $orderItem->save();
                    $orderItem->created_at = $ordertime;
                    $orderItem->save();
                    return response()->json(['result' => 1, 'msg' => 'Success']);
                }else{
                    return response()->json(['result' => 0, 'msg' => 'Item already selected']);
                }
            }else{
                return response()->json(['result' => 0, 'msg' => 'Item not Found']);
            }
        }else{
            return response()->json(['result' => 0, 'msg' => 'Order not Found']);
        }
    }

    public function updateSTOMixItem(Request $request)
    {
        $orderItem = TransferOrderItem::find($request['id']);
        if (!is_null($orderItem)) {
            $orderItem->quantity = $request['quantity'];
            $orderItem->save();

            return response()->json(['success' => 1, 'msg' => 'Item updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Item not Found']);
        }
    }

    public function removeSTOMixItem($id)
    {
        TransferOrderItem::destroy(decrypt($id));
        return redirect()->back()->with('success', 'Item removed successfully');
    }

    public function updateTransferMixItems(Request $request)
    {
        $transorder = TransferOrder::find($request['transfer_order_id']);
        if (!is_null($transorder)) {
            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
            if ($orderitems->count() >= 2) {
                $shop = Shop::find($transorder->shop_id);
                $now = Carbon::now();
                $orderdate = $now->format('Y-m-d');
                if (!empty($request['order_date'])) {
                    $orderdate = $request['order_date'];
                }

                $transorder->order_date = $orderdate;
                $transorder->reason = $request['reason'];
                $transorder->status = 'Transfered';
                $transorder->save();

                $tt_cost = 0;
                foreach ($orderitems as $key => $orderItem) {
                    $tt_cost += $orderItem->quantity*$orderItem->source_unit_cost;
                    dispatch(new StockUpdaterJob($shop, $orderItem->product_id));
                }

                $destin_unit_cost = round($tt_cost/$request['quantity_in'], 2);
                $stock = Stock::where('transfer_order_id', $transorder->id)->first();
                $stock->quantity_in = $request['quantity_in'];
                $stock->unit_cost = $destin_unit_cost;
                $stock->source = 'Transfered (From: Mixed Items)';
                $stock->save();

                dispatch(new StockUpdaterJob($shop, $request['product_id']));
                return redirect('transfer-orders')->with('success', 'Transfer updated successfully');
            }else{
                return redirect()->back()->with('warning', 'Please select at least two Items to mix');
            }
        }else{
            return redirect()->back()->with('error', 'Transfer order not found');
        }
    }


    // Transfer Item to RM

    public function transferToRM()
    {
        $page = 'New Stock Transfer';
        $title = 'New Stock Transfer Item to RM';
        $title_sw = 'Stock mpy ya kuhamisha';
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        $orderdate = $now->format('Y-m-d');
        $products = $shop->products()->where('is_active', true)->select('id', 'name', 'basic_uom')->get();
        $rmaterials = $shop->rawMaterials()->where('is_deleted' , false)->select('raw_materials.id as id', 'name', 'basic_uom')->get();
        return view('products.transfers.item-to-rm', compact('page', 'title', 'title_sw', 'shop', 'orderdate', 'products', 'rmaterials'));
    }

    public function transferItemToRM(Request $request)
    {
        $user = Auth::user();        
        $shop = Shop::find(Session::get('shop_id'));
        
        $now = Carbon::now();
        $orderdate = $now->format('Y-m-d');
        if (!empty($request['order_date'])) {
            $orderdate = $request['order_date'];
        }

        $sproduct = $shop->products()->where('id', $request['product_id'])->first();
        $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $request['rm_id'])->first();
        if (!is_null($sproduct) && !is_null($shop_raw_material)) {
                
            $max_no = TransferOrder::where('shop_id', $shop->id)->orderByRaw('CONVERT(order_no, SIGNED) desc')->first();
            $order_no = 0;
            if (!is_null($max_no)) {
                $order_no = $max_no->order_no+1;
            }else{
                $order_no = 1;
            }

            $confirm_time = Carbon::now();
            $transorder = new TransferOrder();
            $transorder->user_id = $user->id;
            $transorder->confirm_time = $confirm_time;
            $transorder->requester_id = $user->id;
            $transorder->shop_id = $shop->id;
            $transorder->destination_id = $shop->id;
            $transorder->order_no = $order_no;
            $transorder->order_date = $orderdate;
            $transorder->reason = $request['reason'];
            $transorder->is_transfer_to_rm = true;
            $transorder->status = 'Received';
            $transorder->save();

            $ordertime = $orderdate.' '.$now->format('H:i:s');
            
            $orderItem = new TransferOrderItem;
            $orderItem->shop_id = $shop->id;
            $orderItem->transfer_order_id = $transorder->id;
            $orderItem->product_id = $sproduct->id;
            $orderItem->source_stock = $sproduct->in_stock;
            $orderItem->destin_stock = 0;
            $orderItem->source_unit_cost = $sproduct->unit_cost;
            $orderItem->quantity = $request['quantity'];
            $orderItem->save();
            $orderItem->created_at = $ordertime;
            $orderItem->save();

            dispatch(new StockUpdaterJob($shop, $orderItem->product_id));

            $destin_unit_cost = 0;
            if ($request['quantity'] < $request['rm_qty']) {
                $destin_unit_cost = ($orderItem->source_unit_cost/$request['rm_qty'])*$request['quantity'];
            }else{
                $destin_unit_cost = $orderItem->source_unit_cost;
            }

            $stock  = new RmItem;
            $stock->transfer_order_id = $transorder->id;
            $stock->raw_material_id = $request['rm_id'];
            $stock->shop_id = $shop->id;
            $stock->qty = $request['rm_qty'];
            $stock->unit_cost = $destin_unit_cost;
            $stock->total = $stock->qty*$stock->unit_cost;
            $stock->date = $ordertime;
            $stock->save();

            if ($shop_raw_material->in_store == 0 || $shop_raw_material->in_store <= 1 ) {
                $shop_raw_material->unit_cost = $stock->unit_cost;
                $shop_raw_material->save();
            }
            $purchased = RmItem::where('raw_material_id', $request['rm_id'])->where('is_deleted' , false)->where('shop_id', $shop->id)->sum('qty');           
            $used = RmUse::where('raw_material_id', $request['rm_id'])->where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('rm_use_items' , 'rm_use_items.rm_use_id' , '=' , 'rm_uses.id')->sum('quantity');
            $damaged = RmDamage::where('raw_material_id', $request['rm_id'])->where('shop_id', $shop->id)->sum('quantity');

            $instore = $purchased-($used + $damaged); 
            $shop_raw_material->in_store = $instore;
            $shop_raw_material->save();

            return redirect('transfer-orders')->with('success', 'Transfer done successfully');
        }else{
            return redirect()->back()->with('error', 'Product selected not found');
        }   
    }

    public function editTransferToRM($id)
    {
        $page = 'Edit Transfer Order';
        $title = 'Edit Transfer Order';
        $title_sw = 'Edit Transfer Order';
        $transorder = TransferOrder::find(decrypt($id));
        if (!is_null($transorder)) {
            $shop = Shop::find($transorder->shop_id);
            $orderItem = TransferOrderItem::where('transfer_order_id', $transorder->id)->first();
            $rmitem  = RmItem::where('transfer_order_id', $transorder->id)->first();

            $products = $shop->products()->where('is_active', true)->select('id', 'name', 'basic_uom')->get();
            $rmaterials = $shop->rawMaterials()->where('is_deleted' , false)->select('raw_materials.id as id', 'name', 'basic_uom')->get();
            return view('products.transfers.edit-item-to-rm', compact('page', 'title', 'title_sw', 'shop', 'transorder', 'products', 'rmaterials', 'orderItem', 'rmitem'));
        }
    }

    public function updateTransferItemToRM(Request $request)
    {
        $user = Auth::user();        
        $shop = Shop::find(Session::get('shop_id'));
        
        $now = Carbon::now();
        $orderdate = $request['order_date'];
        $transorder = TransferOrder::find($request['transfer_order_id']);
        $sproduct = $shop->products()->where('id', $request['product_id'])->first();
        $shop_raw_material = $shop->rawMaterials()->where('raw_material_id', $request['rm_id'])->first();
        if (!is_null($transorder) && !is_null($sproduct) && !is_null($shop_raw_material)) {
            
            $transorder->order_date = $orderdate;
            $transorder->reason = $request['reason'];
            $transorder->save();

            $ordertime = $orderdate.' '.$now->format('H:i:s');
            
            $orderItem = TransferOrderItem::where('transfer_order_id', $transorder->id)->first();
            $orderItem->transfer_order_id = $transorder->id;
            $orderItem->product_id = $sproduct->id;
            $orderItem->source_stock = $sproduct->in_stock;
            $orderItem->source_unit_cost = $sproduct->unit_cost;
            $orderItem->quantity = $request['quantity'];
            $orderItem->created_at = $ordertime;
            $orderItem->save();

            dispatch(new StockUpdaterJob($shop, $orderItem->product_id));

            $destin_unit_cost = 0;
            if ($request['quantity'] < $request['rm_qty']) {
                $destin_unit_cost = ($orderItem->source_unit_cost/$request['rm_qty'])*$request['quantity'];
            }else{
                $destin_unit_cost = $orderItem->source_unit_cost;
            }

            $stock  = RmItem::where('transfer_order_id', $transorder->id)->first();
            $stock->raw_material_id = $request['rm_id'];
            $stock->qty = $request['rm_qty'];
            $stock->unit_cost = $destin_unit_cost;
            $stock->total = $stock->qty*$stock->unit_cost;
            $stock->date = $ordertime;
            $stock->is_deleted = false;
            $stock->save();

            if ($shop_raw_material->in_store == 0 || $shop_raw_material->in_store <= 1 ) {
                $shop_raw_material->unit_cost = $stock->unit_cost;
                $shop_raw_material->save();
            }
            $purchased = RmItem::where('raw_material_id', $request['rm_id'])->where('is_deleted' , false)->where('shop_id', $shop->id)->sum('qty');           
            $used = RmUse::where('raw_material_id', $request['rm_id'])->where('rm_uses.shop_id', $shop->id)->where('is_deleted' , false)->join('rm_use_items' , 'rm_use_items.rm_use_id' , '=' , 'rm_uses.id')->sum('quantity');
            $damaged = RmDamage::where('raw_material_id', $request['rm_id'])->where('shop_id', $shop->id)->sum('quantity');

            $instore = $purchased-($used + $damaged); 
            $shop_raw_material->in_store = $instore;
            $shop_raw_material->save();

            return redirect('transfer-orders')->with('success', 'Transfer updated successfully');
        }else{
            return redirect()->back()->with('error', 'Product or Raw Material selected not found');
        }   
    }
}
