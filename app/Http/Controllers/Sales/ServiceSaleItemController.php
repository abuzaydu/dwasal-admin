<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Auth;
use Session;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;
use App\Models\Invoice;
use App\Models\CustomerTransaction;
use App\Models\Service;
use App\Models\Stock;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\CreditNoteItem;
use App\Models\SalePayment;

class ServiceSaleItemController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Services';
        $title  = 'Service Sale Details';
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

        $sale = AnSale::find($request['an_sale_id']);
        $shop_service = $shop->services()->where('id', $request['service_id'])->first();
        if (!is_null($shop_service)) {
                
            $saleitemData = new ServiceSaleItem;
            $saleitemData->shop_id = $sale->shop_id;
            $saleitemData->an_sale_id = $sale->id;
            $saleitemData->service_id = $request['service_id'];
            $saleitemData->no_of_repeatition = $request['quantity'];
            $saleitemData->price = $shop_service->price;
            $saleitemData->total = $saleitemData->price*$saleitemData->no_of_repeatition;
            $saleitemData->discount = 0;
            $saleitemData->total_discount = 0;
            $saleitemData->time_created = $sale->time_created;
            $saleitemData->save();
            
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
            
            $success = 'Sale item was successfully updated';
            return redirect()->back()->with('success', $success);
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
        try {
            $saleitem = ServiceSaleItem::find(decrypt($id));
            if (!is_null($saleitem)) {
                    
                $sale = AnSale::find($saleitem->an_sale_id);
                
                $shop = Shop::find(Session::get('shop_id'));
                $services = $shop->services()->get();
                $service = Service::find($saleitem->service_id);
                $page = 'Edit sale item';
                $title = 'Edit sale item';
                $title_sw = 'Hariri Huduma iliyouzwa';
                return view('sales.invoices.edit-servitem', compact('page', 'title', 'title_sw', 'sale', 'saleitem', 'service', 'services'));
            }else{
                return redirect()->back()->with('error', 'Item not Found');
            }
        }catch (DecryptException $e) {
            $msg = 'FAILED. The Payload is invalid.';
            return redirect()->back()->with('error', $msg);
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
        $shop = Shop::find(Session::get('shop_id'));
        $shop_service =$shop->services()->where('id', $request['service_id'])->first();
        $settings = Setting::where('shop_id', $shop->id)->first();

        $saleitem = ServiceSaleItem::find(decrypt($id));
        if (!is_null($shop_service)) {
            if (is_null($saleitem->shop_id)) {
                $saleitem->shop_id = $shop->id;
            }
            $saleitem->service_id = $request['service_id'];
            $saleitem->no_of_repeatition = $request['no_of_repeatition'];
            $saleitem->price = $request['price'];
            $saleitem->total = $saleitem->price*$saleitem->no_of_repeatition;
            $saleitem->discount = $request['total_discount']/$saleitem->no_of_repeatition;
            $saleitem->total_discount = $request['total_discount'];
            $saleitem->with_vat = $request['with_vat'];
            if ($saleitem->with_vat == 'yes') {
                $vat_amount =  ($saleitem->total-$saleitem->total_discount)*($settings->tax_rate/100);
                $saleitem->tax_amount = $vat_amount;
            }else{
                $saleitem->tax_amount = 0;
            }

            $saleitem->save();

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

            $this->updateSaleStatus($sale);

            $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
            if (!is_null($acctrans)) {
                $acctrans->amount = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                $acctrans->save();
            }
            $success = 'Sale item was successfully updated';
            return redirect()->route('an-sales.show', encrypt($saleitem->an_sale_id))->with('success', $success);
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
        $saleitem = ServiceSaleItem::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $sale = AnSale::find($saleitem->an_sale_id);
        if (!is_null($sale)) {
                
            $saleitem->delete();

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
