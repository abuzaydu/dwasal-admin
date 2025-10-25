<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\TripLog;
use App\Models\Device;
use App\Models\Customer;
use App\Models\Service;
use App\Models\AnSale;
use App\Models\ServiceSaleItem;
use App\Models\CustomerTransaction;
use App\Models\SalePayment;
use App\Models\CustomerCategory;

class TripLogsController extends Controller
{
    function __construct()
    {
        $this->middleware(['auth']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Trip Logs';
        $title = 'Trip Logs';

        $now = Carbon::now();
        $start = $now->startOfMonth();
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
        $triplogs = [];
        if (Auth::user()->can('view-all-trips')) {
            $triplogs = TripLog::where('trip_logs.shop_id', $shop->id)->whereBetween('trip_date', [$start, $end])->join('devices', 'devices.id', '=', 'trip_logs.device_id')->join('users', 'users.id', '=', 'trip_logs.user_id')->select('trip_logs.id as id', 'device_number', 'device_name', 'trip_date', 'trip_end_date', 'trip_title', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel', 'fuel_unit_cost', 'trip_logs.created_at as created_at', 'first_name', 'last_name')->get();
        }else{
            $triplogs = TripLog::where('trip_logs.shop_id', $shop->id)->where('user_id', Auth::user()->id)->whereBetween('trip_date', [$start, $end])->join('devices', 'devices.id', '=', 'trip_logs.device_id')->join('users', 'users.id', '=', 'trip_logs.user_id')->select('trip_logs.id as id', 'device_number', 'device_name', 'trip_date', 'trip_end_date', 'trip_title', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel', 'fuel_unit_cost', 'trip_logs.created_at as created_at', 'first_name', 'last_name')->get();
        }

        return view('services.trip-logs.index', compact('page', 'title', 'triplogs', 'is_post_query', 'start_date', 'end_date'));
    }


    public function tripLogs(Request $request)
    {
        $page = 'Trip Logs';
        $title = 'Trip Logs';
        $title_sw = 'Trip Logs';

        $now = Carbon::now();
        $start = $now->startOfMonth();
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
        $currency = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $devices = Device::where('shop_id', $shop->id)->get();
        $device = null;
        $drivers = TripLog::whereNotNull('driver')->select('driver')->groupBy('driver')->get();
        $currdriver = null;
        if (!empty($request['device_id'])) {
            $device = Device::find($request['device_id']);

            if (!empty($request['driver'])) {
                $currdriver = $request['driver'];
                $triplogs = TripLog::where('driver', $currdriver)->whereBetween('trip_date', [$start, $end])->where('device_id', $device->id)->join('customers', 'customers.id', '=', 'trip_logs.customer_id')->select('trip_logs.id as id', 'name', 'trip_date', 'trip_end_date', 'trip_title', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel_start', 'fuel_end', 'fuel_unit_cost', 'container_no', 'container_size', 'bill_no', 'shipping', 'driver')->get();
            }else{
                $triplogs = TripLog::whereBetween('trip_date', [$start, $end])->where('device_id', $device->id)->join('customers', 'customers.id', '=', 'trip_logs.customer_id')->select('trip_logs.id as id', 'name', 'trip_date', 'trip_end_date', 'trip_title', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel_start', 'fuel_end', 'fuel_unit_cost', 'container_no', 'container_size', 'bill_no', 'shipping', 'driver')->get();
            }
        }else{
            if (!empty($request['driver'])) {
                $currdriver = $request['driver'];
                $triplogs = TripLog::where('driver', $currdriver)->whereBetween('trip_date', [$start, $end])->join('customers', 'customers.id', '=', 'trip_logs.customer_id')->join('devices', 'devices.id', '=', 'trip_logs.device_id')->select('trip_logs.id as id', 'device_number', 'device_name', 'name', 'trip_date', 'trip_end_date', 'trip_title', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel_start', 'fuel_end', 'fuel_unit_cost', 'container_no', 'container_size', 'bill_no', 'shipping', 'driver')->get();
            }else {
                $triplogs = TripLog::whereBetween('trip_date', [$start, $end])->join('customers', 'customers.id', '=', 'trip_logs.customer_id')->join('devices', 'devices.id', '=', 'trip_logs.device_id')->select('trip_logs.id as id', 'device_number', 'device_name', 'name', 'trip_date', 'trip_end_date', 'trip_title', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel_start', 'fuel_end', 'fuel_unit_cost', 'container_no', 'container_size', 'bill_no', 'shipping', 'driver')->get();
            }
        }

        $duration = 'From '.date('d/m/Y', strtotime($start)).' To '.date('d/m/Y', strtotime($end)).'.';

        return view('services.trip-logs.report', compact('page', 'title', 'title_sw', 'shop', 'settings', 'device', 'devices', 'drivers', 'currdriver', 'triplogs', 'currency', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Trip';
        $title = 'New Trip';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $devices = Device::where('shop_id', $shop->id)->get();
        $customers = Customer::where('shop_id', $shop->id)->select('id', 'name')->get();
        $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();

        return view('services.trip-logs.create', compact('page', 'title', 'shop', 'settings', 'devices', 'categories', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request;
        $shop = Shop::find(Session::get('shop_id'));
        $triplog = new TripLog();
        $triplog->shop_id = $shop->id;
        $triplog->user_id = Auth::user()->id;
        $triplog->customer_id = $request['customer_id'];
        $triplog->device_id = $request['device_id'];
        $triplog->trip_date = $request['trip_date'];
        $triplog->trip_end_date = $request['trip_end_date'];
        $triplog->trip_title = $request['trip_title'];
        $triplog->trip_price = $request['trip_price'];
        $triplog->from = $request['from'];
        $triplog->to = $request['to'];
        $triplog->mileage_out = $request['mileage_out'];
        $triplog->mileage_in = $request['mileage_in'];
        $triplog->fuel_start = $request['fuel_start'];
        $triplog->fuel_end = $request['fuel_end'];
        if (!is_null($triplog->fuel_end) && !is_null($triplog->fuel_start)) {
            $triplog->fuel = $triplog->fuel_start-$triplog->fuel_end;
        }
        $triplog->fuel_unit_cost = $request['fuel_unit_cost'];
        $triplog->driver = $request['driver'];
        $triplog->container_no = $request['container_no'];
        $triplog->container_size = $request['container_size'];
        $triplog->bill_no = $request['bill_no'];
        $triplog->shipping = $request['shipping'];
        $triplog->gross_weight = $request['gross_weight'];
        $triplog->net_weight = $request['net_weight'];
        $triplog->load_type = $request['load_type'];
        $triplog->is_transit = $request['is_transit'];
        $triplog->save();

        return redirect()->route('trip-logs.show', encrypt($triplog->id))->with('success', 'Trip Log created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Trip Log Details';
        $title = 'Trip Log Details';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $devices = Device::where('shop_id', $shop->id)->get();
        $currency = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
        $trip = TripLog::where('trip_logs.id', decrypt($id))->join('devices', 'devices.id', '=', 'trip_logs.device_id')->join('users', 'users.id', '=', 'trip_logs.user_id')->select('trip_logs.id as id', 'device_number', 'device_name', 'customer_id', 'an_sale_id', 'trip_date', 'trip_end_date', 'trip_title', 'trip_price', 'from', 'to', 'mileage_out', 'mileage_in', 'fuel_start', 'fuel_end', 'fuel', 'fuel_unit_cost', 'trip_logs.created_at as created_at', 'first_name', 'last_name', 'driver', 'container_no', 'container_size', 'bill_no', 'shipping', 'gross_weight', 'net_weight', 'load_type', 'is_transit')->first();
        $customer = Customer::find($trip->customer_id);

        return view('services.trip-logs.show', compact('page', 'title', 'shop', 'settings', 'devices', 'trip', 'customer', 'currency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Trip Log';
        $title = 'Edit Trip Log';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $devices = Device::where('shop_id', $shop->id)->get();
        $trip = TripLog::find(decrypt($id));
        $customers = Customer::where('shop_id', $shop->id)->select('id', 'name')->get();

        return view('services.trip-logs.edit', compact('page', 'title', 'shop', 'settings', 'devices', 'customers', 'trip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $triplog = TripLog::find(decrypt($id));
        $triplog->customer_id = $request['customer_id'];
        $triplog->device_id = $request['device_id'];
        $triplog->trip_date = $request['trip_date'];
        $triplog->trip_end_date = $request['trip_end_date'];
        $triplog->trip_title = $request['trip_title'];
        $triplog->trip_price = $request['trip_price'];
        $triplog->from = $request['from'];
        $triplog->to = $request['to'];
        $triplog->mileage_out = $request['mileage_out'];
        $triplog->mileage_in = $request['mileage_in'];
        $triplog->fuel_start = $request['fuel_start'];
        $triplog->fuel_end = $request['fuel_end'];
        if (!is_null($triplog->fuel_end) && !is_null($triplog->fuel_start)) {
            $triplog->fuel = $triplog->fuel_start-$triplog->fuel_end;
        }
        $triplog->fuel_unit_cost = $request['fuel_unit_cost'];
        $triplog->driver = $request['driver'];
        $triplog->container_no = $request['container_no'];
        $triplog->container_size = $request['container_size'];
        $triplog->bill_no = $request['bill_no'];
        $triplog->shipping = $request['shipping'];
        $triplog->gross_weight = $request['gross_weight'];
        $triplog->net_weight = $request['net_weight'];
        $triplog->load_type = $request['load_type'];
        $triplog->is_transit = $request['is_transit'];
        $triplog->save();

        return redirect()->route('trip-logs.show', encrypt($triplog->id))->with('success', 'Trip Log created successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $triplog = TripLog::find(decrypt($id));
        if (!is_null($triplog)) {
            $sale = AnSale::find($triplog->an_sale_id);
            if (!is_null($sale)) {
                return redirect('trip-logs')->with('info', 'Trip Log with Invoice cannot be deleted');
            }else{
                $triplog->delete();
                return redirect('trip-logs')->with('success', 'Trip Log deleted successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Trip Log not Found');
        }
    }

    public function createInvoice($id, Request $request)
    {
        $page = 'New Invoice';
        $title = 'New Invoice';
        $customer = Customer::find(decrypt($id));
        if (!is_null($customer)) {
            $now = \Carbon\Carbon::now();
            $start = Carbon::now()->subDays(7);
            $end = \Carbon\Carbon::now();
            $start_date = date('Y-m-d', strtotime($start));            
            $end_date = date('Y-m-d', strtotime($end));
            $is_post_query = false;
            //check if user opted for date range
            $is_post_query = false;
            if (!empty($request['start_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $start = $request['start_date'].' 00:00:00';
                $end = $request['end_date'].' 23:59:59';
                $is_post_query = true;
            }

            $shop = Shop::find($customer->shop_id);
            $settings = Setting::where('shop_id', $shop->id)->first();
            $currency = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first()->code;
            $triplogs = TripLog::where('shop_id', $shop->id)->where('customer_id', $customer->id)->whereNull('an_sale_id')->whereNotNull('to')->whereNotNull('trip_end_date')->whereNotNull('mileage_in')->whereBetween('trip_date', [$start, $end])->get();
            $currTrips = [];
            return view('services.trip-logs.create-invoice', compact('page', 'title', 'shop', 'settings', 'currency', 'customer', 'triplogs', 'currTrips', 'is_post_query', 'start_date', 'end_date'));
        }else{
            return redirect('trip-logs')->with('info', 'Client not found');
        }
    }

    public function tripsInvoice(Request $request)
    {
        $now = Carbon::now();
        if (!empty($request['sale_date'])) {
            $now = $request['sale_date'];
        }
        $due_date = Carbon::now()->addDays(10);
        if (!empty($request['due_date'])) {
            $due_date = $request['due_date'];
        }

        Log::info($now.' Due '.$due_date);
        if (!empty($request['trips']) && count($request['trips']) > 0) {
            $shop = Shop::find(Session::get('shop_id'));
            $settings = Setting::where('shop_id', $shop->id)->first();
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $user = Auth::user();

            $maxsaleno = AnSale::where('shop_id', $shop->id)->orderByRaw('CONVERT(invoice_no, SIGNED) desc')->first();
            $invoice_no = null;
            if (!is_null($maxsaleno)) {
                $invoice_no = $maxsaleno->invoice_no + 1;
            } else {
                $invoice_no = 1;
            }

            $sale = new AnSale();
            $sale->customer_id = $request['customer_id'];
            $sale->shop_id = $shop->id;
            $sale->user_id = $user->id;
            $sale->currency = $defcurr->code;
            $sale->defcurr = $defcurr->code;
            $sale->ex_rate = 1;
            $sale->status = 'Unpaid';
            $sale->time_created = $now;
            $sale->sale_type = 'credit';
            $sale->invoice_no = $invoice_no;
            $sale->due_date = $due_date;
            $sale->comments = $request['comments'];
            $sale->save();

            $servsale_amount = 0;
            $servsale_discount = 0;
            $servtax_amount = 0;
            $servitems = array();
            foreach ($request['trips'] as $id) {
                $trip = TripLog::find($id);
                if (!is_null($trip)) {
                    $service = $shop->services()->where('name', $trip->trip_title.' ('.$trip->from.' - '.$trip->to.')')->select('services.id as id')->first();
                    if (is_null($service)) {
                        $service = new Service();
                        $service->name = $trip->trip_title.' ('.$trip->from.' - '.$trip->to.')';
                        $service->save();

                        $code = $this->getAutoCode();
                        $shop->services()->attach($service, ['code' => $code, 'description' => '', 'price' => 0, 'active_for_sale' => 1, 'time_created' => $now]);
                    }
                }

                array_push($servitems, ['service_id' => $service->id, 'price' => $trip->trip_price]);
            }

            $result = [];
            foreach ($servitems as $key => $value) {
                if (isset($result[$value['service_id'].'-'.$value['price']])) {
                    $result[$value['service_id'].'-'.$value['price']]['qty'] = $result[$value['service_id'].'-'.$value['price']]['qty']+1;
                }else{
                    $result[$value['service_id'].'-'.$value['price']]['service_id'] = $value['service_id'];
                    $result[$value['service_id'].'-'.$value['price']]['price'] = $value['price'];
                    $result[$value['service_id'].'-'.$value['price']]['qty'] = 1;
                }
            }

            Log::info($result);

            foreach ($result as $key => $value) {
                $saleitemData = new ServiceSaleItem;
                $saleitemData->an_sale_id = $sale->id;
                $saleitemData->service_id = $value['service_id'];
                $saleitemData->no_of_repeatition = $value['qty'];
                if (!is_null($value['price'])) {
                    $saleitemData->price = $value['price'];
                }else{
                    $saleitemData->price = 0;
                }
                $saleitemData->total = $saleitemData->no_of_repeatition*$saleitemData->price;
                $saleitemData->disc_percent = 0;
                $saleitemData->discount = 0;
                $saleitemData->total_discount = 0;

                $saleitemData->with_vat = $request['with_vat'];
                if ($saleitemData->with_vat == 'yes') {
                    $vat_amount =  ($saleitemData->total-$saleitemData->total_discount)*($settings->tax_rate/100);
                    $saleitemData->tax_amount = $vat_amount;
                }else{
                    $saleitemData->tax_amount = 0;
                }
                $saleitemData->time_created = $now;
                $saleitemData->save();
            }
            
            $servsale_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total');
            $servsale_discount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('total_discount');
            $servtax_amount = ServiceSaleItem::where('an_sale_id', $sale->id)->sum('tax_amount');
            
            $sale->sale_amount = $servsale_amount;
            $sale->sale_discount = $servsale_discount;
            $sale->tax_amount = $servtax_amount;
            $sale->save();
                
            $netsaleamount = ($sale->sale_amount - $sale->sale_discount)+$sale->tax_amount;

            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $sale->customer_id;
            $acctrans->an_sale_id = $sale->id;
            $acctrans->invoice_no = $sale->invoice_no;
            $acctrans->amount = $netsaleamount;
            $acctrans->currency = $sale->currency;
            $acctrans->defcurr = $sale->defcurr;
            $acctrans->ex_rate = $sale->ex_rate;
            $acctrans->date = $now;
            $acctrans->save();
                                

            foreach ($request['trips'] as $id) {
                $trip = TripLog::find($id);
                $trip->an_sale_id = $sale->id;
                $trip->save();
            }

            $utransactions = CustomerTransaction::where('shop_id', $shop->id)->where('customer_id', $sale->customer_id)->whereNotNull('receipt_no')->where('is_utilized', false)->where('is_deleted', false)->get();
            if (!is_null($utransactions)) {
                foreach ($utransactions as $key => $trans) {
                    $rem_amount = $trans->payment - ($trans->trans_invoice_amount + $trans->trans_ob_amount + $trans->trans_credit_amount);
                    if ($rem_amount > 0) {
                        $paidamount = 0;
                        if ($rem_amount > $netsaleamount) {
                            $paidamount = $netsaleamount;
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
                            'pay_date' => $now,
                            'cheque_no' => $trans->cheque_no,
                            'amount' => $paidamount,
                            'currency' => $trans->currency,
                            'defcurr' => $trans->defcurr,
                            'ex_rate' => $trans->ex_rate,
                            'cashier' => $trans->cashier,
                            'cc_time' => $trans->cc_time
                        ]);
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
                    }
                }
            }

            return redirect()->route('invoices.show', encrypt($sale->id))->with('success', 'Invoice created successfully');
        }else{
            return redirect()->back()->with('error', 'No Trip selected. Please select at least one Trip to continue');
        }
    }


    public function getAutoCode()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($shop->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        $service = $shop->services()->orderBy('code', 'desc')->first();
        if (!is_null($service)) {
            $last = str_replace($v.'/S-', '', $service->code);
            $lastcode = (int)$last;
            $id = $v.'/S-'.sprintf('%03d', $lastcode+1);
            return $id;   
        }else{
            $id = $v.'/S-'.sprintf('%03d', 1);
            return $id; 
        }
    }
    
}
