<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Response;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\AnSale;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\ServiceItemTemp;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\ProInvoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\SalePayment;
use App\Models\LatestStockSoldLog;
use App\Models\Device;
use App\Models\DeviceSale;
use App\Models\CustomerTransaction;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Models\BankDetail;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\Grade;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Jobs\SendSMS;
use App\Models\ShopCurrency;
use App\Jobs\StockUpdaterJob;
use App\Models\CustomerCategory;
use App\Models\InvoiceNote;

class SaleController extends Controller
{

    function __construct()
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
        $shop = Shop::find(Session::get('shop_id'));
        // return $shop;
        if (!is_null($shop)) {
            if ($shop->subscription_type_id == 2) {
                $lastpay = Payment::where('shop_id', $shop->id)->where('amount_paid', '>', 0)->where('status', 'Activated')->where('is_for_module', false)->latest()->first();
                $premserv = ServiceCharge::where('type', 2)->where('duration', 'Monthly')->first();
                if (!is_null($lastpay)) {
                    if ($lastpay->amount_paid % $premserv->initial_pay == 0 && $lastpay->subscr_type == 2) {
                        return $this->getPOS($shop);
                    } else {
                        $wrn_en = 'You have Upgraded your account  to have PREMIUM subscription but no any payments verified after this changes.Please make payment for any amount for PREMIUM subscription as shown in table below then enter your verification code here inorder to continue using our service. Thank you for using SmartMauzo service.';
                        $wrn_sw = 'Umeboresha akaunti yako kuwa na usajili wa PREMIUM lakini hakuna malipo yoyote yaliyothibitishwa baada ya mabadiliko haya. Tafadhali fanya malipo kwa kiasi chochote cha usajili wa PREMIUM kama inavyoonyeshwa kwenye jedwali hapa chini kisha ingiza nambari yako ya uthibitishaji hapa ili kuendelea kutumia huduma yetu. Asante kwa kutumia huduma ya SmartMauzo.';
                        if (app()->getLocale() == 'en') {
                            return redirect('verify-payment')->with('info', $wrn_en);
                        } else {
                            return redirect('verify-payment')->with('info', $wrn_sw);
                        }
                    }
                } else {
                    return $this->getPOS($shop);
                }
            } else {
                return $this->getPOS($shop);
            }
        } else {
            
            return view('errors.401');
        }
    }


    public function getPOS($shop)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale(New Invoice)';
        $title_sw = 'Sehemu ya Kuuzia';

        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            if (is_null($dfcurr)) {
                if ($user->can('edit-settings')) {
                    return redirect('settings')->with('error', 'Please add your Default Currency to continue...');
                }else{
                    return redirect('user-profile')->with('info', 'Currency is not unpdated, Please Contact Account Admin');
                }
            }

            $saletemp = SaleTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->whereNull('customer_id')->first();
            if (is_null($saletemp)) {
                $saletemp = new SaleTemp();
                $saletemp->shop_id = $shop->id;
                $saletemp->user_id = $user->id;
                $saletemp->currency = $dfcurr->code;
                $saletemp->defcurr = $dfcurr->code;
                $saletemp->sale_date = Carbon::now();
                $saletemp->save();
            }else{
                $saletemp->sale_date = Carbon::now();
                $saletemp->save();
            }

            $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('sale_temps.user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

            $settings = Setting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);
            }

            $mindays = 0;
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
            $paydate = Carbon::parse($payment->created_at);
        
            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                } else {
                    $mindays = $now->diffInDays($paydate);
                }
            } else {
                $mindays = $now->diffInDays($paydate);
            }

            if ($mindays < 10) {
                $mindays = 15;
            }

            $products = null;
            if ($settings->is_filling_station) {
                $products = $shop->products()->get();
            }
            $accounts = Account::where('shop_id', $shop->id)->get();

            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
            $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
            $utransactions = 0;
            if ($shop->business_type_id == 3) {
                $devices = Device::where('shop_id', $shop->id)->get();
                $grades = Grade::where('shop_id', $shop->id)->get();
                return view('sales.invoices.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'accounts', 'mindays', 'grades', 'custids', 'categories', 'utransactions', 'notes'));
            } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
                return view('sales.invoices.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'custids', 'products', 'categories', 'utransactions', 'notes'));
            } else {
                return view('sales.invoices.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'products', 'custids', 'categories', 'utransactions', 'notes'));
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }

    public function getSuccess()
    {
        $success = 'You have successfully aded sales';

        return redirect()->to('/pos')->with('success', $success);
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
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $user = Auth::user();
        $now = Carbon::now();
        if (!empty($request['sale_date'])) {
            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $now = $request['sale_date'] . ' ' . $time;
        }


        $due_date = Carbon::now()->addDays(10);
        if (!empty($request['due_date'])) {
            $due_date = $request['due_date'];
        }

        $maxsaleno = AnSale::where('shop_id', $shop->id)->orderByRaw('CONVERT(invoice_no, SIGNED) desc')->first();
        $invoice_no = null;
        if (!is_null($maxsaleno)) {
            $invoice_no = $maxsaleno->invoice_no + 1;
        } else {
            $invoice_no = 1;
        }

        $pay_type = null;
        if ($request['pay_type'] == 'Cheque') {
            $pay_type = 'Bank';
        } else {
            $pay_type = $request['pay_type'];
        }

        $account = null;
        $bank_name = null;
        if ($request['sale_type'] == 'cash' && Auth::user()->can('create-sale-payment')) {
            $due_date = $now;
            if ($pay_type == 'Cash') {
                if (!empty($request['cash_acc_id'])) {
                    $account = Account::find($request['cash_acc_id']);
                }else{
                    return redirect('pos')->with('error', 'Selected Payment Method has no Account. Please Create Account for Cash Payments!.');
                }
            }elseif ($pay_type == 'Bank' || $pay_type == 'Cheque') {
                if (!empty($request['bank_acc_id'])) {
                    $account = Account::find($request['bank_acc_id']);
                }else{
                    return redirect('pos')->with('error', 'Selected Payment Method has no Account. Please Create Account for Bank Payments!.');
                }
            }elseif ($pay_type == 'Mobile Money') {
                if (!empty($request['mob_acc_id'])) {
                    $account = Account::find($request['mob_acc_id']);
                }else{
                    return redirect('pos')->with('error', 'Selected Payment Method has no Account. Please Create Account for Mobile Payments!.');
                }
            }else{

            }
            
            if (!is_null($account)) {
                $bank_name = $account->bank_name;
            }
        }

        $cheque_no = null;
        if (!empty($request['slip_no'])) {
            $cheque_no = $request['slip_no'];
        } else {
            $cheque_no = $request['cheque_no'];
        }

        $saletemp = SaleTemp::find($request['sale_temp_id']);
        if (is_null($saletemp)) {
            return redirect('pos');
        } else {
            if (!is_null($saletemp->customer_id)) {
                $servitems = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                $saleitems = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();

                // return $saleitems->count();
                if ($servitems->count() > 0 || $saleitems->count() > 0) {
                    $sale = AnSale::create([
                        'customer_id' => $saletemp->customer_id,
                        'shop_id' => Session::get('shop_id'),
                        'user_id' => Auth::user()->id,
                        'sale_order_id' => $saletemp->sale_order_id,
                        'pro_invoice_id' => $saletemp->pro_invoice_id,
                        'currency' => $saletemp->currency,
                        'defcurr' => $saletemp->defcurr,
                        'ex_rate' => $saletemp->ex_rate,
                        'comments' => $saletemp->comments,
                        'status' => 'Unpaid',
                        'time_created' => $now,
                        'sale_type' => $saletemp->sale_type,
                        'invoice_no' => $invoice_no,
                        'vehicle_no' => $request['vehicle_no'],
                        'due_date' => $due_date,
                    ]);

                    $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                    if (!is_null($notes)) {
                        $sale->note = $notes->content;
                    }
                    if ($settings->is_rental_service) {
                        $sale->rent_end_date = $request['rent_end_date'];
                    }
                    $sale->pay_type = $saletemp->pay_type;
                    if ($sale->pay_type != 'Cash') {
                        if (is_null($account)) {
                            $account = Account::where('shop_id', $shop->id)->where('type', 'Bank')->first();
                        }
                        if (!is_null($account)) {
                            $sale->account_id = $account->id;
                        }
                    }
                    $sale->lpo_no = $request['lpo_no'];
                    $sale->save();

                    $servsale_amount = 0;
                    $servsale_discount = 0;
                    $servtax_amount = 0;
                    $prodsale_amount = 0;
                    $prodsale_discount = 0;
                    $prodtax_amount = 0;

                    if ($servitems->count() > 0) {
                         foreach ($servitems as $key => $value) {
                            $shop_service = $shop->services()->where('id', $value->service_id)->first();
                            if (!is_null($shop_service)) {
                                $servcategory = $shop_service->categories()->where('shop_id', $shop->id)->first();
                                $catId = null;
                                if (!is_null($servcategory)) {
                                    // Log::info($servcategory);
                                    $catId = $servcategory->id;
                                }
                                $saleitemData = new ServiceSaleItem;
                                $saleitemData->shop_id = $shop->id;
                                $saleitemData->an_sale_id = $sale->id;
                                $saleitemData->service_id = $value->service_id;
                                $saleitemData->serv_category_id = $catId;
                                $saleitemData->no_of_repeatition = $value->no_of_repeatition;
                                $saleitemData->price = $value->price;
                                $saleitemData->total = $value->total;
                                $saleitemData->disc_percent = $value->disc_percent;
                                $saleitemData->discount = $value->total_discount/$saleitemData->no_of_repeatition;
                                $saleitemData->total_discount = $value->total_discount;
                                if ($value->vat_amount > 0) {
                                    $saleitemData->tax_amount = $value->vat_amount;
                                }
                                $saleitemData->time_created = $now;
                                $saleitemData->save();
                            }
                        }
                        $servsale_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                        $servsale_discount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                        $servtax_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                    }

                    if ($saleitems->count() > 0) {
                        $temps = array();
                        $valid = array();
                        foreach ($saleitems as $key => $value) {
                            $product = $shop->products()->where('id', $value->product_id)->first();
                            if (is_null($product)) {
                                array_push($valid, $key + 1);
                            }
                            if ($value->quantity_sold == 0) {
                                array_push($temps, $value->quantity_sold);
                            }
                        }
                        if (!empty($temps)) {
                            return redirect()->back()->with('warning', 'Please update the quantity of each item to continue');
                        } else if (!empty($valid)) {
                            return redirect()->back()->with('warning', 'You have selected Product/Products which are not registered for this shop. Please review your products and try again.');
                        } else {
                            foreach ($saleitems as $key => $value) {
                                $product = $shop->products()->where('id', $value->product_id)->first();

                                if (!is_null($product)) {
                                    $category = $product->categories()->where('shop_id', $shop->id)->first();
                                    $catId = null;
                                    if (!is_null($category)) {
                                        // Log::info($category);
                                        $catId = $category->id;
                                    }
                                    $punit = ProductUnit::find($value->product_unit_id);
                                    $quantity_sold = $value->quantity_sold * $punit->qty_equal_to_basic;
                                    $retail_price = $value->retail_price / $punit->qty_equal_to_basic;
                                    $unit_discount = $value->discount / $punit->qty_equal_to_basic;

                                    if ($settings->sale_with_low_stock) {
                                        $saleitemData = new AnSaleItem;
                                        $saleitemData->shop_id = $shop->id;
                                        $saleitemData->an_sale_id = $sale->id;
                                        $saleitemData->product_id = $value->product_id;
                                        $saleitemData->category_id = $catId;
                                        $saleitemData->product_unit_id = $value->product_unit_id;
                                        $saleitemData->quantity_sold = $value->quantity_sold;
                                        $saleitemData->unit_cost = $product->unit_cost;
                                        $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
                                        $saleitemData->retail_price = $retail_price;
                                        $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
                                        $saleitemData->disc_percent = $value->disc_percent;
                                        $saleitemData->discount = $unit_discount;
                                        $saleitemData->total_discount = $saleitemData->quantity_sold*$saleitemData->discount;
                                        $saleitemData->time_created = $now;
                                        $saleitemData->with_vat = $value->with_vat;
                                        if ($value->with_vat == 'yes') {
                                            $saleitemData->tax_amount = ($saleitemData->price-$saleitemData->total_discount)*($settings->tax_rate/100);
                                        }
                                        $saleitemData->sold_in = $value->sold_in;
                                        $saleitemData->save();
                                    }else{
                                        $astocks = Stock::where('product_id', $value->product_id)->where('shop_id', $shop->id)->where('is_deleted', false)->where('is_utilized', false)->get();
                                        $qtysold = $quantity_sold;
                                        foreach ($astocks as $key => $stock) {
                                            $remqty = ($stock->quantity_in-$stock->quantity_out);
                                            if ($qtysold > 0) {
                                                if ($qtysold <= $remqty) {
                                                    $saleitemData = new AnSaleItem;
                                                    $saleitemData->shop_id = $shop->id;
                                                    $saleitemData->an_sale_id = $sale->id;
                                                    $saleitemData->product_id = $value->product_id;
                                                    $saleitemData->category_id = $catId;
                                                    $saleitemData->stock_id = $stock->id;
                                                    $saleitemData->product_unit_id = $value->product_unit_id;
                                                    $saleitemData->quantity_sold = $qtysold;
                                                    $saleitemData->unit_cost = $stock->unit_cost;
                                                    $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
                                                    $saleitemData->retail_price = $retail_price;
                                                    $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
                                                    $saleitemData->disc_percent = $value->disc_percent;
                                                    $saleitemData->discount = $unit_discount;
                                                    $saleitemData->total_discount = $saleitemData->quantity_sold*$saleitemData->discount;
                                                    $saleitemData->time_created = $now;
                                                    $saleitemData->with_vat = $value->with_vat;
                                                    if ($value->with_vat == 'yes') {
                                                        $saleitemData->tax_amount = ($saleitemData->price-$saleitemData->total_discount)*($settings->tax_rate/100);
                                                    }
                                                    $saleitemData->sold_in = $value->sold_in;
                                                    $saleitemData->save();

                                                    $stock->quantity_out = $stock->quantity_out+$qtysold;
                                                    if ($stock->quantity_in == $stock->quantity_out) {
                                                        $stock->is_utilized = true;
                                                    }
                                                    $stock->save();
                                                }else{
                                                    $saleitemData = new AnSaleItem;
                                                    $saleitemData->shop_id = $shop->id;
                                                    $saleitemData->an_sale_id = $sale->id;
                                                    $saleitemData->product_id = $value->product_id;
                                                    $saleitemData->category_id = $catId;
                                                    $saleitemData->stock_id = $stock->id;
                                                    $saleitemData->product_unit_id = $value->product_unit_id;
                                                    $saleitemData->quantity_sold = $remqty;
                                                    $saleitemData->unit_cost = $stock->unit_cost;
                                                    $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
                                                    $saleitemData->retail_price = $retail_price;
                                                    $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
                                                    $saleitemData->disc_percent = $value->disc_percent;
                                                    $saleitemData->discount = $unit_discount;
                                                    $saleitemData->total_discount = $saleitemData->quantity_sold*$saleitemData->discount;
                                                    $saleitemData->time_created = $now;
                                                    $saleitemData->with_vat = $value->with_vat;
                                                    if ($value->with_vat == 'yes') {
                                                        $saleitemData->tax_amount = ($saleitemData->price-$saleitemData->total_discount)*($settings->tax_rate/100);
                                                    }
                                                    $saleitemData->sold_in = $value->sold_in;
                                                    $saleitemData->save();

                                                    $stock->quantity_out = $stock->quantity_out+$remqty;
                                                    if ($stock->quantity_in == $stock->quantity_out) {
                                                        $stock->is_utilized = true;
                                                    }
                                                    $stock->save();
                                                }
                                            }
                                            $qtysold -= $remqty;
                                        }

                                        if ($qtysold > 0) {
                                            $saleitemData = new AnSaleItem;
                                            $saleitemData->shop_id = $shop->id;
                                            $saleitemData->an_sale_id = $sale->id;
                                            $saleitemData->product_id = $value->product_id;
                                            $saleitemData->category_id = $catId;
                                            $saleitemData->product_unit_id = $value->product_unit_id;
                                            $saleitemData->quantity_sold = $qtysold;
                                            $saleitemData->unit_cost = $product->unit_cost;
                                            $saleitemData->buying_price = $saleitemData->quantity_sold*$saleitemData->unit_cost;
                                            $saleitemData->retail_price = $retail_price;
                                            $saleitemData->price = $saleitemData->retail_price*$saleitemData->quantity_sold;
                                            $saleitemData->disc_percent = $value->disc_percent;
                                            $saleitemData->discount = $unit_discount;
                                            $saleitemData->total_discount = $saleitemData->quantity_sold*$saleitemData->discount;
                                            $saleitemData->time_created = $now;
                                            $saleitemData->with_vat = $value->with_vat;
                                            if ($value->with_vat == 'yes') {
                                                $saleitemData->tax_amount = ($saleitemData->price-$saleitemData->total_discount)*($settings->tax_rate/100);
                                            }
                                            $saleitemData->sold_in = $value->sold_in;
                                            $saleitemData->save();
                                        }
                                    }

                                    dispatch(new StockUpdaterJob($shop, $product->id));
                                }
                            }

                            $prodsale_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                            $prodsale_discount = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                            $prodtax_amount = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                        }
                    }

                    $sale->sale_amount = ($servsale_amount+$prodsale_amount);
                    $sale->sale_discount = ($servsale_discount+$prodsale_discount);
                    $sale->tax_amount = ($servtax_amount+$prodtax_amount);
                    $sale->save();
                    
                    $netsaleamount = ($sale->sale_amount - $sale->sale_discount)+$sale->tax_amount;
                    if ($saletemp->is_from_so) {
                        $uninvoicedqty = array();
                        $saleorder = SaleOrder::find($saletemp->sale_order_id);
                        $orderItems = SaleOrderItem::where('sale_order_id', $saletemp->sale_order_id)->get();
                        foreach($orderItems as $key => $orderitem) {
                            $punit = ProductUnit::find($orderitem->product_unit_id);
                            $quantity = $orderitem->quantity * $punit->qty_equal_to_basic;
                            $orderinvitems = AnSaleItem::where('product_id', $orderitem->product_id)->join('an_sales', 'an_sales.id', '=', 'an_sale_items.an_sale_id')->where('sale_order_id', $saleorder->id)->get();
                            $itemqtyinvoieced = 0;
                            foreach ($orderinvitems as $key => $value) {
                                $itemqtyinvoieced += $value->quantity_sold;
                            }
                            if ($quantity != $itemqtyinvoieced) {
                                array_push($uninvoicedqty, ['product_id' => $orderitem->product_id, 'qty' => $quantity-$itemqtyinvoieced]);
                            }
                        }
                        // Log::info($uninvoicedqty);
                        if (count($uninvoicedqty) <= 0) {
                            $saleorder->status = 'Full Invoiced';
                            $saleorder->is_invoiced = true;
                            $saleorder->save();
                        }else{
                            $saleorder->status = 'Partially Invoiced';
                            $saleorder->save();
                        }
                    }

                    if (!is_null($saletemp->pro_invoice_id)) {
                        $uninvoicedqty = array();
                        $proinvoice = ProInvoice::find($saletemp->pro_invoice_id);
                        $orderItems = InvoiceItem::where('pro_invoice_id', $proinvoice->id)->get();
                        foreach($orderItems as $key => $orderitem) {
                            $punit = ProductUnit::find($orderitem->product_unit_id);
                            $quantity = $orderitem->quantity * $punit->qty_equal_to_basic;
                            $orderinvitems = AnSaleItem::where('product_id', $orderitem->product_id)->join('an_sales', 'an_sales.id', '=', 'an_sale_items.an_sale_id')->where('pro_invoice_id', $proinvoice->id)->get();
                            $itemqtyinvoieced = 0;
                            foreach ($orderinvitems as $key => $value) {
                                $itemqtyinvoieced += $value->quantity_sold;
                            }
                            if ($quantity != $itemqtyinvoieced) {
                                array_push($uninvoicedqty, ['product_id' => $orderitem->product_id, 'qty' => $quantity-$itemqtyinvoieced]);
                            }
                        }
                        // Log::info($uninvoicedqty);
                        if (count($uninvoicedqty) <= 0) {
                            $proinvoice->status = 'Full Invoiced';
                            $proinvoice->save();
                        }else{
                            $proinvoice->status = 'Partially Invoiced';
                            $proinvoice->save();
                        }
                    }

                    if ($request['sale_type'] == 'cash' && Auth::user()->can('create-sale-payment')) {
                        if ($pay_type != 'Multiple') {
                            $amount_paid = $netsaleamount;
                            $sale->sale_amount_paid = $amount_paid;
                            if (!$saletemp->is_from_so) {
                                $sale->status = 'Paid';
                                $sale->is_paid = true;
                                $sale->time_paid = \Carbon\Carbon::now();
                            }
                            $sale->save();

                            $maxrec_no = SalePayment::where('shop_id', $shop->id)->latest()->first();
                            $receipt_no = 0;
                            if (!is_null($maxrec_no)) {
                                $receipt_no = $maxrec_no->receipt_no + 1;
                            } else {
                                $receipt_no = 1;
                            }

                            $acctrans = new CustomerTransaction();
                            $acctrans->shop_id = $shop->id;
                            $acctrans->user_id = $user->id;
                            $acctrans->customer_id = $sale->customer_id;
                            $acctrans->an_sale_id = $sale->id;
                            $acctrans->invoice_no = $sale->invoice_no;
                            $acctrans->amount = $netsaleamount;
                            $acctrans->currency = $saletemp->currency;
                            $acctrans->defcurr = $saletemp->defcurr;
                            $acctrans->ex_rate = $saletemp->ex_rate;
                            $acctrans->date = $now;
                            $acctrans->save();

                            $payacctrans = new CustomerTransaction();
                            $payacctrans->shop_id = $shop->id;
                            $payacctrans->user_id = $user->id;
                            $payacctrans->customer_id = $sale->customer_id;
                            $payacctrans->invoice_no = $sale->invoice_no;
                            $payacctrans->currency = $saletemp->currency;
                            $payacctrans->defcurr = $saletemp->defcurr;
                            $payacctrans->ex_rate = $saletemp->ex_rate;
                            $payacctrans->date = $now;
                            $payacctrans->receipt_no = $receipt_no;
                            $payacctrans->payment = $amount_paid;
                            $payacctrans->trans_invoice_amount = $amount_paid;
                            $payacctrans->payment_mode = $pay_type;
                            $payacctrans->bank_name = $bank_name;
                            $payacctrans->bank_branch = $request['bank_branch'];
                            $payacctrans->cheque_no = $cheque_no;
                            $payacctrans->save();

                            $payment = new SalePayment();
                            $payment->an_sale_id = $sale->id;
                            $payment->shop_id = $shop->id;
                            $payment->trans_id = $payacctrans->id;
                            $payment->receipt_no = $receipt_no;
                            $payment->pay_mode = $pay_type;
                            $payment->bank_name = $bank_name;
                            $payment->bank_branch = $request['bank_branch'];
                            $payment->pay_date = $now;
                            $payment->cheque_no = $cheque_no;
                            $payment->amount = $amount_paid;
                            $payment->currency = $saletemp->currency;
                            $payment->defcurr = $saletemp->defcurr;
                            $payment->ex_rate = $saletemp->ex_rate;
                            if (!$saletemp->is_from_so) {
                                $payment->cashier = Auth::user()->first_name.' '.Auth::user()->last_name;
                                $payment->cc_time = Carbon::now();
                            }
                            $payment->save();

                            $astmt = new AccountStatement();
                            $astmt->shop_id = $shop->id;
                            $astmt->user_id = $user->id;
                            $astmt->customer_transaction_id = $payacctrans->id;
                            $astmt->account_id = $account->id;
                            $astmt->date = $now;
                            $astmt->debit = $payacctrans->payment;
                            $astmt->credit = 0;
                            $astmt->description = 'Sales Payment (Receipt No. '.sprintf('%04d', $payacctrans->receipt_no).')';
                            $astmt->save();
                        }else{
                            Log::info('User is redirected to Invoice to create Payments');
                        }

                    } else {
                        $acctrans = new CustomerTransaction();
                        $acctrans->shop_id = $shop->id;
                        $acctrans->user_id = $user->id;
                        $acctrans->customer_id = $sale->customer_id;
                        $acctrans->an_sale_id = $sale->id;
                        $acctrans->invoice_no = $sale->invoice_no;
                        $acctrans->amount = $netsaleamount;
                        $acctrans->currency = $saletemp->currency;
                        $acctrans->defcurr = $saletemp->defcurr;
                        $acctrans->ex_rate = $saletemp->ex_rate;
                        $acctrans->date = $now;
                        $acctrans->save();
                        // Log::info($request['use_pre_payment']);
                        if ($request['use_pre_payment'] == 1) {
                            $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $sale->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();

                            if (!is_null($utransactions)) {
                                $curr_amount = $netsaleamount;
                                foreach ($utransactions as $key => $trans) {
                                    $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                                    if ($rem_amount > 0) {
                                        $paidamount = 0;
                                        if ($rem_amount > $curr_amount) {
                                            $paidamount = $curr_amount;
                                            $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                            $trans->save();
                                        } else {
                                            $paidamount = $rem_amount;
                                            $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                                            $trans->is_utilized = true;
                                            $trans->save();
                                        }

                                        $payment = new SalePayment();
                                        $payment->an_sale_id = $sale->id;
                                        $payment->shop_id = $shop->id;
                                        $payment->trans_id = $trans->id;
                                        $payment->receipt_no = $trans->receipt_no;
                                        $payment->pay_mode = $trans->payment_mode;
                                        $payment->bank_name = $trans->bank_name;
                                        $payment->bank_branch = $trans->bank_branch;
                                        $payment->pay_date = $trans->date;
                                        $payment->cheque_no = $trans->cheque_no;
                                        $payment->amount = $paidamount;
                                        $payment->currency = $trans->currency;
                                        $payment->defcurr = $trans->defcurr;
                                        $payment->ex_rate = $trans->ex_rate;
                                        $payment->cashier = $trans->cashier;
                                        $payment->cc_time = $trans->cc_tim;
                                        $payment->is_fresh_pay = false;
                                        $payment->save();
                                        
                                        $sale->sale_amount_paid = $paidamount;
                                        $sale->save();
                                        if ($netsaleamount == $sale->sale_amount_paid) {
                                            $sale->status = 'Paid';
                                            $sale->is_paid = true;
                                            $sale->time_paid = \Carbon\Carbon::now();
                                            $sale->save();
                                        } elseif ($netsaleamount > $sale->sale_amount_paid && $sale->sale_amount_paid > 0) {
                                            $sale->status = 'Partially Paid';
                                            $sale->is_paid = false;
                                            $sale->save();
                                        } elseif ($netsaleamount < $sale->sale_amount_paid) {
                                            $sale->status = 'Excess Paid';
                                            $sale->is_paid = true;
                                            $sale->time_paid = \Carbon\Carbon::now();
                                            $sale->save();
                                        } elseif ($sale->sale_amount_paid == 0) {
                                            $sale->status = 'Unpaid';
                                            $sale->is_paid = false;
                                            $sale->save();
                                        }
                                        
                                        $curr_amount -= $paidamount;
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($request['device_id'])) {
                        DeviceSale::create([
                            'device_id' => $request['device_id'],
                            'an_sale_id' => $sale->id
                        ]);
                    }
                        
                    $cust = Customer::find($sale->customer_id);
                    if (!is_null($cust)) {
                        // Check customer balance and limitation
                        $custbalance = AnSale::where('an_sales.shop_id', $shop->id)->where('an_sales.is_deleted', false)->whereRaw('((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid) > 0')->where('an_sales.customer_id', $cust->id)->select(\DB::raw('SUM(((((sale_amount-sale_discount)+tax_amount)-((return_amount-return_discount)+return_tax))-sale_amount_paid)) as amount'))->first();
                        if ($cust->due_amount_limit > 0 && $custbalance->amount > $cust->due_amount_limit) {
                            $cust->is_active = false;
                            $cust->save();
                        }

                        $smsacc = SmsAccount::where('shop_id', $shop->id)->first();
                        if (!is_null($smsacc)) {
                            $senderid = SenderId::where('sms_account_id', $smsacc->id)->where('auto_sms', true)->first();
                            if (!is_null($senderid)) {
                                $autotemp = SmsTemplate::where('shop_id', $shop->id)->where('is_auto_sms', true)->where('temp_for', 'sale')->first();
                                if (!is_null($autotemp)) {
                                    $message = $autotemp->message;
                                    if (!is_null($this->formattedNumber($cust->phone))) {
                                        $phone = $this->formattedNumber($cust->phone);
                                        $invoice_no = sprintf('%05d', $sale->invoice_no);
                                        $due_date = date('d, M Y', strtotime($sale->due_date));
                                        $amount_due = $netsaleamount - $sale->amount_paid;
                                        $sms = str_replace('{customer_name}', $cust->name, $message);
                                        $sms1 = str_replace('{sale_date}', date('d, M Y', strtotime($cust->sale_date)), $sms);
                                        $sms2 = str_replace('{due_date}', $due_date, $sms1);
                                        $sms3 = str_replace('{invoice_no}', $invoice_no, $sms2);
                                        $msg = str_replace('{amount_due}', number_format($amount_due), $sms3);

                                        dispatch(new SendSMS($smsacc->username, $smsacc->password, $senderid->name, $phone, $msg));
                                    }else{
                                        Log::info('Mobile Number '.$cust->phone.' is invalid. SMS not sent');
                                    }
                                }
                            }
                        }
                    }

                    //delete all data on SaleItemTemp model
                    $is_from_so = $saletemp->is_from_so;
                    $temp_items = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
                    foreach ($temp_items as $key => $item) {
                        $item->delete();
                    }
                    
                    foreach ($servitems as $key => $value) {
                        $value->delete();
                    }
                    $saletemp->delete();

                    if ($request['issue_vfd'] == 'on') {
                        $this->issueVFD($sale->id);
                    }

                    if ($pay_type == 'Multiple') {
                        return redirect()->route('invoices.show', encrypt($sale->id))->with('success', 'Invoice created successfully. Please Add Payments from different payment mode');
                    }elseif($settings->always_print_invoice) {
                        return redirect()->route('invoices.show', encrypt($sale->id))->with('success', 'Invoice created successfully');
                    }else{
                        return redirect('pos')->with('success', 'Invoice created successfully');
                    }
                } else {
                    return redirect()->back()->with('warning', 'Please Select at least one item to create Invoice!.');
                }
            }else{
                return redirect('pos')->with('error', 'Customer required. Please select customer');
            }
        }
    }

    public function formattedNumber($number)
    {
        if ($this->validate_mobile($number)) {
            $num = preg_replace('/^(?:\+?255|0)?/','255', $number);
            return $num;
        } else{
            return null;
        }
    }

    public function validate_mobile($mobile)
    {   
        $mobile = str_replace(' ', '', $mobile);
        $mobile = preg_replace('/^(?:\+?255|0)?/','0', $mobile);
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }


    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        if ($shop->business_type_id == 3) {
            $temp_items = ServiceItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
            $temp_serv_items = ServiceItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_serv_items as $key => $item) {
                $item->delete();
            }
            $temp_items = SaleItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        } else {
            $temp_items = SaleItemTemp::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
        }

        $success = 'Sale was successfully canceled.';
        return redirect('pos')->with('success', $success);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find($id);
        $scount = AnSale::where('shop_id', $shop->id)->where('id', '<', $sale->id)->count();
        $recno = $scount + 1;
        $customer = Customer::find($sale->customer_id);
        $items = AnSaleItem::where('an_sale_id',  $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->get();
        $date = \Carbon\Carbon::now()->toDayDateTimeString();
        $change = 0;
        $due = 0;
        if ($sale->sale_amount_paid > $sale->sale_amount) {
            $change = $sale->sale_amount_paid - $sale->sale_amount;
        }
        if ($sale->sale_amount_paid <= $sale->sale_amount) {
            $due = $sale->sale_amount - $sale->sale_amount_paid;
        }
        // return Response::json(['sale' => $sale]);
        return response()->json(['shop' => $shop, 'sale' => $sale, 'recno' => $recno, 'customer' => $customer, 'items' => $items, 'date' => $date, 'change' => $change, 'due' => $due]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale(New Invoice)';
        $title_sw = 'Sehemu ya Kuuzia';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::find($request['id']);
            if (!is_null($saletemp)) {
                $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

                $settings = Setting::where('shop_id', $shop->id)->first();
                if (is_null($settings)) {
                    $settings = Setting::create([
                        'shop_id' => $shop->id,
                        'tax_rate' => 18,
                        'inv_no_type' => 'Automatic'
                    ]);
                }

                $mindays = 0;
                $date = Carbon::parse($payment->expire_date);
                $now = Carbon::now();
                $status = $date->diffInDays($now);
                $paydate = Carbon::parse($payment->created_at);

                $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
                if (!is_null($lastpay)) {
                    $lastexp = Carbon::parse($lastpay->expire_date);
                    $oldpaydate = Carbon::parse($lastpay->created_at);
                    $slipdays = $paydate->diffInDays($lastexp);
                    // return $slipdays;
                    if ($slipdays < 15) {
                        $mindays = $now->diffInDays($oldpaydate);
                    } else {
                        $mindays = $now->diffInDays($paydate);
                    }
                } else {
                    $mindays = $now->diffInDays($paydate);
                }

                if ($mindays < 10) {
                    $mindays = 15;
                }

                $products = null;
                if ($settings->is_filling_station) {
                    $products = $shop->products()->get();
                }
                $accounts = Account::where('shop_id', $shop->id)->get();

                $custids = array(
                    ['id' => 1, 'name' => 'TIN'],
                    ['id' => 2, 'name' => 'Driving License'],
                    ['id' => 3, 'name' => 'Voters Number'],
                    ['id' => 4, 'name' => 'Passport'],
                    ['id' => 5, 'name' => 'NIN'],
                    ['id' => 6, 'name' => 'NIL'],
                    ['id' => 7, 'name' => 'Meter No']
                );

                $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
                $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $saletemp->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->count();
                if ($shop->business_type_id == 3) {
                    $devices = Device::where('shop_id', $shop->id)->get();
                    $grades = Grade::where('shop_id', $shop->id)->get();
                    return view('sales.invoices.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'accounts', 'mindays', 'grades', 'custids', 'categories', 'currencies', 'utransactions', 'notes'));
                } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
                    return view('sales.invoices.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'custids', 'categories', 'products', 'currencies', 'utransactions', 'notes'));
                } else {
                    return view('sales.invoices.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'products', 'custids', 'categories', 'currencies', 'utransactions', 'notes'));
                }
            }else{
                return redirect('pos');
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }

    public function postTempData(Request $request)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale(New Invoice)';
        $title_sw = 'Sehemu ya Kuuzia';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::find($request['sale_temp_id']);
            if (!is_null($saletemp)) {
                $saletemp->customer_id = $request['customer_id'];
                $saletemp->sale_date = $request['sale_date'];
                $saletemp->sale_type = $request['sale_type'];
                $saletemp->pay_type = $request['pay_type'];
                $saletemp->save();
                
                $customer = Customer::find($saletemp->customer_id);
                if ($customer->is_active) {
                    $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

                    $settings = Setting::where('shop_id', $shop->id)->first();
                    if (is_null($settings)) {
                        $settings = Setting::create([
                            'shop_id' => $shop->id,
                            'tax_rate' => 18,
                            'inv_no_type' => 'Automatic'
                        ]);
                    }

                    $mindays = 0;
                    $date = Carbon::parse($payment->expire_date);
                    $now = Carbon::now();
                    $status = $date->diffInDays($now);
                    $paydate = Carbon::parse($payment->created_at);

                    $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
                    if (!is_null($lastpay)) {
                        $lastexp = Carbon::parse($lastpay->expire_date);
                        $oldpaydate = Carbon::parse($lastpay->created_at);
                        $slipdays = $paydate->diffInDays($lastexp);
                        // return $slipdays;
                        if ($slipdays < 15) {
                            $mindays = $now->diffInDays($oldpaydate);
                        } else {
                            $mindays = $now->diffInDays($paydate);
                        }
                    } else {
                        $mindays = $now->diffInDays($paydate);
                    }

                    if ($mindays < 10) {
                        $mindays = 15;
                    }

                    $products = null;
                    if ($settings->is_filling_station) {
                        $products = $shop->products()->get();
                    }
                    $accounts = Account::where('shop_id', $shop->id)->get();

                    $custids = array(
                        ['id' => 1, 'name' => 'TIN'],
                        ['id' => 2, 'name' => 'Driving License'],
                        ['id' => 3, 'name' => 'Voters Number'],
                        ['id' => 4, 'name' => 'Passport'],
                        ['id' => 5, 'name' => 'NIN'],
                        ['id' => 6, 'name' => 'NIL'],
                        ['id' => 7, 'name' => 'Meter No']
                    );

                    $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                    $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
                    $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $saletemp->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->count();
                    if ($shop->business_type_id == 3) {
                        $devices = Device::where('shop_id', $shop->id)->get();
                        $grades = Grade::where('shop_id', $shop->id)->get();
                        return view('sales.invoices.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'accounts', 'mindays', 'grades', 'custids', 'categories', 'currencies', 'utransactions', 'notes'));
                    } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
                        return view('sales.invoices.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'custids', 'products', 'currencies', 'categories', 'utransactions', 'notes'));
                    } else {
                        return view('sales.invoices.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'products', 'custids', 'categories', 'currencies', 'utransactions', 'notes'));
                    }
                }else{
                    $temp_serv_items = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
                    foreach ($temp_serv_items as $key => $item) {
                        $item->delete();
                    }

                    $temp_items = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
                    foreach ($temp_items as $key => $item) {
                        $item->delete();
                    }
                    $saletemp->delete();
                    return redirect('pos')->with('info', 'Customer '.$customer->name.' is currently disabled');
                }
            }else{
                return redirect('pos');
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }


    public function resetTempData(Request $request)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale(New Invoice)';
        $title_sw = 'Sehemu ya Kuuzia';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::find($request['id']);
            if (!is_null($saletemp)) {
                $saletemp->sale_type = null;
                $saletemp->due_date = null;
                $saletemp->save();
                $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

                $settings = Setting::where('shop_id', $shop->id)->first();
                if (is_null($settings)) {
                    $settings = Setting::create([
                        'shop_id' => $shop->id,
                        'tax_rate' => 18,
                        'inv_no_type' => 'Automatic'
                    ]);
                }

                $mindays = 0;
                $date = Carbon::parse($payment->expire_date);
                $now = Carbon::now();
                $status = $date->diffInDays($now);
                $paydate = Carbon::parse($payment->created_at);

                $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
                if (!is_null($lastpay)) {
                    $lastexp = Carbon::parse($lastpay->expire_date);
                    $oldpaydate = Carbon::parse($lastpay->created_at);
                    $slipdays = $paydate->diffInDays($lastexp);
                    // return $slipdays;
                    if ($slipdays < 15) {
                        $mindays = $now->diffInDays($oldpaydate);
                    } else {
                        $mindays = $now->diffInDays($paydate);
                    }
                } else {
                    $mindays = $now->diffInDays($paydate);
                }

                if ($mindays < 10) {
                    $mindays = 15;
                }

                $products = null;
                if ($settings->is_filling_station) {
                    $products = $shop->products()->get();
                }
                $accounts = Account::where('shop_id', $shop->id)->get();

                $custids = array(
                    ['id' => 1, 'name' => 'TIN'],
                    ['id' => 2, 'name' => 'Driving License'],
                    ['id' => 3, 'name' => 'Voters Number'],
                    ['id' => 4, 'name' => 'Passport'],
                    ['id' => 5, 'name' => 'NIN'],
                    ['id' => 6, 'name' => 'NIL'],
                    ['id' => 7, 'name' => 'Meter No']
                );

                $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
                $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $saletemp->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->count();
                if ($shop->business_type_id == 3) {
                    $devices = Device::where('shop_id', $shop->id)->get();
                    $grades = Grade::where('shop_id', $shop->id)->get();
                    return view('sales.invoices.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'accounts', 'mindays', 'grades', 'custids', 'categories', 'currencies', 'utransactions', 'notes'));
                } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
                    return view('sales.invoices.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'custids', 'categories', 'products', 'currencies', 'utransactions', 'notes'));
                } else {
                    return view('sales.invoices.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'products', 'custids', 'categories', 'currencies', 'utransactions', 'notes'));
                }
            }else{
                return redirect('pos');
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }

    public static function createSaleFromSO(Request $request)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale(New Invoice)';
        $title_sw = 'Sehemu ya Kuuzia';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $sale = AnSale::where('shop_id', $shop->id)->count();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::find($request['id']);
            $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

            $settings = Setting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);
            }

            $mindays = 0;
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                } else {
                    $mindays = $now->diffInDays($paydate);
                }
            } else {
                $mindays = $now->diffInDays($paydate);
            }

            if ($mindays < 10) {
                $mindays = 15;
            }

            $products = null;
            if ($settings->is_filling_station) {
                $products = $shop->products()->get();
            }
            $accounts = Account::where('shop_id', $shop->id)->get();

            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
            $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
            $utransactions = 0;
            if ($shop->business_type_id == 3) {
                $devices = Device::where('shop_id', $shop->id)->get();
                $grades = Grade::where('shop_id', $shop->id)->get();
                return view('sales.invoices.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'accounts', 'mindays', 'grades', 'custids', 'currencies', 'utransactions', 'categories', 'notes'));
            } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
                return view('sales.invoices.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'custids', 'products', 'currencies', 'utransactions', 'categories', 'notes'));
            } else {
                return view('sales.invoices.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'products', 'custids', 'currencies', 'utransactions', 'categories', 'notes'));
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }

    public static function createSaleFromProforma(Request $request)
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale(New Invoice)';
        $title_sw = 'Sehemu ya Kuuzia';
        $shop = Shop::find(Session::get('shop_id'));
        $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', false)->first();
        if (!is_null($payment)) {
            $user = Auth::user();
            $sale = AnSale::where('shop_id', $shop->id)->count();
            $customers = Customer::where('shop_id', $shop->id)->orderBy('id', 'desc')->get();

            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $saletemp = SaleTemp::find($request['id']);
            $pendingtemps = SaleTemp::where('sale_temps.shop_id', $shop->id)->where('user_id', $user->id)->whereNotNull('customer_id')->join('customers', 'customers.id', '=', 'sale_temps.customer_id')->select('sale_temps.id as id', 'name', 'sale_temps.created_at as created_at')->get();

            $settings = Setting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);
            }

            $mindays = 0;
            $date = Carbon::parse($payment->expire_date);
            $now = Carbon::now();
            $status = $date->diffInDays($now);
            $paydate = Carbon::parse($payment->created_at);

            $lastpay = Payment::where('shop_id', $shop->id)->where('is_expired', 1)->where('is_for_module', false)->orderBy('created_at', 'desc')->first();
            if (!is_null($lastpay)) {
                $lastexp = Carbon::parse($lastpay->expire_date);
                $oldpaydate = Carbon::parse($lastpay->created_at);
                $slipdays = $paydate->diffInDays($lastexp);
                // return $slipdays;
                if ($slipdays < 15) {
                    $mindays = $now->diffInDays($oldpaydate);
                } else {
                    $mindays = $now->diffInDays($paydate);
                }
            } else {
                $mindays = $now->diffInDays($paydate);
            }

            if ($mindays < 10) {
                $mindays = 15;
            }

            $products = null;
            if ($settings->is_filling_station) {
                $products = $shop->products()->get();
            }
            $accounts = Account::where('shop_id', $shop->id)->get();

            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
            $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
            $utransactions = 0;
            if ($shop->business_type_id == 3) {
                $devices = Device::where('shop_id', $shop->id)->get();
                $grades = Grade::where('shop_id', $shop->id)->get();
                return view('sales.invoices.service-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'devices', 'accounts', 'mindays', 'grades', 'custids', 'currencies', 'utransactions', 'categories', 'notes'));
            } elseif ($shop->business_type_id == 4 || $settings->is_manufacturing_with_service) {
                return view('sales.invoices.both-pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'custids', 'products', 'currencies', 'utransactions', 'categories', 'notes'));
            } else {
                return view('sales.invoices.pos', compact('page', 'title', 'title_sw', 'payment', 'status', 'sale', 'saletemp', 'pendingtemps', 'customers', 'settings', 'shop', 'accounts', 'mindays', 'products', 'custids', 'currencies', 'utransactions', 'categories', 'notes'));
            }
        } else {
            $info = 'Dear customer your account is not activated please make payment and activate now.';
            return redirect('verify-payment')->with('info', $info);
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Log::info($request);
        $saletemp = SaleTemp::find($id);
        if (!is_null($saletemp)) {
                
            $local_ex_rate = 1;
            $foreign_ex_rate = 1;
            $ex_rate = 1;
            if ($request['currency'] != $saletemp->defcurr) {
                if ($request['ex_rate_mode'] == 'Foreign') {
                    $local_ex_rate = $request['local_ex_rate'];
                    $lastsale = AnSale::where('shop_id', $saletemp->shop_id)->where('currency', $request['currency'])->where('ex_rate', '!=', 1)->latest()->first();
                    if (!is_null($lastsale) && $local_ex_rate == 1) {
                        $ex_rate = $lastsale->ex_rate;
                        $local_ex_rate = $lastsale->ex_rate;
                    }else{
                        $ex_rate = $local_ex_rate;
                    }
                } else {
                    $foreign_ex_rate = $request['foreign_ex_rate'];
                    $ex_rate = 1 / $foreign_ex_rate;
                }
            }

            $saletemp->customer_id = $request['customer_id'];
            $saletemp->date_set = $request['date_set'];
            $saletemp->sale_date = $request['sale_date'];
            $saletemp->sale_type = $request['sale_type'];
            $saletemp->pay_type = $request['pay_type'];
            $saletemp->currency = $request['currency'];
            $saletemp->ex_rate_mode = $request['ex_rate_mode'];
            $saletemp->local_ex_rate = $local_ex_rate;
            $saletemp->foreign_ex_rate = $foreign_ex_rate;
            $saletemp->ex_rate = $ex_rate;
            $saletemp->due_date = $request['due_date'];
            $saletemp->comments = $request['comments'];
            $saletemp->save();

            return $saletemp;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AnSale  $anSale
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $saletemp = SaleTemp::find(decrypt($id));
        if (!is_null($saletemp)) {
            $is_from_so = $saletemp->is_from_so;
            $temp_serv_items = ServiceItemTemp::where('sale_temp_id', $saletemp->id)->get();
            foreach ($temp_serv_items as $key => $item) {
                $item->delete();
            }

            $temp_items = SaleItemTemp::where('sale_temp_id', $saletemp->id)->get();
            foreach ($temp_items as $key => $item) {
                $item->delete();
            }
            $saletemp->delete();
            if ($is_from_so) {
                $success = 'Sale was successfully canceled.';
                return redirect('sale-orders')->with('success', $success);
            }else{
                $success = 'Sale was successfully canceled.';
                return redirect('pos')->with('success', $success);
            }
        }
    }

    public function issueVFD($saleid)
    {
        
    }
}
