@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
    function confirmDeletePayment(id) {
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
            window.location.href="{{url('sale-payments/destroy/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeleteItem(id) {
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
            window.location.href="{{url('delete-serviceitem/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
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
            window.location.href="{{url('delete-item/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function validateform(form){
        if ($('$pay_date').val() == '') {
            return false;
        }
        return true;
    }


    function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');
        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
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
            dpm.style.display = "none";
            slip.style.display = 'none'
            chq.style.display = 'none';
            expire.style.display = "none";
            m.style.display = 'block';
        }else{
            b.style.display = 'none';
            m.style.display = 'none';
            dpm.style.display = 'none';
            slip.style.display = "none";
            chq.style.display = "none";
            expire.style.display = "none";
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
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <a href="{{ url('print-receipt/'.encrypt($sale->id))}}" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print Receipt</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <table class="table mb-0 table-striped" style="width: 100%; font-size: 13px;">
                        <tbody>
                            <?php 
                                $netsales = (($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount);
                                $netreturn = (($sale->return_amount-$sale->return_discount)+$sale->return_tax);
                                $netpayable = $netsales-$netreturn 
                            ?>
                            <tr>
                                <td>{{trans('navmenu.subtotal')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->sale_amount, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.discount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->sale_discount, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            @if($settings->is_vat_registered)
                            <tr>
                                <td>{{trans('navmenu.vat')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->tax_amount, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            @endif
                            <tr>
                                <td><b>{{trans('navmenu.total')}}</b></td> 
                                <td style="text-align: right;"><b>{{number_format($netsales, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.adjustments')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($netreturn, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td><b>Net Payable</b></td> 
                                <td style="text-align: right;"><b>{{number_format($netpayable, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.paid_amount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($sale->sale_amount_paid, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.unpaid_amount')}}</td> 
                                <td style="text-align: right;"><b>{{number_format($netpayable-$sale->sale_amount_paid, 2, '.', ',')}}</b>/=</td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.sale_type')}} </td>
                                <td style="text-align: right;"><b>{{$sale->sale_type}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.comments')}}</td>
                                <td style="text-align: right;"><b>{{$sale->comments}}</b></td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.saledate')}} </td>
                                <td style="text-align: right;"><b>{{$sale->time_created}}</b></td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>
                    @if(($netpayable-$sale->sale_amount_paid) > 0)
                    <div class="col-sm-12 text-center">
                        <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-backdrop="static" data-keyboard="false" style="margin: 5px;"><b><i class="fa fa-money"></i>{{trans('navmenu.add_amount_paid')}}</b></a>
                    </div>
                    @endif
                    @if($sale->return_amount == 0 && $sale_items->count() > 0)
                    <div class="col-sm-12 text-center">
                        <a href="{{url('create-sale-return/'.encrypt($sale->id))}}"  class="btn btn-danger btn-sm" style="margin: 5px;"><i class="fa fa-file-o"></i> {{trans('navmenu.create_a_sale_return')}}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-8 mx-auto">
            @if($message = Session::get('info'))
            <div class="alert alert-info border-0 bg-info alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-dark"><i class='fa fa-info-square'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-dark">Info Alerts</h6>
                        <div class="text-dark">{{$message}}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <h6 class="mb-0 text-uppercase">{{ trans('navmenu.sale_items') }}</h6>
                        </div>
                        <div class="ms-auto">
                            @if($shop->business_type_id == 3)
                            <button type="button" class="btn btn-warning btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#servitemModal" style="margin-left: 5px;"><i class="fa fa-cart"></i> {{trans('navmenu.add_serv_sale_item')}}</button>
                            @elseif($shop->business_type_id == 4)
                            <button type="button" class="btn btn-warning btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#servitemModal" style="margin-left: 5px;"><i class="fa fa-cart"></i> {{trans('navmenu.add_serv_sale_item')}}</button>
                            <button type="button" class="btn btn-success btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="fa fa-cart"></i> {{trans('navmenu.add_sale_item')}}</button>
                            @else
                            <button type="button" class="btn btn-success btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="fa fa-cart"></i> {{trans('navmenu.add_sale_item')}}</button>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 border rounded">
                        @if($sale_items->count() > 0)
                        <div class="table-responsive">
                            <table id="saleitems" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 13px;">
                                <thead>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.buying')}}</th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th>{{trans('navmenu.selling')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($sale_items as $index => $item)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->quantity_sold}}</td>
                                        <th>{{number_format($item->unit_cost, 2, '.', ',')}}</th>
                                        <td>{{number_format($item->buying_price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->retail_price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                        @if($settings->is_vat_registered)
                                        <td>{{number_format($item->tax_amount, 2, '.', ',')}}</td>
                                        @endif
                                        <td>
                                            @if($sale->created_at > \Carbon\Carbon::now()->subDays(180)->toDateTimeString())
                                            <a href="{{ route('sale-items.edit',encrypt($item->id)) }}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                            <a href="#" onclick="confirmDelete('<?php echo encrypt($item->id); ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            @endif
                                        </td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                        @if($serv_items->count() > 0)
                        <hr>
                        <div class="table-responsive">
                            <table id="servitems" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 13px;">
                                <thead>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.service')}}</th>
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.price')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <!-- <th>Date stored</th> -->
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($serv_items as $index => $item)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->no_of_repeatition}}</td>
                                        <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                        @if($settings->is_vat_registered)
                                        <td>{{number_format($item->tax_amount, 2, '.', ',')}}</td>
                                        @endif
                                        <!-- <td>{{$item->created_at}}</td> -->
                                        <td>

                                            @if($sale->created_at > \Carbon\Carbon::now()->subDays(360)->toDateTimeString())
                                            <a href="{{route('service-items.edit', encrypt($item->id))}}"> <i class="fa fa-edit" style="color: blue;"></i></a>
                                            <a href="#" onclick="confirmDeleteItem('<?php echo encrypt($item->id); ?>')"> <i class="fa fa-trash" style="color: red;"></i></a>
                                            @endif
                                        </td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <h6 class="mb-0 text-uppercase">{{trans('navmenu.sale_payments')}}</h6>
                        </div>
                    </div>
                    <div class="p-4 border rounded table-responsive">
                        <table id="example" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.pay_date')}}</th>
                                    <th>{{trans('navmenu.amount')}}</th>
                                    <th>{{trans('navmenu.pay_mode')}}</th>
                                    <th>{{trans('navmenu.record_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $index => $payment)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$payment->pay_date}}</td>
                                    <td>{{number_format($payment->amount, 2, '.', ',')}}</td>
                                    <td>
                                        @if($payment->pay_mode == 'Cash')
                                          @if(app()->getLocale() == 'en')
                                            {{$payment->pay_mode}}
                                          @else
                                          {{trans('navmenu.cash')}}
                                          @endif
                                        @elseif($payment->pay_mode == 'Mobile Money')
                                          @if(app()->getLocale() == 'en')
                                            {{$payment->pay_mode}}
                                          @else
                                            {{trans('navmenu.mobilemoney')}}
                                          @endif
                                        @elseif($payment->pay_mode == 'Cheque')
                                          @if(app()->getLocale() == 'en')
                                        {{$payment->pay_mode}}
                                          @else
                                            {{trans('navmenu.cheque')}}
                                          @endif
                                        @elseif($payment->pay_mode == 'Bank')
                                          @if(app()->getLocale() == 'en')
                                            {{$payment->pay_mode}}
                                          @else
                                            {{trans('navmenu.bank')}}
                                          @endif                           
                                        @endif
                                    </td>
                                    <td>{{$payment->created_at}}</td>
                                    <td>
                                        <a href="{{ url('edit-acc-payment/'.encrypt($payment->trans_id)) }}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a>
                                        <!-- <a href="#" onclick="confirmDeletePayment('<?php echo encrypt($payment->id); ?>')">
                                            <i class="fa fa-trash" style="color: red;"></i>
                                        </a> -->    
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Modal -->
    <div class="modal animated zoomIn" id="payModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_payment')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('acc-payments') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{$sale->customer_id}}">
                        <input type="hidden" name="invoice_id" value="{{$sale->id}}">
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" type="text" step="any" name="amount" value="0" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>

                        @if($settings->allow_multi_currency)
                            <div class="col-md-4">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-1" onchange="wegCurr(this)" required>
                                    @foreach($currencies as $curr)
                                    <option>{{$curr->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4" id="rate-mode-col" style="display: none;">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-1" onchange="wegRate(this)">
                                </select>
                            </div>
                            <div class="col-md-4" id="locale" style="display: none;">
                                <label class="form-label" id="locale-label"></label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" value="1" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" value="1" class="form-control form-control-sm mb-1">
                            </div>
                        @else
                        <input type="hidden" name="currency" value="{{$defcurr}}">
                        @endif
                          
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                    
                        <div class="col-sm-6" id="cashaccount">
                            <label class="form-label">Cash Account </label>
                            <select class="form-select form-select-sm mb-1" name="cash_acc_id"> 
                                @foreach($accounts->where('type', 'Cash') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="deposit_mode" style="display: none;">
                            <label class="form-label">Deposit Mode</label>
                            <select name="deposit_mode" class="form-select form-select-sm mb-3">
                                <option>Direct Deposit</option>
                                <option>Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="bankdetail" style="display: none;">
                            <label class="form-label">Bank Account </label>
                            <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Bank') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>                           
                        </div>

                        <div class="col-md-6" id="cheque" style="display: none;">
                            <label class="form-label">Cheque Number</label>
                            <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-6" id="expire" style="display: none;">
                            <label class="form-label">Expire Date</label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-3">
                            </div>
                        </div>

                        <div class="col-md-6" id="slip" style="display: none;">
                            <label class="form-label">Bank Slip Number</label>
                            <input id="name" type="text" name="slip_no" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-6" id="mobaccount" style="display: none;">
                            <label class="form-label">Mobile Money Account </label>
                            <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="servitemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_serv_sale_item')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('service-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                    <div class="col-md-6">
                        <label>{{trans('navmenu.service')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select select2" id="serv-select" name="service_id" required style="width: 100%;">
                            <option value="">Select Service</option>
                            @foreach($services as $key => $service)
                            <option value="{{$service->id}}">{{$service->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" value="1" required>
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
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.add_sale_item')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('sale-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                    <div class="col-md-12 mb-4">
                        <label>{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select select2" id="my-select" name="product_id" required style="width: 100%;">
                            <option value="">Select Product</option>
                            @foreach($products as $key => $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity_sold" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
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
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('side/assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 

    <script>
        $(function () {
            $('#example').DataTable();
            $('#saleitems').DataTable();
            $('#servitems').DataTable();
        });
    </script>
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
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
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