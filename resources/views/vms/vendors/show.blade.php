@extends('layouts.vms')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
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
            ca.style.display = 'none';
            b.style.display = 'none';
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
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('del-acc-pv/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteTrans(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('del-supp-trans/')}}/"+id;
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
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="dashform row g-3"  action="{{ url('vendor-account-stmt/'.encrypt($vendor->id))}}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{$vendor->id}}">
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-12">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
                <!-- /.form group -->
            </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="dashform row g-3"  action="{{ url('vendor-account-stmt/'.encrypt($vendor->id))}}" method="POST">
                @csrf
                <div class="col-sm-12 text-right">
                    <a href="#"  class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-money"></i> {{trans('navmenu.add_payment')}}</a>
                    <a href="#"  class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#smsModal" data-backdrop="static" data-keyboard="false" style="margin-right: 5px;"><i class="fa fa-send"></i> {{trans('navmenu.send_sms')}}</a>
                    @if(is_null($obal))
                    <a href="#"  class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#obModal" data-backdrop="static" data-keyboard="false" style="margin-right: 5px;"><i class="fa fa-money"></i> {{trans('navmenu.opening_balance')}}</a>
                    @endif
                    <a href="#"  class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#adjustModal" data-backdrop="static" data-keyboard="false" style="margin-right: 5px;"><i class="fa fa-edit"></i> {{trans('navmenu.update_adjustment')}}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- title row -->
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card radius-10 ">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1-0" data-bs-toggle="tab">{{trans('navmenu.print_preview')}}</a></li>
                        <li class="nav-item"><a  class="nav-link" href="#tab_1-1" data-bs-toggle="tab">{{trans('navmenu.creditor_account_stmt')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2-2" data-bs-toggle="tab">{{trans('navmenu.payments')}}</a></li>
                        <li class="nav-item"><a href="#tab_3-3" class="nav-link"data-bs-toggle="tab">{{trans('navmenu.invoices')}}</a></li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane active" id="tab_1-0">
                            <div  class="row g-1 print_invoice"  id="inv-content">
                                <div class="col-md-12">
                                    <table class="table mb-1">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" style="text-align: center; background: #3498db;">
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
                                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
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
                                                <strong style="font-size: 14px;">{{$vendor->vendor_name}}</strong><br>
                                                <small style="font-size: 8px">
                                                    {{$vendor->address}}<br>Mobile : <a href="#">{{$vendor->phone}} </a> Email :<a href="#" style="text-transform: lowercase;">{{$vendor->email}}</a><br>TIN : {{$vendor->tin}} VRN : {{$vendor->vrn}}<br>
                                                </small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class ="items mt-0" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.pv_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.payments')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.cn_no')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.adjustments')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.balance')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $balance = 0; ?> 
                                            @foreach($transactions as $index => $trans)
                                            <?php $balance += ($trans->amount-($trans->payment+$trans->adjustment)); ?>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff !important;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                @if(!is_null($trans->invoice_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{ sprintf('%04d', $trans->invoice_no)}}</td>
                                                @else
                                                    @if($trans->is_ob)
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">OB</td>
                                                    @else
                                                        @if(!is_null($trans->cash_in_id))
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">CIS</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                        @endif
                                                    @endif
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($trans->amount, 2,'.', ',')}}</td>
                                                @if(!is_null($trans->receipt_no))
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{ sprintf('%05d', $trans->receipt_no)}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;"> - </td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($trans->payment, 2,'.', ',')}}</td>
                                                @if(!is_null($trans->cn_no))
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{ sprintf('%03d', $trans->cn_no)}}</td>
                                                @else
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;"> - </td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($trans->adjustment, 2,'.', ',')}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($balance, 2,'.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="invoice-footer pt-3" style="border-top: 1px solid gray;">
                                    @if($settings->show_end_note)
                                    <div class="end text-center">This is an electronic Statement and is valid without the signature and seal.</div>
                                    @endif
                                </div>
                            </div>
                            <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                        </div>
                        <div class="tab-pane" id="tab_1-1">
                            <div class="col-xs-12 table-responsive">
                                <table id="creditor-acc-stmt" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.pv_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.payments')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.cn_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.adjustments')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.balance')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $balance = 0; ?> 
                                        @foreach($transactions as $index => $trans)
                                        <?php $balance += ($trans->amount-($trans->payment+$trans->adjustment)); ?>
                                        <tr>
                                            <td style="text-align: center;">{{date('F, j Y', strtotime($trans->date))}}</td>
                                            @if(!is_null($trans->invoice_no))
                                                <td style="text-align: center;"><a href="{{url('purchase-items/'.encrypt($trans->purchase_id))}}">{{ sprintf('%04d', $trans->invoice_no)}}</a></td>
                                            @else
                                                @if($trans->is_ob)
                                                <td style="text-align: center;"><a href="#" data-bs-toggle="modal" data-bs-target="#obModal" data-backdrop="static" data-keyboard="false"> OB</a></td>
                                                @else
                                                    @if(!is_null($trans->cash_in_id))
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><a href="{{ route('cash-flows.show', encrypt($trans->cash_in_id))}}">CIS</a></td>
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                    @endif
                                                @endif
                                            @endif
                                            <td style="text-align: center;">{{number_format($trans->amount, 2,'.', ',')}}</td>
                                            @if(!is_null($trans->pv_no))
                                            <td style="text-align: center;"><a href="{{url('show-voucher' , encrypt($trans->id))}}">{{ sprintf('%05d', $trans->pv_no)}}</a></td>
                                            @else
                                            <td style="text-align: center;"> - </td>
                                            @endif
                                            <td style="text-align: center;">{{number_format($trans->payment, 2,'.', ',')}}</td>
                                            @if(!is_null($trans->cn_no))
                                            <td style="text-align: center;"><a href="{{url('show-cn/'.encrypt($trans->id))}}">{{ sprintf('%03d', $trans->cn_no)}}</a></td>
                                            @else
                                            <td style="text-align: center;"> - </td>
                                            @endif
                                            <td style="text-align: center;">{{number_format($trans->adjustment, 2,'.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($balance, 2,'.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /Tabpane -->
                        <div class="tab-pane" id="tab_3-3">
                            <div class="col-xs-12 table-responsive">
                                <table id="example2" class="table table-striped display nowrap"  style="width :100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $balance = 0; ?> 
                                        @foreach($invtrans as $index => $trans)
                                        <?php $balance += ($trans->amount-($trans->payment+$trans->adjustment)); ?>
                                        <tr>
                                            <td style="text-align: center;">{{date('F, j Y', strtotime($trans->date))}}</td>
                                            @if(!is_null($trans->invoice_no))
                                                @if($trans->invoice_no == 'OB')
                                                <td style="text-align: center;"><a href="#" data-bs-toggle="modal" data-bs-target="#obModal" data-backdrop="static" data-keyboard="false"> {{$trans->invoice_no}}</a></td>
                                                @else
                                                    @if(!is_null(App\Models\Purchase::where('shop_id', $shop->id)->where('vendor_id', $vendor->id)->where('invoice_no', $trans->invoice_no)->first()))
                                                    <td style="text-align: center;"><a href="{{url('purchase-items/'.encrypt(App\Models\Purchase::where('shop_id', $shop->id)->where('vendor_id', $vendor->id)->where('invoice_no', $trans->invoice_no)->first()->id))}}">{{ sprintf('%04d', $trans->invoice_no)}}</a></td>
                                                    @else
                                                    <td style="text-align: center;"> - </td>
                                                    @endif
                                                @endif
                                            @else
                                            <td style="text-align: center;"> - </td>
                                            @endif
                                            <td style="text-align: center;">{{number_format($trans->amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">
                                            @if(is_null(App\Models\Purchase::where('id', $trans->purchase_id)->where('shop_id', $shop->id)->where('vendor_id', $vendor->id)->first()))
                                            <a href="#" onclick="confirmDeleteTrans('<?php echo encrypt($trans->id) ?>')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                            @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /Tabpane -->
                        <div class="tab-pane" id="tab_2-2">
                            <div class="col-xs-12 table-responsive">
                                <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.pv_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total_payments')}}</th>
                                            <th style="text-align: center;">OB Payment</th>
                                            <th>CIS Payment</th>
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
                                            <td style="text-align: center;">{{ sprintf('%05d', $trans->pv_no)}}</td>
                                            <td style="text-align: center;">{{number_format($trans->payment, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($trans->trans_ob_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($trans->trans_credit_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($trans->trans_invoice_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{url('show-voucher' , encrypt($trans->id))}}"><i class="fa fa-eye"></i> {{trans('navmenu.show_voucher')}}</a> | 
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
                        <!-- /tab-pane -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /Box body -->
            </div>
            <!-- /Box -->
        </div>
        <!-- col -->
    </div>
    <!-- row -->

    <!-- Modal -->  
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_payment')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('vendor-acc-payments')}}">
                @csrf
                <div class="modal-body row">
                    <input type="hidden" name="vendor_id" value="{{$vendor->id}}">
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.invoice_to_pay')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="invoice_to_pay" id="invoice_to_pay" onchange="weg(this)" required>
                                <option value="old">{{trans('navmenu.old_first')}}</option>
                                <option value="specific">{{trans('navmenu.specific')}}</option>
                            </select>
                        </div>

                        <div class="col-md-3" id="select_invoice" style="display: none;">
                            <label class="form-label">{{trans('navmenu.invoice_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="purchase_id" id="invoice_no">
                                <option value="">{{trans('navmenu.select_invoice')}}</option>
                                @foreach($purchases as $purchase)
                                <option value="{{$purchase->purchase_id}}">{{ sprintf('%04d', $purchase->id)}}</option>
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
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
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
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-3" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
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
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div> 
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
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-3" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal -->  
<div class="modal fade" id="adjustModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.update_adjustment')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" method="POST" action="{{ url('update-vendor-adjustment')}}">
                @csrf
                <div class="modal-body row">
                    <input type="hidden" name="vendor_id" value="{{$vendor->id}}">
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.invoice_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="invoice_no" id="invoice_no" required>
                            <option value="">{{trans('navmenu.select_invoice')}}</option>
                            @foreach($purchases as $purchase)
                            <option value="{{$purchase->invoice_no}}">{{ sprintf('%04d', $purchase->id)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="adjust_date" id="adjust_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.cn_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="cn_no" required placeholder="{{trans('navmenu.hnt_cn_no')}}" class="form-control form-control-sm mb-3">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="adjustment" required placeholder="{{trans('navmenu.hnt_amount')}}" class="form-control form-control-sm mb-3">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">{{trans('navmenu.reason')}}</label>
                        <textarea name="reason" rows="1" placeholder="{{trans('navmenu.hnt_reason')}}" class="form-control form-control-sm mb-3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="obModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.opening_balance')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" method="POST" action="{{ url('set-vendor-ob')}}">
                @csrf
                <div class="modal-body row">
                    <input type="hidden" name="vendor_id" value="{{$vendor->id}}">
                    <input type="hidden" name="currency" value="{{$defcurr}}">
                    @if(!is_null($obal))
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="open_date" id="open_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{$obal->date}}" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="{{$obal->amount}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="ob_paid" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="{{$obal->ob_paid}}" class="form-control form-control-sm mb-1">
                    </div>
                    @else
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="open_date" id="open_date" placeholder="{{trans('navmenu.pick_date')}}" value="" class="form-control form-control-sm mb-3" required>
                        </div>
                    </div>

                    <div class="form-group col-md-8">
                        <label class="form-label">{{trans('navmenu.amount')}} in {{$defcurr}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="" class="form-control form-control-sm mb-3">
                    </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <div class="col-md-12 ">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.send_sms')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" method="POST" action="{{ route('sms-notifications.store')}}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="vendor_id" value="{{$vendor->id}}">

                        <div class="col-md-6 pt-2">
                            <div class="form-group">
                                <label>Sender ID <span style="color: red;">*</span></label>
                                <select name="sender" class="form-control form-control-sm mb-1" required>
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
                        </div>
                        <div class="col-md-6 pt-2">
                            <div class="form-group">
                                <label for="userinput8">Send SMS to: {{$vendor->name}}</label>
                                <input type="text" name="phone" value="{{$vendor->contact_no}}" class="form-control form-control-sm mb-1" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <div class="form-group">
                                <label for="userinput8">Message <span style="color: red;">*</span></label>
                                <textarea name="message" rows="2" id="message" class="form-control form-control-sm mb-1" placeholder="Please Type her Your Message" required></textarea>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="col-md-12 ">
                        <button type="submit" class="btn btn btn-success btn-sm">{{trans('navmenu.btn_send')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
     <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function(){ var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var shop_name = "<?php echo $shop->name; ?>";
            var stmttable = $('#creditor-acc-stmt').DataTable({
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.creditor_account_stmt') }}_" + date,
                        title: "{{ trans('navmenu.creditor_account_stmt') }}",
                        messageTop: 'DATE : ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.creditor_account_stmt') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.creditor_account_stmt') }} \n Date : " + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                    }
                ],
            });
            stmttable.buttons().container().appendTo('#creditor-acc-stmt_wrapper .col-md-6:eq(1)');

            $('#example1').DataTable();
            $('#example2').DataTable();

        })
    </script>
@endsection
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="adjust_date"]');
                $opn = document.querySelector('[name="open_date"]');

            $opn.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
    </script>


    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo trans('navmenu.creditor_account_stmt').'_'.$reporttime; ?>";
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
                // window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
    </script>