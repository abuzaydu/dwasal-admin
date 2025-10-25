<?php

namespace App\Http\Controllers\VFD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \GuzzleHttp\Client;
use Session;
use Log;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\EfdmsRegInfo;
use App\Models\EfdmsZReport;
use App\Models\EfdmsRctInfo;
use App\Models\EfdmsRctPayment;
use App\Models\EfdmsRctVatTotal;
use App\Models\EfdmsZReportPayment;
use App\Models\EfdmsZReportVatTotal;

class ZReportController extends Controller
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
        $page = 'VFD Z-Reports';
        $title = 'VFD Z-Reports';
        $title_sw = 'VFD Z-Ripoti';
        $shop = Shop::find(Session::get('shop_id'));
        $zreports = EfdmsZReport::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->get();
    
        return view('vfd.zreports.index', compact('page', 'title', 'title_sw', 'zreports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
        $zreport = EfdmsZReport::where('shop_id', $shop->id)->where('status', 'Not Submitted')->first();
        if (!is_null($zreport)) {
            
            $token = $reginfo->access_token;
            $receipts = EfdmsRctInfo::where('shop_id', $shop->id)->where('efdms_z_report_id', $zreport->id)->get();

            //Report Totals
            $daily_total_amount = 0;
            $gross = 0;
            $corrections = 0;
            $discounts = 0;
            $surchargs = 0;
            $ticketsvoid = EfdmsRctInfo::where('efdms_z_report_id', $zreport->id)->where('status', 'Cancelled')->count();
            $ticketsvoid_total = 0;
            $titcketsfiscal = EfdmsRctInfo::where('efdms_z_report_id', $zreport->id)->where('status', 'Submitted')->count();
            $titcketsnonfiscal = EfdmsRctInfo::where('efdms_z_report_id', $zreport->id)->where('status', 'Cancelled')->count();

            // VAT Totals
            $code_a_netamount = 0; $code_a_taxamount = 0;
            $code_b_netamount = 0; $code_b_taxamount = 0;
            $code_c_netamount = 0; $code_c_taxamount = 0;

            // Report Payments
            $cashpayment = 0;
            $chequepayment = 0;
            $ccardpayment = 0;
            $emoneypayment = 0;
            $invoicepayment = 0;

            foreach ($receipts as $key => $rct) {
                $daily_total_amount += $rct->total_tax_incl;
                $discounts += $rct->discount;

                $rctpayments = EfdmsRctPayment::where('efdms_rct_info_id', $rct->id)->where('pmtamount', '>', 0)->get();
                foreach ($rctpayments as $key => $rctp) {
                    if ($rctp->pmttype == 'CASH') {
                        $cashpayment += $rctp->pmtamount;
                    }elseif ($rctp->pmttype == 'CHEQUE') {
                        $chequepayment += $rctp->pmtamount;
                    }elseif ($rctp->pmttype == 'CCARD') {
                        $ccardpayment += $rctp->pmtamount;
                    }elseif ($rctp->pmttype == 'EMONEY') {
                        $emoneypayment += $rctp->pmtamount;
                    }elseif ($rctp->pmttype == 'INVOICE') {
                        $invoicepayment += $rctp->pmtamount;
                    }
                }

                $rctvattotals = EfdmsRctVatTotal::where('efdms_rct_info_id', $rct->id)->get();
                foreach ($rctvattotals as $key => $rctvatt) {
                    if ($rctvatt->vatrate == 'A') {
                        $code_a_netamount += $rctvatt->netamount;
                        $code_a_taxamount += $rctvatt->taxamount;
                    }elseif ($rctvatt->vatrate == 'B') {
                        $code_a_netamount += $rctvatt->netamount;
                        $code_a_taxamount += $rctvatt->taxamount;
                    }elseif($rctvatt->vatrate == 'C') {
                        $code_c_netamount += $rctvatt->netamount;
                        $code_c_taxamount += $rctvatt->taxamount; 
                    }
                }
            }

            $zreport->daily_total_amount = $daily_total_amount;
            $zreport->corrections = $corrections;
            $zreport->discounts = $discounts;
            $zreport->surchargs = 0;
            $zreport->ticketsvoid = $ticketsvoid;
            $zreport->ticketsvoid_total = $ticketsvoid_total;
            $zreport->titcketsfiscal = $titcketsfiscal;
            $zreport->titcketsnonfiscal = $titcketsnonfiscal;
            $zreport->save();
            $gross = EfdmsZReport::where('shop_id', $shop->id)->sum('daily_total_amount');
            $zreport->gross = $gross;
            $zreport->save();
            $pmttypes = array(
                ['efdms_z_report_id' => $zreport->id, 'pmttype' => 'CHEQUE',  'pmtamount' => $chequepayment],
                ['efdms_z_report_id' => $zreport->id, 'pmttype' => 'CCARD', 'pmtamount' => $ccardpayment],
                ['efdms_z_report_id' => $zreport->id, 'pmttype' => 'CASH', 'pmtamount' => $cashpayment],
                ['efdms_z_report_id' => $zreport->id, 'pmttype' => 'EMONEY', 'pmtamount' => $emoneypayment],
                ['efdms_z_report_id' => $zreport->id, 'pmttype' => 'INVOICE', 'pmtamount' => $invoicepayment]
            );

            foreach ($pmttypes as $key => $pmt) {
                EfdmsZReportPayment::create($pmt);
            }

            // VAT Totals
            $vattotals = array(
                ['efdms_z_report_id' => $zreport->id, 'vatrate' => 'A',  'netamount' => $code_a_netamount, 'taxamount' => $code_a_taxamount],
                ['efdms_z_report_id' => $zreport->id, 'vatrate' => 'B', 'netamount' => $code_b_netamount, 'taxamount' => $code_b_taxamount],
                ['efdms_z_report_id' => $zreport->id, 'vatrate' => 'C', 'netamount' => $code_c_netamount, 'taxamount' => $code_c_taxamount],
                ['efdms_z_report_id' => $zreport->id, 'vatrate' => 'D', 'netamount' => 0, 'taxamount' => 0],
                ['efdms_z_report_id' => $zreport->id, 'vatrate' => 'E', 'netamount' => 0, 'taxamount' => 0]
            );

            foreach ($vattotals as $key => $vatt) {
                EfdmsZReportVatTotal::create($vatt);
            }

            $this->submitZReport($zreport, $reginfo, $token);

            return redirect('vfd-zreports')->with('success', 'Z-Report submitted successfully');
        }else{
            return redirect()->back()->with('info', 'No Pedding Z-Report');
        }
    }
    
    public function resubmitZReport($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
        $zreport = EfdmsZReport::find(decrypt($id));
        if (!is_null($zreport)) {
            
            $token = $reginfo->access_token;

            $this->submitZReport($zreport, $reginfo, $token);
            return redirect('vfd-zreports')->with('success', 'Z-Report submitted successfully');
        }else{
            return redirect()->back()->with('info', 'No Pedding Z-Report');
        }
    }

    public function submitZReport($zreport, $reginfo, $token)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $xmldoc =  "<?xml version='1.0' encoding='UTF-8'?>";
        $efdms_open = "<EFDMS>";
        $efdms_close = "</EFDMS>";
        $efdms_signatureOpen="<EFDMSSIGNATURE>";
        $efdms_signatureClose="</EFDMSSIGNATURE>";

        //VATToatals
        $codeA = EfdmsZReportVatTotal::where('efdms_z_report_id', $zreport->id)->where('vatrate', 'A')->first();
        $codeB = EfdmsZReportVatTotal::where('efdms_z_report_id', $zreport->id)->where('vatrate', 'B')->first();
        $codeC = EfdmsZReportVatTotal::where('efdms_z_report_id', $zreport->id)->where('vatrate', 'C')->first();

        $cash = EfdmsZReportPayment::where('efdms_z_report_id', $zreport->id)->where('pmttype', 'CASH')->first();
        $cheque = EfdmsZReportPayment::where('efdms_z_report_id', $zreport->id)->where('pmttype', 'CHEQUE')->first();
        $ccard = EfdmsZReportPayment::where('efdms_z_report_id', $zreport->id)->where('pmttype', 'CCARD')->first();
        $emoney = EfdmsZReportPayment::where('efdms_z_report_id', $zreport->id)->where('pmttype', 'EMONEY')->first();
        $invoice = EfdmsZReportPayment::where('efdms_z_report_id', $zreport->id)->where('pmttype', 'INVOICE')->first();

        $zrdate = date('Y-m-d', strtotime($zreport->date));
        $zrtime = date('H:i:s', strtotime($zreport->date));
        $zreportxml = '<ZREPORT><DATE>'.$zrdate.'</DATE><TIME>'.$zrtime.'</TIME><HEADER><LINE>'.$reginfo->name.'</LINE><LINE>'.$reginfo->address.'</LINE><LINE>'.$reginfo->mobile.'</LINE><LINE>'.$reginfo->city.','.$reginfo->country.'</LINE></HEADER><VRN>'.$zreport->vrn.'</VRN><TIN>'.$zreport->tin.'</TIN><TAXOFFICE>'.$zreport->taxoffice.'</TAXOFFICE><REGID>'.$zreport->regid.'</REGID><ZNUMBER>'.$zreport->znumber.'</ZNUMBER><EFDSERIAL>'.$zreport->efdserial.'</EFDSERIAL><REGISTRATIONDATE>'.$zreport->registration_date.'</REGISTRATIONDATE><USER>'.$zreport->user.'</USER><SIMIMSI>WEBAPI</SIMIMSI><TOTALS><DAILYTOTALAMOUNT>'.number_format($zreport->daily_total_amount, 2, '.', '').'</DAILYTOTALAMOUNT><GROSS>'.number_format($zreport->gross, 2, '.', '').'</GROSS><CORRECTIONS>0.00</CORRECTIONS><DISCOUNTS>'.number_format($zreport->discounts, 2, '.', '').'</DISCOUNTS><SURCHARGES>'.number_format($zreport->surchargs, 2, '.', '').'</SURCHARGES><TICKETSVOID>'.$zreport->ticketsvoid.'</TICKETSVOID><TICKETSVOIDTOTAL>'.number_format($zreport->ticketsvoid_total, 2, '.', '').'</TICKETSVOIDTOTAL><TICKETSFISCAL>'.$zreport->titcketsfiscal.'</TICKETSFISCAL><TICKETSNONFISCAL>'.$zreport->titcketsnonfiscal.'</TICKETSNONFISCAL></TOTALS><VATTOTALS><VATRATE>A-18.00</VATRATE><NETTAMOUNT>'.number_format($codeA->netamount, 2, '.', '').'</NETTAMOUNT><TAXAMOUNT>'.number_format($codeA->taxamount, 2, '.', '').'</TAXAMOUNT><VATRATE>B-10.00</VATRATE><NETTAMOUNT>'.number_format($codeB->netamount, 2, '.', '').'</NETTAMOUNT><TAXAMOUNT>'.number_format($codeB->taxamount, 2, '.', '').'</TAXAMOUNT><VATRATE>C-0.00</VATRATE><NETTAMOUNT>'.number_format($codeC->netamount, 2, '.', '').'</NETTAMOUNT><TAXAMOUNT>'.number_format($codeC->taxamount, 2, '.', '').'</TAXAMOUNT><VATRATE>D-0.00</VATRATE><NETTAMOUNT>0.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT><VATRATE>E-0.00</VATRATE><NETTAMOUNT>0.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT></VATTOTALS><PAYMENTS><PMTTYPE>CASH</PMTTYPE><PMTAMOUNT>'.number_format($cash->pmtamount, 2, '.', '').'</PMTAMOUNT><PMTTYPE>CHEQUE</PMTTYPE><PMTAMOUNT>'.number_format($cheque->pmtamount, 2, '.', '').'</PMTAMOUNT><PMTTYPE>CCARD</PMTTYPE><PMTAMOUNT>'.number_format($ccard->pmtamount, 2, '.', '').'</PMTAMOUNT><PMTTYPE>EMONEY</PMTTYPE><PMTAMOUNT>'.number_format($emoney->pmtamount, 2, '.', '').'</PMTAMOUNT><PMTTYPE>INVOICE</PMTTYPE><PMTAMOUNT>'.number_format($invoice->pmtamount, 2, '.', '').'</PMTAMOUNT></PAYMENTS><CHANGES><VATCHANGENUM>0</VATCHANGENUM><HEADCHANGENUM>0</HEADCHANGENUM></CHANGES><ERRORS></ERRORS><FWVERSION>3.0</FWVERSION><FWCHECKSUM>WEBAPI</FWCHECKSUM></ZREPORT>';

        $cert_store = file_get_contents(Storage::path('/public/'.$reginfo->file_path));
        $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, decrypt($reginfo->cert_pass));
        
        $privateKey = $cert_info['pkey'];
        $publicKey = openssl_get_privatekey($privateKey);
        $certBase = base64_encode($reginfo->certbase);

        $zreportsignature = $this->sign_payload_plain($zreportxml, $publicKey);

        $xmlbody = $xmldoc.$efdms_open.$zreportxml.$efdms_signatureOpen.$zreportsignature.$efdms_signatureClose.$efdms_close;
        Log::info($xmlbody);
        $client = new Client();
        // $urlZReport = 'http://localhost/smartmauzoold/public/efdms-zreport-ack-infos';
        $urlZReport = 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmszreport';

        $createRequest = new \GuzzleHttp\Psr7\Request(
            'POST', 
            $urlZReport, 
            [
                'Content-Type' => 'Application\xml',
                'Routing-Key' => 'vfdzreport',
                'Cert-Serial' => $certBase,
                'Client' => 'WEBAPI',
                'Authorization' => 'Bearer '.$token
            ],
            $xmlbody
        );

        $response = $client->send($createRequest);
        $zreport->status = 'Submitted';
        $zreport->save();


        $zreportackxml = $response->getBody();
        $respxmlObject = simplexml_load_string($zreportackxml);
                       
        $respjson = json_encode($respxmlObject);
        $respphpDataArray = json_decode($respjson, true); 

        Log::info($respphpDataArray);
        if ($respphpDataArray['ZACK']['ACKCODE'] == 0) {
            $zreport->ack_date = $respphpDataArray['ZACK']['DATE'].' '.$respphpDataArray['ZACK']['TIME'];
            $zreport->ackcode = $respphpDataArray['ZACK']['ACKCODE'];
            $zreport->ackmsg = $respphpDataArray['ZACK']['ACKMSG'];
            $zreport->status = 'Received';
            $zreport->save();
        }else{
            $zreport->ack_date = $respphpDataArray['ZACK']['DATE'].' '.$respphpDataArray['ZACK']['TIME'];
            $zreport->ackcode = $respphpDataArray['ZACK']['ACKCODE'];
            $zreport->ackmsg = $respphpDataArray['ZACK']['ACKMSG'];
            $zreport->save();
        }

    }

    public function sign_payload_plain($payloadData, $publicKey)
    {
        //compute signature with SHA-256
        openssl_sign($payloadData, $signature, $publicKey, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Z-Reports';
        $title = 'Z-Reports';
        $title_sw = 'Risiti za Z';
        $shop = Shop::find(Session::get('shop_id'));
        $reginfo = EfdmsRegInfo::where('shop_id', $shop->id)->first();
        $zreport = EfdmsZReport::find(decrypt($id));
        if (!is_null($zreport)) {
            $zreportpayments = EfdmsZReportPayment::where('efdms_z_report_id', $zreport->id)->where('pmtamount', '>', 0)->get();
            $zreportvattotals = EfdmsZReportVatTotal::where('efdms_z_report_id', $zreport->id)->get();

            $lastzreport = EfdmsZReport::where('shop_id', $shop->id)->latest()->skip(1)->first();
            $lastzrdate = '';
            if (!is_null($lastzreport)) {
                $lastzrdate = date('d-m-Y H:i:s', strtotime($lastzreport->date));
            }
            $firstRct = EfdmsRctInfo::where('shop_id', $shop->id)->where('efdms_z_report_id', $zreport->id)->whereDate('date', Carbon::today())->orderBy('created_at', 'asc')->first();
            // return $firstRct;
            $lastRct = EfdmsRctInfo::where('shop_id', $shop->id)->where('efdms_z_report_id', $zreport->id)->whereDate('date', Carbon::today())->latest()->first();
            return view('vfd.zreports.show', compact('page', 'title', 'title_sw', 'shop', 'reginfo', 'zreport', 'zreportpayments', 'zreportvattotals', 'lastzrdate', 'firstRct', 'lastRct'));
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
        //
    }
}
