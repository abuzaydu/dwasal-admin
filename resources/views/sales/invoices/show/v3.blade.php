@extends('layouts.app')
<script type="text/javascript">

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

    function confirmDelete() {
        Swal.fire({
            title: "Are you sure you want to cancel this Invoice",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes Cancel",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form').submit();
                Swal.fire(
                    "Cancelled",
                    "{{ trans('navmenu.cancelled') }}",
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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('an-sales') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                @if(!$sale->is_paid)
                @if(Auth::user()->can('edit-invoice'))
                <a class="btn btn-outline-primary btn-sm" style="margin-left: 2px; padding-top: 5px;" href="{{ route('an-sales.edit', encrypt($sale->id)) }}"><i class="bx bx-edit"></i>Update</a>
                <a class="btn btn-outline-warning btn-sm" style="margin-left: 2px; padding-top: 5px;" href="{{ url('create-credit-note/'.encrypt($sale->id)) }}"><i class="bx bx-pencil"></i>Credit Note</a>
                @endif
                @if(Auth::user()->can('cancel-invoice'))
                <a class="btn btn-outline-danger btn-sm" href="#" title="Delete" onclick="confirmDelete()"><i class="bx bx-x" style="color: red;"></i> Cancel Invoice</a>
                <form id="delete-form" method="POST" action="{{ route('an-sales.destroy', encrypt($sale->id)) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                </form>
                @endif
                @if(Auth::user()->can('create-sale-payment'))
                <a href="#"  class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px; padding-top: 5px"><i class="bx bx-money"></i> {{trans('navmenu.add_payment')}}</a>
                @endif 
                @endif
                <a href="{{ url('create-dnote/' . encrypt($sale->id)) }}" class="btn btn-primary btn-sm" title="Create Delivery Note"><i class="fa fa-file"></i> Delivery Note</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <ul class="nav nav-tabs nav-tabs-new2">
                @if(Auth::user()->can('view-invoice'))
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#invoice-view"><i class='fa fa-list-check font-18 me-1'></i> Invoice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#receipt-view"><i class='fa fa-list-check font-18 me-1'></i> Invoice Receipt View</a>
                </li>
                @endif
            </ul>
            <div class="tab-content py-3">
                <?php 
                    $netsales = (($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount);
                    $netreturn = (($sale->return_amount-$sale->return_discount)+$sale->return_tax);
                    $netpayable = $netsales-$netreturn 
                ?>
                <div class="tab-pane fade" id="receipt-view" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10">
                                    @if(Auth::user()->can('print-invoice'))
                                    <!-- <a href="#" onclick="javascript:printInvoice()" class="btn btn-outline-primary btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="fa fa-printer"></i>Direct Print Invoice</a> -->
                                    <a id="btnPrintInvoice" class="btn btn-secondary btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="fa fa-print"></i> Print</a>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <form method="GET" action="{{route('invoices.show', encrypt($sale->id))}}" >
                                        <select class="form-select form-select-sm mb-1" name="stmt_currency" onchange="this.form.submit()" style="display: inline; margin-left: 5px;">
                                            @foreach($stmtcurrencies as $curr)
                                            @if($stmtcurr == $curr)
                                            <option selected>{{$curr}}</option>
                                            @else
                                            <option>{{$curr}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </div> 
                            </div>
                            <div class="col-md-6 mx-auto" style="border: 1px solid gray;">
                                <div class="print_invoice" id="receipt-invoice">
                                    <center id="top">
                                        <div class="logo">
                                            @if(!is_null($company->logo_url))
                                            <figure>
                                                <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="80">
                                            </figure>
                                            @endif
                                        </div>
                                        <div class="info" style="text-align: center; font-size: 11px; border-bottom: 2px dashed #000;"> 
                                            <span class="mb-0" style="font-size: 12px;">{{$company->name}}<br> {{$shop->name}}</span><br>
                                            @if(!is_null($shop->short_desc))<small style="font-size: 8px;">{{$shop->short_desc}}</small><br>@endif
                                            <span style="font-size: 9px;">{{$shop->postal_address}} {{$shop->physical_address}} @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br>
                                            @if(!is_null($shop->email)){{trans('navmenu.email')}}   : {{$shop->email}} @endif @if(!is_null($shop->mobile)){{trans('navmenu.mobile')}}   : {{$shop->mobile}}@endif<br>
                                            @if(!is_null($shop->tin)){{trans('navmenu.tin')}}   : {{$shop->tin}} @endif @if(!is_null($shop->vrn)){{trans('navmenu.vrn')}}   : {{$shop->vrn}}@endif</span>
                                        </div><!--End Info-->
                                        <div class="header-title" style="text-align: center; print margin-bottom: 5px; margin-top: 10px; border-bottom: 2px dashed #000;">
                                            <span style="font-size: 14px; font-weight: bold; color: #2874a6; text-transform: uppercase;"><b>{{$settings->invoice_title}}</b></span>
                                        </div>
                                    </center><!--End InvoiceTop-->
                                                
                                    <div id="mid" style="text-align: center;">
                                        <table style="width: 100%; font-size: 9px !important;">
                                            <tr>
                                                <td><span><b>{{trans('navmenu.invoice_no')}}: {{ sprintf('%04d', $sale->invoice_no)}}</b></span></td>
                                                <td style="text-align: right;"><span>{{trans('navmenu.date')}}: <strong>{{$date}}</strong></span></td>
                                            </tr>
                                            <tr>
                                                <td><span>{{trans('navmenu.customer')}}: <strong>{{$sale->name}}</strong></span></td>
                                                <td style="text-align: right;"><span>@if(!is_null($sale->phone)){{trans('navmenu.mobile')}}: <b>{{$sale->phone}}</b>@endif</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="text-align: center;">@if(!is_null($sale->tin))<span>{{trans('navmenu.tin')}}: <b>{{$sale->tin}}</b></span>@endif @if(!is_null($sale->vrn))<span>{{trans('navmenu.vrn')}}: <b>{{$sale->vrn}}</b></span>@endif</td>
                                            </tr>
                                        </table>
                                    </div><!--End Invoice Mid-->
                                    <div id="bot">
                                        <div id="table" style="padding: 0px;">
                                            <table class="items mt-0" style="width: 100%; font-size: 9px;">
                                                <thead>
                                                    <tr>
                                                        @if($settings->show_discounts)
                                                        <th style="text-align: left; width: 35%; border-bottom: 1px solid #000;"> {{trans('navmenu.description')}}</th>
                                                        <th style="text-align: center; width: 10%; border-bottom: 1px solid #000;">Qty</th>
                                                        <th style="text-align: center; width: 20%; border-bottom: 1px solid #000;">Unit Price</th>
                                                        <th style="text-align: center; width: 10%; border-bottom: 1px solid #000;">Disc @if($settings->discount_by_percent)(%)@endif</th>
                                                        <th style="text-align: right; width: 25%; border-bottom: 1px solid #000;  padding-right: 5px;">{{trans('navmenu.total')}} ({{$sale->currency}})</th>
                                                        @else
                                                        <th style="text-align: left; width: 40%; border-bottom: 1px solid #000;"> {{trans('navmenu.description')}}</th>
                                                        <th style="text-align: center; width: 15%; border-bottom: 1px solid #000;">Qty</th>
                                                        <th style="text-align: center; width: 20%; border-bottom: 1px solid #000;">Unit Price</th>
                                                        <th style="text-align: right; width: 25%; border-bottom: 1px solid #000;  padding-right: 5px;">{{trans('navmenu.total')}} ({{$sale->currency}})</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $key => $item)
                                                    <?php
                                                        $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                                        $quantity_sold = $item->quantity_sold/$punit->qty_equal_to_basic;
                                                        $retail_price = $item->retail_price*$punit->qty_equal_to_basic;
                                                        $unit_discount = $item->discount*$punit->qty_equal_to_basic;

                                                    ?>
                                                    <tr>
                                                        @if($settings->show_discounts)
                                                        <td style="">{{$item->name}}</td>
                                                        <td style="text-align: center; ">{{$quantity_sold + 0}} {{$punit->unit_name}}</td>
                                                        <td style="text-align: center; ">{{number_format($retail_price/$ex_rate, 2, '.', ',')}}</td>
                                                        <td style="text-align: center; ">
                                                            @if($settings->discount_by_percent)
                                                            {{$item->disc_percent+0}}
                                                            @else
                                                            {{number_format($item->total_discount/$ex_rate, 2, '.', ',')}}
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right; padding-right: 5px; ">
                                                            {{number_format(($item->price-$item->total_discount)/$ex_rate, 2, '.', ',')}}
                                                        </td>
                                                        @else
                                                        <td style="">{{$item->name}}</td>
                                                        <td style="text-align: center; ">{{$quantity_sold + 0}} {{$punit->unit_name}}</td>
                                                        <td style="text-align: center; ">{{number_format($retail_price/$ex_rate, 2, '.', ',')}}</td>
                                                        <td style="text-align: right;  padding-right: 5px;">
                                                            {{number_format($item->price/$ex_rate, 2, '.', ',')}}
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @endforeach
                                                    @foreach($servitems as $key => $item)
                                                    <tr>
                                                        @if($settings->show_discounts)
                                                        <td style="">{{$item->name}}</td>
                                                        <td style="text-align: center; ">{{round($item->qty,2)}}</td>
                                                        <td style="text-align: center; ">{{number_format($item->price/$ex_rate, 2, '.', ',')}}</td>
                                                        <td style="text-align: center; ">
                                                            @if($settings->discount_by_percent)
                                                            {{$item->disc_percent+0}}
                                                            @else
                                                            {{number_format($item->total_discount, 2, '.', ',')}}
                                                            @endif</td>
                                                        <td style="text-align: right;  padding-right: 5px;">{{number_format(($item->total-$item->total_discount)/$ex_rate, 2, '.', ',')}}</td>
                                                        @else
                                                        <td style="">{{$item->name}}</td>
                                                        <td style="text-align: center; ">{{$item->qty}}</td>
                                                        <td style="text-align: center; ">{{number_format($item->price/$ex_rate, 2, '.', ',')}}</td>
                                                        <td style="text-align: right;  padding-right: 5px;">{{number_format(($item->total-$item->total_discount)/$ex_rate, 2, '.', ',')}}</td>
                                                        @endif
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <table class="items" width="100%" style="font-size: 11px; padding-right: 5px;">
                                                <tbody>
                                                    @if($settings->show_discounts)
                                                    <tr style="border-top: 2px dashed #000;">
                                                        <td colspan="2" style="text-align: right;">{{trans('navmenu.subtotal')}}</td>
                                                        <td style="text-align: right; padding-right: 5px;">{{number_format(($sale->sale_amount-$sale->sale_discount)/$ex_rate, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @else
                                                    <tr>
                                                        <td colspan="2" style="text-align: right; border-top: 2px solid #e0e0e0;">{{trans('navmenu.subtotal')}}</td>
                                                        <td style="text-align: right; border-top: 2px solid #e0e0e0; padding-right: 5px;">{{number_format($sale->sale_amount/$ex_rate, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @if($sale->sale_discount > 0)
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">{{trans('navmenu.discount')}}</td>
                                                        <td style="text-align: right;">{{number_format($sale->sale_discount/$ex_rate, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @endif
                                                    @endif    

                                                    @if($settings->is_vat_registered && $sale->tax_amount > 0)
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">{{trans('navmenu.vat')}}</td>
                                                        <td style="text-align: right;">{{number_format($sale->tax_amount/$ex_rate, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @endif
                                                
                                                    <tr>
                                                        <th colspan="2" style="text-align: right;"><b>{{trans('navmenu.total')}}</b></th>
                                                        <th style="text-align: right;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)/$ex_rate, 2, '.', ',')}}</b></th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;"><span>{{trans('navmenu.sale_type')}} : <b>{{$sale->sale_type}}</b></span></td>
                                                        <td style="text-align: right;"> <span>{{trans('navmenu.pay_type')}} : <b>{{$sale->pay_type}}</span><br></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div><!--End Table-->
                                        <div id="legalcopy" style="text-align: center; font-size: 10px; border-bottom: 2px dashed #000;">
                                            <p class="legal">
                                                <small>Thank you for shopping at {{$shop->name}}<br>@if(!is_null($shop->website))For more info.about our products & services, please visit {{$shop->website}}@endif</small>
                                            </p>
                                        </div>
                                    </div><!--End InvoiceBot-->
                                </div><!--End Invoice-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show active" id="invoice-view" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10">
                                    @if(Auth::user()->can('print-invoice'))
                                    <a onclick="javascript:savePdf()" class="btn btn-outline-success btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="fa fa-download"></i> {{trans('navmenu.download')}} PDF</a>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <form method="GET" action="{{route('invoices.show', encrypt($sale->id))}}" >
                                        <select class="form-select form-select-sm mb-1" name="stmt_currency" onchange="this.form.submit()" style="display: inline; margin-left: 5px;">
                                            @foreach($stmtcurrencies as $curr)
                                            @if($stmtcurr == $curr)
                                            <option selected>{{$curr}}</option>
                                            @else
                                            <option>{{$curr}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </div> 
                            </div>
                            <div class="row g-1 print_invoice" id="print-invoice">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="text-align: left; padding-left: 15px; width: 65%;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="200" style="border: 1px solid white;">
                                                </figure>
                                                @endif
                                            </td>
                                            <td style="width: 35%;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td colspan="2" style="text-align: left;">
                                                            <strong style="font-size: 14px;">{{$company->name}}</strong><br><span style="text-align: center;">({{$shop->name}})</span><br>
                                                            @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br>@endif
                                                            <small>@if(!is_null($shop->postal_address)){{$shop->postal_address}}@endif @if(!is_null($shop->physical_address)){{$shop->physical_address}} <br>@endif @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif @if(!is_null($shop->city)){{$shop->city}}@endif<br> @if(!is_null($shop->email))Email: <b>{{$shop->email}}</b><br>@endif @if(!is_null($shop->tel))Tel: <b>{{$shop->tel}}</b>@endif @if(!is_null($shop->mobile))Mobile: <b>{{$shop->mobile}}</b><br>@endif @if(!is_null($shop->tin))TIN: <b>{{$shop->tin}}</b>@endif @if(!is_null($shop->vrn))VRN: <b>{{$shop->vrn}}</b>@endif</small>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="background: <?php echo $settings->invoice_color; ?>; padding-left: 15px;   border-radius: 0px;border-top-left-radius: 15px; border-top-right-radius: 15px; text-align: center;">
                                                    <h6 class="mb-0 text-uppercase" style="color: <?php echo $settings->invoice_title_color; ?>;">{{$settings->invoice_title}}</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 customer mt-2 mb-0">
                                    <table style="width: 100%">
                                        <tr>
                                            <td style="width: 65%;">
                                                <table style="width: 100%; font-size: 16px;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="meta-head" colspan="2"><b>Bill To:</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="meta-head" style="width: 20%;">Contact Name:</td>
                                                            <td style="width: 80%;"><b>{{$sale->name}}</b></td>
                                                        </tr>
                                                        @if(!is_null($sale->ph_address) || !is_null($sale->po_address))
                                                        <tr>
                                                            <td class="meta-head" style="width: 20%;">Address:</td>
                                                            <td style="width: 80%;"><b>@if(!is_null($sale->ph_address)){{$sale->ph_address}}@endif @if(!is_null($sale->po_address)){{$sale->po_address}}@endif</b></td>
                                                        </tr>
                                                        @endif
                                                        @if(!is_null($sale->email))
                                                        <tr>
                                                            <td class="meta-head" style="width: 20%;">Email address:</td>
                                                            <td style="width: 80%;"><b> <a href="#" style="text-transform: lowercase;">{{$sale->email}}</a></b></td>
                                                        </tr>
                                                        @endif
                                                        @if(!is_null($sale->phone))
                                                        <tr>
                                                            <td class="meta-head" style="width: 20%;">Mobile:</td>
                                                            <td style="width: 80%;">{{$sale->phone}}</td>
                                                        </tr>
                                                        @endif
                                                        
                                                        <tr>
                                                            <td colspan="2">
                                                                @if(!is_null($sale->tin))TIN : <b>{{$sale->tin}}</b>@endif
                                                                @if(!is_null($sale->vrn))VRN : <b>{{$sale->vrn}}</b>@endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td style="width: 35%; text-align: right;">
                                                <table style="width: 100%; font-size: 16px;">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right;">Invoice No.</td>
                                                            <th style="text-align: right;"><b class="date">{{ sprintf('%06d', $sale->invoice_no)}}</b></th>
                                                        </tr>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right;">Issue Date</td>
                                                            <td style="text-align: right;"><b id="date">{{ date('d M, Y', strtotime($sale->time_created)) }}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right;">Due Date</td>
                                                            <th style="text-align: right;"><b class="date">{{ date('d M, Y', strtotime($sale->due_date))}}</b></th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>                                    
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class=" mt-3">
                                        <thead>
                                            <tr style="background: <?php echo $settings->invoice_color; ?>; color: <?php echo $settings->invoice_title_color; ?>; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                <th style="text-align: center; width: 3%;">#</th>
                                                <th style="text-align: right; width: 10%; border-left: 1px solid <?php echo $settings->invoice_title_color; ?>;">Code</th>
                                                <th style="width: 50%;">Item Description</th>
                                                <th style="text-align: center; width: 10%; border-left: 1px solid <?php echo $settings->invoice_title_color; ?>;">Qty</th>
                                                @if($settings->show_discounts)
                                                <th style="text-align: center; width: 7%; border-left: 1px solid <?php echo $settings->invoice_title_color; ?>;">Price ({{$sale->currency}})</th>
                                                <th style="text-align: center; width: 5%; border-left: 1px solid <?php echo $settings->invoice_title_color; ?>;">Disc @if($settings->discount_by_percent)(%)@endif</th>
                                                @else
                                                <th style="text-align: center; width: 12%; border-left: 1px solid <?php echo $settings->invoice_title_color; ?>;">Price ({{$sale->currency}})</th>
                                                @endif
                                                <th style="text-align: right; width: 15%; border-left: 1px solid <?php echo $settings->invoice_title_color; ?>;">Amount ({{$sale->currency}})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $tqty = 0; ?>
                                            @foreach($items as $key => $item)
                                            <?php
                                                $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                                $quantity_sold = $item->quantity_sold/$punit->qty_equal_to_basic;
                                                $retail_price = $item->retail_price*$punit->qty_equal_to_basic;
                                                $unit_discount = $item->discount*$punit->qty_equal_to_basic;
                                                $tqty += $quantity_sold;

                                                $slug = str_replace($item->name, '', $item->slug);
                                                // $slug = str_replace('-', ' ', $slug);
                                            ?>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                                <td style="text-align: center; "> {{$key+1}} </td>
                                                <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">@if(!is_null($item->product_code)){{$item->product_code}}@endif</td>
                                                <td class="desc" style="">{{$item->name}} @if($slug != '')- {{$slug}}@endif</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{number_format($quantity_sold + 0)}} {{$punit->unit_name}}</td>
                                                @if($settings->show_discounts)
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{number_format($retail_price/$ex_rate, 2, '.', ',')}}</td>
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">
                                                    @if($settings->discount_by_percent)
                                                    {{$item->disc_percent+0}}
                                                    @else
                                                    {{number_format($item->total_discount, 2, '.', ',')}}
                                                    @endif
                                                </td>
                                                <td class="total" style=" text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">{{number_format(($item->price-$item->total_discount)/$ex_rate, 2, '.', ',')}}</td>
                                                @else
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray;">{{number_format($retail_price/$ex_rate, 2, '.', ',')}}</td>
                                                <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($item->price/$ex_rate, 2, '.', ',')}}</td>
                                                @endif
                                            </tr>
                                            @endforeach

                                            <?php $tsqty = 0; ?>
                                            @foreach($servitems as $key => $servitem)
                                            <?php $tsqty += $servitem->qty; ?>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                                <td style="text-align: center;"> {{$items->count()+$key+1}} </td>
                                                <td style="text-align: right; border-left: 1px solid gray;">{{$servitem->code}}</td>
                                                <td class="desc" style="">{{$servitem->name}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{round($servitem->qty,2)}}</td>
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray;">{{number_format($servitem->price/$ex_rate, 2, '.', ',')}}</td>
                                                @if($settings->show_discounts)
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray;">
                                                    @if($settings->discount_by_percent)
                                                    {{$servitem->disc_percent+0}}
                                                    @else
                                                    {{number_format($servitem->total_discount, 2, '.', ',')}}
                                                    @endif
                                                </td>
                                                <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($servitem->total/$ex_rate, 2, '.', ',')}}</td>
                                                @else
                                                <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($servitem->total/$ex_rate, 2, '.', ',')}}</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                            <tr class="blank_row" style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                                <td colspan="3" style="" class="desc"><b></b></td>
                                                <td style=" text-align: center; border-left: 1px solid gray;" class="qty"></b></td>
                                                <td style="border-left: 1px solid gray;"></td>
                                                @if($settings->show_discounts)
                                                <td style="border-left: 1px solid gray;" class="unit"></td>
                                                @endif
                                                <td style="border-left: 1px solid gray;" class="total"></td>
                                            </tr>
                                            <!-- <tr style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                                <td></td>
                                                <td colspan="2" class="desc"><b>{{trans('navmenu.total')}} Items</b></td>
                                                <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b>{{$tqty+$tsqty}}</b></td>
                                                @if($settings->show_discounts)
                                                <td style="border-left: 1px solid gray;"></td>
                                                @endif
                                                <td class="unit" style="border-left: 1px solid gray;"></td>
                                                <td class="total" style="text-align: right; border-left: 1px solid gray;">
                                                    @if($settings->show_discounts)
                                                    {{number_format(($sale->sale_amount-$sale->sale_discount)/$ex_rate, 2, '.', ',')}}
                                                    @else
                                                    {{number_format(($sale->sale_amount)/$ex_rate, 2, '.', ',')}}
                                                    @endif
                                                </td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12" style="border-bottom: 2px solid <?php echo $settings->invoice_color; ?>; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 60%;"></td>
                                                <td style="width: 40%; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                                    <table class="meta mt-0" style="width: 100%;">
                                                        <tbody>
                                                            @if($settings->show_discounts)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>Subtotal:</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format(($sale->sale_amount-$sale->sale_discount)/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @if($settings->is_vat_registered)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>VAT ({{number_format($settings->tax_rate)}}%):</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format($sale->tax_amount/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @endif
                                                            <tr style="border-bottom: 2px solid <?php echo $settings->invoice_color; ?>;">
                                                                <th class="unit" style="text-align: right;"><b>TOTAL ({{$stmtcurr}}):</b></th>
                                                                <th class="total" style="text-align: right;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)/$ex_rate, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            @else
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>Subtotal:</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format(($sale->sale_amount)/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @if($sale->sale_discount > 0)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>Discount:</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format($sale->sale_discount/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @endif
                                                            @if($settings->is_vat_registered)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>VAT ({{number_format($settings->tax_rate)}}%):</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format($sale->tax_amount/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @endif
                                                            <tr style="border-bottom: 2px solid <?php echo $settings->invoice_color; ?>;">
                                                                <th class="unit" style="text-align: right; border-bottom: 1px solid gray;"><b>TOTAL ({{$stmtcurr}}):</b></th>
                                                                <th class="total" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)/$ex_rate, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            @endif
                                                            @if($sale->sale_amount_paid > 0)
                                                            <tr style="border: 1px solid #e3e4e8;">
                                                                <td class="unit" style="text-align: right; border: none;">CN or Excess Received :</td>
                                                                <td class="total" style="text-align: right; border: none;">{{number_format($oldbalance, 2, '.', ',')}}</td>
                                                            </tr>
                                                            <tr style="border: 1px solid #e3e4e8;">
                                                                <td class="unit" style="text-align: right; border-bottom: 1px solid gray;">New Payments :</td>
                                                                <td class="total" style="text-align: right; border-bottom: 1px solid gray;">{{number_format($newpayments, 2, '.', ',')}}</td>
                                                            </tr>
                                                            <tr style="border: 1px solid #e3e4e8;">
                                                                <th class="unit" style="text-align: right; border: none;"><b>Total Paid :</b></th>
                                                                <th class="total" style="text-align: right; border: none;"><b>{{number_format($sale->sale_amount_paid, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            <tr style="border: 1px solid #e3e4e8;">
                                                                <th class="unit" style="text-align: right; border-bottom: 1px solid black;"><b>Remaining Unpaid:</b></th>
                                                                <th class="total" style="text-align: right; border-bottom: 1px solid black;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)-$sale->sale_amount_paid, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            <tr style="border: 1px solid #e3e4e8;">
                                                                <td colspan="2" style="padding-top: 10px;">
                                                                    <span>Payment already done</span>
                                                                    <table class="mt-1" style="width: 100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Date</th>
                                                                                <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Amount</th>
                                                                                <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Method</th>
                                                                                <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Receipt No.</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($payments as $pay)
                                                                            <tr>
                                                                                <td style="border-bottom: 1px solid black;">{{ date('d/m/Y', strtotime($pay->pay_date)) }}</td>
                                                                                <td style="border-bottom: 1px solid black;">{{ number_format($pay->amount, 2, '.', ',') }}</td>
                                                                                <td style="border-bottom: 1px solid black;">{{$pay->pay_mode}}</td>
                                                                                <td style="border-bottom: 1px solid black;">{{ sprintf('%05d', $pay->receipt_no)}}</td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="width: 100%; padding-left: 5%;">
                                                    @if($settings->show_bd)
                                                    <table style="width: 100%; font-size: 8px;">
                                                        <tbody>
                                                            <tr>
                                                                <td>Payment Options :</td>
                                                            </tr>
                                                            @if(!is_null($sale->bank_detail_id))
                                                            <?php $bankdetail = App\Models\Account::find($sale->bank_detail_id) ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="col-sm-6" style="border: 1px solid #e3e4e8;">
                                                                        @if($bankdetail->type == 'Bank') Bank Name @else MNO Name @endif: <b>{{$bankdetail->bank_name}}</b><br>
                                                                        Account Name: <b>{{$bankdetail->account_name}}</b><br>
                                                                        Account No : <b>{{$bankdetail->account_number}}@if(!is_null($bankdetail->currency)) - {{$bankdetail->currency}}@endif</b><br>
                                                                        @if($bankdetail->type == 'Bank')
                                                                        Branch Name/Code : <b>{{$bankdetail->branch_name}}</b><br>
                                                                        Swift : <b>{{$bankdetail->swift_code}}</b>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @elseif($accounts->where('type', '!=', 'Cash')->count() > 0)
                                                            <tr>
                                                                <td class="row">
                                                                    @foreach($accounts->where('type', '!=', 'Cash') as $bankdetail)
                                                                    <div class="col-sm-6" style="border: 1px solid #e3e4e8;">
                                                                        @if($bankdetail->type == 'Bank') Bank Name @else MNO Name @endif: <b>{{$bankdetail->bank_name}}</b><br>
                                                                        Account Name: <b>{{$bankdetail->account_name}}</b><br>
                                                                        Account No : <b>{{$bankdetail->account_number}}@if(!is_null($bankdetail->currency)) - {{$bankdetail->currency}}@endif</b><br>
                                                                        @if($bankdetail->type == 'Bank')
                                                                        Branch name/Code : <b>{{$bankdetail->branch_name}}</b><br>
                                                                        Swift : <b>{{$bankdetail->swift_code}}</b>
                                                                        @endif
                                                                    </div>
                                                                    @endforeach
                                                                </td>
                                                            </tr>
                                                            @else
                                                            <tr>
                                                                <td><span style="color: orange;">Your bank details not updated. Please update your bank details <a href="{{ route('pro-invoices.edit', encrypt($sale->id)) }}">Here</a></span>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 65%;">
                                                    @if($settings->show_declaration)
                                                    <div class="notice col-md-12">
                                                        <div><b>DECLARATION</b>:</div>
                                                        <div>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</div>
                                                    </div>
                                                    @endif
                                                    @if(!is_null($sale->note))
                                                    <div class="notice col-md-12 pt-3">
                                                        <div>NOTE:</div>
                                                        <div>{!! $sale->note !!}</div>
                                                    </div>
                                                    @endif
                                                </td>
                                                @if($settings->show_authorization_sign)
                                                <td style="width: 35%; border-left: 1px solid gray;">
                                                    <div class="text-center">
                                                        <span style="font-size: 14px; font-weight: bold;">For {{$company->name}}</span><br>
                                                        @if(!is_null($company->stamp))
                                                        <figure>
                                                            <img class="invoice-logo" src="{{asset('storage/stamps/'.$shop->stamp)}}" alt="" width="80">
                                                        </figure>
                                                        @endif
                                                        <br>
                                                        <b>-----------------------------------------</b><br>
                                                        <b>Authorized Signatory</b>
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if($triplogs->count() > 0)
                                <div class="col-md-12">
                                    <table class="table display nowrap" style="width: 100%;">
                                        <tbody>
                                            <tr style="border-bottom: 2px solid gray;">
                                                <th colspan="4">Trip Details</th>
                                            </tr>
                                            <tr style="border-bottom: 1px solid black;">
                                                <td><b>Shipping Line</b></td>
                                                <td><b>Billing Number</b></td>
                                                <td><b>Trip Description</b></td>
                                            </tr>
                                            @foreach($triplogs as $trip)
                                            <tr>
                                                <td>{{$trip->shipping}}</td>
                                                <td>{{$trip->bill_no}}</td>
                                                <td>{{$trip->trip_title}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                                @if(!is_null($sale->terms_and_conditions))
                                <div class="col-md-12">
                                    <label class="form-label">Terms & Conditions</label>
                                    {!! $sale->terms_and_conditions !!}
                                </div>
                                @endif
                                @if($settings->show_end_note && !is_null($settings->invoice_end_note))
                                <div class="col-md-12 mx-auto text-center" style="border-top: 1px solid gray; background: <?php echo $settings->invoice_color; ?>; font-style: italic; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; color: <?php echo $settings->invoice_title_color; ?>;"><b>{{$settings->invoice_end_note}}.</b></div>
                                @endif
                            </div>
                            <div id="editor"></div>
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
                <form class="row g-3" method="POST" action="{{ url('acc-payments') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{$sale->customer_id}}">
                        <input type="hidden" name="invoice_id" value="{{$sale->id}}">
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required>
                            </div>
                        </div>
                        @if($sale->sale_type == 'cash')
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" type="text" step="any" name="amount" required value="{{ $netpayable-$sale->sale_amount_paid }}" placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @else
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" type="text" step="any" name="amount" value="0" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @endif
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
                            
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
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

    <link rel="stylesheet" type="text/css" href="{{ asset('css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.invoice_no').'_'.$sale->invoice_no.'_'.$date ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });

            $('#btnPrintInvoice').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt-invoice").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link rel="stylesheet" href="../side/assets/css/bootstrap.min.css"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.invoice_no').'_'.$sale->invoice_no.'_'.$date ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script language="javascript" type="text/javascript">
        function printOrder() {
            Swal.fire({
              title: "Are you sure you want to print this Order",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Ye Print",
              cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.value) {
                    var saleid = "<?php echo $sale->id; ?>";
                    $.ajax({
                        url:"{{ url('print-order') }}",
                        type:'GET',
                        data:{'id':saleid},
                        success:function (response) {
                            
                        }
                    })   
                }
            })
        }

        function printInvoice() {
            Swal.fire({
              title: "Are you sure you want to print this Invoice",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Ye Print",
              cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.value) {
                    var saleid = "<?php echo $sale->id; ?>";
                    $.ajax({
                        url:"{{ url('print-invoice') }}",
                        type:'GET',
                        data:{'id':saleid},
                        success:function (response) {
                            console.log(response);
                            callFunction(response);
                        }
                    })  


                }
            })
        }

        function callFunction(response)
        {
            setInterval(ajax(response),500);
        }

        function ajax(response)
        {
            // var rawdat = response;
            // var xhttp = new XMLHttpRequest();
               
            // url = 'http://127.0.0.1:9100/htbin/kp.py';
            // xhttp.open("POST", url, false); //browser has to wait until the data finished loaded
            // xhttp.setRequestHeader('Access-Control-Allow-Origin', 'http://127.0.0.1:8000');
            // xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            // xhttp.onreadystatechange = function(){
            //     if(this.readyState==4 && this.status == 200)
            //     {
            //        alert(this.responseText);   
            //     }
            // }
            // xhttp.send("p=POSPRINTER&data="+rawdat);

           
        }
        
        function savePdf() {
            const element = document.getElementById("print-invoice");
            var filename = "<?php echo 'Tax Invoice_'.sprintf('%04d', $sale->invoice_no).'_'.$sale->time_created; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
          // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
        }

        function saveOrderPdf() {
          const element = document.getElementById("print-order");
          var filename = "<?php echo 'Tax Invoice_'.sprintf('%06d', $sale->inv_no).'_'.$sale->created_at; ?>";
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
