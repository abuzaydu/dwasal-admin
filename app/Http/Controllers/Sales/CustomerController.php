<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Models\AccountStatement;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\CustomerTransaction;
use App\Models\DailyDeposit;
use App\Models\DeviceSale;
use App\Models\Garantor;
use App\Models\InvoiceItem;
use App\Models\ProInvoice;
use App\Models\SaleItemTemp;
use App\Models\SalePayment;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\SaleTemp;
use App\Models\ServiceItemTemp;
use App\Models\ServiceSaleItem;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\TripLog;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class CustomerController extends Controller
{
    public function __construct()
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
        if (!is_null($shop)) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            
            $page = 'Customers';
            $title = 'My Customers';
            $title_sw = 'Wateja wangu';
            if ($settings->is_cm_business) {
                $page = 'Riders';
                $title = 'Riders';
            }
            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );
            $inactivecustomers = Customer::where('shop_id', $shop->id)->where('is_active', false)->select('id', 'name', 'phone', 'time_created')->orderBy('name', 'asc')->get();
            $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
            return view('sales.customers.index', compact('page', 'title', 'title_sw', 'shop', 'settings', 'categories', 'custids', 'inactivecustomers'));
        }else{
            return redirect('login');
        }
    }

    public function getShopCustomers(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Customer::where('shop_id', $shop->id)->where('is_active', true)->select('count(*) as allcount')->count();
        $totalRecordswithFilter = Customer::where('shop_id', $shop->id)->where('is_active', true)->select('count(*) as allcount')->where(\DB::raw('CONCAT_WS(" ", `name`)'), 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = Customer::where('shop_id', $shop->id)->where('is_active', true)->select('id', 'name', 'phone', 'time_created')->orderBy('name', 'asc')->where(\DB::raw('CONCAT_WS(" ", `name`, `phone`)'), 'like', '%' . $searchValue . '%')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $key => $record) {
            $id = $record->id;
            $name = "<a target='_blank' href='" . url('customer-account-stmt/'.encrypt($record->id)) . "'>".$record->name . "</a>";
            $phone = $record->phone;
            $date = date('Y-m-d H:i:s', strtotime($record->time_created));
            if (Auth::user()->can('edit-customer')) {
                $editbtn = "<a href='" . route('customers.edit', encrypt($record->id)) . "'><i class='fa fa-edit' style='color: blue;''></i> Edit</a>";
            } else {
                $editbtn = "";
            }
            if (Auth::user()->can('delete-customer')) {
                $deletebtn = "<form id='delete-form-" . $record->id . "' method='POST' action='" . route('customers.destroy', encrypt($record->id)) . "' style='display: inline;'> 
                   " . csrf_field() . "
                    <input type='hidden' name='_method' value='DELETE'> | 
                <a href='javascript:;' onclick=' return confirmDelete(" . $record->id . ")'><span class='fa fa-trash' aria-hidden='true' style='color: red'></span>Delete</a>
                </form>";
            } else {
                $deletebtn = '';
            }
            $action = $editbtn . ' ' . $deletebtn;


            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "phone" => $phone,
                'date' => $date,
                'action' => $action
            );
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function export()
    {
        $page = 'Customers';
        $title = 'My Customers';
        $title_sw = 'Wateja wangu';

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            $customers = Customer::where('shop_id', $shop->id)->select('id', 'name', 'phone', 'email', 'postal_address', 'physical_address', 'street', 'time_created')->get();
            
            return view('sales.customers.export', compact('page', 'title', 'title_sw', 'shop', 'settings', 'customers'));
        }else{
            return redirect('login');
        }
    }

    public function autoSearch(Request $request)
    {
        if ($request->ajax()) {
            $shop = Shop::find(Session::get('shop_id'));
            if (!empty($request->search_customer_key) && strlen($request->search_customer_key) >= 2) {
                $data = Customer::where('shop_id', $shop->id)->where(\DB::raw('CONCAT_WS(" ", `name`)'),'LIKE', '%'.$request->search_customer_key.'%')->select('id', 'name')->get();

                return $data;
            }
        }
    }

    public function fetchCustomer(Request $request)
    {
        $customer = \App\Models\Customer::where('id', $request->customer_id)->where('shop_id', Session::get('shop_id'))->where('is_active', true)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json($customer);
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
        $now = \Carbon\Carbon::now();
        $checkexists = Customer::where('name', $request['name'])->where('shop_id', $shop->id)->first();
        if (is_null($checkexists)) {

            $custno = 0;
            $max_no = Customer::where('shop_id', $shop->id)->latest()->first();
            if (!is_null($max_no)) {
                $custno = $max_no->cust_no+1;            
            }else{
                $custno = 1;
            }

            $customer = new Customer();
            $customer->shop_id = $shop->id;
            $customer->name = $request['name'];
            $customer->contact_person = $request['contact_person'];
            $customer->email = $request['email'];
            $customer->phone = $request['phone'];
            $customer->postal_address = $request['postal_address'];
            $customer->physical_address = $request['physical_address'];
            $customer->street = $request['street'];
            $customer->tin = $request['tin'];
            $customer->vrn = $request['vrn'];
            $customer->country_code = $request['phone_country'];
            $customer->cust_id_type = $request['cust_id_type'];
            $customer->custid = $request['custid'];
            $customer->time_created = $now;
            $customer->cust_no = $custno;
            $customer->customer_category_id = $request['customer_category_id'];
            $customer->save();

            $success = 'Customer was successfully registered';
            // Alert::success('Success!', $success);
            return redirect('customers')->with('success', $success);
        }else{
            return redirect()->back()->with('info', 'Customer with the same name already exists');
        }
    }


    public function createNew(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $now = \Carbon\Carbon::now();
        $checkexists = Customer::where('name', $request['name'])->where('shop_id', $shop->id)->first();
        if (is_null($checkexists)) {

            $custno = 0;
            $max_no = Customer::where('shop_id', $shop->id)->latest()->first();
            if (!is_null($max_no)) {
                $custno = $max_no->cust_no+1;            
            }else{
                $custno = 1;
            }

            $customer = new Customer();
            $customer->shop_id = $shop->id;
            $customer->name = $request['name'];
            $customer->contact_person = $request['contact_person'];
            $customer->email = $request['email'];
            $customer->phone = $request['phone'];
            $customer->postal_address = $request['postal_address'];
            $customer->physical_address = $request['physical_address'];
            $customer->street = $request['street'];
            $customer->tin = $request['tin'];
            $customer->vrn = $request['vrn'];
            $customer->country_code = $request['phone_country'];
            $customer->cust_id_type = $request['cust_id_type'];
            $customer->custid = $request['custid'];
            $customer->time_created = $now;
            $customer->cust_no = $custno;
            $customer->customer_category_id = $request['customer_category_id'];
            $customer->save();
            $success = 'Customer was successfully registered';

            // Alert::success('Success!', $success);
            return redirect()->back()->with('success', $success);
        }else{
            return redirect()->back()->with('info', 'Customer with the same name already exists');
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
        $shop = Shop::find(Session::get('shop_id'));
        $customer = Customer::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($customer)) {
            $settings = Setting::where('shop_id', $shop->id)->first();
            $page = 'Edit customer';
            $title = 'Edit customer info';
            $title_sw = 'Hariri Taarifa za Mteja';
            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            $categories = CustomerCategory::where('shop_id', $shop->id)->select('id', 'cat_name')->get();
            return view('sales.customers.edit', compact('page', 'title', 'title_sw', 'settings', 'customer', 'categories', 'custids'));
        }else{
            return redirect()->back()->with('error', 'Customer not Found');
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
        $customer = Customer::find(decrypt($id));
        $customer->name = $request['name'];
        $customer->contact_person = $request['contact_person'];
        $customer->phone = $request['phone'];
        $customer->email = $request['email'];
        $customer->customer_category_id = $request['customer_category_id'];
        $customer->postal_address = $request['postal_address'];
        $customer->physical_address = $request['physical_address'];
        $customer->street = $request['street'];
        $customer->tin = $request['tin'];
        $customer->vrn = $request['vrn'];
        $customer->country_code = $request['phone_country'];
        $customer->cust_id_type = $request['cust_id_type'];
        $customer->custid = $request['custid'];
        $customer->check_last_sale = $request['check_last_sale'];
        $customer->payment_reference = $request['payment_reference'];
        if (Auth::user()->can('activate-customer')) {
            $customer->is_active = $request['is_active'];
            $customer->default_due_days = $request['default_due_days'];
            $customer->due_amount_limit = $request['due_amount_limit'];
        }
        $customer->save();

        $success = 'Customer info was updated successfully';

        return redirect('customers')->with('success', $success);
    }

    public function activateCustomer($id)
    {
        $customer = Customer::find(decrypt($id));
        $customer->is_active = true;
        $customer->save();

        return redirect('customers')->with('success', 'Customer activated successfully');
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
        $customer = Customer::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (!is_null($customer)) {
            $sales = AnSale::where('customer_id', $customer->id)->count();
            if ($sales > 0) {
                $page = 'Customer Delete Confirm';
                $title = 'Customer Delete Confirm';
                $info = 'Customer has '.$sales.' Invoice(s) associated with. Click button below to confirm if you want to delete. This action will permanently delete this customer with all details related';
                return view('sales.customers.confirm', compact('page', 'title', 'customer', 'info'));
            }else{  
               return $this->deleteCustomerWithData(encrypt($customer->id));
            }
        }else{
            return redirect()->back()->with('error', 'Customer not Found');
        }
    }

    public function deleteCustomerWithData($id)
    {
        $customer = Customer::find(decrypt($id));
        if (!is_null($customer)) {
            $sales = AnSale::where('customer_id', $customer->id)->get();
            foreach ($sales as $key => $sale) {
                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                foreach ($payments as $key => $pay) {
                    $pay->delete();
                }

                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                foreach ($items as $key => $item) {
                    $item->delete();
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                foreach ($servitems as $key => $value) {
                    $value->delete();
                }

                $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();
                if (!is_null($dsale)) {
                    $dsale->delete();
                }

                $return = SaleReturn::where('an_sale_id', $sale->id)->first();
                if (!is_null($return)) {
                    $ritems = SaleReturnItem::where('sale_return_id', $return->id)->get();
                    foreach ($ritems as $key => $value) {
                        $value->delete();
                    }

                    $return->delete();
                }

                $sale->delete();
            }

            $transactions = CustomerTransaction::where('customer_id', $customer->id)->get();
            foreach ($transactions as $key => $trans) {
                $stmts = AccountStatement::where('customer_transaction_id', $trans->id)->get();
                foreach ($stmts as $key => $value) {
                    $value->delete();
                }

                $trans->delete();
            }

            // $contracts = Contract::where('customer_id', $customer->id)->get();
            // foreach ($contracts as $key => $contract) {
            //     $cservices = ContractService::where('contract_id', $contract->id)->get();
            //     foreach ($cservices as $key => $value) {
            //         $value->delete();
            //     }

            //     $deposits = DailyDeposit::where('contract_id', $contract->id)->get();
            //     foreach ($deposits as $key => $value) {
            //         $value->delete();
            //     }
            //     $contract->delete();
            // }

            $triplogs = TripLog::where('customer_id', $customer->id)->get();
            foreach ($triplogs as $key => $value) {
                $value->delete();
            }

            $proinvoices = ProInvoice::where('customer_id', $customer->id)->get();
            foreach ($proinvoices as $key => $invoice) {
                $invitems = InvoiceItem::where('pro_invoice_id', $invoice->id)->get();
                foreach ($invitems as $key => $value) {
                    $value->delete();
                }

                // $invservitems = InvoiceServitem::where('pro_invoice_id', $invoice->id)->get();
                // foreach ($invservitems as $key => $value) {
                //     $value->delete();
                // }
            }

            $saletemps = SaleTemp::where('customer_id', $customer->id)->get();
            foreach ($saletemps as $key => $value) {
                $prodtemps = SaleItemTemp::where('sale_temp_id', $value->id)->get();
                foreach ($prodtemps as $key => $item) {
                    $item->delete();
                }

                $servtemps = ServiceItemTemp::where('sale_temp_id', $value->id)->get();
                foreach ($servtemps as $key => $item) {
                    $item->delete();
                }

                $value->delete();
            }

            // $garanters = Garantor::where('customer_id', $customer->id)->get();
            // foreach ($garanters as $key => $value) {
            //     $value->delete();
            // }

            $customer->delete();

            return redirect('customers')->with('success', 'Customer Details deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Customer not Found');
        }
    }

    public function deleteMultiple(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(Session::get('user_id'));
        foreach ($request->input('ids') as $key => $id) {
            $customer = Customer::where('id', $id)->where('shop_id', $shop->id)->first();
            if (!is_null($customer)) {
                $sales = AnSale::where('customer_id', $customer->id)->count();
                if ($sales > 0) {
                    $info = 'Customer associated with sales cannot be deleted.';
                    Log::info($info);
                }else{  
                    $contracts = Contract::where('customer_id', $customer->id)->get();
                    foreach ($contracts as $key => $value) {
                        $value->delete();
                    }
                    $bookings = Booking::where('customer_id', $customer->id)->get();
                    foreach ($bookings as $key => $value) {
                        $value->delete();
                    }
                    $customer->delete();
                }
            }
        }
        $success = 'Customer was successfully removed from your customer list';
        return redirect('customers')->with('success', $success);      
    }

    public function download()
    {
        return response()->download(public_path('sample-customers.xlsx'));
    }


    public function import(Request $request) 
    {
         $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) 
        {
            return \Redirect::to('customers')->withErrors($validator);
        }else{
            Excel::import(new CustomerImport, request()->file('file'));
            return redirect('customers')->with('success', 'Customers were imported successfully!');
        }
    }

}
