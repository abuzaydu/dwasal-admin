<?php

namespace App\Http\Controllers\VFD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \GuzzleHttp\Client;
use Session;
use Auth;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\EfdmsRegInfo;
use App\Models\Taxcode;
use App\Models\EfdmsZReport;
use App\Models\EfdmsRctInfo;
use App\Models\EfdmsRctInfoAck;
use App\Models\EfdmsRctItem;
use App\Models\EfdmsRctPayment;
use App\Models\EfdmsRctVatTotal;
use App\Models\Module;
use App\Models\Payment;
use App\Models\EfdmsItemTemp;

class RctInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $page = 'VFD Receipts';
        $title = 'VFD Receipts';
        $title_sw = 'Risiti za VFD';
        $shop = Shop::find(Session::get('shop_id'));
        $rctinfos = EfdmsRctInfo::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->get();
        $custids = array(
            ['id' => 1, 'name' => 'TIN'],
            ['id' => 2, 'name' => 'Driving License'],
            ['id' => 3, 'name' => 'Voters Number'],
            ['id' => 4, 'name' => 'Passport'],
            ['id' => 5, 'name' => 'NID'],
            ['id' => 6, 'name' => 'NIL'],
            ['id' => 7, 'name' => 'Meter No']
        );

        return view('vfd.receipts.index', compact('page', 'title', 'title_sw', 'rctinfos', 'custids'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Point of Sale';
        $title = 'Point of Sale';
        $title_sw = 'Sehemu ya Kuuzia';
        $module = Module::where('name', 'vfd')->first();
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $status = null;
        if (is_null($module)) {
            return redirect()->back()->with('error', 'The VFD Module is not created yet!.');
        }else{

            $payment = Payment::where('shop_id', $shop->id)->where('is_expired', 0)->where('is_for_module', true)->where('module', $module->id)->first();
            if (!is_null($payment)) {
                $rctnum = EfdmsRctInfo::where('shop_id', $shop)->whereDate('created_at', Carbon::today())->count()+1;
                $custids = array(
                    ['id' => 1, 'name' => 'TIN'],
                    ['id' => 2, 'name' => 'Driving License'],
                    ['id' => 3, 'name' => 'Voters Number'],
                    ['id' => 4, 'name' => 'Passport'],
                    ['id' => 5, 'name' => 'NID'],
                    ['id' => 6, 'name' => 'NIL'],
                    ['id' => 7, 'name' => 'Meter No']
                );

                return view('vfd.receipts.create', compact('page', 'title', 'title_sw', 'shop', 'settings', 'status', 'payment', 'rctnum', 'custids'));
            }else{
                $info = 'Dear customer you have not subscribed to this module please make payment and activate now.';
                return redirect('verify-module-payment/'.encrypt($module->id))->with('error', $info);
            }
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $now = Carbon::now();
        $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
        if (!is_null($reginfo)) {
            $zreport = EfdmsZReport::where('shop_id', $shop->id)->where('status', 'Not Submitted')->first();
            $znum = null;
            if (!is_null($zreport)) {
                $znum = $zreport->znum;
            }else{
                $lastzr_sub = EfdmsZReport::where('shop_id', $shop->id)->latest()->first();
                if (!is_null($lastzr_sub)) {
                    $znum = $lastzr_sub->znum+1;
                }else{
                    $znum = 1;
                }

                $znumber = date('Ymd', strtotime($now));
                $zreport = new EfdmsZReport();
                $zreport->shop_id = $shop->id;
                $zreport->date = $now;
                $zreport->tin = $reginfo->tin;
                $zreport->vrn = $reginfo->vrn;
                $zreport->taxoffice = $reginfo->taxoffice;
                $zreport->regid = $reginfo->regid;
                $zreport->znum = $znum;
                $zreport->znumber = $znumber;
                $zreport->efdserial = $reginfo->serial;
                $zreport->registration_date = date('Y-m-d', strtotime($reginfo->reg_date));
                $zreport->user = $reginfo->uin;
                $zreport->simimsi = "WEBAPI";
                $zreport->fwversion = '3.0';
                $zreport->fwchecksum = 'WEBAPI';
                $zreport->save();
            }
            $lastrct = EfdmsRctInfo::where('shop_id', $shop->id)->latest()->first();
            $rctnum = 1;
            if (!is_null($lastrct)) {
                $rctnum = $lastrct->rctnum+1;
            }
            $ldc = EfdmsRctInfo::where('shop_id', $shop->id)->whereDate('created_at', Carbon::today())->count();
            $lgc = EfdmsRctInfo::where('shop_id', $shop->id)->count();

            $rectvnum = $reginfo->receiptcode.''.($lgc+1);
            $rctinfo = new EfdmsRctInfo();
            $rctinfo->shop_id = $shop->id;
            $rctinfo->user_id = $user->id;
            $rctinfo->an_sale_id = null;
            $rctinfo->efdms_z_report_id = $zreport->id;
            $rctinfo->date = $now;
            $rctinfo->tin = $reginfo->tin;
            $rctinfo->regid = $reginfo->regid;
            $rctinfo->efdserial = $reginfo->serial;
            $rctinfo->custidtype = $request['cust_id_type'];
            $rctinfo->custid = $request['custid'];
            $rctinfo->custname = $request['name'];
            $rctinfo->rctnum = $rctnum;
            $rctinfo->mobilenum = $request['mobile'];
            $rctinfo->dc = $ldc+1;
            $rctinfo->gc = $ldc+1;
            $rctinfo->znum = $zreport->znumber;
            $rctinfo->rctvnum = $rectvnum;
            $rctinfo->save();

            $code_a_netamount = 0; $code_a_taxamount = 0;
            $code_b_netamount = 0; $code_b_taxamount = 0;
            $code_c_netamount = 0; $code_c_taxamount = 0;

            $items = EfdmsItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($items as $key => $item) {
                $taxcode = Taxcode::find($item->taxcode);
                $rctitem = new EfdmsRctItem();
                $rctitem->efdms_rct_info_id = $rctinfo->id;
                $rctitem->item_code = $item->item_code;
                $rctitem->desc = $item->desc;
                $rctitem->qty = $item->qty;
                $rctitem->taxcode = $item->taxcode;
                if ($item->taxcode == 1 || $item->taxcode == 2) {
                    $rctitem->amt = $item->amt*(1+($taxcode->value/100));
                    $code_a_netamount += $item->amt; $code_a_taxamount += $item->amt*($taxcode->value/100);
                }else{
                    $rctitem->amt = $item->amt;
                    $code_c_netamount += $item->amt;
                }
                $rctitem->save();
            }

            $totalamount = $code_a_netamount+$code_b_netamount+$code_c_netamount;
            $totaltax = $code_a_taxamount+$code_b_taxamount;

            $rctinfo->total_tax_excl = $totalamount;
            $rctinfo->total_tax_incl = $totalamount+$totaltax;
            $rctinfo->discount = $request['discount'];
            $rctinfo->save();

            $cashpayment = 0;
            $chequepayment = 0;
            $ccardpayment = 0;
            $emoneypayment = 0;
            $invoicepayment = 0;
            if ($request['sale_type'] == 'cash') {
                if ($request['pay_type'] == 'CASH') {
                    $cashpayment = $rctinfo->total_tax_incl;
                }elseif ($request['pay_type'] == 'CHEQUE') {
                    $chequepayment = $rctinfo->total_tax_incl;
                }elseif ($request['pay_type'] == 'CCARD') {
                    $ccardpayment = $rctinfo->total_tax_incl;
                }else{
                    $emoneypayment = $rctinfo->total_tax_incl;
                }
            }else{
                $invoicepayment = $rctinfo->total_tax_incl;
            }
            // Payment Types
            $pmttypes = array(
                ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CHEQUE',  'pmtamount' => $chequepayment],
                ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CCARD', 'pmtamount' => $ccardpayment],
                ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'CASH', 'pmtamount' => $cashpayment],
                ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'EMONEY', 'pmtamount' => $emoneypayment],
                ['efdms_rct_info_id' => $rctinfo->id, 'pmttype' => 'INVOICE', 'pmtamount' => $invoicepayment]
            );

            foreach ($pmttypes as $key => $pmt) {
                if ($pmt['pmtamount'] > 0) {
                    EfdmsRctPayment::create($pmt);    
                }
            }

            // VAT Totals
            $vattotals = array(
                ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'A',  'netamount' => $code_a_netamount, 'taxamount' => $code_a_taxamount],
                ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'B', 'netamount' => $code_b_netamount, 'taxamount' => $code_b_taxamount],
                ['efdms_rct_info_id' => $rctinfo->id, 'vatrate' => 'C', 'netamount' => $code_c_netamount, 'taxamount' => $code_c_taxamount]
            );

            foreach ($vattotals as $key => $vatt) {
                if ($vatt['netamount'] > 0) {
                    EfdmsRctVatTotal::create($vatt);
                }
            }

            $this->sendReceiptReq($rctinfo, $reginfo);
            $temps = EfdmsItemTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
            foreach ($temps as $key => $temp) {
                $temp->delete();
            }
            return redirect()->route('vfd-rct-infos.show', encrypt($rctinfo->id))->with('success', 'Receipt submitted successful');
            // return redirect()->back()->with('success', 'Receipt submitted successful');
        }else{
            return redirect()->back()->with('Sorry!. Your registration for VFD not Acknowledged yet or Something went wrong please check registration status and try again');
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
        $page = 'VFD Receipts';
        $title = 'VFD Receipt';
        $title_sw = 'Risiti ya VFD';
        $shop = Shop::find(Session::get('shop_id'));
        $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
        $vfdreceipt = EfdmsRctInfo::find(decrypt($id));
        if (!is_null($vfdreceipt)) {
            $vfdreceipt = EfdmsRctInfo::find(decrypt($id));
            // return $vfdreceipt;
            $rctitems = EfdmsRctItem::where('efdms_rct_info_id', $vfdreceipt->id)->get();
            $rctpayments = EfdmsRctPayment::where('efdms_rct_info_id', $vfdreceipt->id)->get();
            $rctvattotals = EfdmsRctVatTotal::where('efdms_rct_info_id', $vfdreceipt->id)->get();
            $pmttypes = '';
            foreach ($rctpayments as $key => $ptype) {
                if ($ptype->pmtamount > 0) {
                    $pmttypes.= $ptype->pmttype.' ';
                }    
            }

            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );
            // return $rctitems;
            return view('vfd.receipts.show', compact('page', 'title', 'title_sw', 'shop', 'reginfo', 'vfdreceipt', 'rctitems', 'rctpayments', 'rctvattotals', 'pmttypes', 'custids'));
        }else{
            return redirect()->back()->with('error', 'Something went wrong!, Receipt not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rctinfo = EfdmsRctInfo::find(decrypt($id));
        if (!is_null($rctinfo) && $rctinfo->status != 'Submitted') {
            $rctinfo->status = 'Cancelled';
            $rctinfo->save(); 
        }

        return redirect()->back()->with('success', 'Receipt Cancelled successful');
    }


    public function submitReceipt($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
        if (!is_null($reginfo)) {
            $rctinfo = EfdmsRctInfo::find(decrypt($id));
            $this->sendReceiptReq($rctinfo, $reginfo);
            return redirect()->back()->with('success', 'Receipt submitted successful');
        }
    }

    public function sendReceiptReq($rctinfo, $reginfo)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        if (!is_null($shop)) {
            $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
            if (!is_null($reginfo)) {

                $token = $reginfo->access_token;
                $routingKey = $reginfo->routing_key;
                $rctitems = EfdmsRctItem::where('efdms_rct_info_id', $rctinfo->id)->get();

                $xmldoc =  "<?xml version='1.0' encoding='UTF-8'?>";
                $efdms_open = "<EFDMS>";
                $efdms_close = "</EFDMS>";
                $efdms_signatureOpen="<EFDMSSIGNATURE>";
                $efdms_signatureClose="</EFDMSSIGNATURE>";

                $rctitemsxmlopen = '<ITEMS>';
                $rctitemsxmlclose = '</ITEMS>'; 
                $xmlitems = '';
                foreach ($rctitems as $key => $rctitem) {
                    $xmlitems.= '<ITEM><ID>'.$rctitem->item_code.'</ID><DESC>'.$rctitem->desc.'</DESC><QTY>'.$rctitem->qty.'</QTY><TAXCODE>'.$rctitem->taxcode.'</TAXCODE><AMT>'.$rctitem->amt.'</AMT></ITEM>';
                }

                $rctitemsxml = $rctitemsxmlopen.$xmlitems.$rctitemsxmlclose;

                $xmlpayments = '';
                $rctpayments = EfdmsRctPayment::where('efdms_rct_info_id', $rctinfo->id)->get();
                foreach ($rctpayments as $key => $rctp) {
                    $xmlpayments.= '<PMTTYPE>'.$rctp->pmttype.'</PMTTYPE><PMTAMOUNT>'.$rctp->pmtamount.'</PMTAMOUNT>';
                }

                $xmlvattotals = '';
                $vattotals = EfdmsRctVatTotal::where('efdms_rct_info_id', $rctinfo->id)->get();
                foreach ($vattotals as $key => $vatt) {
                    $xmlvattotals.= '<VATRATE>'.$vatt->vattotals.'</VATRATE><NETTAMOUNT>'.$vatt->netamount.'</NETTAMOUNT><TAXAMOUNT>'.$vatt->taxamount.'</TAXAMOUNT>';
                }

                $payloadData = '<RCT><DATE>'.date('Y-m-d', strtotime($rctinfo->date)).'</DATE><TIME>'.date('H:i:s', strtotime($rctinfo->date)).'</TIME><TIN>'.$rctinfo->tin.'</TIN><REGID>'.$rctinfo->regid.'</REGID><EFDSERIAL>'.$rctinfo->efdserial.'</EFDSERIAL><CUSTIDTYPE>'.$rctinfo->custidtype.'</CUSTIDTYPE><CUSTID>'.$rctinfo->custid.'</CUSTID><CUSTNAME>'.$rctinfo->custname.'</CUSTNAME><MOBILENUM>'.str_replace(" ", "", $rctinfo->mobilenum).'</MOBILENUM><RCTNUM>'.$rctinfo->rctnum.'</RCTNUM><DC>'.$rctinfo->dc.'</DC><GC>'.$rctinfo->gc.'</GC><ZNUM>'.$rctinfo->znum.'</ZNUM><RCTVNUM>'.$rctinfo->rctvnum.'</RCTVNUM>'.$rctitemsxml.'<TOTALS><TOTALTAXEXCL>'.$rctinfo->total_tax_excl.'</TOTALTAXEXCL><TOTALTAXINCL>'.$rctinfo->total_tax_incl.'</TOTALTAXINCL><DISCOUNT>'.$rctinfo->discount.'</DISCOUNT></TOTALS><PAYMENTS>'.$xmlpayments.'</PAYMENTS><VATTOTALS>'.$xmlvattotals.'</VATTOTALS></RCT>';

                $cert_store = file_get_contents(Storage::path('/public/'.$reginfo->file_path));
                $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, decrypt($reginfo->cert_pass));
                
                $privateKey = $cert_info['pkey'];
                $publicKey = openssl_get_privatekey($privateKey);
                $certBase = base64_encode($reginfo->certbase);
                // Log::info($certBase);

                $rctsignature = $this->sign_payload_plain($payloadData, $publicKey);

                // Log::info($rctsignature);

                $xmlbody = $xmldoc.$efdms_open.$payloadData.$efdms_signatureOpen.$rctsignature.$efdms_signatureClose.$efdms_close;
                Log::info($xmlbody);
                
                $client = new Client();

                // $urlReceipt = 'http://localhost/smartmauzoold/public/efdms-rct-ack-infos';
                $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmsRctInfo';

                $createRequest = new \GuzzleHttp\Psr7\Request(
                    'POST', 
                    $urlReceipt, 
                    [
                        'Content-type' => 'Application/xml',
                        'Routing-Key' => $routingKey,
                        'Cert-Serial' => $certBase,
                        'Client' => 'WEBAPI',
                        'Authorization' => 'Bearer '.$token
                    ],
                    $xmlbody
                );
                $rctinfo->status = 'Submitted';
                $rctinfo->save();

                $response = $client->send($createRequest);
            
                $respxmlObject = simplexml_load_string($response->getBody());
                $respjson = json_encode($respxmlObject);
                $respphpDataArray = json_decode($respjson, true); 

                Log::info($respphpDataArray);
                if ($respphpDataArray['RCTACK']['ACKCODE'] == 0) {
                    $rctinfo->ack_date = $respphpDataArray['RCTACK']['DATE'].' '.$respphpDataArray['RCTACK']['TIME'];
                    $rctinfo->ackcode = $respphpDataArray['RCTACK']['ACKCODE'];
                    $rctinfo->ackmsg = $respphpDataArray['RCTACK']['ACKMSG'];
                    $rctinfo->is_acknowledged = true;
                    $rctinfo->save();
                }else{
                    $rctinfo->ack_date = $respphpDataArray['RCTACK']['DATE'].' '.$respphpDataArray['RCTACK']['TIME'];
                    $rctinfo->ackcode = $respphpDataArray['RCTACK']['ACKCODE'];
                    $rctinfo->ackmsg = $respphpDataArray['RCTACK']['ACKMSG'];
                    $rctinfo->save();
                }
                return redirect()->back()->with('success', 'Receipt submitted successfully');
            }else{

            }
        }
    }

    public function sign_payload_plain($payloadData, $publicKey)
    {
        //compute signature with SHA-256
        openssl_sign($payloadData, $signature, $publicKey, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }
}
