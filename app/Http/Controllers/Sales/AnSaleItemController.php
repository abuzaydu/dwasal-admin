<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Jobs\StockUpdaterJob;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\CustomerTransaction;
use App\Models\ProdDamage;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\SalePayment;
use App\Models\SaleReturnItem;
use App\Models\ServiceSaleItem;
use App\Models\Setting;
use App\Models\Settings;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\TransferOrderItem;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;

class AnSaleItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $product =$shop->products()->where('id', $request['product_id'])->first();
        $in_stock = $product->in_stock;
        if (!is_null($product->retail_price)) {
                
            $sale = AnSale::find($request['an_sale_id']);
            if ($request['quantity_sold'] <= $in_stock) {
                $punit = ProductUnit::where('product_id', $request['product_id'])->where('shop_id', $shop->id)->where('is_basic', true)->first();
                if (is_null($punit)) {
                    $punit = new ProductUnit();
                    $punit->shop_id = $shop->id;
                    $punit->product_id = $request['product_id'];
                    if (is_null($product->basic_uom)) {
                        $punit->unit_name = 'pc';
                    }else{
                        $punit->unit_name = $product->basic_uom;
                    }
                    $punit->qty_equal_to_basic = 1;
                    $punit->unit_price = $product->retail_price;
                    $punit->is_basic = true;
                    $punit->save();
                }
                $quantity_sold = $request['quantity_sold']* $punit->qty_equal_to_basic;
                $retail_price = $product->retail_price / $punit->qty_equal_to_basic;

                $saleitem = new AnSaleItem;
                $saleitem->an_sale_id = $sale->id;
                $saleitem->product_id = $request['product_id'];
                $saleitem->shop_id = $shop->id;
                $saleitem->product_unit_id = $punit->id;
                $saleitem->quantity_sold = $quantity_sold;
                $saleitem->unit_cost = $product->unit_cost;
                $saleitem->buying_price = $saleitem->quantity_sold*$saleitem->unit_cost;
                $saleitem->retail_price = $retail_price;
                $saleitem->price = $saleitem->retail_price*$saleitem->quantity_sold;
                $saleitem->tax_amount = 0;
                $saleitem->time_created = $sale->time_created;
                $saleitem->save();

                dispatch(new StockUpdaterJob($shop, $saleitem->product_id));
                
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

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->amount = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $acctrans->save();
                }

                $spays = SalePayment::where('an_sale_id', $sale->id)->count();
                // Log::info($sale->sale_type.' Payments '.$spays);
                if ($sale->sale_type == 'cash' && $spays == 1) {
                    $payment = SalePayment::where('an_sale_id', $sale->id)->first();
                    $this->updatePaymentTrans($payment, $sale);
                }else{
                    $this->updateSaleStatus($sale);
                }
                
                
                $success = 'Sale item was successfully updated';
                return redirect()->back()->with('success', $success);
            }else{
                $msg_warning = 'Your '.$product->name.' stock is insufficient please reduce no of items or update your stock';
                return redirect('sale-items/'.encrypt($sale->id))->with('warning', $msg_warning);
            }   
        }else{
            $msg_error = 'Your '.$product->name.' has no selling price. Update selling price to sale';
            return redirect('sale-items/'.encrypt($sale->id))->with('error', $msg_error);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $saleitem = AnSaleItem::find(decrypt($id));
        $sale = AnSale::find($saleitem->an_sale_id);
        
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $product = Product::find($saleitem->product_id);
        $page = 'Edit sale item';
        $title = 'Edit sale item';
        $title_sw = 'Hariri Bidhaa iliyouzwa';
        $prod_detail = 'No';
        return view('sales.invoices.edit-item', compact('page', 'title', 'title_sw', 'sale', 'saleitem', 'product', 'products', 'prod_detail'));
    }


    public function editItem($id, Request $request)
    {
        $saleitem = AnSaleItem::find(decrypt($id));
        $sale = AnSale::find($saleitem->an_sale_id);
        
        $shop = Shop::find(Session::get('shop_id'));
        $products = $shop->products()->get();
        $product = Product::find($saleitem->product_id);
        $page = 'Edit sale item';
        $title = 'Edit sale item';
        $title_sw = 'Hariri Bidhaa iliyouzwa';
        $prod_detail = 'Yes';
        return view('sales.invoices.edit-item', compact('page', 'title', 'title_sw', 'sale', 'saleitem', 'product', 'products', 'prod_detail'));
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
        if (Auth::user()->can('edit-invoice')) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            $product =$shop->products()->where('id', $request['product_id'])->first();
            $in_stock = $product->in_stock;

            $saleitem = AnSaleItem::find(decrypt($id));

            if ($request['quantity_sold'] <= ($in_stock+$saleitem->quantity_sold)) {
                $saleitem->product_id = $request['product_id'];
                $saleitem->quantity_sold = $request['quantity_sold'];
                $saleitem->unit_cost = $request['unit_cost'];
                $saleitem->buying_price = $saleitem->quantity_sold*$saleitem->unit_cost;
                $saleitem->retail_price = $request['retail_price'];
                $saleitem->price = $saleitem->retail_price*$saleitem->quantity_sold;
                if ($saleitem->quantity_sold > 0) {
                    $saleitem->discount = $request['total_discount']/$saleitem->quantity_sold;
                }
                $saleitem->total_discount = $request['total_discount'];
                $saleitem->with_vat = $request['with_vat'];
                if ($saleitem->with_vat == 'yes') {
                    $vat_amount =  ($saleitem->price-$saleitem->total_discount)*($settings->tax_rate/100);
                    $saleitem->tax_amount = $vat_amount;
                }else{
                    $saleitem->tax_amount = 0;
                }
                $saleitem->save();

                dispatch(new StockUpdaterJob($shop, $saleitem->product_id));
                
                $sale = AnSale::find($saleitem->an_sale_id);

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

                 $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->amount = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                    $acctrans->save();
                }

                $spays = SalePayment::where('an_sale_id', $sale->id)->count();
                // Log::info($sale->sale_type.' Payments '.$spays);
                if ($sale->sale_type == 'cash' && $spays == 1) {
                    $payment = SalePayment::where('an_sale_id', $sale->id)->first();
                    $this->updatePaymentTrans($payment, $sale);
                }else{
                    $this->updateSaleStatus($sale);
                }
                
                $success = 'Sale item was successfully updated';
                
                if ($request['prod_detail'] == 'Yes') {
                    return redirect('product-sale-history/'.encrypt($saleitem->product_id))->with('success', $success);
                }else{
                    return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('success', $success);
                }
            }else{
                $msg_warning = 'Your '.$product->name.' stock is insufficient please reduce no of items or update your stock';
                return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('warning', $msg_warning);
            }
        }else{
            return view('errors.401');
        }
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
    
    public function updatePaymentTrans($payment, $sale)
    {   
        $acctrans = CustomerTransaction::find($payment->trans_id);
        if (!is_null($acctrans)) {
            $shop = Shop::find($acctrans->shop_id);
            $user = Auth::user();
            $settings = Setting::where('shop_id', $shop->id)->first();

            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
            $netsales_amount = $tnetsales-$tnetreturn;

            $acctrans->payment = $netsales_amount;
            $acctrans->save();

            $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
            if (is_null($astmt)) {
                $account = Account::where('shop_id', $shop->id)->where('type', $payment->pay_mode)->first();
                $astmt = new AccountStatement();
                $astmt->shop_id = $shop->id;
                $astmt->user_id = $user->id;
                $astmt->customer_transaction_id = $acctrans->id;
                $astmt->account_id = $account->id;
            }
            $astmt->date = $acctrans->date;
            $astmt->debit = $acctrans->payment;
            $astmt->credit = 0;
            $astmt->description = 'Sales Payment (Receipt No. '.sprintf('%04d', $acctrans->receipt_no).')';
            $astmt->save();
            
            $sale_payments = SalePayment::where('trans_id', $acctrans->id)->where('shop_id', $shop->id)->get();
            if ($sale_payments->count() == 1) {
                $payment = SalePayment::where('trans_id', $acctrans->id)->where('shop_id', $shop->id)->first();
                $payment->amount = $acctrans->payment;
                $payment->save();
                
                $sale->sale_amount_paid = $netsales_amount;
                $sale->save();
                $this->updateSaleStatus($sale);
            }
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
        $saleitem = AnSaleItem::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('edit-invoice')) {
            if (!is_null($saleitem)) {
                $sale = AnSale::find($saleitem->an_sale_id);
                if (!is_null($sale)) {
                    $saleitem->delete();
                    dispatch(new StockUpdaterJob($shop, $saleitem->product_id));

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

                $success = 'Sale item was successfully deleted';
                return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('success', $success);
            }
        }else{
            return view('errors.401');
        }
    }
}