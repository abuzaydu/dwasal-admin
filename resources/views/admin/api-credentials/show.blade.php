@extends('layouts.adm')
<script type="text/javascript">
    function CopyToClipboard(element) {

        var doc = document, text = doc.getElementById(element), range, selection;
    
    if (doc.body.createTextRange)
    {
        range = doc.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } 
    
    else if (window.getSelection)
    {
        selection = window.getSelection();        
        range = doc.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    document.getElementById("btn").value="Copied";
}

</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>
                    <li class="breadcrumb-item"><a href="{{ url('admin/payment-auths') }}">Payment Auths</a></li>    
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 rounded">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <th>Shop/Business</th>
                                    <td>
                                        {{ $shop->name }} ({{ $shop->mobile }})
                                    </td>
                                    <th>Merchant MSISDN</th>
                                    <td>{{$payauth->merchant_msisdn}}</td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td>{{$payauth->username}}</td>
                                    <th>Password</th>
                                    <td>{{$payauth->passhint}}</td>
                                </tr>
                                <tr>
                                    <th>Account Type</th>
                                    <td>
                                         @if($acc->type == 'Bank')
                                            {{trans('navmenu.bank')}}
                                        @else
                                            {{trans('navmenu.mobilemoney')}}
                                        @endif
                                    </td>
                                    <th>Account Number</th>
                                    <td>{{$acc->account_number}}</td>
                                </tr>
                                <tr>
                                    <th>Account Name</th>
                                    <td>{{$acc->account_name}}</td>
                                    <th>Provider Name</th>
                                    <td>{{$acc->bank_name}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 rounded">
                        <label>JWT Token</label>
                        <p id="text">{{$payauth->access_token}}</p>
                        <input id="btn" onclick="CopyToClipboard('text')" type="button" value="Copy"></input>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection