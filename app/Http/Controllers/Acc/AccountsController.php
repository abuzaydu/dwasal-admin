<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\Account;
use App\Models\BankDetail;
use App\Models\AccountStatement;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Payment Accounts';
        $title = 'Payment Accounts';
        $title_sw =  'Akaunti za Malipo';

        $shop = Shop::find(Session::get('shop_id'));
        $accounts = Account::where('shop_id', $shop->id)->get();
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();

        return view('accounting.cash-flows.accounts.index', compact('page', 'title', 'title_sw', 'accounts', 'currencies', 'dfcurr'));
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
        if (!empty($request['account_number'])) {
            if (empty($request['bank_name'])) {
                return redirect()->back()->with('error', 'Please enter Bankname / Operator');
            }
        }
        $shop = Shop::find(Session::get('shop_id'));
        $acc = Account::where('shop_id', $shop->id)->where('account_number', $request['account_number'])->first();
        if (is_null($acc)) {
            $acc = new Account();
            $acc->shop_id = $shop->id;
            $acc->type = $request['type'];
            $acc->bank_name = $request['bank_name'];
            $acc->branch_name = $request['branch_name'];
            $acc->swift_code = $request['swift_code'];
            $acc->account_number = $request['account_number'];
            $acc->account_name = $request['account_name'];
            $acc->currency = $request['currency'];
            $acc->save();
            
            // if (!empty($request['account_number'])) {
            //     $bdetail = BankDetail::create([
            //         'shop_id' => $shop->id,
            //         'bank_name' => $request['bank_name'],
            //         'branch_name' => $request['branch_name'],
            //         'swift_code' => $request['swift_code'],
            //         'account_number' => $request['account_number'],
            //         'account_name' => $request['account_name']
            //     ]);
            // }

            return redirect('accounts')->with('success', 'Payment Account created successfully');
        }else{
            return redirect('accounts')->with('info', 'Payment Account already created');
        }
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
        $page = 'Edit Account';
        $title = 'Edit Account';
        $title_sw = 'Hariri Akaunti';
        $acc = Account::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $currencies = ShopCurrency::where('shop_id', $shop->id)->get();
        $dfcurr = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        return view('accounting.cash-flows.accounts.edit', compact('page', 'title', 'title_sw', 'acc', 'shop', 'currencies', 'dfcurr'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find(Session('shop_id'));
        $acc = Account::find(decrypt($id));
        $acc->bank_name = $request['bank_name'];
        $acc->branch_name = $request['branch_name'];
        $acc->swift_code = $request['swift_code'];
        $acc->account_number = $request['account_number'];
        $acc->account_name = $request['account_name'];
        $acc->currency = $request['currency'];
        $acc->save();

        // $bdetail = BankDetail::where('shop_id', $shop->id)->where('account_number', $acc->account_number)->first();
        // if (!is_null($bdetail)) {            
        //     $bdetail->shop_id = $shop->id;
        //     $bdetail->bank_name = $request['bank_name'];
        //     $bdetail->branch_name = $request['branch_name'];
        //     $bdetail->swift_code = $request['swift_code'];
        //     $bdetail->account_number = $request['account_number'];
        //     $bdetail->account_name = $request['account_name'];
        //     $bdetail->save();

        // }else{
        //     if (!empty($request['account_number'])) {
        //         $bdetail = new BankDetail();
        //         $bdetail->shop_id = $shop->id;
        //         $bdetail->bank_name = $request['bank_name'];
        //         $bdetail->branch_name = $request['branch_name'];
        //         $bdetail->swift_code = $request['swift_code'];
        //         $bdetail->account_number = $request['account_number'];
        //         $bdetail->account_name = $request['account_name'];
        //         $bdetail->save();
        //     }
        // }
        
        return redirect('accounts')->with('success', 'Account info updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $acc = Account::find(decrypt($id));
        if (!is_null($acc)) {
            $astmt = AccountStatement::where('account_id', $acc->id)->count();
            if ($astmt > 0) {
                return redirect()->back()->with('info', 'Account with transactions cannot be deleted');
            }else{
                $acc->delete();
                return redirect()->back()->with('success', 'Account deleted successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Account Not found');
        }
    }

    public function reconcile(Request $request)
    {
        
    }
}
