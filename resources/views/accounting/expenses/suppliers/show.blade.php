@extends('layouts.acc')
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
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            }else{
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        }else{
            b.style.display = 'none';
            m.style.display = 'none';
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
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item">Expenses</li>
                    <li class="breadcrumb-item"><a href="{{ url('exp-suppliers')}}">Suppliers</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <form class="dashform form-horizontal"  action="{{url('expense-account-stmt' , encrypt($supplier->id))}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-12">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
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
        <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
          
            <a href="{{url('expenses')}}" class="btn btn-info btn-sm "><i class="fa fa-arrow-back"></i>{{trans('navmenu.expenses')}}</a>

            <a href="#"  class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-money"></i> {{trans('navmenu.add_payment')}}</a>
            <a href="#"  class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#smsModal" data-backdrop="static" data-keyboard="false" style="margin-right: 5px;"><i class="fa fa-send"></i> {{trans('navmenu.send_sms')}}</a>
            @if(is_null($obal))
            <a href="#"  class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#obModal" data-backdrop="static" data-keyboard="false" style="margin-right: 5px;"><i class="fa fa-money"></i> {{trans('navmenu.opening_balance')}}</a>
            @endif
            <a href="#"  class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#adjustModal" data-backdrop="static" data-keyboard="false" style="margin-right: 5px;"><i class="fa fa-edit"></i> {{trans('navmenu.update_adjustment')}}</a>
            
        </div>
    </div>

    <!-- title row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="row">
                        <ul class="nav nav-tabs nav-primary">
                            <li class="nav-item"><a class="nav-link" href="#tab_1-0" data-bs-toggle="tab">{{trans('navmenu.print_preview')}}</a></li>
                            <li class="nav-item"><a  class="nav-link active" href="#tab_1-1" data-bs-toggle="tab">{{trans('navmenu.creditor_account_stmt')}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#tab_2-2" data-bs-toggle="tab">{{trans('navmenu.payments')}}</a></li>
                            <li class="nav-item"><a href="#tab_3-3" class="nav-link"data-bs-toggle="tab">{{trans('navmenu.invoices')}}</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane " id="tab_1-0">
                                <div class="pt-3">
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF</a>
                                <a href="#" onclick="javascript:printDiv('inv-content')"  class="btn btn-info " style="margin: 5px;"><i class="fa fa-printer"></i> Print</a>
                                </div>
        
                                <div id="inv-content" class="invoice overflow-auto">
                                  <div class="clearfix invoice-header">

                                    <div class="title text-center" style="margin-bottom: 5px;"><h3>{{trans('navmenu.creditor_account_stmt')}}</h3></div>
                                    <figure>
                                      <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
                                    </figure>
                                    <div class="company-address">
                                      <h2 class="title">{{$shop->name}} <br>
                                      <small style="font-size: 12px;">{{$shop->short_desc}}</small></h2>
                                      <p style="font-size: 11px;">
                                        {{$shop->postal_address}} {{$shop->physical_address}}
                                        {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
                                        <b>E-Mail</b> : <a href="#">{{$shop->email}}</a>
                                        <b>Tel</b> : <a href="#">{{$shop->mobile}}</a>
                                        <b>Web</b> : <a href="#">{{$shop->website}}</a><br>
                                        <b>TIN</b> : {{$shop->tin}} 
                                        <b>VRN</b> : {{$shop->vrn}}
                                      </p>
                                    </div>
                                    
                                    <div class="company-contact">
                                      <p style="font-size: 12px; text-transform: uppercase;">
                                          {{trans('navmenu.supplier_name')}} : {{$supplier->name}}<br>
                                          {{trans('navmenu.supplier_id')}} : {{ sprintf('%03d', $supplier->supp_id)}}<br>
                                          TIN : {{$supplier->tin}} 
                                          VRN : {{$supplier->vrn}}<br>
                                          Email :<a href="#">{{$supplier->email}}</a>
                                          Tel : <a href="#">{{$supplier->phone}}</a>
                                        </p>
                                        <p style="font-size: 12px; text-transform: uppercase;">
                                            <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b>
                                      </p>
                                    </div>
                                  </div>

                                  <div class="invoice-content">
                                        <table class ="table table-striped display nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.amount')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.receipt_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.payments')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.cn_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.adjustments')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.balance')}}({{$settings->currency}})</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $balance = 0; ?> 
                                            @foreach($transactions as $index => $trans)
                                                <?php $balance += ($trans->amount-($trans->payment+$trans->adjustment)); ?>
                                                <tr>
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff !important;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                    @if(!is_null($trans->invoice_no))
                                                        @if($trans->invoice_no == 'OB')
                                                        <td>{{$trans->invoice_no}}</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{ sprintf('%04d', $trans->invoice_no)}}</td>
                                                        @endif
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;"> - </td>
                                                    @endif
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($trans->amount)}}</td>
                                                    @if(!is_null($trans->receipt_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{ sprintf('%05d', $trans->receipt_no)}}</td>
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;"> - </td>
                                                    @endif
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($trans->payment)}}</td>
                                                    @if(!is_null($trans->cn_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{ sprintf('%03d', $trans->cn_no)}}</td>
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;"> - </td>
                                                    @endif
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($trans->adjustment)}}</td>
                                                    <td style="text-align: center; border-bottom: 1px solid #ffffff;">{{number_format($balance)}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                      </table>
                                    
                                  </div>

                                  <div class="invoice-footer">
                                    <div class="end">This is an electronic Statement and is valid without the signature and seal.</div>
                                  </div>
                                </div>
                                
                            </div>

                            <div class="tab-pane active" id="tab_1-1">
                                
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                        </figure>
                                        @endif
                                        <h4>{{Session::get('shop_name')}}</h4>
                                        <h5 class="title">{{trans('navmenu.creditor_account_stmt')}}<br><br>
                                        {{trans('navmenu.name')}} : <b>{{$supplier->name}}</b><br><br> 
                                        <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h5>
                                    </div>
                                </div>
                                <!-- /row -->
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        <table id="creditor-acc-stmt" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.amount')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.receipt_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.payments')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.cn_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.adjustments')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.balance')}}({{$settings->currency}})</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $balance = 0; ?> 
                                                @foreach($transactions as $index => $trans)
                                                <?php $balance += ($trans->amount-($trans->payment+$trans->adjustment)); ?>
                                                <tr>
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                    @if(!is_null($trans->invoice_no))
                                                        @if($trans->invoice_no == 'OB')
                                                        <td>{{$trans->invoice_no}}</td>
                                                        @else
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%04d', $trans->invoice_no)}}</td>
                                                        @endif
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                    @endif
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->amount)}}</td>
                                                    @if(!is_null($trans->receipt_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%05d', $trans->receipt_no)}}</td>
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                    @endif
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->payment)}}</td>
                                                    @if(!is_null($trans->cn_no))
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ sprintf('%03d', $trans->cn_no)}}</td>
                                                    @else
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> - </td>
                                                    @endif
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->adjustment)}}</td>
                                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($balance)}}</td>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                                <div class="row" style="border-top: 2px solid #82B1FF;">
                                    <div class="col-xs-6">
                                        <span>{{Session::get('shop_name')}} reports</span>
                                    </div>
                                    <div class="col-xs-6">
                                        <span class="float-end">Generated on: {{$reporttime}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /Tabpane -->

                            <div class="tab-pane" id="tab_3-3">
                                
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                        </figure>
                                        @endif
                                        <h4>{{Session::get('shop_name')}}</h4>
                                        <h5 class="title">{{trans('navmenu.invoices')}}<br><br>
                                        {{trans('navmenu.name')}} : <b>{{$supplier->name}}</b><br><br> 
                                        <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h5>
                                    </div>
                                </div>
                                <!-- /row -->
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.amount')}}({{$settings->currency}})</th>
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
                                                            <td style="text-align: center;"><a href="{{url('inv-expenses/'.$trans->id)}}">{{ sprintf('%04d', $trans->invoice_no)}}</a></td>
                                                        @endif
                                                    @else
                                                    <td style="text-align: center;"> - </td>
                                                    @endif
                                                    <td style="text-align: center;">{{number_format($trans->amount)}}</td>
                                                    <td style="text-align: center;">
                                                        @if(is_null(App\Models\Expense::where('invoice_no', $trans->invoice_no)->where('shop_id', $shop->id)->where('supplier_id', $supplier->id)->first()))
                                                          
                                                        <a href="#" onclick="confirmDeleteTrans('<?php echo encrypt($trans->id) ?>')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                                <div class="row" style="border-top: 2px solid #82B1FF;">
                                    <div class="col-xs-6">
                                        <span>{{Session::get('shop_name')}} reports</span>
                                    </div>
                                    <div class="col-xs-6">
                                        <span class="float-end">Generated on: {{$reporttime}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /Tabpane -->

                            <div class="tab-pane" id="tab_2-2">
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                        </figure>
                                        @endif
                                        <h4>{{Session::get('shop_name')}}</h4>
                                        <h5 class="title">{{trans('navmenu.payments')}} <br><br>
                                        {{trans('navmenu.name')}} : <b>{{$supplier->name}}</b><br><br> 
                                        <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h5>
                                    </div>
                                </div>
                                <!-- Table row -->
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.pv_no')}}</th>
                                                    <th style="text-align: center;">{{trans('navmenu.payments')}}({{$settings->currency}})</th>
                                                    <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $total = 0; ?> 
                                            @foreach($payments as $index => $trans)
                                                <?php $total += $trans->payment; ?>
                                                <tr>
                                                    <td style="text-align: center;">{{date('d M, Y', strtotime($trans->date))}}</td>
                                                    <td style="text-align: center;">{{ sprintf('%05d', $trans->pv_no)}}</td>
                                                    <td style="text-align: center;">{{number_format($trans->payment)}}</td>
                                                    <td style="text-align: center;">
                                                        <a href="{{route('expense-payments.show' , encrypt($trans->pv_no))}}"><i class="fa fa-eye"></i> {{trans('navmenu.show_voucher')}}</a> | 
                                                        <form action="{{route('expense-payments.destroy' , encrypt($trans->pv_no))}}" method="POST" id="delete-form-{{$index}}" style="display : inline;">
                                                             <a href="#" onclick="confirmDelete('{{encrypt($trans->pv_no)}}')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                                        </form>
                                                       
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                                <div class="row" style="border-top: 2px solid #82B1FF;">
                                    <div class="col-xs-6">
                                      <span>{{Session::get('shop_name')}} reports</span>
                                    </div>
                                    <div class="col-xs-6">
                                      <span class="pull-right">Generated on: {{$reporttime}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /tab-pane -->
                        </div>
                        <!-- /.tab-content -->
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_payment')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" method="POST" action="{{ url('expense-payments/acc-payments')}}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="supplier_id" value="{{$supplier->id}}">

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.invoice_to_pay')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="invoice_to_pay" id="invoice_to_pay" onchange="weg(this)" required>
                                <option value="old">{{trans('navmenu.old_first')}}</option>
                                <option value="specific">{{trans('navmenu.specific')}}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4" id="select_invoice" style="display: none;">
                            <label class="form-label">{{trans('navmenu.invoice_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="invoice_no" id="invoice_no">
                                <option value="">{{trans('navmenu.select_invoice')}}</option>
                                @foreach($purchases as $purchase)
                                <option value="{{$purchase->invoice_no}}">{{ sprintf('%04d', $purchase->invoice_no)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>{{trans('navmenu.pay_date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control" required>
                                    
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                        
                        @if($shop->subscription_type_id ==2)
                        <div id="bankdetail" style="display: none;">
                            <div class="form-group col-md-4" id="deposit_mode" style="display: none;">
                                <label class="form-label">Deposit Mode</label>
                                <select name="deposit_mode" class="form-control">
                                    <option>Direct Deposit</option>
                                    <option>Bank Transfer</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Bank Name </label>
                                <select name="bank_name" class="form-control">
                                    @foreach($bdetails as $detail)
                                    <option>{{$detail->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">Bank Branch </label>
                                <input id="name" type="text" name="bank_branch" placeholder="Please enter Bank Branch" class="form-control">
                            </div>

                            <div class="form-group col-md-4" id="cheque" style="display: none;">
                                <label class="form-label">Cheque Number</label>
                                <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control">
                            </div>

                            <div class="form-group col-md-4" id="expire" style="display: none;">
                                <label class="form-label">Expire Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div> 
                                    <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control">
                                </div>
                            </div>

                            <div class="form-group col-md-6" id="slip" style="display: none;">
                                <label class="form-label">Credit Card/Bank Slip Number</label>
                                <input id="name" type="text" name="slip_no" placeholder="Please enter Credit Card/Bank Slip number" class="form-control">
                            </div>
                        </div>
                        <div id="mobaccount" style="display: none;">
                            <div class="form-group col-md-4">
                                <label class="form-label">Mobile Money Operator </label>
                                <select class="form-control" name="operator">
                                    <option>AirtelMoney</option>
                                    <option>EzyPesa</option>
                                    <option>M-Pesa</option>
                                    <option>TigoPesa</option>
                                    <option>HaloPesa</option>
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
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
            <form class="form" method="POST" action="{{ url('expense-payments/update-adjustment')}}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="supplier_id" value="{{$supplier->id}}">

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.invoice_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="invoice_no" id="invoice_no" required>
                                <option value="">{{trans('navmenu.select_invoice')}}</option>
                                @foreach($purchases as $purchase)
                                <option value="{{$purchase->invoice_no}}">{{ sprintf('%04d', $purchase->invoice_no)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>{{trans('navmenu.date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="adjust_date" id="adjust_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control" required>
                                    
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.cn_no')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="cn_no" required placeholder="{{trans('navmenu.hnt_cn_no')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="adjustment" required placeholder="{{trans('navmenu.hnt_amount')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-8">
                            <label>{{trans('navmenu.reason')}}</label>
                            <textarea name="reason" placeholder="{{trans('navmenu.hnt_reason')}}" class="form-control"></textarea>
                        </div>
                    </div>
            </div>
                <div class="modal-footer">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
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
            <form class="form" method="POST" action="{{ url('expense-payments/setOpeningBalance')}}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="supplier_id" value="{{$supplier->id}}">
                        @if(!is_null($obal))
                        <div class="form-group col-md-4">
                            <label>{{trans('navmenu.date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="open_date" id="open_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{$obal->date}}" class="form-control" required>
                                    
                            </div>
                        </div>

                        <div class="form-group col-md-8">
                            <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="{{$obal->amount}}" class="form-control">
                        </div>
                        
                        <div class="form-group col-md-8">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="ob_paid" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="{{$obal->ob_paid}}" class="form-control">
                        </div>
                        @else
                        <div class="form-group col-md-4">
                            <label>{{trans('navmenu.date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>                                
                                <input type="text" name="open_date" id="open_date" placeholder="{{trans('navmenu.pick_date')}}" value="" class="form-control" required>
                                    
                            </div>
                        </div>

                        <div class="form-group col-md-8">
                            <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_open_balance')}}" value="" class="form-control">
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="col-md-12 ">
                        <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
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
                        <input type="hidden" name="supplier_id" value="{{$supplier->id}}">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sender ID</label>
                                <select name="sender" class="form-control" required>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userinput8">Send SMS to: {{$supplier->name}}</label>
                                <input type="text" name="phone" value="{{$supplier->contact_no}}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="userinput8">Message</label>
                                <textarea name="message" id="message" class="form-control" placeholder="Please Type her Your Message" required></textarea>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="col-md-12 ">
                        <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_send')}}</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection


<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
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


    <script language="javascript" type="text/javascript">
        function printDiv(divID) {

            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;


            //File name for printed ducument
            document.title = "<?php echo trans('navmenu.creditor_account_stmt').'_'.$reporttime; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

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

          // New Promise-based usage:
          html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                window.open(pdf.output('bloburl'), '_blank');
            });
          
        }
    </script>