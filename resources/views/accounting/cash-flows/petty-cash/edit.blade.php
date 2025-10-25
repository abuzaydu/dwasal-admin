@extends('layouts.acc')
@section('content')
    <script type="text/javascript">
        function detailUpdate(elem) {
            var bank = document.getElementById('bank');
            var slip = document.getElementById('slip');
            if (elem.value === 'Bank') {
                slip.style.display = 'block'
                bank.style.display = 'block';
            }else{
                slip.style.display = "none";
                bank.style.display = "none";
            }
        }
    </script>
@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body p-2">
                    <div class="p-4 border rounded">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('petty-cash.update', encrypt($petty->id)) }}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            @if(!$petty->is_approved)
                            <div class="col-md-3">
                                <label class="form-label">Amount <span  style="color: red; font-weight: bold;">*</span></label>
                                <input id="inputAmount" step="any" name="amount" value="{{$petty->amount}}" required placeholder="Enter Amount" class="form-control form-control-sm mb-3">
                            </div>
                            @else
                            <div class="col-md-3">
                                <label class="form-label">Amount <span  style="color: red; font-weight: bold;">*</span></label>
                                <input id="inputAmount" step="any" name="amount" value="{{$petty->amount}}" required placeholder="Enter Amount" class="form-control form-control-sm mb-3" readonly>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control form-control-sm mb-3" name="description" rows="1" placeholder="Enter petty Description (Optional)....">{{$petty->description}}</textarea>
                            </div>
                            @if($petty->status == 'Issued' && Auth::user()->can('confirm-petty-cash-issue'))
                            <div class="col-md-6">
                                <label class="form-label">Received from <span  style="color: red; font-weight: bold;">*</span> </label>
                                <select name="account_id" class="form-select form-select-sm mb-3" required>
                                    @foreach($accounts as $acc)
                                    @if($acc->id == $petty->account_id)
                                    <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @else
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference Number</label>
                                <input id="name" type="text" name="ref_no" value="{{$petty->ref_no}}" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                            </div>
                            @endif
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit">Save</button>
                                <a href="javascript:history.back()" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $( document ).ready(function() {
        inputamt = $("#inputAmount");
        var n = inputamt.val();
        var output = getCommaSeparatedTwoDecimalsNumber(n);
        inputamt.val(output);

        inputamt.on('focus', function(){
            var n = $(this).val();
            let output = parseFloat(n.replace(/,/g, ''));
            $(this).val(output);
        });

        inputamt.on('blur', function(){
            var n = $(this).val();
            var output = getCommaSeparatedTwoDecimalsNumber(n);
            $(this).val(output);
        });
    });

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
