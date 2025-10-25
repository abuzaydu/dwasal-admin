<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \GuzzleHttp\Client;
use \Carbon\Carbon;
use Session;
use Log;
use App\Models\Shop;
use App\Models\User;
use App\Models\EfdmsRegInfo;
use App\Models\EfdmsRegAckInfo;

class VFDRegisterController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'VFD Registrations';
        $title = 'VFD Registrations';
        $title_sw = 'Usajili wa VFD';

        $reginfos = EfdmsRegInfo::all();
        $shops = User::role('manager')->join('shop_user', 'shop_user.user_id', '=', 'users.id')->join('shops', 'shops.id', '=', 'shop_user.shop_id')->join('payments', 'payments.shop_id', '=', 'shops.id')->where('is_real', false)->where('is_expired', false)->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.id as id', 'shops.display_name as display_name', 'shops.created_at as created_at')->groupBy('id')->orderBy('created_at', 'desc')->get();

        return view('vfd.index', compact('page', 'title', 'title_sw', 'reginfos', 'shops'));
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
        $shop = Shop::find($request['shop_id']);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file->move(storage_path('app/public/vfd-certs'), $fileName);
            $file_path = 'vfd-certs/'.$fileName;
                
            $reginfo = new EfdmsRegInfo();
            $reginfo->shop_id = $shop->id;
            $reginfo->tin = str_replace('-', '', $request['tin']);
            $reginfo->certkey = $request['certkey'];
            $reginfo->certbase = $request['certbase'];
            $reginfo->file_path = $file_path;
            $reginfo->cert_pass = encrypt($request['cert_pass']);
            $reginfo->save();

            $this->sendRegRequest($reginfo);

            return redirect('admin/vfd-reg-infos')->with('success', 'Your request submitted successfully');
        }else{
            return redirect()->back()->with('error', 'No certificate file select. Please select your cert file');
        }
    }

    public function sendRegInfo($id)
    {
        $reginfo = EfdmsRegInfo::find(decrypt($id));

        $this->sendRegRequest($reginfo);

        return redirect('admin/vfd-reg-infos')->with('success', 'Your request submitted successfully');
    }

    public function sendRegRequest($reginfo)
    {
        $xmldoc =  "<?xml version='1.0' encoding='UTF-8'?>";
        $efdms_open = "<EFDMS>";
        $efdms_close = "</EFDMS>";
        $efdms_signatureOpen="<EFDMSSIGNATURE>";
        $efdms_signatureClose="</EFDMSSIGNATURE>";

        $payloadData = "<REGDATA><TIN>".$reginfo->tin."</TIN><CERTKEY>".$reginfo->certkey."</CERTKEY></REGDATA>";

        $cert_store = file_get_contents(Storage::path('/public/'.$reginfo->file_path));
        $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, decrypt($reginfo->cert_pass));
        
        $privateKey = $cert_info['pkey'];
        $publicKey = openssl_get_privatekey($privateKey);
        $certBase = base64_encode($reginfo->certbase);

        $regsignature = $this->sign_payload_plain($payloadData, $publicKey);

        Log::info($regsignature);

        $xmlbody = $xmldoc.$efdms_open.$payloadData.$efdms_signatureOpen.$regsignature.$efdms_signatureClose.$efdms_close;
        Log::info($xmlbody);
        $client = new Client();
        // $urlReg = 'https://smartmauzo.ovaltechtz.com/efdms-reg-ack-infos';
        $urlReg = 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq';

        $createRequest = new \GuzzleHttp\Psr7\Request(
            'POST', 
            $urlReg, 
            [
                'Content-Type' => 'Application\xml',
                'Cert-Serial' => $certBase,
                'Client' => 'WEBAPI'
            ],
            $xmlbody
        );
        
        $response = $client->send($createRequest);
        
        // Log::info($response->getBody());

        $respxmlObject = simplexml_load_string($response->getBody());
                   
        $respjson = json_encode($respxmlObject);
        $resparray = json_decode($respjson, true);

        log::info($resparray);
        
        if ($resparray['EFDMSRESP']['ACKCODE'] == 0) {
                
            $reginfo->ackcode = $resparray['EFDMSRESP']['ACKCODE'];
            $reginfo->ackmsg = $resparray['EFDMSRESP']['ACKMSG'];
            $reginfo->reg_date = Carbon::now();
            $reginfo->regid = $resparray['EFDMSRESP']['REGID'];
            $reginfo->serial = $resparray['EFDMSRESP']['SERIAL'];
            $reginfo->uin = $resparray['EFDMSRESP']['UIN'];
            $reginfo->tin = $resparray['EFDMSRESP']['TIN'];
            $reginfo->vrn = $resparray['EFDMSRESP']['VRN'];
            $reginfo->mobile = $resparray['EFDMSRESP']['MOBILE'];
            $reginfo->street = $resparray['EFDMSRESP']['STREET'];
            $reginfo->city = $resparray['EFDMSRESP']['CITY'];
            $reginfo->address = $resparray['EFDMSRESP']['ADDRESS'];
            $reginfo->country = $resparray['EFDMSRESP']['COUNTRY'];
            $reginfo->name = $resparray['EFDMSRESP']['NAME'];
            $reginfo->receiptcode = $resparray['EFDMSRESP']['RECEIPTCODE'];
            $reginfo->region = $resparray['EFDMSRESP']['REGION'];
            $reginfo->routing_key = $resparray['EFDMSRESP']['ROUTINGKEY'];
            $reginfo->gc = $resparray['EFDMSRESP']['GC'];
            $reginfo->taxoffice = $resparray['EFDMSRESP']['TAXOFFICE'];
            $reginfo->username = $resparray['EFDMSRESP']['USERNAME'];
            $reginfo->password = $resparray['EFDMSRESP']['PASSWORD'];
            $reginfo->tokenpath = $resparray['EFDMSRESP']['TOKENPATH'];
            $reginfo->taxcode = $resparray['EFDMSRESP']['TAXCODES']['CODEC'];
            $reginfo->save();


            // $urlToken = 'https://smartmauzo.ovaltechtz.com/efdms-token-req';
            $urlToken = 'https://virtual.tra.go.tz/efdmsRctApi/vfdtoken';
            
            $tokenResp = $client->request('POST', $urlToken, [
                'form_params' => [
                    'username' => $reginfo->username,
                    'password' => $reginfo->password,
                    'grant_type' => 'password',
                ]
            ]);
            
            // Log::info($requestToken->getBody());
            // $tokenResp = $client->send($requestToken);
            $result = json_decode($tokenResp->getBody(), true);
            log::info($result);
            $reginfo->access_token = $result['access_token'];
            $reginfo->token_type = $result['token_type'];
            $reginfo->expires_in = $result['expires_in'];
            $reginfo->save();
 
        }else{
            $reginfo->ackcode = $resparray['EFDMSRESP']['ACKCODE'];
            $reginfo->ackmsg = $resparray['EFDMSRESP']['ACKMSG'];
            $reginfo->save();
        }
    }

    public function sign_payload_plain($payloadData, $publicKey)
    {
        //compute signature with SHA-256
        openssl_sign($payloadData, $signature, $publicKey, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'VFD Registration';
        $title = 'VFD Registration';
        $title_sw = 'Usajili VFD';            
        $reginfo = EfdmsRegInfo::find(decrypt($id));
        return view('vfd.show', compact('page', 'title', 'title_sw', 'reginfo'));
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
