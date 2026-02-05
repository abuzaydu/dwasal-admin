<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Models\StockCorrection;

class StockUpdaterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shop;
    protected $product_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shop, $product_id)
    {
        $this->shop = $shop;
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        Log::info('New Job from '.$this->shop->name);
        $product = $this->shop->products()->where('id', $this->product_id)->first();
        if (!is_null($product)) {
            $stock_in = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $this->shop->id)->sum('quantity_in');
            $stock_out = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $this->shop->id)->sum('quantity_out');
            $sold = AnSaleItem::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $this->shop->id)->sum('quantity_sold');
            $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $this->shop->id)->sum('quantity');
            $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $this->shop->id)->sum('quantity');
            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $this->shop->id)->sum('quantity');
            $diff_qty = StockCorrection::where('product_id', $product->id)->where('shop_id', $this->shop->id)->sum('diff_qty');
            
                                        
            $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered+$diff_qty);
            $product->in_stock = $instock;
            $product->save();

            if ($product->in_stock > $product->reorder_point) {
                $product->status = 'In Stock';
                $product->save();
            }elseif ($product->in_stock <= 0) {
                $product->status = 'Out of Stock';
                $product->save();
            }elseif($product->in_stock <= $product->reorder_point && $product->in_stock > 0){
                $product->status = 'Low Stock';
                $product->save();
            }

            $laststock = Stock::where('product_id', $product->id)->where('is_deleted', false)->where('shop_id', $this->shop->id)->orderBy('stock_date', 'desc')->first();
            if (!is_null($laststock)) {
                if ($product->unit_cost != $laststock->unit_cost) {
                    // Log::info('Updating buying price for '.$product->name.' at '.$this->shop->display_name);
                    $product->unit_cost = $laststock->unit_cost;
                    $product->save();
                }else{
                    // Log::info('Buying price for '.$product->name.' is already updated at '.$this->shop->display_name);                
                }
            }

            // Log::info('Balance '.$product->name.'  = '.($stock_in-$stock_out));
            if ($instock > ($stock_in-$stock_out)) {
                $stocks = Stock::where('product_id', $product->id)->where('shop_id', $this->shop->id)->where('is_deleted',false)->orderBy('stock_date', 'desc')->get();
                $redqty = $instock-($stock_in-$stock_out);
                // Log::info('Reduce qty '.$product->name.'  = '.$redqty);
                foreach ($stocks as $key => $stock) {
                    $reduced = 0;
                    if ($redqty > 0) {
                        if (($stock->quantity_out > $redqty)) {
                            $stock->quantity_out = $stock->quantity_out-$redqty;
                            $stock->is_utilized = false;
                            $stock->save();
                            $reduced = $redqty;
                        }else{
                            $stock->quantity_out = 0;
                            $stock->is_utilized = false;
                            $stock->save();
                            $reduced = $stock->quantity_out;
                        }
                    }
                    $redqty -= $reduced;
                }
            }elseif ($instock < ($stock_in-$stock_out)) {
                $stocks = Stock::where('product_id', $product->id)->where('shop_id', $this->shop->id)->where('is_deleted', false)->orderBy('stock_date', 'desc')->get();
                $addqty = -($instock-($stock_in-$stock_out));
                // Log::info('Add qty '.$product->name.'  = '.$addqty);
                foreach ($stocks as $key => $stock) {
                    $remqty = ($stock->quantity_in-$stock->quantity_out);
                    if ($addqty > 0) {
                        if ($addqty <= $remqty) {
                            $stock->quantity_out = $stock->quantity_out+$addqty;
                            if ($stock->quantity_in == $stock->quantity_out) {
                                $stock->is_utilized = true;
                            }
                            $stock->save();
                        }else{
                            $stock->quantity_out = $stock->quantity_out+$remqty;
                            if ($stock->quantity_in == $stock->quantity_out) {
                                $stock->is_utilized = true;
                            }
                            $stock->save();
                        }
                    }
                    $addqty -= $remqty;
                }
            }else{
                // Log::info('In Stock '.$instock);
                // Log::info('Balance '.$product->name.'  = '.($stock_in-$stock_out));
            }
        }else{
            Log::info('product not found');
        }
    }
}
