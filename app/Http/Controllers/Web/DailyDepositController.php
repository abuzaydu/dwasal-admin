<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\ShopCurrency;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\DailyDeposit;
use App\Models\AnSale;

class DailyDepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Daily Deposits';
        $title = 'Daily Deposits';

        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $defcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        if (is_null($defcurr)) {
            return redirect('settings')->with('warning', 'Please set your Default Currency to continue');
        }
        $accounts = Account::where('shop_id', $shop->id)->get();

        $contract = null;
        $deposits = null;
        $customer = null;
        $custid = null;
        $custname = null;
        if (!empty($request['customer_id'])) {
            $customer = Customer::find($request['customer_id']);
            $custid = $customer->id;
            $custname = $customer->name;

            $contract = Contract::where('customer_id', $customer->id)->orderBy('id', 'desc')->first();
            if (!is_null($contract)) {
                $sale = AnSale::find($contract->an_sale_id);
                if (!is_null($sale)) {
                    $deposits = DailyDeposit::where('contract_id', $contract->id)->orderBy('date', 'desc')->select('date', 'amount', 'created_at')->get();

                    return view('sales.contracts.deposits.index', compact('page', 'title', 'shop', 'defcurr', 'settings', 'accounts', 'customer', 'custid', 'custname', 'contract', 'deposits'));
                }else{
                    $dumdeposits = DailyDeposit::where('contract_id', $contract->id)->get();
                    foreach ($dumdeposits as $key => $value) {
                        $value->delete();
                    }
                    return redirect()->route('contracts.edit', encrypt($contract->id))->with('warning', 'Contract not Confirmed. Please Confirm the contract first to continue with Daily Deposits');
                }
            }else{
                return redirect('daily-deposits')->with('error', 'This Rider has not Contract');
            }
        }else{
            return view('sales.contracts.deposits.index', compact('page', 'title', 'shop', 'defcurr', 'settings', 'accounts', 'customer', 'custid', 'custname', 'contract', 'deposits'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
