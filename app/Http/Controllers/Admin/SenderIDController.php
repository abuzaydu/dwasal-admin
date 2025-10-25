<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SenderId;
use App\Models\SmsAccount;

class SenderIDController extends Controller
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
        $senderid = new SenderId();
        $senderid->sms_account_id = $request['sms_account_id'];
        $senderid->name = $request['name'];
        $senderid->auto_sms = $request['auto_sms'];
        $senderid->save();

        return redirect()->back()->with('success', 'SMS Account sender ID created successfully');
 
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
        $page = 'Eidt Sender ID';
        $title = 'Eidt Sender ID';
        $senderid = SenderId::find(decrypt($id));
        $sms_accounts = SmsAccount::join('shops', 'shops.id', '=', 'sms_accounts.shop_id')->select('sms_accounts.id as id', 'sms_accounts.username as username', 'sms_accounts.password as password', 'name')->get();

        return view('admin.sms-accounts.edit-id', compact('page', 'title', 'sms_accounts', 'senderid'));
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
        $senderid = SenderId::find(decrypt($id));
        $senderid->sms_account_id = $request['sms_account_id'];
        $senderid->name = $request['name'];
        $senderid->auto_sms = $request['auto_sms'];
        $senderid->save();
        return redirect('admin/sms-accounts')->with('success', 'SMS Account updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $senderid = SenderId::find(decrypt($id));
        if (!is_null($senderid)) {
            $senderid->delete();
        }
        return redirect('admin/sms-accounts')->with('success', 'SMS Account updated successfully');
    }
}