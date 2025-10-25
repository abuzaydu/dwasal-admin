<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Models\Company;
use App\Models\Product;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\ServiceSaleItem;

class ModifyProductCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:modify-product-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companies = Company::all();
        foreach ($companies as $key => $company) {
            // if ($company->id == 39) {
                
                Log::info($company->name);
                $shops = $company->shops()->get();
                
                // return $this->checkDuplicates($company);

                $this->checkDuplicateSaleItems($shops);
            // }
        }

    }

    public function FunctionName($shops)
    {
        foreach ($shops as $key => $shop) {
            if ($key == 0) {
                $products = $shop->products()->get();
                foreach ($products as $key => $product) {
                    // $othershop = $product->shops()->where('product_id', $product->id)->where('shop_id', '!=', $shop->id)->first();
                    // if (!is_null($othershop)) {
                        Log::info('Product ID : '.$product->id.' Slug : '.$product->slug.' Company ID : '.$product->company_id);
                    // }else{

                    //     Log::info('Product ID : '.$product->id.' Slug : '.$product->slug.' Company ID : '.$product->company_id);
                    //     $delproduct = Product::find($product->id);
                    //     $shop->products()->detach($delproduct);
                    //     $delproduct->delete();
                    // }

                    // $product->company_id = $company->id;
                    // $product->save();
                }
            }
        }
    }

    public function checkDuplicates($company)
    {
        $products = Product::where('company_id', $company->id)->groupBy('slug')->get([
            \DB::raw('slug'),
            \DB::raw('COUNT(id) as dupl')
        ]);
        foreach ($products as $key => $value) {
            if ($value->dupl > 1) {
                Log::info($value->slug.' has '.$value->dupl.' duplicates');
            }
        }
    }

    public function checkDuplicateSaleItems($shops)
    {
        foreach ($shops as $key => $shop) {
            $sales = AnSale::where('shop_id', $shop->id)->get();
            foreach ($sales as $key => $sale) {
                $proditems = AnSaleItem::where('an_sale_id', $sale->id)->count();
                if ($proditems > 0) {
                    $servitemstotal = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                    $itemstotal = AnSaleItem::where('an_sale_id', $sale->id)->sum('price');
                    $total = $itemstotal+$servitemstotal;
                    if ($itemstotal > 0 && $total > $sale->sale_amount) {
                        Log::info('Sale Amount : '.$sale->sale_amount.' Items Total : '.$total);
                        $diff = ($total-$sale->sale_amount);
                        Log::info('Difference '.$diff);
                        $items = AnSaleItem::where('an_sale_id', $sale->id)->select('id', 'product_id', 'quantity_sold', 'retail_price', 'price')->get();
                        foreach ($items as $key => $item) {
                            if ($item->price == $diff) {
                                $remItems = AnSaleItem::where('an_sale_id', $sale->id)->where('product_id', $item->product_id)->where('price', $diff)->get();
                                if ($remItems->count() > 1) {
                                    Log::info('Bakisha moja tu.');
                                    foreach ($remItems as $pkey => $value) {
                                        Log::info('QTY : '.$value->quantity_sold.' price : '.$value->price);
                                        if ($pkey > 0) {
                                            // $value->delete();
                                        }
                                    }
                                }else{
                                    foreach ($remItems as $key => $value) {
                                        Log::info('QTY : '.$value->quantity_sold.' price : '.$value->price);
                                        // $value->delete();
                                    }
                                }
                            }
                        }

                        
                        // $amountp = AnSaleItem::where('an_sale_id', $sale->id)->sum('price'); 
                        // $discountp = AnSaleItem::where('an_sale_id', $sale->id)->sum('total_discount'); 
                        // $amounts = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
                        // $discounts = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
                        // $taxp = AnSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
                        // $taxs = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');

                        // $sale->sale_amount = ($amountp+$amounts);
                        // $sale->sale_discount = ($discountp+$discounts);
                        // $sale->tax_amount = ($taxp+$taxs);
                        // $sale->save();
                        // $this->updateSaleStatus($sale);
                    }
                }
            }
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
}
