@extends('layouts.app')
<script type="text/javascript">

    var currency = '';
    function wegCurr(elem) {
        var defc = "<?php echo $defcurr->code; ?>";
        var rateMode = document.getElementById('ex-rate-mode');
        var rateModeCol = document.getElementById('rate-mode-col');
        var locale = document.getElementById('locale');
        if (elem.value != defc) {
            currency = elem.value;
            var option1 = document.createElement("option");
            option1.value = 'locale';
            option1.text = "1 "+defc+" Equals ? "+currency;
            rateMode.appendChild(option1);
            var option2 = document.createElement("option");
            option2.value = 'foreign';
            option2.text = "1 "+currency+" Equals ? "+defc;
            rateMode.appendChild(option2);
            rateModeCol.style.display = 'block';
            locale.style.display = 'block';
            document.getElementById('locale-label').innerHTML = 'Rate Amount in '+currency;
        }else{
            rateModeCol.style.display = 'none';
            locale.style.display = 'none';
        }
    }

    function wegRate(exrm) {
        var locale = document.getElementById('locale');
        var foreign = document.getElementById('foreign');
        if (exrm.value == 'locale') {
            locale.style.display = 'block';
            foreign.style.display = 'none';
        }else{
            locale.style.display = 'none';
            foreign.style.display = 'block';
        }
    }

     function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');
        var ca = document.getElementById('cashaccount');
        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            ca.style.display = 'none';
            if (elem.value === 'Bank') {
                m.style.display = 'none';
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            }else{
                m.style.display = 'none';
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            ca.style.display ='none';
            dpm.style.display = "none";
            slip.style.display = 'none'
            chq.style.display = 'none';
            expire.style.display = "none";
            m.style.display = 'block';
        }else{
            ca.style.display = 'block';
            b.style.display = 'none';
            m.style.display = 'none';
            dpm.style.display = 'none';
            slip.style.display = "none";
            chq.style.display = "none";
            expire.style.display = "none";
        }
    }

    function confirmCancel(id) {
        Swal.fire({
          title: "Are you sure you want to cancel this contract",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, cancel It",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('cancel-contract') }}/"+id;
            Swal.fire(
              "cancelled",
              "cancelled",
              'success'
            )
          }
        })
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-3">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">contracts & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('contracts') }}">Contracts</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="invoice-view" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <form class="form row g-1" method="POST" action="{{ url('fetch-daily-deposits') }}" id="dd-form">
                                @csrf
                                <input type="hidden" name="customer_id" id="cust-id" value="{{$custid}}" class="form-control form-control-sm mb-1">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-sm-4">
                                    <label for="customer_id" class="form-label">Search Rider to view Last Deposits <span style="color: red;">*</span>
                                    </label></label>
                                    <input id="search_customer_key" placeholder="Search Rider" value="{{$custname}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                    <ul id="searchResult2"></ul>
                                </div>
                                @if(!is_null($contract))
                                <div class="col-sm-4">
                                    <label>Contract Status</label>
                                    <input type="text" name="status" value="{{$contract->status}}" readonly class="form-control form-control-sm mb-1">
                                </div>
                                @if(!is_null($customer))
                                <div class="col-sm-4">
                                    @if(Auth::user()->can('create-sale-payment') && $contract->status == 'Working')
                                    <a href="#"  class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px;"><i class="fa fa-money"></i> {{trans('navmenu.add_payment')}}</a>
                                    @endif
                                </div>
                                @endif
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!is_null($customer))
        <div class="col-md-6 mx-auto">
            <label class="form-label">Daily Deposits for Rider : {{$customer->name}}</label>
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 border rounded p-0">
                        <table class="mt-0" style="width: 100%;">
                            <thead>
                                <tr style="background: #0459c6; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                    <th style="text-align: right; border-left: 1px solid #fff;">Date</th>
                                    <th style="text-align: right; border-left: 1px solid #fff;">Amount ({{$defcurr->code}})</th>
                                    <th style="text-align: right; border-left: 1px solid #fff;">Pay Date Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_dp = 0; ?>
                                @if(!is_null($deposits))
                                @foreach($deposits as $key => $dep)
                                <?php
                                    $total_dp += $dep->amount;
                                ?>
                                <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                    <td style="text-align: right; border-left: 1px solid gray;">{{date('d-m-Y', strtotime($dep->date))}}</td>
                                    <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($dep->amount, 2, '.', ',')}}</td>
                                    <td style="text-align: right; border-left: 1px solid gray;">{{date('d-m-Y H:i:s', strtotime($dep->created_at))}}</td>
                                </tr>
                                @endforeach
                                @endif
                                <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                    <td class="desc"><b>{{trans('navmenu.total')}}</b></td>
                                    <td class="total" style="text-align: right; border-left: 1px solid gray;"><b>{{number_format(($total_dp), 2, '.', ',')}}</b></td>
                                    <td style="border-left: 1px solid gray;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

     <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_payment')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="row g-3" method="POST" action="{{ url('acc-payments') }}">
                    <div class="modal-body row">
                        @csrf
                        @if(!is_null($customer))
                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                        <input type="hidden" name="invoice_id">
                    
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" type="text" step="any" name="amount" value="0" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @if($settings->allow_multi_currency)
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-1" onchange="wegCurr(this)" required>
                                    @foreach($currencies as $curr)
                                    <option>{{$curr->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" id="rate-mode-col" style="display: none;">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-1" onchange="wegRate(this)">
                                </select>
                            </div>
                            <div class="col-md-3" id="locale" style="display: none;">
                                <label class="form-label" id="locale-label"></label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" value="1" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr->code}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" value="1" class="form-control form-control-sm mb-1">
                            </div>
                        @else
                        <input type="hidden" name="currency" value="{{$defcurr->code}}">
                        @endif
                            
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-3" id="cashaccount">
                            <label class="form-label">Cash Account </label>
                            <select class="form-select form-select-sm mb-1" name="cash_acc_id"> 
                                @foreach($accounts->where('type', 'Cash') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="deposit_mode" style="display: none;">
                            <label class="form-label">Deposit Mode</label>
                            <select name="deposit_mode" class="form-select form-select-sm mb-3">
                                <option>Direct Deposit</option>
                                <option>Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="bankdetail" style="display: none;">
                            <label class="form-label">Bank Name </label>
                            <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Bank') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>                         
                        </div>

                        <div class="col-md-3" id="cheque" style="display: none;">
                            <label class="form-label">Cheque Number</label>
                            <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-3" id="expire" style="display: none;">
                            <label class="form-label">Expire Date</label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon bx bx-calendar"></i>
                                <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-3">
                            </div>
                        </div>

                        <div class="col-md-3" id="slip" style="display: none;">
                            <label class="form-label">Bank Slip Number</label>
                            <input id="name" type="text" name="slip_no" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-3" id="mobaccount" style="display: none;">
                            <label class="form-label">Mobile Money Account </label>
                            <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
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

        $('#search_customer_key').on('keyup',function () {
            var query = $(this).val();
            $.ajax({
                url:"{{ url('search-customer') }}",
                type:'GET',
                data:{'search_customer_key':query},
                success:function (response) {
                    var len = response.length;
                    $("#searchResult2").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        $("#searchResult2").append("<li value='"+id+"'>"+name+"</li>");
                    }
                    // binding click event to li
                    $("#searchResult2 li").bind("click",function(){
                        setSelectedCustomer(this);
                    });
                }
            })
        });

    });

    function setSelectedCustomer(element) {
            var value = $(element).text();
            var custId = $(element).val();
            $('#cust-id').val(custId);
            $('#dd-form').submit();
        }

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>

    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{ asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
