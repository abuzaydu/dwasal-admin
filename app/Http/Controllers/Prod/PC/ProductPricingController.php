<?php

namespace App\Http\Controllers\Prod\PC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Log;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ProductPricing;
use App\Models\RawMaterial;
use App\Models\ProductionStage;
use App\Models\MaterialCost;
use App\Models\LabourCost;
use App\Models\TransportationCost;
use App\Models\IndirectCost;
use App\Models\LocalIndirectCost;
use App\Models\PackagingCost;
use App\Models\LocalPackagingCost;
use App\Models\ExportHandlingCost;
use App\Models\Product;

class ProductPricingController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');  
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $page = 'Pricing Calculators';
        $title = 'Pricing Calculators';
        $title_sw = 'Vikokotoo vya Bei';
        $now = Carbon::now();
        $start = $now->copy()->startOfYear();
        $end = $now->copy()->endOfYear();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
  
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['year'])) {
            $date = Carbon::createFromDate($request['year'], 1, 25);
            $start = $date->copy()->startOfYear();
            $end = $date->copy()->endOfYear();
            $start_date = $start->format('Y-m-d');            
            $end_date = $end->format('Y-m-d');
            $is_post_query = true;

        }

        $pricings = ProductPricing::where('product_pricings.shop_id', $shop->id)->whereBetween('date', [$start, $end])->join('users', 'users.id', '=', 'product_pricings.user_id')->join('products','products.id', '=', 'product_pricings.product_id')->select('product_pricings.id as id', 'name', 'first_name', 'last_name', 'date', 'is_pending')->get();

        return view('production.pricings.index', compact('page', 'title', 'start_date', 'end_date', 'is_post_query', 'pricings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $page = 'New Pricing Calculator';
        $title = 'New Pricing Calculator';
        $title_sw = 'Kikokotoo Kipya cha Bei';
        $products = $shop->products()->select('id', 'name')->get();

        $pricing = ProductPricing::where('is_pending', 1)->where('shop_id', $shop->id)->where('user_id', $user->id)->first();
        if (!is_null($request['product_id'])) {
            $pricing = ProductPricing::where('is_pending', 1)->where('shop_id', $shop->id)->where('user_id', $user->id)->where('product_id', $request['product_id'])->first();
            if (is_null($pricing)) {
                $pricing = new ProductPricing();
                $pricing->shop_id = $shop->id;
                $pricing->user_id = $user->id;
                $pricing->product_id = $request['product_id'];
                $pricing->date = Carbon::now();
                $pricing->save();
            }else{
                $pricing->product_id = $request['product_id'];
                $pricing->date = Carbon::now();
                $pricing->save();
            }
        }

        return view('production.pricings.create', compact('page', 'title', 'products', 'pricing'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pricing = ProductPricing::find($request['pricing_id']);
        if (!is_null($pricing)) {
            $pricing->is_pending = false;
            $pricing->save();

            return redirect()->route('product-pricings.show', encrypt($pricing->id))->with('Pricing created successfully');
        }else{
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Pricing Calculator';
        $title = 'Product Pricing Calculator';
        $pricing = ProductPricing::find(decrypt($id));
        if (!is_null($pricing)) {
            $product = Product::find($pricing->product_id);
            
            $materialcosts = MaterialCost::where('product_pricing_id', $pricing->id)->get();
            $labourcosts = LabourCost::where('product_pricing_id', $pricing->id)->get();
            $transportcosts = TransportationCost::where('product_pricing_id', $pricing->id)->get();
            $indirectcosts = IndirectCost::where('product_pricing_id', $pricing->id)->get();
            $localindirectcosts = LocalIndirectCost::where('product_pricing_id', $pricing->id)->get();
            $packagecosts = PackagingCost::where('product_pricing_id', $pricing->id)->get();
            $localpackagecosts = LocalPackagingCost::where('product_pricing_id', $pricing->id)->get();
            $handlingcosts = ExportHandlingCost::where('product_pricing_id', $pricing->id)->get();

            return view('production.pricings.show', compact('page', 'title', 'pricing', 'product', 'materialcosts', 'labourcosts', 'transportcosts', 'indirectcosts', 'localindirectcosts', 'packagecosts', 'localpackagecosts', 'handlingcosts'));
        }else{
            return redirect()->back()->with('error', 'Product Pricing Calculator not found');
        }
    }

    public function setProductPrice($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $pricing = ProductPricing::find(decrypt($id));
        if (!is_null($pricing)) {
            $product = Product::find($pricing->product_id);
            
            $tmcost = MaterialCost::where('product_pricing_id', $pricing->id)->sum('cost_per_piece');
            $tlcost = LabourCost::where('product_pricing_id', $pricing->id)->sum('cost_per_piece');
            $ttpcost = TransportationCost::where('product_pricing_id', $pricing->id)->sum('cost_per_unit');
            $ticost = IndirectCost::where('product_pricing_id', $pricing->id)->sum('amount');
            $tlocal_icost = LocalIndirectCost::where('product_pricing_id', $pricing->id)->sum('amount');
            $tpackcost = PackagingCost::where('product_pricing_id', $pricing->id)->sum('unit_cost');
            $tlocalpackcost = LocalPackagingCost::where('product_pricing_id', $pricing->id)->sum('unit_cost');
            $thcost = ExportHandlingCost::where('product_pricing_id', $pricing->id)->sum('amount');


            $dr_price = 0;
            $dw_price = 0;
            $final_fob_r_price = 0;
            $fob_w_price = 0;
            $final_fob_r_price_10 = 0;
            $fob_w_price_10 = 0;

            // Domestic Prices
            $total_ex_wprice = ($tmcost+$tlcost+$ttpcost+$ticost);
            $dw_price = $total_ex_wprice+($total_ex_wprice*($pricing->domestic_w_margin/100));
            $dr_price = $dw_price+($dw_price*($pricing->domestic_r_margin/100));

            Log::info('Domestic Retail Price '.$dr_price);
            Log::info('Domestic Wholesale Price '.$dw_price);
            // Export Prices 
            if ($pricing->min_order_value == 5000) {
                // 5000 Min Order value
                $totalcost = ($total_ex_wprice+$tpackcost);
                $fobprice = $totalcost+($totalcost*($thcost/$pricing->min_order_value));
                Log::info('FOB Price '.$fobprice);
                // 10000 Min Order Value                
                $fobprice_10 = $totalcost+($totalcost*($thcost/10000));
                Log::info('FOB 1000 Price '.$fobprice_10);
            }else{
                // 5000 Min Order value
                $totalcost = ($total_ex_wprice+$tpackcost);
                $fobprice = $totalcost+($totalcost*($thcost/5000));
                Log::info('FOB Price '.$fobprice);
                // 10000 Min Order Value                
                $fobprice_10 = $totalcost+($totalcost*($thcost/$pricing->min_order_value));
                Log::info('FOB 1000 Price '.$fobprice_10);
            }

            // Local Prices
            $local_price = ($tmcost+$tlcost+$ttpcost+$tlocal_icost+$tlocalpackcost);

            $product = $shop->products()->where('id', $pricing->product_id)->first();
            if (!is_null($product)) {
                $product->retail_price = $local_price;
                $product->wholesale_price = $local_price;
                $product->retail_price_fob = $fobprice;
                // $product->wholesale_price_fob = $fob_w_price;
                $product->retail_price_fob_10 = $fobprice_10;
                // $product->wholesale_price_fob_10 = $fob_w_price_10;
                $product->save();
            }

            return redirect()->route('product-pricings.show', encrypt($pricing->id))->with('success', 'Product Price Set successfully');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $page = 'Edit Pricing Calculator';
        $title = 'Edit Pricing Calculator';
        $pricing = ProductPricing::find($request['id']);
        if (!is_null($pricing)) {
            $shop = Shop::find(Session::get('shop_id'));
            $product = Product::find($pricing->product_id);
            $products = $shop->products()->select('id', 'name')->get();
            
            return view('production.pricings.edit', compact('page', 'title', 'pricing', 'product', 'products'));
        }else{
            return redirect()->back()->with('error', 'Product Pricing Calculator not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $pricing = ProductPricing::find($id);
        if ($pricing->product_id != $request['product_id']) {
            $pricing->product_id = $request['product_id'];
            $pricing->save();
            $this->clearPricing($pricing);
            $oldpricing = ProductPricing::where('product_id', $request['product_id'])->where('is_pending', 0)->latest()->first();
            // Log::info($oldpricing);
            if (!is_null($oldpricing)) {
                $materialcosts = MaterialCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($materialcosts as $key => $value) {
                    $mcost = MaterialCost::where('product_pricing_id', $pricing->id)->where('item_desc', $value->item_desc)->first();
                    if (is_null($mcost)) {
                        $mcost = new MaterialCost();
                        $mcost->product_pricing_id = $pricing->id;
                        $mcost->item_desc = $value->item_desc;
                        $mcost->unit_cost = $value->unit_cost;
                        $mcost->no_of_piece_made = $value->no_of_piece_made;
                        $mcost->cost_per_piece = $value->cost_per_piece;
                        $mcost->save();
                    }      
                }

                $labourcosts = LabourCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($labourcosts as $key => $value) {
                    $labourcost = LabourCost::where('product_pricing_id', $pricing->id)->where('stage', $value->stage)->first();
                    if (is_null($labourcost)) {
                        $labourcost = new LabourCost();
                        $labourcost->product_pricing_id = $pricing->id;
                        $labourcost->stage = $value->stage;
                        $labourcost->daily_wage_rate = $value->daily_wage_rate;
                        $labourcost->no_of_piece = $value->no_of_piece;
                        $labourcost->cost_per_piece = $value->cost_per_piece;
                        $labourcost->save();
                    }
                }

                $transportcosts = TransportationCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($transportcosts as $key => $value) {
                    $transportcost = TransportationCost::where('product_pricing_id', $pricing->id)->first();
                    if (is_null($transportcost)) {
                        $transportcost = new TransportationCost();
                        $transportcost->product_pricing_id = $pricing->id;
                        $transpoertcost->description = $value->description;
                        $transpoertcost->transport_cost = $value->transport_cost;
                        $transpoertcost->no_of_items = $value->no_of_items;
                        $transpoertcost->cost_per_unit = $value->cost_per_unit;
                        $transpoertcost->save();
                    }
                }
                $indirectcosts = IndirectCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($indirectcosts as $key => $value) {
                    $indirectcost = IndirectCost::where('product_pricing_id', $pricing->id)->where('description', $value->description)->first();
                    if (is_null($indirectcost)) {
                        $indirectcost = new IndirectCost();
                        $indirectcost->product_pricing_id = $pricing->id;
                        $indirectcost->description = $value->description;
                        $indirectcost->percent = $value->percent;
                        $indirectcost->amount = $value->amount;
                        $indirectcost->save();
                    }
                }

                $localindirectcosts = LocalIndirectCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($localindirectcosts as $key => $value) {
                    $localindirectcost = LocalIndirectCost::where('product_pricing_id', $pricing->id)->where('description', $value->description)->first();
                    if (is_null($localindirectcost)) {
                        $localindirectcost = new LocalIndirectCost();
                        $localindirectcost->product_pricing_id = $pricing->id;
                        $localindirectcost->description = $value->description;
                        $localindirectcost->percent = $value->percent;
                        $localindirectcost->amount = $value->amount;
                        $localindirectcost->save();
                    }
                }

                $packagecosts = PackagingCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($packagecosts as $key => $value) {
                    $packagecost = PackagingCost::where('product_pricing_id', $pricing->id)->where('item_desc', $value->item_desc)->first();
                    if (is_null($packagecost)) {
                        $packagecost = new PackagingCost();
                        $packagecost->product_pricing_id = $pricing->id;
                        $packagecost->item_desc = $value->item_desc;
                        $packagecost->package_cost = $value->package_cost;
                        $packagecost->no_of_items = $value->no_of_items;
                        $packagecost->unit_cost = $value->unit_cost;
                        $packagecost->save();
                    }
                }

                $localpackagecosts = LocalPackagingCost::where('product_pricing_id', $oldpricing->id)->get();
                if ($localpackagecosts->count() > 0) {
                    foreach ($localpackagecosts as $key => $value) {
                        $localpackagecost = LocalPackagingCost::where('product_pricing_id', $pricing->id)->where('item_desc', $value->item_desc)->first();
                        if (is_null($localpackagecost)) {
                            $localpackagecost = new LocalPackagingCost();
                            $localpackagecost->product_pricing_id = $pricing->id;
                            $localpackagecost->item_desc = $value->item_desc;
                            $localpackagecost->package_cost = $value->package_cost;
                            $localpackagecost->no_of_items = $value->no_of_items;
                            $localpackagecost->unit_cost = $value->unit_cost;
                            $localpackagecost->save();
                        }
                    }
                }else{
                    foreach ($packagecosts as $key => $value) {
                        $localpackagecost = LocalPackagingCost::where('product_pricing_id', $pricing->id)->where('item_desc', $value->item_desc)->first();
                        if (is_null($localpackagecost)) {
                            $localpackagecost = new LocalPackagingCost();
                            $localpackagecost->product_pricing_id = $pricing->id;
                            $localpackagecost->item_desc = $value->item_desc;
                            $localpackagecost->package_cost = $value->package_cost;
                            $localpackagecost->no_of_items = $value->no_of_items;
                            $localpackagecost->unit_cost = $value->unit_cost;
                            $localpackagecost->save();
                        }
                    }
                }

                $handlingcosts = ExportHandlingCost::where('product_pricing_id', $oldpricing->id)->get();
                foreach ($handlingcosts as $key => $value) {
                    $handlingcost = ExportHandlingCost::where('product_pricing_id', $pricing->id)->where('description', $value->description)->first();
                    if (is_null($handlingcost)) {
                        $handlingcost = new ExportHandlingCost();
                        $handlingcost->product_pricing_id = $pricing->id;
                        $handlingcost->description = $value->description;
                        $handlingcost->amount = $value->amount;
                        $handlingcost->save();
                    }
                }
            }else{
                $materials = $shop->rawMaterials()->where('product_id', $request['product_id'])->get();
                foreach ($materials as $key => $value) {
                    $mcost = MaterialCost::where('product_pricing_id', $pricing->id)->where('item_desc', $value->name)->first();
                    if (is_null($mcost)) {
                        $mcost = new MaterialCost();
                        $mcost->product_pricing_id = $pricing->id;
                        $mcost->item_desc = $value->name;
                        $mcost->save();
                    }
                }

                $materials = $shop->rawMaterials()->whereNull('product_id')->get();
                foreach ($materials as $key => $value) {
                    $mcost = MaterialCost::where('product_pricing_id', $pricing->id)->where('item_desc', $value->name)->first();
                    if (is_null($mcost)) {
                        $mcost = new MaterialCost();
                        $mcost->product_pricing_id = $pricing->id;
                        $mcost->item_desc = $value->name;
                        $mcost->save();
                    }
                }

                $stages = ProductionStage::where('shop_id', $shop->id)->get();
                foreach ($stages as $key => $value) {
                    $labourcost = LabourCost::where('product_pricing_id', $pricing->id)->where('stage', $value->stage)->first();
                    if (is_null($labourcost)) {
                        $labourcost = new LabourCost();
                        $labourcost->product_pricing_id = $pricing->id;
                        $labourcost->stage = $value->stage;
                        $labourcost->save();
                    }
                }

                $indirects = ['General Overhead costs (fixed costs)', 'Design/Product development costs', 'Sales & Marketing', 'PROFIT MARGIN', 'Reject Margin', 'Currency Fluctuations %'];
                foreach ($indirects as $key => $value) {
                    $indirectcost = IndirectCost::where('description', $value)->where('product_pricing_id', $pricing->id)->first();
                    if (is_null($indirectcost)) {
                        $indirectcost = new IndirectCost();
                        $indirectcost->product_pricing_id = $pricing->id;
                        $indirectcost->description = $value;
                        $indirectcost->save();
                    }
                }

                $localindirects = ['General Overhead costs (fixed costs)', 'Design/Product development costs', 'Sales & Marketing', 'LOCAL PROFIT MARGIN', 'Reject Margin', 'Currency Fluctuations %'];
                foreach ($localindirects as $key => $value) {
                    $localindirectcost = LocalIndirectCost::where('description', $value)->where('product_pricing_id', $pricing->id)->first();
                    if (is_null($localindirectcost)) {
                        $localindirectcost = new LocalIndirectCost();
                        $localindirectcost->product_pricing_id = $pricing->id;
                        $localindirectcost->description = $value;
                        $localindirectcost->save();
                    }
                }

                $packages = ['Box size "outside"+ transport', 'Box size small + box divider + transport', 'Container packing labour', 'Rubber ring', 'Other packing materials', 'Labour'];
                foreach ($packages as $key => $value) {
                    $packagecost = PackagingCost::where('item_desc', $value)->where('product_pricing_id', $pricing->id)->first();
                    if (is_null($packagecost)) {
                        $packagecost = new PackagingCost();
                        $packagecost->product_pricing_id = $pricing->id;
                        $packagecost->item_desc = $value;
                        $packagecost->save();
                    }
                }
                foreach ($packages as $key => $value) {
                    $packagecost = LocalPackagingCost::where('item_desc', $value)->where('product_pricing_id', $pricing->id)->first();
                    if (is_null($packagecost)) {
                        $packagecost = new LocalPackagingCost();
                        $packagecost->product_pricing_id = $pricing->id;
                        $packagecost->item_desc = $value;
                        $packagecost->save();
                    }
                }

                $exhandlings = ['Loading container (labour)', 'Transport to Chako and then to the port', 'Certificate of origin', 'Other export handling costs', 'Shipping agents fees'];
                foreach ($exhandlings as $key => $value) {
                    $handlingcost = ExportHandlingCost::where('description', $value)->where('product_pricing_id', $pricing->id)->first();
                    if (is_null($handlingcost)) {
                        $handlingcost = new ExportHandlingCost();
                        $handlingcost->product_pricing_id = $pricing->id;
                        $handlingcost->description = $value;
                        $handlingcost->save();
                    }
                }
            }

            return $pricing;
        }else{
            $pricing->date = $request['date'];
            $pricing->currency = $request['currency'];
            $pricing->ex_rate = $request['ex_rate'];
            $pricing->min_order_value = $request['min_order_value'];
            $pricing->no_of_piece_per_set = $request['no_of_piece_per_set'];
            $pricing->shipping_import_fee = $request['shipping_import_fee'];
            $pricing->wholesale_eu_margin = $request['wholesale_eu_margin'];
            $pricing->vat = $request['vat'];
            $pricing->target_rrp = $request['target_rrp'];
            $pricing->domestic_w_margin = $request['domestic_w_margin'];
            $pricing->domestic_r_margin = $request['domestic_r_margin'];
            $pricing->save();

            return $pricing;
        }
    }

    public function clearPricing($pricing)
    {
            $materialcosts = MaterialCost::where('product_pricing_id', $pricing->id)->get();
            $labourcosts = LabourCost::where('product_pricing_id', $pricing->id)->get();
            $transportcosts = TransportationCost::where('product_pricing_id', $pricing->id)->get();
            $indirectcosts = IndirectCost::where('product_pricing_id', $pricing->id)->get();
            $localindirectcosts = LocalIndirectCost::where('product_pricing_id', $pricing->id)->get();
            $packagecosts = PackagingCost::where('product_pricing_id', $pricing->id)->get();
            $localpackagecosts = LocalPackagingCost::where('product_pricing_id', $pricing->id)->get();
            $handlingcosts = ExportHandlingCost::where('product_pricing_id', $pricing->id)->get();

            foreach ($materialcosts as $key => $mcost) {
                $mcost->delete();
            }

            foreach ($labourcosts as $key => $lcost) {
                $lcost->delete();
            }

            foreach ($transportcosts as $key => $tpcost) {
                $tpcost->delete();
            }

            foreach ($indirectcosts as $key => $icost) {
                $icost->delete();
            }

            foreach ($localindirectcosts as $key => $licost) {
                $licost->delete();
            }

            foreach ($packagecosts as $key => $pcost) {
                $pcost->delete();
            }

            foreach ($localpackagecosts as $key => $pcost) {
                $pcost->delete();
            }

            foreach ($handlingcosts as $key => $hcost) {
                $hcost->delete();
            }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pricing = ProductPricing::find(decrypt($id));
        if (!is_null($pricing)) {
            
            $materialcosts = MaterialCost::where('product_pricing_id', $pricing->id)->get();
            $labourcosts = LabourCost::where('product_pricing_id', $pricing->id)->get();
            $transportcosts = TransportationCost::where('product_pricing_id', $pricing->id)->get();
            $indirectcosts = IndirectCost::where('product_pricing_id', $pricing->id)->get();
            $localindirectcosts = LocalIndirectCost::where('product_pricing_id', $pricing->id)->get();
            $packagecosts = PackagingCost::where('product_pricing_id', $pricing->id)->get();
            $localpackagecosts = LocalPackagingCost::where('product_pricing_id', $pricing->id)->get();
            $handlingcosts = ExportHandlingCost::where('product_pricing_id', $pricing->id)->get();

            foreach ($materialcosts as $key => $mcost) {
                $mcost->delete();
            }

            foreach ($labourcosts as $key => $lcost) {
                $lcost->delete();
            }

            foreach ($transportcosts as $key => $tpcost) {
                $tpcost->delete();
            }

            foreach ($indirectcosts as $key => $icost) {
                $icost->delete();
            }

            foreach ($localindirectcosts as $key => $licost) {
                $licost->delete();
            }

            foreach ($packagecosts as $key => $pcost) {
                $pcost->delete();
            }

            foreach ($localpackagecosts as $key => $pcost) {
                $pcost->delete();
            }

            foreach ($handlingcosts as $key => $hcost) {
                $hcost->delete();
            }

            $pricing->delete();

            return redirect('product-pricings')->with('success', 'Pricing Calculator deleted successfully');
        }else{
            return redirect('product-pricings')->with('error', 'Pricing Calculator not found');
        }
    }
}
