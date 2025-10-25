<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\DailyClosingStockValue;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\SaleReturnItem;
use App\Models\TransferOrderItem;
use App\Models\ProdDamage;

class DailyClosingStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-closing-stock';

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
        $shops = Shop::select('shops.id as id', 'name')->get();
        foreach ($shops as $key => $shop) {
            // if ($shop->id == 1) {
                Log::info($shop->name);
                $products = $shop->products()->get();
                foreach ($products as $key => $product) {
                    // Log::info($product->name);
                    $now = Carbon::now();

                    $date = $now->subDays(1)->format('Y-m-d');
                    // Log::info($date);

                    $checkdcs = DailyClosingStockValue::where('shop_id', $shop->id)->where('product_id', $product->id)->whereDate('date', $date)->first();
                    if (is_null($checkdcs)) {
                        
                        $start_qty = 0;
                        $start_value = 0;
                        $start_retail_value = 0;
                        $start_wholesale_value = 0;
                        $lastdcs = DailyClosingStockValue::where('shop_id', $shop->id)->where('product_id', $product->id)->whereDate('date', Carbon::now()->subDays(2)->format('Y-m-d'))->first();
                        if (!is_null($lastdcs)) {
                            $start_qty = $lastdcs->end_qty;
                            $start_value = $lastdcs->end_value;
                            $start_retail_value = $lastdcs->start_retail_value;
                            $start_wholesale_value = $lastdcs->start_wholesale_value;
                        }

                        $start = $date.' 00:00:00';
                        $end = $date.' 23:59:59';
                        $purchase_qty = 0;
                        $stocks = Stock::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('time_created', [$start, $end])->where('is_deleted', false)->get();
                        if ($stocks->count() > 0) {
                            foreach ($stocks as $key => $stock) {
                                $purchase_qty += $stock->quantity_in;
                            }
                        }

                        $return_qty = 0;
                        $returns = SaleReturnItem::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('created_at', [$start, $end])->get();
                        if ($returns->count() > 0) {
                            foreach ($returns as $key => $return) {
                                $return_qty += $return->quantity;
                            }
                        }

                        $soldqty = 0;
                        $items = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('time_created', [$start, $end])->where('is_deleted',false)->get();
                        if ($items->count() > 0) {
                            foreach ($items as $key => $item) {
                                $soldqty += $item->quantity_sold;
                            }
                        }

                        $transfer_qty = 0;
                        $transfers = TransferOrderItem::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('created_at', [$start, $end])->get();
                        if ($transfers->count() > 0) {
                            foreach ($transfers as $key => $transfer) {
                                $transfer_qty += $transfer->quantity;
                            }
                        }

                        $dam_qty = 0;
                        $damages = ProdDamage::where('shop_id', $shop->id)->where('product_id', $product->id)->whereBetween('time_created', [$start, $end])->get();
                        if ($damages->count() > 0) {
                            foreach ($damages as $key => $damage) {
                                $dam_qty += $damage->quantity;
                            }
                        }

                        $qty_sold_std = 0;
                        $value_of_qty_sold_std = 0;
                        $std_items = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $product->id)->where('time_created', '>', Carbon::now()->startOfDay())->where('is_deleted',false)->get();
                        if ($std_items->count() > 0) {
                            foreach ($std_items as $key => $item) {
                                $qty_sold_std += $item->quantity_sold;
                                $value_of_qty_sold_std += $item->quantity_sold*$item->unit_cost;
                            }
                        }

                        // Log::info('Qty Sold after STD'.$qty_sold_std.'  Value :  '.$value_of_qty_sold_std);
                        $rem_qty = 0;
                        $rem_stock_value = 0;
                        $stocks = Stock::where('shop_id', $shop->id)->where('product_id', $product->id)->where('is_deleted', false)->where('is_utilized', false)->get();
                        foreach ($stocks as $key => $stock) {
                            $rem_qty += ($stock->quantity_in-$stock->quantity_out);
                            $rem_stock_value += ($stock->quantity_in-$stock->quantity_out)*$stock->unit_cost;
                        }
                        
                        $end_qty = $rem_qty+$qty_sold_std;
                        $end_value = $rem_stock_value+$value_of_qty_sold_std;

                        $end_retail_value = $rem_qty*$product->retail_price;
                        $end_wholesale_value = $rem_qty*$product->wholesale_price;

                        $dcs = new DailyClosingStockValue();
                        $dcs->shop_id = $shop->id;
                        $dcs->product_id = $product->id;
                        $dcs->date = $date;
                        $dcs->start_qty = $start_qty;
                        $dcs->purchase_qty = $purchase_qty;
                        $dcs->return_qty = $return_qty;
                        $dcs->sold_qty = $soldqty;
                        $dcs->transfer_qty = $transfer_qty;
                        $dcs->dam_qty = $dam_qty;
                        $dcs->end_qty = $end_qty;
                        $dcs->start_value = $start_value;
                        $dcs->start_retail_value = $start_retail_value;
                        $dcs->start_wholesale_value = $start_wholesale_value;
                        $dcs->end_value = $end_value;
                        $dcs->end_retail_value = $end_retail_value;
                        $dcs->end_wholesale_value = $end_wholesale_value;                    
                        $dcs->save();
                    }else{
                        Log::info('Item already created');
                    }
                }
            // }
        }
    }
}
