<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Account;
use App\Models\Contract;
use App\Models\Device;
use App\Models\Room;
use App\Models\BookedRoom;
use App\Models\ContractService;
use App\Models\DailyDeposit;
use App\Models\CustomerCategory;
use App\Models\Customer;
USE App\Models\Garantor;
use App\Models\BookingAgent;
use App\Models\AnSale;
use App\Models\DeviceSale;
use App\Models\ServiceSaleItem;
use App\Models\SalePayment;
use App\Models\InvoiceNote;
use App\Models\CustomerTransaction;
use App\Models\AccountStatement;
use App\Models\SmsAccount;
use App\Models\SenderId;
use App\Models\SmsTemplate;
use App\Jobs\SendSMS;
use App\Models\ActionLog;


class ContractController extends Controller
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
        $page = 'Contracts';
        $title = 'Contracts';
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
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($defcurr)) {
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }

        $confcontracts = Contract::where('shop_id', $shop->id)->where('status', 'Confirmed')->get();
        foreach ($confcontracts as $key => $value) {
            $value->status = 'Created';
            $value->save();
        }

        $contracts = Contract::where('contracts.shop_id', $shop->id)->whereBetween('contracts.created_at', [$start, $end])->join('customers', 'customers.id', '=', 'contracts.customer_id')->join('users', 'users.id', '=', 'contracts.user_id')->join('devices', 'devices.id', '=', 'contracts.device_id')->select('contracts.id as id', 'first_name', 'last_name', 'tl_name', 'name', 'customers.phone as phone', 'cuid', 'device_number', 'device_name', 'start_date', 'end_date', 'status', 'contracts.created_at as created_at', 'contracts.updated_at as updated_at', 'is_deleted')->orderBy('start_date', 'desc')->get();

        return view('sales.contracts.index', compact('page', 'title', 'start_date', 'end_date', 'is_post_query', 'contracts', 'defcurr'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Contract';
        $title = 'New Contract';
        $shop = Shop::find(Session::get('shop_id'));
        $users = $shop->users()->select('first_name', 'last_name')->get();
        $custids = array(
            ['id' => 1, 'name' => 'TIN'],
            ['id' => 2, 'name' => 'Driving License'],
            ['id' => 3, 'name' => 'Voters Number'],
            ['id' => 4, 'name' => 'Passport'],
            ['id' => 5, 'name' => 'NIN'],
            ['id' => 6, 'name' => 'NIL'],
            ['id' => 7, 'name' => 'Meter No']
        );

        $devices = Device::where('shop_id', $shop->id)->where('is_assigned', false)->select('id', 'device_number', 'device_name')->orderBy('id', 'desc')->get();
        $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
        return view('sales.contracts.create', compact('page', 'title', 'shop', 'users', 'custids', 'categories', 'devices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (!empty($request['start_date'])) {
            $starting = Carbon::parse($request['start_date']);
            $enddate = $starting->addDays($request['period'])->format('Y-m-d');

            $customer = Customer::where('name', $request['name'])->where('phone', $request['phone'])->where('shop_id', $shop->id)->first();
            if (is_null($customer)) {
                $customer = new Customer();
                $customer->shop_id = $shop->id;
                $customer->name = $request['name'];
                $customer->email = $request['email'];
                $customer->phone = $request['phone'];
                $customer->physical_address = $request['physical_address'];
                $customer->tin = $request['tin'];
                $customer->time_created = Carbon::now();
                $customer->save();
            }

            $contract = Contract::where('shop_id', $shop->id)->where('customer_id', $customer->id)->where('device_id', $request['device_id'])->first();
            if (is_null($contract)) {
                    
                $g1= new Garantor();
                $g1->customer_id = $customer->id;
                $g1->full_name = $request['garantor_1'];
                $g1->mobile = $request['garantor_1_mobile'];
                $g1->save();

                $g2= new Garantor();
                $g2->customer_id = $customer->id;
                $g2->full_name = $request['garantor_2'];
                $g2->mobile = $request['garantor_2_mobile'];
                $g2->save();


                $contract = new Contract();
                $contract->shop_id = $shop->id;
                $contract->user_id = $user->id;
                $contract->cuid = time();
                $contract->customer_id = $customer->id;
                $contract->device_id = $request['device_id'];
                $contract->tl_name = $request['tl_name'];
                $contract->type = $request['type'];
                $contract->start_date = $request['start_date'];
                $contract->end_date = $enddate;
                $contract->save();

                $device = Device::find($contract->device_id);
                $device->is_assigned = true;
                $device->save();

                return redirect()->route('contracts.edit', encrypt($contract->id))->with('success', 'Contract created successfully');
            }else{
                return redirect()->route('contracts.edit', encrypt($contract->id))->with('error', 'Contract with the same details already created. Please Continue..');
            }
        }else{
            return redirect()->back()->with('error', 'Contract start date is required');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Contract Details';
        $title = 'Contract Details';
        $contract = Contract::where('contracts.id', decrypt($id))->join('customers', 'customers.id', '=', 'contracts.customer_id')->join('users', 'users.id', '=', 'contracts.user_id')->join('devices', 'devices.id', '=', 'contracts.device_id')->select('contracts.id as id', 'contracts.shop_id as shop_id', 'first_name', 'last_name', 'an_sale_id', 'customer_id', 'name', 'customers.phone as phone', 'customers.email as email', 'physical_address', 'cuid', 'start_date', 'end_date', 'terminated_at', 'actual_end_date', 'status', 'device_number', 'device_name', 'contracts.created_at as created_at', 'contracts.notes as notes', 'is_deleted')->first();
        if (!is_null($contract)) {
            $company = Company::find(Session::get('company_id'));
            $shop = Shop::find($contract->shop_id);
            $settings = Setting::where('shop_id', $shop->id)->first();
            $accounts = Account::where('shop_id', $shop->id)->get();
            $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $contractservices = ContractService::where('contract_id', $contract->id)->join('services', 'services.id', '=', 'contract_services.service_id')->select('contract_services.id as id', 'is_add_on', 'code', 'name', 'qty', 'unit_price', 'total')->get();
            $garantors = Garantor::where('customer_id', $contract->customer_id)->select('full_name', 'mobile')->get();
            $deposits = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'asc')->select('date', 'amount', 'created_at')->get();
            $ex_rate = 1;
            if($contract->currency != $contract->defcurr){
                $ex_rate = $contract->ex_rate;
            }

            $sale = AnSale::find($contract->an_sale_id);
            $payment = null;
            if (!is_null($sale)) {
                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
            }

            return view('sales.contracts.show', compact('page', 'title', 'company', 'shop', 'settings', 'accounts', 'defcurr', 'currencies', 'contract', 'garantors', 'ex_rate', 'sale', 'payment', 'contractservices', 'deposits'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Update Contract';
        $title = 'Update Contract';
        $contract = Contract::find(decrypt($id));
        if (!is_null($contract)) {
            $shop = Shop::find($contract->shop_id);
            $settings = Setting::where('shop_id', $shop->id)->first();
            $users = $shop->users()->select('first_name', 'last_name')->get();
            $customer = Customer::find($contract->customer_id);
            $garantors = Garantor::where('customer_id', $customer->id)->select('id', 'full_name', 'mobile')->get();
            $services = $shop->services()->select('service_id', 'code', 'name', 'price')->get();
            $devices = Device::where('shop_id', $shop->id)->where('is_assigned', false)->select('id', 'device_number', 'device_name')->get();
            $currdevice = Device::find($contract->device_id);
            $contractservices = ContractService::where('contract_id', $contract->id)->join('services', 'services.id', '=', 'contract_services.service_id')->select('contract_services.id as id', 'code', 'name', 'qty', 'unit_price', 'total')->get();

            $diff = strtotime($contract->end_date)-strtotime($contract->start_date);
            $period = round($diff / (60 * 60 * 24));
            return view('sales.contracts.edit', compact('page', 'title', 'shop', 'settings', 'users', 'contract', 'customer', 'garantors', 'services', 'devices', 'currdevice', 'contractservices', 'period'));
        }
    }

    public function saveChanges(Request $request)
    {
        // Log::info($request);
        $contract = Contract::find($request['contract_id']);
        if (!is_null($contract)) {

            $starting = Carbon::parse($request['start_date']);
            $enddate = $starting->addDays($request['period'])->format('Y-m-d');
            if ($contract->device_id != $request['device_id']) {
                $device = Device::find($request['device_id']);
                $device->is_assigned = true;
                $device->save();

                $olddevice = Device::find($contract->device_id);
                $olddevice->is_assigned = false;
                $olddevice->save();
            }
            $contract->type = $request['type'];
            $contract->start_date = $request['start_date'];
            $contract->end_date = $enddate;
            $contract->actual_end_date = $enddate;
            $contract->tl_name = $request['tl_name'];
            $contract->device_id = $request['device_id'];
            $contract->save();

            $diff = strtotime($contract->end_date) - strtotime($contract->start_date);
            $days = round($diff / (60 * 60 * 24));

            $ContractServices = ContractService::where('contract_id', $contract->id)->where('is_add_on', false)->get();
            foreach ($ContractServices as $key => $bservice) {
                $bservice->qty = $days;
                $bservice->total = $bservice->qty*$bservice->unit_price;
                $bservice->save();
            }
            
            return response()->json(['success' => 1, 'msg' => 'Currency Changed successfully']);
        }
    }
    public function changeCurrency(Request $request)
    {
        $contract = Contract::find($request['contract_id']);
        $contract->currency = $request['currency'];
        $contract->save();

        return response()->json(['success' => 1, 'msg' => 'Currency Changed successfully']);
    }

    public function changeRateMode(Request $request)
    {
        $contract = Contract::find($request['contract_id']);
        $contract->ex_rate_mode = $request['ex_rate_mode'];
        $contract->save();

        return response()->json(['success' => 1, 'msg' => 'Rate Mode Changed successfully']);
    }

    public function saveForeignRate(Request $request)
    {
        $contract = Contract::find($request['contract_id']);
        if (!is_null($contract)) {
            $local_ex_rate = 1;
            $foreign_ex_rate = 1;
            $ex_rate = 1;
            if ($contract->currency != $contract->defcurr) {
                $foreign_ex_rate = $request['foreign_ex_rate'];
                $ex_rate = 1 / $foreign_ex_rate;
            }
            $contract->local_ex_rate = $local_ex_rate;
            $contract->foreign_ex_rate = $foreign_ex_rate;
            $contract->ex_rate = $ex_rate;
            $contract->save();

            return response()->json(['success' => 1, 'msg' => 'Rate updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'contract not found']);
        }
    }

    public function saveLocalRate(Request $request)
    {
        $contract = Contract::find($request['contract_id']);
        if (!is_null($contract)) {
            $local_ex_rate = 1;
            $foreign_ex_rate = 1;
            $ex_rate = 1;
            if ($contract->currency != $contract->defcurr) {
                $local_ex_rate = $request['local_ex_rate'];
                $ex_rate = $local_ex_rate;
            }

            $contract->local_ex_rate = $local_ex_rate;
            $contract->foreign_ex_rate = $foreign_ex_rate;
            $contract->ex_rate = $ex_rate;
            $contract->save();

            return response()->json(['success' => 1, 'msg' => 'Rate updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'contract not found']);
        }
    }

    public function addRoom(Request $request)
    {
        // Log::info($request);
        $room = Room::find($request['room_id']);
        if (!is_null($room)) {   
            $broom = new BookedRoom();
            $broom->contract_id = $request['contract_id'];
            $broom->room_id = $room->id;
            $broom->save();

            return response()->json(['success' => 1, 'msg' => 'Room Added successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Room not found']);
        }
    }

    public function removeRoom($id)
    {
        BookedRoom::destroy(decrypt($id));

        return redirect()->back();
    }


    public function addService(Request $request)
    {
        // Log::info($request);
        $shop = Shop::find(Session::get('shop_id'));
        $service = $shop->services()->where('id', $request['service_id'])->select('service_id', 'price')->first();
        if (!is_null($service)) {
            $contract = Contract::find($request['contract_id']);
            $days = 1;
            if (!$request['is_add_on']) {
                $diff = strtotime($contract->end_date) - strtotime($contract->start_date);
                $days = round($diff / (60 * 60 * 24));
            }
            $bservice = new ContractService();
            $bservice->contract_id = $request['contract_id'];
            $bservice->service_id = $service->service_id;
            $bservice->qty = $days;
            $bservice->unit_price = $service->price;
            $bservice->total = $bservice->qty*$bservice->unit_price;
            $bservice->is_add_on = $request['is_add_on'];
            $bservice->save();

            return response()->json(['success' => 1, 'msg' => 'Service Added successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Service not found']);
        }
    }

    public function updateService(Request $request)
    {
        $bservice = ContractService::find($request['id']);
        if (!is_null($bservice)) {
            $contract = Contract::find($bservice->contract_id);
            $bservice->qty = $request['qty'];
            $bservice->total = $bservice->qty*$bservice->unit_price;
            $bservice->save();
            return response()->json(['success' => 1, 'msg' => 'Service updated successfully']);
        }else{
            return response()->json(['success' => 0, 'msg' => 'Service not found']);
        }

    }

    public function removeService($id)
    {
        ContractService::destroy(decrypt($id));
        return redirect()->back();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contract = Contract::find(decrypt($id));
        if (!is_null($contract)) {
            $shop = Shop::find($contract->shop_id);
            $user = Auth::user();

            if ($contract->device_id != $request['device_id']) {
                $device = Device::find($request['device_id']);
                $device->is_assigned = true;
                $device->save();

                $olddevice = Device::find($contract->device_id);
                $olddevice->is_assigned = false;
                $olddevice->save();
            }

            $contract->type = $request['type'];
            $contract->start_date = $request['start_date'];
            $contract->end_date = $request['end_date'];
            $contract->actual_end_date = $contract->end_date;
            $contract->tl_name = $request['tl_name'];
            $contract->device_id = $request['device_id'];
            $contract->comments = $request['comments'];
            $contract->save();

            $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
            $maxsaleno = AnSale::where('shop_id', $shop->id)->orderByRaw('CONVERT(invoice_no, SIGNED) desc')->first();
            $invoice_no = null;
            if (!is_null($maxsaleno)) {
                $invoice_no = $maxsaleno->invoice_no + 1;
            } else {
                $invoice_no = 1;
            }

            $timenow = Carbon::now();
            $time = date('H:i:s', strtotime($timenow));
            $now = $contract->start_date . ' ' . $time;

            $sale = AnSale::find($contract->an_sale_id);
            if (is_null($sale)) {
                $sale = new AnSale();
                $sale->customer_id = $contract->customer_id;
                $sale->shop_id = $shop->id;
                $sale->user_id = $user->id;
                $sale->comments = $contract->comments;
                $sale->currency = $defcurr->code;
                $sale->defcurr = $defcurr->code;
                $sale->ex_rate = 1;
                $sale->status = 'Unpaid';
                $sale->time_created = $now;
                $sale->sale_type = 'credit';
                $sale->invoice_no = $invoice_no;
                $sale->due_date = $contract->end_date;
                $sale->save();

                $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                if (!is_null($notes)) {
                    $sale->note = $notes->content;
                }
                $sale->save();


            }else{
                $sale->comments = $contract->comments;
                $sale->time_created = $now;
                $sale->currency = $defcurr->code;
                $sale->defcurr = $defcurr->code;
                $sale->ex_rate = 1;
                $sale->sale_type = 'credit';
                $sale->due_date = $contract->end_date;
                $sale->save();

                $notes = InvoiceNote::where('shop_id', $shop->id)->where('used_in', 'Invoice')->where('note_type', 'Notes')->first();
                if (!is_null($notes)) {
                    $sale->note = $notes->content;
                }
                $sale->save();
            }

            $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();
            if (!is_null($dsale)) {
                $dsale->device_id = $request['device_id'];
                $dsale->save();
            }else{
                DeviceSale::create([
                    'device_id' => $request['device_id'],
                    'an_sale_id' => $sale->id
                ]);
            }
            $contract_amount = 0;
            $servsale_amount = 0;
            $servsale_discount = 0;
            $servtax_amount = 0;
            $contractservices = ContractService::where('contract_id', $contract->id)->get();
            foreach ($contractservices as $key => $value) {
                $saleitemData = ServiceSaleItem::where('an_sale_id', $sale->id)->where('service_id', $value->service_id)->first();
                if (is_null($saleitemData)) {
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
                        $saleitemData->no_of_repeatition = $value->qty;
                        $saleitemData->price = $value->unit_price;
                        $saleitemData->total = $value->total;
                        $saleitemData->disc_percent = 0;
                        $saleitemData->discount = 0;
                        $saleitemData->total_discount = 0;
                        $saleitemData->tax_amount = 0;
                        $saleitemData->time_created = $now;
                        $saleitemData->save();
                    }
                }else{
                    $saleitemData->no_of_repeatition = $value->qty;
                    $saleitemData->price = $value->unit_price;
                    $saleitemData->total = $value->total;
                    $saleitemData->disc_percent = 0;
                    $saleitemData->discount = 0;
                    $saleitemData->total_discount = 0;
                    $saleitemData->tax_amount = 0;
                    $saleitemData->time_created = $now;
                    $saleitemData->save();
                }

                $servsale_amount += $saleitemData->total;

                if (!$value->is_add_on) {
                    $contract_amount += $value->total;
                }
            }

            $sale->sale_amount = $servsale_amount;
            $sale->sale_discount = $servsale_discount;
            $sale->tax_amount = $servtax_amount;
            $sale->save();
                
            $netsaleamount = ($sale->sale_amount - $sale->sale_discount)+$sale->tax_amount;
            $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('customer_id', $sale->customer_id)->where('shop_id', $shop->id)->first();
            if (is_null($acctrans)) {
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
            }else{
                $acctrans->amount = $netsaleamount;
                $acctrans->currency = $sale->currency;
                $acctrans->defcurr = $sale->defcurr;
                $acctrans->ex_rate = $sale->ex_rate;
                $acctrans->date = $now;
                $acctrans->save();
            }

            $contract->an_sale_id = $sale->id;
            $contract->amount = $contract_amount;
            if ($sale->sale_amount_paid > 0) {
                $contract->status = 'Working';
            }else{
                $contract->status = 'Created';
            }
            $contract->notes = $sale->note;
            $contract->save();

            $firstdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'asc')->first();
            if (!is_null($firstdeposit) && $contract->start_date != $firstdeposit->date) {
                Log::info('Contract start date '.$contract->start_date.' First deposit date'.$firstdeposit->date);
                $ddeposits = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'asc')->get();
                $currdate = $contract->start_date;
                foreach ($ddeposits as $key => $value) {
                    $date = strtotime("+".$key." day", strtotime($currdate));
                    if ($key > 0) {
                        $date = strtotime("+1 day", strtotime($currdate));
                    }
                    $currdate = date("Y-m-d", $date);
                    $value->date = $currdate;
                    $value->save();
                }
            }else{
                Log::info('Same dates ');
            }

            $cust = Customer::where('id', $sale->customer_id)->whereNotNull('phone')->first();
            if (!is_null($cust)) {
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
                                Log::info('Models Number '.$cust->phone.' is invalid. SMS not sent');
                            }
                        }
                    }
                }
            }

            return redirect()->route('contracts.show', encrypt($contract->id))->with('success', 'contract confirm successfully, Please complete Payment Now');
        }else{
            return redirect()->back()->with('error', 'contract not found');
        }
    }

    public function cancelcontract($id)
    {
        $contract = Contract::find(decrypt($id));
        if (!is_null($contract)) {
            $shop = Shop::find($contract->shop_id);
            $sale = AnSale::where('id', $contract->an_sale_id)->where('shop_id', $shop->id)->first();
            if (!is_null($sale)) {
                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                    $sitem->is_deleted  = true;
                        $sitem->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                        $sitem->save();
                        // $sitem->delete();
                    }
                }

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->is_deleted = true;
                    $acctrans->save();
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->is_deleted = true;
                        $payment->save();
                        $acctrans = CustomerTransaction::find($payment->trans_id);
                        if (!is_null($acctrans)) {
                            if ($acctrans->payment == $payment->amount) {
                                $acctrans->is_deleted = true;
                                $acctrans->save();
                                $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                if (!is_null($astmt)) {
                                    $astmt->is_deleted = true;
                                    $astmt->save();
                                }
                            }else{
                                $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                                $acctrans->is_utilized = false;
                                $acctrans->save();
                            }
                        }
                    }
                }
                        
                $sale->is_deleted = true;
                $sale->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                $sale->save();
                // $sale->delete();
                $actlog = new ActionLog();
                $actlog->shop_id = $shop->id;
                $actlog->user_id = Auth::user()->id;
                $actlog->action_type = 'Cancel contract & Invoice';
                $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been cancelled';
                $actlog->save();
            }

            $contract->is_deleted = true;
            $contract->status = 'Cancelled';
            $contract->save();

            return redirect('contracts')->with('success', 'contract cancelled successfully');
        }else{
            return redirect()->back()->with('error', 'contract not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contract = Contract::find(decrypt($id));
        if (!is_null($contract)) {
            $shop = Shop::find($contract->shop_id);
            $sale = AnSale::where('id', $contract->an_sale_id)->where('shop_id', $shop->id)->first();
            if (!is_null($sale)) {
                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->delete();
                    }
                }

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->delete();
                        $acctrans = CustomerTransaction::find($payment->trans_id);
                        if (!is_null($acctrans)) {
                            if ($acctrans->payment == $payment->amount) {
                                $acctrans->delete();
                                $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                if (!is_null($astmt)) {
                                    $astmt->delete();
                                }
                            }else{
                                $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount-$payment->amount;
                                $acctrans->delete();
                            }
                        }
                    }
                }

                $sale->delete();
                $actlog = new ActionLog();
                $actlog->shop_id = $shop->id;
                $actlog->user_id = Auth::user()->id;
                $actlog->action_type = 'Delete contract and Invoice';
                $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been deleted';
                $actlog->save();
            }

            $ContractServices = ContractService::where('contract_id', $contract->id)->get();
            foreach ($ContractServices as $key => $value) {
                $value->delete();
            }

            $deposits = DailyDeposit::where('contract_id', $contract->id)->get();
            foreach ($deposits as $key => $value) {
                $value->delete();
            }
            
            $contract->delete();

            return redirect('contracts')->with('success', 'contract deleted successfully');
        }else{
            return redirect()->back()->with('error', 'contract not found');
        }
    }


    public function terminateContract(Request $request)
    {
        $contract = Contract::find($request['contract_id']);
        $contract->termination_reason = $request['termination_reason'];
        $contract->status = 'Terminated';
        $contract->terminated_by = Auth::user()->first_name.' '.Auth::user()->last_name;
        $contract->terminated_at = Carbon::now();
        $lastdeposit = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->first();
        if (!is_null($lastdeposit)) {
            $contract->actual_end_date = $lastdeposit->date;
        }
        $contract->save();

        $device = Device::find($contract->device_id);
        $device->is_assigned = false;
        $device->save();

        $customer = Customer::find($contract->customer_id);
        $customer->is_active = false;
        $customer->save();

        return redirect()->back()->with('success', 'Contract terminated successfully');
    }

    public function resumeContract($id)
    {
        $contract = Contract::find(decrypt($id));
        if (!is_null($contract)) {
            $contract->status = 'Working';
            $contract->termination_reason = null;
            $contract->terminated_at = null;
            $contract->actual_end_date = null;
            $contract->terminated_by = null;
            $contract->save();
            
            $device = Device::find($contract->device_id);
            $device->is_assigned = true;
            $device->save();
            
            $customer = Customer::find($contract->customer_id);
            $customer->is_active = true;
            $customer->save();
            
            return redirect()->back()->with('success', 'Contract terminated successfully');
        }else{
            return redirect()->back()->with('info', 'Contract not found');
        }
    }
}
