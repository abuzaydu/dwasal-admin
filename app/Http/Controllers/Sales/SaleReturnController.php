<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \DB;
use \Carbon\Carbon;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Stock;
use App\Jobs\StockUpdaterJob;
use App\Models\SalePayment;
use App\Models\Customer;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\CreditNote;
use App\Models\CustomerTransaction;

class SaleReturnController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Sales Returns';
        $title = 'Sales Returns';
        $title_sw = 'Mauzo yaliyorudishwa';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sreturns = SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_returns.id as id', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.return_tax_amount as return_tax_amount', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'customers.name as name')->orderBy('created_at', 'desc')->get();

        $customer = Customer::where('shop_id', $shop->id)->first();

        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();
        $is_post_query = false;
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
        if (!empty($request['sale_date'])) {
            $start_date = $request['sale_date'];
            $end_date = $request['sale_date'];
            $start = $request['sale_date'].' 00:00:00';
            $end = $request['sale_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = '';
        $mysales = AnSale::where('an_sales.shop_id', $shop->id)->whereBetween('an_sales.time_created', [$start, $end])->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('an_sales.id as id', 'an_sales.time_created as time_created', 'customers.name')->get();
        
        $sales = array();
        foreach ($mysales as $key => $sale) {
            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold')
            ]);

            $saleitems = array();
            foreach ($items as $key => $item) {
                array_push($saleitems, $item->name.'('.$item->quantity_sold.')');
            }
            array_push($sales, ['id' => $sale->id, 'customer' => $sale->name, 'date' => $sale->time_created, 'items' => implode(',', $saleitems)]);
        }

        $salesdate = date('d M Y', strtotime($start));

        return view('sales.returns.index', compact('page', 'title', 'title_sw', 'sreturns', 'customer', 'duration', 'is_post_query', 'start_date', 'end_date', 'settings', 'sales', 'salesdate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page = 'Create Sale Return';
        $title = 'Create Sale Return';
        $title_sw = 'Tengeneza Uzo lilirudishwa';

        $is_post_query = false;
        $start_date = '';
        $end_date = '';
        $duration = '';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sale = AnSale::where('an_sales.id', decrypt($id))->where('an_sales.shop_id', $shop->id)->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.phone as phone', 'customers.email as email', 'an_sales.id as id', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.time_created as time_created')->first();
        if (!is_null($sale)) {
            $items = AnSaleItem::where('an_sale_id', $sale->id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.retail_price as retail_price'),
                DB::raw('SUM(an_sale_items.price) as price')
            ]);

            $date = Carbon::now()->toDayDateTimeString();

            $salereturn = SaleReturn::where('an_sale_id', $sale->id)->first();
            if (is_null($salereturn)) {
                $salereturn = SaleReturn::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                ]);
            }
            $creditnote = CreditNote::where('an_sale_id', $sale->id)->first();
            if (is_null($creditnote)) {
                $max_no = CreditNote::where('shop_id', $shop->id)->latest()->first();
                $credit_note_no = 0;
                if (!is_null($max_no)) {
                    $credit_note_no = $max_no->credit_note_no+1;
                }else{
                    $credit_note_no = 1;
                }
                $creditnote = CreditNote::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'credit_note_no' => $credit_note_no,
                ]);
            }
    
            $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_uom as basic_uom', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.retail_price as retail_price', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

            $total = 0;
            $discount = 0;
            $tax = 0;
            foreach ($sritems as $key => $item) {
                $total += $item->price;
                $discount += $item->discount;
                $tax += $item->tax_amount;
            }

            return view('sales.returns.create', compact('page', 'title', 'title_sw', 'sale', 'sale', 'items', 'shop', 'settings', 'date', 'salereturn', 'sritems', 'total', 'discount', 'is_post_query', 'start_date', 'end_date', 'duration'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find($request['an_sale_id']);
        $user = Auth::user();
        if (!is_null($sale)) {
            $salereturn = SaleReturn::where('an_sale_id', $sale->id)->first();
            if (is_null($salereturn)) {
                $salereturn = SaleReturn::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                ]);
            }

            return redirect('create-sale-return/'.encrypt($sale->id));
        }else{
            return redirect()->back()->with('info', 'No Sales with info provided');
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
        $page = 'Sales Return';
        $title = 'Sale Return';
        $title_sw = 'Uzo lilirudishwa';
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $accounts = Account::where('shop_id', $shop->id)->get();  

        $salereturn = SaleReturn::where('sale_returns.id', decrypt($id))->where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->join('users', 'users.id', '=', 'sale_returns.user_id')->select('sale_returns.id as id', 'an_sale_id', 'customer_id', 'return_date', 'invoice_no', 'sale_returns.sale_return_amount as sale_return_amount', 'sale_returns.sale_return_discount as sale_return_discount', 'sale_returns.reason as reason', 'sale_returns.created_at as created_at', 'sale_returns.updated_at as updated_at', 'customers.name as name', 'customers.postal_address as postal_address', 'customers.physical_address as physical_address', 'customers.street as street', 'customers.email as email', 'customers.phone as phone', 'first_name', 'last_name')->first();

        $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'product_code', 'products.basic_uom as basic_uom', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.retail_price as retail_price', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

        return view('sales.returns.show', compact('page', 'title', 'title_sw', 'settings', 'shop', 'settings', 'accounts', 'salereturn', 'sritems'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $page = 'Edit sale Return';
        $title = 'Edit Sale Return';
        $title_sw = 'Hariri uzo lilirudishwa';
        
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();    

        $salereturn = SaleReturn::where('sale_returns.id', decrypt($id))->where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'sale_returns.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('sale_returns.id as id', 'an_sale_id', 'sale_returns.reason as reason', 'an_sales.time_created as time_created', 'customers.name as name')->first();

        $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_uom as basic_uom', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.retail_price as retail_price', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

        $total = 0;
        $discount = 0;
        $tax = 0;
        foreach ($sritems as $key => $item) {
            $total += $item->price;
            $discount += $item->discount;
            $tax += $item->tax_amount;
        }

        $items = AnSaleItem::where('an_sale_id', $salereturn->an_sale_id)->join('products', 'products.id', '=', 'an_sale_items.product_id')->groupBy('name')->orderBy('an_sale_items.time_created', 'desc')->get([
                DB::raw('products.name as name'),
                DB::raw('an_sale_items.product_id as product_id'),
                DB::raw('SUM(an_sale_items.quantity_sold) as quantity_sold'),
                DB::raw('an_sale_items.retail_price as retail_price'),
                DB::raw('SUM(an_sale_items.price) as price')
            ]);

        // return $items;
        return view('sales.returns.edit', compact('page', 'title', 'title_sw', 'settings', 'shop', 'settings', 'salereturn', 'sritems', 'total', 'discount', 'tax', 'items'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $salereturn = SaleReturn::find($id);
        if (!is_null($salereturn)) {
            $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->join('products', 'products.id', '=', 'sale_return_items.product_id')->select('products.id as p_id', 'products.name as name', 'products.basic_uom as basic_uom', 'sale_return_items.product_id as product_id', 'sale_return_items.id as id', 'sale_return_items.quantity as quantity', 'sale_return_items.retail_price as retail_price', 'sale_return_items.price as price','sale_return_items.total_discount as discount', 'sale_return_items.tax_amount as tax_amount', 'sale_return_items.created_at as created_at')->orderBy('sale_return_items.created_at', 'desc')->get();

            $total = 0;
            $discount = 0;
            $tax = 0;
            foreach ($sritems as $key => $item) {
                $total += $item->price;
                $discount += $item->discount;
                $tax += $item->tax_amount;
            }

            $amount = ($total-$discount)+$tax;

            $returndate = Carbon::now();
            if (!empty($request['return_date'])) {
                $returndate = $request['return_date'];
            }
            $salereturn->return_date = $returndate;
            $salereturn->reason = $request['reason'];
            $salereturn->sale_return_amount = $total;
            $salereturn->sale_return_discount = $discount;
            $salereturn->return_tax_amount = $tax;
            $salereturn->save();

            $sale = AnSale::find($salereturn->an_sale_id);
            $sale->return_amount = $salereturn->sale_return_amount;
            $sale->return_discount = $salereturn->sale_return_discount;
            $sale->return_tax = $salereturn->return_tax_amount;
            $sale->save();


            $creditnote = CreditNote::where('an_sale_id', $salereturn->an_sale_id)->first();
            if (is_null($creditnote)) {    
                $max_no = CreditNote::where('shop_id', $shop->id)->latest()->first();
                $credit_note_no = 0;
                if (!is_null($max_no)) {
                    $credit_note_no = $max_no->credit_note_no+1;
                }else{
                    $credit_note_no = 1;
                }
                $creditnote = CreditNote::create([
                    'an_sale_id' => $sale->id,
                    'shop_id' => $shop->id,
                    'user_id' => $salereturn->user_id,
                    'credit_note_no' => $credit_note_no,
                ]);
            }

            $creditnote->date = $returndate;
            $creditnote->reason = $request['reason'];
            $creditnote->amount = $amount;
            $creditnote->save();

            $payments = SalePayment::where('an_sale_id', $sale->id)->get();
            if (!is_null($payments)) {
                $curr_adjs = $amount;
                foreach ($payments as $key => $payment) {
                    $acctrans = CustomerTransaction::find($payment->trans_id);
                    if (!is_null($acctrans)) {
                        $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                        $acctrans->is_utilized = false;
                        $acctrans->save();
                    }
                    $payment->delete();
                }
            }
            $sale->sale_amount_paid = 0;
            $sale->save();
            
            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
            $netsaleamount = $tnetsales-$tnetreturn;
            $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $sale->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();
            if (!is_null($utransactions)) {
                $tunpaid = $netsaleamount-$sale->sale_amount_paid;
                foreach ($utransactions as $key => $trans) {
                    $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                    if ($rem_amount > 0) {
                        $paidamount = 0;
                        if ($rem_amount > $tunpaid) {
                            $paidamount = $tunpaid;
                            $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                            $trans->save();
                        } else {
                            $paidamount = $rem_amount;
                            $trans->trans_invoice_amount = $trans->trans_invoice_amount + $paidamount;
                            $trans->is_utilized = true;
                            $trans->save();
                        }

                        $payment = SalePayment::create([
                            'an_sale_id' => $sale->id,
                            'shop_id' => $shop->id,
                            'trans_id' => $trans->id,
                            'receipt_no' => $trans->receipt_no,
                            'pay_mode' => $trans->payment_mode,
                            'bank_name' => $trans->bank_name,
                            'bank_branch' => $trans->bank_branch,
                            'pay_date' => $trans->date,
                            'cheque_no' => $trans->cheque_no,
                            'amount' => $paidamount,
                            'currency' => $trans->currency,
                            'defcurr' => $trans->defcurr,
                            'ex_rate' => $trans->ex_rate,
                            'cashier' => $trans->cashier,
                            'cc_time' => $trans->cc_time,
                            'is_fresh_pay' => false
                        ]);

                        $tunpaid -= $paidamount;
                    }
                }
            }
                
            $amount_paid = 0;
            $payments = SalePayment::where('an_sale_id', $sale->id)->get();
            foreach ($payments as $key => $pay) {  
                $amount_paid += $pay->amount;
            }

            $sale->sale_amount_paid = $amount_paid;
            $sale->save();
            $this->updateSaleStatus($sale);

            $acctrans = CustomerTransaction::where('shop_id', $shop->id)->where('cn_no', $creditnote->credit_note_no)->first();
            if (!is_null($acctrans)) {
                    
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->customer_id = $sale->customer_id;
                $acctrans->invoice_no = $sale->invoice_no;
                $acctrans->cn_no = $creditnote->credit_note_no;
                $acctrans->adjustment = $creditnote->amount;
                $acctrans->date = $returndate;
                $acctrans->save();
            }else{
                $acctrans = new CustomerTransaction();
                $acctrans->shop_id = $shop->id;
                $acctrans->user_id = $user->id;
                $acctrans->customer_id = $sale->customer_id;
                $acctrans->invoice_no = $sale->invoice_no;
                $acctrans->cn_no = $creditnote->credit_note_no;
                $acctrans->adjustment = $creditnote->amount;
                $acctrans->date = $returndate;
                $acctrans->save();
            }
            return redirect('sales-returns')->with('success', 'Sale Return was creadted successfully');
        }else{
            return redirect('sales-returns')->with('error', 'Sale Return was not Found');
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
        $shop = Shop::find(Session::get('shop_id'));
        $salereturn = SaleReturn::find(decrypt($id));
        if (!is_null($salereturn)) {
            $sale = AnSale::find($salereturn->an_sale_id);
            $sritems = SaleReturnItem::where('sale_return_id', $salereturn->id)->get();
            foreach ($sritems as $key => $item) {
                $stock = Stock::find($item->stock_id);
                if (!is_null($stock)) {
                    // Restore returned
                    $stock->quantity_out = $stock->quantity_out+$item->quantity;
                    $stock->save();
                    if ($stock->quantity_in == $stock->quantity_out) {
                        $stock->is_utilized = true;
                    }else{
                        $stock->is_utilized = false;
                    }
                    $stock->save();
                }
                $item->delete();
                dispatch(new StockUpdaterJob($shop, $item->product_id));
            }
            $salereturn->delete();

            $creditnote = CreditNote::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
            if (!is_null($creditnote)) {
                $acctrans = CustomerTransaction::where('cn_no', $creditnote->credit_note_no)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }
                $creditnote->delete();
            }
            
            $sale->return_amount = 0;
            $sale->return_discount = 0;
            $sale->return_tax = 0;
            $sale->save();
            
            $this->updateSaleStatus($sale);
        }

        return redirect('sales-returns')->with('success', 'Sale Return was successfully canceled');
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
}