<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use \DB;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Customer;
use App\Models\CustomerTransaction;

class CreditNoteController extends Controller
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
    public function index()
    {
       $page = 'Credit Notes';
        $title = 'Credit Notes';
        $title_sw = 'Vidokezo vya Mkopo';

        $shop = Shop::find(Session::get('shop_id'));
        $cnotes = CreditNote::where('credit_notes.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'credit_notes.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('credit_notes.id as id', 'credit_notes.credit_note_no as credit_note_no', 'credit_notes.amount as amount', 'credit_notes.created_at as created_at', 'credit_notes.updated_at as updated_at', 'invoice_no', 'due_date', 'customers.name as name')->get();

        $customer = Customer::where('shop_id', $shop->id)->first();

        $duration = '';
        
        return view('sales.invoices.credit-notes.index', compact('page', 'title', 'title_sw', 'cnotes', 'customer', 'duration'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page = 'Create Credit Note';
        $title = 'Create Credit Note';
        $title_sw = 'Tengeneza Ujumbe wa mkopo';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $sale = AnSale::where('an_sales.id', decrypt($id))->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.phone as phone', 'customers.email as email', 'an_sales.id as id', 'invoice_no', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.time_created as time_created')->first();

        if (!is_null($sale)) {
            $date = Carbon::now()->toDayDateTimeString();

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

            return view('sales.invoices.credit-notes.create', compact('page', 'title', 'title_sw', 'sale', 'shop', 'settings', 'date', 'creditnote'));
        }else{
            return redirect('an-sales')->with('info', 'Invoice Not found');
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Invoice';
        $title = 'Credit Note';
        $title_sw = 'Ujumbe wa Mkopo';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();    

        $creditnote = CreditNote::where('credit_notes.id', decrypt($id))->where('credit_notes.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'credit_notes.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('credit_notes.id as id', 'credit_notes.credit_note_no as credit_note_no', 'credit_notes.reason as reason', 'credit_notes.amount as amount', 'credit_notes.created_at as created_at', 'credit_notes.updated_at as updated_at', 'invoice_no', 'an_sales.time_created as time_created', 'customers.name as name', 'customers.postal_address as po_address', 'customers.physical_address as ph_address', 'customers.email as email', 'customers.phone as phone', 'customers.tin as tin', 'customers.vrn as vrn', 'an_sales.currency as currency', 'an_sales.ex_rate as ex_rate')->first();

        return view('sales.invoices.credit-notes.show', compact('page', 'title', 'title_sw', 'settings', 'shop', 'settings', 'creditnote'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Credit Note';
        $title = 'Edit Credit Note';
        $title_sw = 'Hariri Ujumbe wa Mkopo';
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $settings = Setting::where('shop_id', $shop->id)->first();    

        $creditnote = CreditNote::where('credit_notes.id', decrypt($id))->where('credit_notes.shop_id', $shop->id)->join('an_sales', 'an_sales.id', '=', 'credit_notes.an_sale_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('credit_notes.id as id', 'credit_notes.credit_note_no as credit_note_no', 'credit_notes.reason as reason', 'credit_notes.amount as amount', 'invoice_no', 'due_date', 'customers.name as name', 'an_sales.time_created as time_created')->first();

        return view('sales.invoices.credit-notes.edit', compact('page', 'title', 'title_sw', 'settings', 'shop', 'settings', 'creditnote'));
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
        $creditnote = CreditNote::find($id);

        $creditnote->reason = $request['reason'];
        $creditnote->amount = $request['amount'];
        $creditnote->save();

        $sale = AnSale::find($creditnote->an_sale_id);
        $sale->return_amount = $creditnote->amount;
        $sale->save();

        $acctrans = CustomerTransaction::where('shop_id', $shop->id)->where('cn_no', $creditnote->credit_note_no)->first();
        if (!is_null($acctrans)) {
                
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $sale->customer_id;
            $acctrans->invoice_no = $sale->invoice_no;
            $acctrans->cn_no = $creditnote->credit_note_no;
            $acctrans->adjustment = $creditnote->amount;
            $acctrans->date = \Carbon\Carbon::now();
            $acctrans->save();
        }else{
            $acctrans = new CustomerTransaction();
            $acctrans->shop_id = $shop->id;
            $acctrans->user_id = $user->id;
            $acctrans->customer_id = $sale->customer_id;
            $acctrans->invoice_no = $sale->invoice_no;
            $acctrans->cn_no = $creditnote->credit_note_no;
            $acctrans->adjustment = $creditnote->amount;
            $acctrans->date = \Carbon\Carbon::now();
            $acctrans->save();
        }

        return redirect('credit-notes')->with('success', 'Credit Note was creadted successfully');
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
        $creditnote = CreditNote::find(decrypt($id));
        $an_sale_id = null;
        if (!is_null($creditnote)) {
            $sale = AnSale::find($creditnote->an_sale_id);
            if (!is_null($sale)) {
                $an_sale_id = $sale->id;
                $sale->return_amount = 0;
                $sale->save();
            }
            $trans = CustomerTransaction::where('cn_no', $creditnote->credit_note_no)->where('shop_id', $shop->id)->first();
            if (!is_null($trans)) {
                $trans->delete();
            }
            
            $creditnote->delete();
        }

        return redirect('invoices/'.encrypt($an_sale_id))->with('success', 'Credit note was successfully canceled');
    }
}
