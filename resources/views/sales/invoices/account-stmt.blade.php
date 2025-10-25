@extends('layouts.app')
@section('page-styles')
    <!-- <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" /> -->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endsection
<script type="text/javascript">
    function weg(elem) {
        var x = document.getElementById("select_invoice");
        if(elem.value !== "old") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#invoice_no").val('');
        }
    }

    var currency = '';
    function wegCurr(elem) {
        var defc = "<?php echo $defcurr; ?>";
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

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('del-acc-payment/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmChangeDiscount() {
        Swal.fire({
          title: "{{trans('navmenu.sure_change')}} "+document.getElementById('from_date').value+" - "+document.getElementById('to_date').value,
          text: "{{trans('navmenu.will_affect')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.yes_change')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('change-discount').submit();
            Swal.fire(
              "{{trans('navmenu.changed')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteTrans(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('del-acc-inv/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                @if(is_null($obal))
                <a href="#"  class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#obModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px;"><i class="bx bxs-box"></i> {{trans('navmenu.opening_balance')}}</a>
                @endif
                @if(Auth::user()->can('create-sale-payment'))
                <a href="#"  class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px;"><i class="fa fa-money"></i> {{trans('navmenu.add_payment')}}</a>
                @endif
                <a href="#"  class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#smsModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px;"><i class="fa fa-send"></i> {{trans('navmenu.send_sms')}}</a>
                
                @if($settings->is_filling_station)
                    <a href="#"  class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#discountModal" data-bs-backdrop="static" data-bs-keyboard="false"><i class="fa fa-edit"></i> {{trans('navmenu.change_discount')}}</a>
                @endif
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="dashform row g-1" action="{{url('customer-account-stmt/'.encrypt($customer->id))}}" method="POST" id="stockform">
                        @csrf
                        <input type="hidden" name="customer_id" id="cust-id" value="{{$customer->id}}" class="form-control form-control-sm mb-1">
                        <div class="col-md-5">
                            <input id="search_customer_key" placeholder="Search customer" value="{{$customer->name}}" class="form-control form-control-sm mb-1" autocomplete="off">
                            <ul id="searchResult2"></ul>
                        </div>
                        <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                        <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                        <!-- Date and time range -->
                        <div class="form-group col-md-6">
                            <div class="input-group">
                                <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <select class="form-select form-select-sm mb-1" name="stmt_currency" onchange="this.form.submit()" style="display: inline; margin-left: 5px;">
                                @foreach($stmtcurrencies as $curr)
                                @if($stmtcurr == $curr)
                                <option selected>{{$curr}}</option>
                                @else
                                <option>{{$curr}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#stmt-pdf" role="tab" aria-selected="true"><i class='fa fa-file-pdf-o font-18 me-1'></i>{{trans('navmenu.debtor_account_stmt')}} (PDF)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#stmt-excel" role="tab" aria-selected="false"><i class='fa fa-file font-18 me-1'></i> {{trans('navmenu.debtor_account_stmt')}} (Excel)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#invoices" role="tab" aria-selected="false"><i class='fa fa-list font-18 me-1'></i> {{trans('navmenu.invoices')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab" aria-selected="false"><i class='fa fa-list-alt font-18 me-1'></i>{{trans('navmenu.payments')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="stmt-pdf" role="tabpanel">
                            <div class="row g-1 print_invoice" id="print-stmt">
                                <div class="col-md-12">
                                    <table class="table mb-1">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" style="text-align: center; background:  #2874a6;">
                                                    <h4 class="mb-0 text-uppercase" style="color: #fff;">{{trans('navmenu.debtor_account_stmt')}}</h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12 border-bottom pb-4" style="border-bottom: 1px solid black;">
                                    <table class="items mt-0">
                                        <tr>
                                            <td style="width: 50%; padding-left: 30px;">
                                                @if(!is_null($shop->logo_location))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="250" style="border: 1px solid gray;">
                                                </figure>
                                                @endif
                                            </td>
                                            <td style="width: 50%; padding-right: 20px;">
                                                <table class="items mt-0">
                                                    <tbody>
                                                        <tr>
                                                            <td>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Amounts In : {{$defcurr}}</th>
                                                        </tr>
                                                    </tbody>
                                                </table>  
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%; padding-left: 30px;">
                                                <span>From: </span><br>
                                                <strong style="font-size: 14px;">{{$shop->name}}.</strong><br>
                                                <small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>@if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                            </td>
                                            <td style="width: 50%; border: 1px solid gray;">
                                                <span>To:</span><br>
                                                <strong style="font-size: 14px;">{{$customer->name}}</strong><br>
                                                <small>
                                                    {{$customer->ph_address}}<br>
                                                    {{$customer->po_address}}<br>
                                                    Mobile : <a href="#">{{$customer->phone}} </a> Email :<a href="#" style="text-transform: lowercase;">{{$customer->email}}</a><br>
                                                    TIN : {{$customer->tin}} 
                                                    VRN : {{$customer->vrn}}<br>
                                                </small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mt-0" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                @if($settings->is_filling_station)
                                                <th style="text-align: center;">{{trans('navmenu.vehicle_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                                @endif
                                                @if($settings->show_qty_is_stmt)
                                                <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                                @endif
                                                <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.receipt_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.payments')}}</th>
                                                @if(!$settings->is_filling_station || !$settings->show_qty_in_stmt)
                                                <th style="text-align: center;">{{trans('navmenu.cn_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.adjustments')}}</th>
                                                @endif
                                                <th style="text-align: center;">{{trans('navmenu.balance')}}</th>
                                            </tr>
                                        </thead>
                                        @if($stmtcurr == $defcurr)
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><b>Bal Before {{date('d M, Y', strtotime($start_date))}}</b></td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @if($settings->is_filling_station)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @endif
                                                @if($settings->show_qty_in_stmt)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @if(!$settings->is_filling_station || !$settings->show_qty_in_stmt)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @endif
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($bal_before_start, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <?php $balance = $bal_before_start; ?> 
                                            @foreach($transactions as $index => $trans)
                                            <?php $balance += (($trans->amount+$trans->refund_amt)-($trans->payment+$trans->adjustment)); ?>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                @if(!is_null($trans->invoice_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%04d', $trans->invoice_no)}}</td>
                                                @else
                                                    @if($trans->is_ob)
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">OB</td>
                                                    @else
                                                        @if(!is_null($trans->cash_out_id))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">COC</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if($settings->is_filling_station)
                                                    @if(!is_null($trans->an_sale_id))
                                                    <?php $item = App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first(); ?>
                                                        @if(!is_null($item))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{App\Models\AnSale::find($trans->an_sale_id)->vehicle_no}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->quantity_sold}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->retail_price-$item->discount}}</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        @endif
                                                    @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                    @endif
                                                @endif
                                                @if($settings->show_qty_in_stmt)
                                                    @if(!is_null($trans->an_sale_id))
                                                    <?php $item = App\Models\ServiceSaleItem::where('an_sale_id', $trans->an_sale_id)->first(); ?>
                                                        @if(!is_null($item))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->no_of_repeatition}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->price-$item->discount}}</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        @endif
                                                    @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                    @endif
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->amount,2, '.', ',')}}</td>
                                                @if(!is_null($trans->receipt_no))
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%05d', $trans->receipt_no)}}</td>
                                                @elseif(!is_null($trans->refund_no))
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">REF-{{ sprintf('%04d', $trans->refund_no)}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                @endif
                                                @if($trans->payment > 0)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->payment,2, '.', ',')}}</td>
                                                @elseif($trans->refund_amt > 0)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> <span style="color: red;">-</span>{{number_format($trans->refund_amt,2, '.', ',')}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> 0 </td>
                                                @endif

                                                @if(!$settings->is_filling_station || !$settings->show_qty_in_stmt)
                                                @if(!is_null($trans->cn_no))
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%03d', $trans->cn_no)}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->adjustment,2, '.', ',')}}</td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($balance,2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @else
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><b>Bal Before {{date('d M, Y', strtotime($start_date))}}</b></td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @if($settings->is_filling_station)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @endif
                                                @if($settings->show_qty_in_stmt)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @if(!$settings->is_filling_station || !$settings->show_qty_in_stmt)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                @endif
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($bal_before_start, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <?php $balance = $bal_before_start; ?> 
                                            @foreach($transactions as $index => $trans)
                                            <?php $balance += (($trans->amount*$trans->ex_rate)-(($trans->payment*$trans->ex_rate)+($trans->adjustment*$trans->ex_rate))); ?>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                @if(!is_null($trans->invoice_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%04d', $trans->invoice_no)}}</td>
                                                @else
                                                    @if($trans->is_ob)
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">OB</td>
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                    @endif
                                                @endif
                                                @if($settings->is_filling_station)
                                                    @if(!is_null($trans->an_sale_id))
                                                    <?php $item = App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first(); ?>
                                                        @if(!is_null($item))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{App\Models\AnSale::find($trans->an_sale_id)->vehicle_no}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->quantity_sold}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->retail_price-$item->discount}}</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        @endif
                                                    @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                    @endif
                                                @endif
                                                @if($settings->is_filling_station)
                                                    @if(!is_null($trans->an_sale_id))
                                                    <?php $item = App\Models\ServiceSaleItem::where('an_sale_id', $trans->an_sale_id)->first(); ?>
                                                        @if(!is_null($item))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->no_of_repeatition}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->price-$item->discount}}</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        @endif
                                                    @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                                    @endif
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->amount*$trans->ex_rate,2, '.', ',')}}</td>
                                                @if(!is_null($trans->receipt_no))
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%05d', $trans->receipt_no)}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->payment*$trans->ex_rate,2, '.', ',')}}</td>

                                                @if(!$settings->is_filling_station || !$settings->show_qty_in_stmt)
                                                @if(!is_null($trans->cn_no))
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%03d', $trans->cn_no)}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->adjustment*$trans->ex_rate,2, '.', ',')}}</td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($balance,2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>

                                @if($settings->is_filling_station)
                                <div class="invoice-content p-4" style="border-top: 2px solid gray;">
                                    <h2>{{trans('navmenu.totals')}}</h2>
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th>{{trans('navmenu.product_name')}}</th>
                                                <th>{{trans('navmenu.qty')}}</th>
                                                <th>{{trans('navmenu.price')}}</th>
                                                <th>{{trans('navmenu.total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!is_null($items))
                                            @foreach($items as $item)
                                            <tr>
                                                <td style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                                <td style="border-bottom: 1px solid #e0e0e0;">{{$item->quantity}}</td>
                                                <td style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->retail_price-$item->discount, 2, '.', ',')}}</td>
                                                <td style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->price-$item->total_discount, 2,'.',',')}}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            <?php $total_amount = 0; ?>
                                            @if(!is_null($itemtotals))
                                            @foreach($itemtotals as $itemtotal)
                                            <?php $total_amount += $itemtotal->price-$itemtotal->total_discount; ?>
                                            <tr style="border-top: 2px solid gray;">
                                                <td>{{trans('navmenu.total')}}({{$itemtotal->name}})</td>
                                                <td>{{$itemtotal->quantity}}</td>
                                                <td></td>
                                                <td>{{number_format($itemtotal->price-$itemtotal->total_discount, 2,'.',',')}}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            <tr style="border-top: 2px solid gray;">
                                                <td style="text-align: right;"><b>{{trans('navmenu.total')}}</b></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: right;"><b>{{number_format($total_amount,2, '.', ',')}}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                                <div class="invoice-footer pt-3" style="border-top: 1px solid gray;">
                                    @if($settings->show_end_note)
                                    <div class="end text-center">This is an electronic Statement and is valid without the signature and seal.</div>
                                    @endif
                                </div>
                            </div>
                            <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                        </div>
                        <div class="tab-pane fade" id="stmt-excel" role="tabpanel">
                           <!-- Table row -->
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="debtor-acc-stmt" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                @if($settings->is_filling_station)
                                                <th style="text-align: center;">{{trans('navmenu.vehicle_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                                @endif
                                                <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.receipt_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.payments')}}</th> 
                                                <th style="text-align: center;">{{trans('navmenu.cn_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.adjustments')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.balance')}}</th>
                                            </tr>
                                        </thead>

                                        @if($stmtcurr == $defcurr)
                                        <tbody>
                                            <?php $balance = 0; ?> 
                                            @foreach($transactions as $index => $trans)
                                            <?php $balance += ($trans->amount-($trans->payment+$trans->adjustment)); ?>
                                            <tr>
                                                <td style="text-align: center;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                @if(!is_null($trans->invoice_no))
                                                <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($trans->an_sale_id)) }}">{{ sprintf('%04d', $trans->invoice_no)}}</a></td>
                                                @else
                                                    @if($trans->is_ob)
                                                    <td style="text-align: center;"><a href="#" data-bs-toggle="modal" data-bs-target="#obModal" data-bs-backdrop="static" data-bs-keyboard="false"> OB</a></td>
                                                    @else
                                                        @if(!is_null($trans->cash_out_id))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><a href="{{ route('cash-flows.show', encrypt($trans->cash_out_id))}}">COC</a></td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if($settings->is_filling_station)
                                                    @if(!is_null($trans->an_sale_id))
                                                    <td style="text-align: center;">{{App\Models\AnSale::find($trans->an_sale_id)->vehicle_no}}</td>
                                                    <td style="text-align: center;">{{App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first()->quantity_sold}}</td>
                                                    <td style="text-align: center;">{{App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first()->retail_price-App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first()->discount}}</td>
                                                    @else
                                                    <td style="text-align: center;">-</td>
                                                    <td style="text-align: center;">-</td>
                                                    <td style="text-align: center;">-</td>
                                                    @endif
                                                @endif
                                                <td style="text-align: center;">{{number_format($trans->amount, 2, '.', ',')}}</td>
                                                @if(!is_null($trans->receipt_no))
                                                <td style="text-align: center;"><a href="{{url('show-receipt/'.encrypt($trans->id))}}">{{ sprintf('%05d', $trans->receipt_no)}}</a></td>
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                                <td style="text-align: center;">{{number_format($trans->payment, 2,'.',',')}}</td>
                                                @if(!is_null($trans->cn_no))
                                                <td style="text-align: center;"><a href="{{ route('credit-notes.show', encrypt(App\Models\CreditNote::where('shop_id', $shop->id)->where('credit_note_no', $trans->cn_no)->first()->id)) }}">{{ sprintf('%03d', $trans->cn_no)}}</a></td>
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                                <td style="text-align: center;">{{number_format($trans->adjustment, 2,'.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($balance, 2,'.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @else
                                        <tbody>
                                            <?php $balance = 0; ?> 
                                            @foreach($transactions as $index => $trans)
                                            <?php $balance += (($trans->amount*$trans->ex_rate)-(($trans->payment*$trans->ex_rate)+($trans->adjustment*$trans->ex_rate))); ?>
                                            <tr>
                                                <td style="text-align: center;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                @if(!is_null($trans->invoice_no))
                                                    @if($trans->invoice_no == 'OB')
                                                        <td><a href="#" data-bs-toggle="modal" data-bs-target="#obModal" data-bs-backdrop="static" data-bs-keyboard="false"> {{$trans->invoice_no}}</a></td>
                                                    @elseif(strpos($trans->invoice_no,'CO') !== false)
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><a href="{{route('cash-flows.show', encrypt(str_replace('CO_', '', $trans->invoice_no)))}}">{{$trans->invoice_no}}</a></td>
                                                    @else
                                                    @if(!is_null($trans->an_sale_id))
                                                        <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($trans->an_sale_id)) }}">{{ sprintf('%04d', $trans->invoice_no)}}</a></td>
                                                    @else
                                                    <td style="text-align: center;"> - </td>
                                                    @endif
                                                @endif
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                                @if($settings->is_filling_station)
                                                    @if(!is_null($trans->an_sale_id))
                                                    <td style="text-align: center;">{{App\Models\AnSale::find($trans->an_sale_id)->vehicle_no}}</td>
                                                    <td style="text-align: center;">{{App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first()->quantity_sold}}</td>
                                                    <td style="text-align: center;">{{App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first()->retail_price-App\Models\AnSaleItem::where('an_sale_id', $trans->an_sale_id)->first()->discount}}</td>
                                                    @else
                                                    <td style="text-align: center;">-</td>
                                                    <td style="text-align: center;">-</td>
                                                    <td style="text-align: center;">-</td>
                                                    @endif
                                                @endif
                                                <td style="text-align: center;">{{number_format($trans->amount*$trans->ex_rate, 2, '.', ',')}}</td>
                                                @if(!is_null($trans->receipt_no))
                                                <td style="text-align: center;"><a href="{{url('show-receipt/'.encrypt($trans->receipt_no))}}">{{ sprintf('%05d', $trans->receipt_no)}}</a></td>
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                                <td style="text-align: center;">{{number_format($trans->payment*$trans->ex_rate, 2,'.',',')}}</td>
                                                @if(!is_null($trans->cn_no))
                                                <td style="text-align: center;"><a href="{{ route('credit-notes.show', encrypt(App\CreditNote::where('shop_id', $shop->id)->where('credit_note_no', $trans->cn_no)->first()->id)) }}">{{ sprintf('%03d', $trans->cn_no)}}</a></td>
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                                <td style="text-align: center;">{{number_format($trans->adjustment*$trans->ex_rate, 2,'.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($balance, 2,'.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="tab-pane fade" id="invoices" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $totalinv = 0; ?> 
                                    @foreach($invtrans as $index => $trans)
                                        <?php $totalinv += $trans->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                            @if(!is_null($trans->invoice_no))
                                                @if(!is_null($trans->an_sale_id))
                                                <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($trans->an_sale_id)) }}">{{ sprintf('%04d', $trans->invoice_no)}}</a></td>
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                            @else
                                                @if($trans->is_ob)
                                                <td><a href="#" data-bs-toggle="modal" data-bs-target="#obModal" data-bs-backdrop="static" data-bs-keyboard="false"> OB</a></td>
                                                @else
                                                <td style="text-align: center;"> - </td>
                                                @endif
                                            @endif
                                            <td style="text-align: center;">{{number_format($trans->amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">
                                            @if(is_null(App\Models\AnSale::where('id', $trans->an_sale_id)->where('is_deleted', false)->first()))
                                            <a href="#" onclick="confirmDeleteTrans('<?php echo encrypt($trans->an_sale_id) ?>')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                            @endif</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>Total</td>
                                            <td></td>
                                            <td>{{number_format($totalinv)}}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>                            
                        </div>
                        <div class="tab-pane fade" id="payments" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.receipt_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total_payments')}}</th>
                                            <th style="text-align: center;">OB Payment</th>
                                            <th>COC Payment</th>
                                            <th>{{trans('navmenu.invoice_payment')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; $totalsp = 0; $totalob = 0; $totalcoc = 0;?> 
                                        @foreach($payments as $index => $trans)
                                        <?php $total += $trans->payment; $totalsp += $trans->trans_invoice_amount; $totalob += $trans->trans_ob_amount; $totalcoc += $trans->trans_credit_amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                            <td style="text-align: center;">{{ sprintf('%05d', $trans->receipt_no)}}</td>
                                            <td style="text-align: center;">{{number_format($trans->payment, 2,'.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($trans->trans_ob_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($trans->trans_credit_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($trans->trans_invoice_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ url('show-receipt/'.encrypt($trans->id))}}" class="text-secondary"><i class="fa fa-detail"></i> {{trans('navmenu.show_receipt')}}</a> | 
                                                <a href="{{ url('edit-acc-payment/'.encrypt($trans->id)) }}"><i class="fa fa-edit"></i> Edit Payment</a> | 
                                                <a href="#" onclick="confirmDelete('<?php echo encrypt($trans->id) ?>')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="text-align: center;"><b>Total</b></td>
                                            <td></td>
                                            <td style="text-align: center;"><b>{{number_format($total, 2, '.', ',')}}</b></td>
                                            <td style="text-align: center;"><b>{{number_format($totalob, 2, '.', ',')}}</b></td>
                                            <td style="text-align: center;"><b>{{number_format($totalcoc, 2, '.', ',')}}</b></td>
                                            <td style="text-align: center;"><b>{{number_format($totalsp, 2, '.',',')}}</b></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_payment')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" id="new-pay-form" method="POST" action="{{ url('acc-payments') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.invoice_to_pay')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="invoice_to_pay" id="invoice_to_pay" onchange="weg(this)" required>
                                <option value="old">{{trans('navmenu.old_first')}}</option>
                                <option value="specific">{{trans('navmenu.specific')}}</option>
                            </select>
                        </div>

                        <div class="col-md-3" id="select_invoice" style="display: none;">
                            <label class="form-label">{{trans('navmenu.invoice_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="invoice_no" id="invoice_no">
                                <option value="">{{trans('navmenu.select_invoice')}}</option>
                                @foreach($invoices as $invoice)
                                <option value="{{$invoice->id}}">{{ sprintf('%04d', $invoice->inv_no)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" type="text" step="any" name="amount" value="0" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                        </div>
                        @if($settings->allow_multi_currency)
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" onchange="wegCurr(this)" required>
                                    @foreach($currencies as $curr)
                                    <option>{{$curr->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" id="rate-mode-col" style="display: none;">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-3" onchange="wegRate(this)">
                                </select>
                            </div>
                            <div class="col-md-3" id="locale" style="display: none;">
                                <label class="form-label" id="locale-label"></label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" value="1" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-3" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" value="1" class="form-control form-control-sm mb-3">
                            </div>
                        @else
                        <input type="hidden" name="currency" value="{{$defcurr}}">
                        @endif
                            
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
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
                            <label class="form-label">Bank Account </label>
                            <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Bank') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) ({{$acc->account_number}})@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
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
                                <i class="myaddon fa fa-calendar"></i>
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
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) ({{$acc->account_number}})@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-3" name="comments" rows="1" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-submit-pay">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="obModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.opening_balance')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('set-ob')}}">
                    @csrf
                    <div class="modal-body row">
                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                        <input type="hidden" name="currency" value="{{$defcurr}}">
                        @if(!is_null($obal))
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>        
                                <input type="text" name="open_date" id="open_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{$obal->date}}" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="{{$obal->amount}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="ob_paid" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="{{$obal->ob_paid}}" class="form-control form-control-sm mb-3">
                        </div>
                        @else
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="open_date" id="open_date" placeholder="{{trans('navmenu.pick_date')}}" value="" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">{{trans('navmenu.amount')}} in {{$defcurr}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="" class="form-control form-control-sm mb-3">
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


    <!-- Modal -->
    <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.change_discount')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="change-discount" class="form" method="POST" action="{{ url('change-discount')}}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                        <div class="col-md-4">
                            <label>{{trans('navmenu.from')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="from_date" id="from_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label>{{trans('navmenu.to')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="to_date" id="to_date" placeholder="{{trans('navmenu.pick_date')}}" value="" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label>{{trans('navmenu.product_name')}}</label>
                            <select class="form-control" name="product_id" required>
                                  <option value="">{{trans('navmenu.select_product')}}</option>
                                  @if(!is_null($products))
                                  @foreach($products as $product)
                                  <option value="{{$product->id}}">{{$product->name}}</option>
                                  @endforeach
                                  @endif
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.discount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="discount" required placeholder="{{trans('navmenu.hnt_new_discount')}}" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="smsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.send_sms')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form class="form" method="POST" action="{{ route('sms-notifications.store')}}">
                    @csrf
                    <div class="modal-body row">
                        <input type="hidden" name="customer_id" value="{{$customer->id}}">
                        <div class="col-md-6">
                            <label class="form-label">Sender ID</label>
                            <select name="sender" class="form-control form-control-sm mb-3" required>
                                @if(!is_null($senderids))
                                    @if($senderids->count() == 1)
                                        @foreach($senderids as $senderid)
                                        <option>{{$senderid->name}}</option>
                                        @endforeach
                                    @else
                                        <option value="">Select Sender ID</option>
                                        @foreach($senderids as $senderid)
                                        <option>{{$senderid->name}}</option>
                                        @endforeach
                                    @endif
                                @else
                                    <option value="">No Sender Id registered for this Account</option>
                                    @endif
                                </select>
                            </div>
                        <div class="col-md-6">
                            <label class="form-label">Send SMS to: {{$customer->name}}</label>
                            <input type="text" name="phone" value="{{$customer->phone}}" class="form-control form-control-sm mb-3" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control form-control-sm mb-3" placeholder="Please Type her Your Message" required></textarea>
                        </div> 
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
@section('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <!-- Datatables -->
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script> -->
    <script>
        $(function(){
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var customer = "<?php echo $customer->name; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

            var daccstmtcl = [];
            var isfill = "<?php echo $settings->is_filling_station; ?>";

            if (isfill == 1) {
                daccstmtcl = [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ];
            } else {
                daccstmtcl = [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ];
            }
            DataTable.datetime('D MMM, YYYY');
            
            var stmtable = $('#debtor-acc-stmt').DataTable({
                "scrollX": true,
                "aoColumns": daccstmtcl,
                'order': [[0, 'asc']],
                'bInfo': true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        filename: "{{ trans('navmenu.debtor_account_stmt') }}_" + date,
                        title: "{{ trans('navmenu.debtor_account_stmt') }} : " + customer,
                        messageTop: duration
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        orientation: 'landscape',
                        filename: "{{ trans('navmenu.debtor_account_stmt') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.debtor_account_stmt') }} \n {{ trans('navmenu.name') }} : " + customer + " \n"+duration
                    }
                ],
            });
            stmtable.buttons().container().appendTo('#debtor-acc-stmt_wrapper .col-md-4:eq(1)');

            $('#example').DataTable();
            $('#example2').DataTable();
        });
    </script>
@endsection
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
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
            $("#search_customer_key").val(value);
            $("#searchResult2").empty();
            $('#stockform').submit();
        }
    </script>   

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

        $("#new-pay-form").one("submit", submitFormFunction);
        function submitFormFunction(event) {
            event.preventDefault(); 
            $('#btn-submit-pay').prop("disabled",true);
            $("#new-pay-form").submit();
        }
    });

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{ asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $opn = document.querySelector('[name="open_date"]'),
                $invf = document.querySelector('[name="from_date"]'),
                $invt = document.querySelector('[name="to_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $opn.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });


            $invf.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $invt.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

        });
    </script>

    <script language="javascript" type="text/javascript">
        function printDiv(divID) {

            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;


            //File name for printed ducument
            document.title = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
          const element = document.getElementById("print-stmt");
          var filename = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
    </script>