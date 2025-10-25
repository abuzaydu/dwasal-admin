<?php

namespace App\Http\Controllers\VFD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use \Carbon\Carbon;
use App\Models\EfdmsRegInfo;
use App\Models\EfdmsRctInfo;
use App\Models\EfdmsZReport;


class ApiTestController extends Controller
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
        $xmlDataString = $request->getContent();
        $xmlObject = simplexml_load_string($xmlDataString);
                   
        $json = json_encode($xmlObject);
        $phpDataArray = json_decode($json, true); 
        
        $respxml = "<EFDMS><EFDMSRESP> 
             <ACKCODE>0</ACKCODE> 
             <ACKMSG>Registration Successful</ACKMSG> 
             <REGID>TZ01005517</REGID> 
             <SERIAL>".$phpDataArray['REGDATA']['CERTKEY']."</SERIAL> 
             <UIN>09VFDWEBAPI-10131758710786855010TZ100280</UIN> 
             <TIN>".$phpDataArray['REGDATA']['TIN']."</TIN> 
             <VRN>40902909R</VRN> 
             <MOBILE>+255 713 655 545</MOBILE> 
             <STREET>OBAMA ST</STREET> 
             <CITY>DAR ES SALAAM</CITY> 
             <ADDRESS>P. O. BOX 5 DAR ES SALAAM</ADDRESS> 
             <COUNTRY>TANZANIA</COUNTRY> 
             <NAME>TANZANIA ELECTRIC SUPPLY COMPANY LTD</NAME> 
             <RECEIPTCODE>T61C7J</RECEIPTCODE> 
             <REGION>Large Taxpayer</REGION> 
             <ROUTINGKEY>vfdrct</ROUTINGKEY> 
             <GC>1</GC> 
             <TAXOFFICE>Tax Office Large Taxpayer</TAXOFFICE> 
             <USERNAME>babaaaib8490pawv</USERNAME> 
             <PASSWORD>SGyG!v8qndUk26h!</PASSWORD> 
             <TOKENPATH>vfdtoken</TOKENPATH> 
             <TAXCODES> 
             <CODEA>18</CODEA> 
             <CODEB>10</CODEB> 
             <CODEC>0</CODEC> 
             <CODED>0</CODED> 
             </TAXCODES> 
             </EFDMSRESP></EFDMS>";
        return $respxml;
    }


    public function storeRctAck(Request $request)
    {
        $xmlDataString = $request->getContent();
        $xmlObject = simplexml_load_string($xmlDataString);
                   
        $json = json_encode($xmlObject);
        $phpDataArray = json_decode($json, true); 
        
        Log::info($phpDataArray);

        $rctackinfo = EfdmsRctInfo::where('rctnum', $phpDataArray['RCT']['RCTNUM'])->first();
        if (!is_null($rctackinfo)) {
            $now = Carbon::now();
            $date = date('Y-m-d', strtotime($now));
            $time = date('H:i:s', strtotime($now));
            $rctackxml = '<EFDMS><RCTACK> 
                <RCTNUM>'.$rctackinfo->rctnum.'</RCTNUM> 
                <DATE>'.$date.'</DATE> 
                <TIME>'.$time.'</TIME> 
                <ACKCODE>0</ACKCODE> 
                <ACKMSG>Success</ACKMSG> 
            </RCTACK></EFDMS>';

            return $rctackxml;
        }
    }

    public function storeZReportAck(Request $request)
    {
        $xmlDataString = $request->getContent();
        $xmlObject = simplexml_load_string($xmlDataString);
                   
        $json = json_encode($xmlObject);
        $phpDataArray = json_decode($json, true); 
        
        Log::info($phpDataArray);
        $zreport = EfdmsZReport::where('znumber', $phpDataArray['ZREPORT']['ZNUMBER'])->first();
        if (!is_null($zreport)) {
            $now = Carbon::now();
            $date = date('Y-m-d', strtotime($now));
            $time = date('H:i:s', strtotime($now));
            $zreportackxml = '<EFDMS><ZACK> 
                 <ZNUMBER>'.$zreport->znumber.'</ZNUMBER> 
                 <DATE>'.$date.'</DATE> 
                 <TIME>'.$time.'</TIME> 
                 <ACKCODE>0</ACKCODE> 
                 <ACKMSG>Success</ACKMSG> 
            </ZACK></EFDMS>';

            return $zreportackxml;
        }
    }

    public function tokenRequest(Request $request)
    {
        $tokeresp = json_encode([
            'access_token' => base64_encode($request->username.$request->password.$request->grant_type),
            'token_type' => 'baarer',
            'expires_in' => 86399
        ]);

        return $tokeresp;
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
