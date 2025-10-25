<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\ShopCurrency;
use App\Models\BankDetail;
use App\Models\Account;
use App\Models\Invoice;

class BankDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $bdetail = BankDetail::create([
            'shop_id' => $shop->id,
            'bank_name' => $request['bank_name'],
            'branch_name' => $request['branch_name'],
            'swift_code' => $request['swift_code'],
            'account_number' => $request['account_number'],
            'account_name' => $request['account_name']
        ]);

        return redirect()->back()->with('success', 'Your Bank details were updated successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Bank Details';
        $title = 'Edit Bank Details';
        $title_sw = 'Hariri Taarifa za Benki';
        $bankdetail = BankDetail::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));
        $currencies = ShopCurrency::where('shop_id', $shop->id)->select('code')->get();
        return view('account.banks.edit', compact('page', 'title', 'title_sw', 'bankdetail', 'shop','currencies'));
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
        $shop = Shop::find(Session('shop_id'));
        $bdetail = BankDetail::find($id);
        $bdetail->bank_name = $request['bank_name'];
        $bdetail->branch_name = $request['branch_name'];
        $bdetail->swift_code = $request['swift_code'];
        $bdetail->account_number = $request['account_number'];
        $bdetail->account_name = $request['account_name'];
        $bdetail->currency = $request['currency'];
        $bdetail->save();

        $acc = Account::where('shop_id', $shop->id)->where('account_number', $bdetail->account_number)->first();
        if (is_null($acc)) {
            $acc = new Account();
        }
        $acc->shop_id = $shop->id;
        $acc->type = 'Bank';
        $acc->bank_name = $request['bank_name'];
        $acc->branch_name = $request['branch_name'];
        $acc->swift_code = $request['swift_code'];
        $acc->account_number = $request['account_number'];
        $acc->account_name = $request['account_name'];
        $acc->save();

        return redirect()->route('shops.show', encrypt($shop->id))->with('success', 'Your Bank details were updated successfully');
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
        // $invoice = Invoice::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        // $bankdetail = BankDetail::where('id', $invoice->bank_detail_id)->first();
        $bdetail = BankDetail::find(decrypt($id));
        if (!is_null($bdetail)) {
            // if($invoice->BankDetails != null){
            $bdetail->delete();
        }
        // else{
        //     return redirect()->back()->with('error', 'You cannot delete this bank because it have transaction associated with your busness');
        // }
    // }

        return redirect()->back()->with('success', 'Your Bank details was deleted successfully');
    }
}
