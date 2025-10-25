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
                <!-- <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#receipt-view"><i class='fa fa-list-check font-18 me-1'></i> Invoice Receipt View</a>
                </li> -->
                @endif
            </ul>
            <?php 
                $netsales = (($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount);
                $netreturn = (($sale->return_amount-$sale->return_discount)+$sale->return_tax);
                $netpayable = $netsales-$netreturn 
            ?>
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="invoice-view" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row g-1">
                                <div class="col-md-8">
                                    @if(Auth::user()->can('print-invoice'))
                                    <a onclick="javascript:savePdf()" class="btn btn-outline-success btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="bx bx-download"></i> {{trans('navmenu.download')}} PDF</a>
                                    @endif
                                </div>
                                <div class="col-md-4 float-end">
                                    <form method="GET" action="{{route('invoices.show', encrypt($sale->id))}}" >
                                        <select class="form-select form-select-sm mb-1" name="stmt_currency" onchange="this.form.submit()" style="display: inline; margin-left: 5px;">
                                            @foreach($stmtcurrencies as $curr)
                                            @if($sale->currency == $curr)
                                            <option selected>{{$curr}}</option>
                                            @else
                                            <option>{{$curr}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-1 print_invoice" id="print-invoice">
                                <div class="col-md-12">
                                    <table class="table mb-1">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" style="text-align: center; background: #037c1e;">
                                                    <h4 class="mb-0 text-uppercase" style="color: #fff;">{{$settings->invoice_title}}</h4>
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
                                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="150" style="border: 1px solid white;">
                                                </figure>
                                                @endif
                                            </td>
                                            <td style="width: 50%; padding-right: 20px;">
                                                <table class="meta">
                                                    <tbody>
                                                        <tr>
                                                            <td style="border: none; text-align: right; font-size: 16px !important;" class="meta-head">Invoice # : <b>{{ sprintf('%06d', $sale->invoice_no)}}</b></td>
                                                        </tr>
                                                        @if(!is_null($sale->ref_customer))
                                                        <tr>
                                                            <td style="border: none; text-align: right;" class="meta-head">Ref. Customer : <b>{{ $sale->ref_customer }}</b></td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td style="border: none; text-align: right;" class="meta-head">Invoice Date : <b id="date">{{ date('d F, Y', strtotime($sale->time_created)) }}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: none; text-align: right;" class="meta-head">Due Date : <b>{{ date('d F, Y', strtotime($sale->due_date))}}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: none; text-align: right;" class="meta-head">LPO No : <b>{{ $sale->lpo_no }}</b></td>
                                                        </tr>
                                                    </tbody>
                                                </table>    
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%; padding-left: 30px;">
                                                <span>From: </span><br>
                                                <strong style="font-size: 14px;">{{$shop->name}}</strong><br>
                                                @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small>@endif<br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>@if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                            </td>
                                            <td style="width: 50%; border: 1px solid gray;">
                                                <span>To:</span><br>
                                                @if(!is_null($bagent))
                                                <strong style="font-size: 14px;">{{$bagent->name}}</strong><br>
                                                <small>{{$bagent->address}}</small>
                                                <span style="padding-top: 10px;">
                                                    Mobile : <a href="#"><b>{{$bagent->mobile}}</b> </a> Email :<a href="#" style="text-transform: lowercase;">{{$bagent->email}}</a><br>
                                                    TIN : <b>{{$bagent->tin}}</b> 
                                                    VRN : <b>{{$bagent->vrn}}</b><br>
                                                </span>
                                                @else
                                                <strong style="font-size: 14px;">{{$sale->name}}</strong><br>
                                                <small>
                                                    {{$sale->po_address}}
                                                    {{$sale->ph_address}}</small>
                                                <span style="padding-top: 10px;">
                                                    Mobile : <a href="#"><b>{{$sale->phone}}</b> </a> Email :<a href="#" style="text-transform: lowercase;">{{$sale->email}}</a><br>
                                                    TIN : <b>{{$sale->tin}}</b> 
                                                    VRN : <b>{{$sale->vrn}}</b><br>
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-12">
                                    <table class=" mt-3">
                                        <thead>
                                            <tr style="background: #037c1e; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                <th style="text-align: center; width: 3%;">#</th>
                                                <th style="text-align: right; width: 10%; border-left: 1px solid #fff;">Code</th>
                                                <th style="width: 50%;">Item Description</th>
                                                <th style="width: 3%; text-align: center; border-left: 1px solid #fff;">No. Persons/Rooms</th>
                                                <th style="text-align: center; width: 3%; border-left: 1px solid #fff;">No. Nights</th>
                                                @if($settings->show_discounts)
                                                <th style="text-align: center; width: 7%; border-left: 1px solid #fff;">Price</th>
                                                <th style="text-align: center; width: 5%; border-left: 1px solid #fff;">Disc @if($settings->discount_by_percent)(%)@endif</th>
                                                @else
                                                <th style="text-align: center; width: 12%; border-left: 1px solid #fff;">Price</th>
                                                @endif
                                                <th style="text-align: right; width: 15%; border-left: 1px solid #fff;">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $tperson = 0; $tnights = 0 ?>
                                            @foreach($servitems as $key => $servitem)
                                            <?php 
                                                $tperson += $servitem->persons;
                                                $tnights += ($servitem->quantity/$servitem->persons);
                                             ?>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #037c1e; border-right: 1px solid #037c1e;">
                                                <td style="text-align: center;"> {{$key+1}} </td>
                                                <td style="text-align: right; border-left: 1px solid gray;">{{$servitem->code}}</td>
                                                <td class="desc" style="">{{$servitem->name}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{$servitem->persons}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{$servitem->quantity/$servitem->persons}}</td>
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
                                            <tr class="blank_row" style="border-bottom: 1px solid gray; border-left: 1px solid #037c1e; border-right: 1px solid #037c1e;">
                                                <td colspan="3" style="" class="desc"><b></b></td>
                                                <td style=" text-align: center; border-left: 1px solid gray;" class="qty"></b></td>
                                                <td style=" text-align: center; border-left: 1px solid gray;" class="qty"></b></td>
                                                <td style="border-left: 1px solid gray;"></td>
                                                @if($settings->show_discounts)
                                                <td style="border-left: 1px solid gray;" class="unit"></td>
                                                @endif
                                                <td style="border-left: 1px solid gray;" class="total"></td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #037c1e; border-right: 1px solid #037c1e;">
                                                <td></td>
                                                <td colspan="2" class="desc"><b>{{trans('navmenu.total')}} Items</b></td>
                                                <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b>{{$tperson}}</b>
                                                <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b>{{$tnights}}</b></td>
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
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr style="border-top: 1px solid gray;">
                                                <td style="width: 65%">
                                                    @if($settings->show_bd)
                                                    <table style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td><span style="color: blue;">Bank Details :</span></td>
                                                                <td></td>
                                                            </tr>
                                                            @if($accounts->where('type', 'Bank')->count() > 0)
                                                            <tr>
                                                                @foreach($accounts->where('type', 'Bank') as $bankdetail)
                                                                <td>
                                                                    <b>{{$bankdetail->account_name}}</b><br>
                                                                    Bank Name: <b>{{$bankdetail->bank_name}}</b><br>
                                                                    Account No : <b>{{$bankdetail->account_number}}</b><br>
                                                                    Branch : <b>{{$bankdetail->branch_name}}</b><br>
                                                                    Swift : <b>{{$bankdetail->swift_code}}</b>
                                                                </td>
                                                                @endforeach
                                                            </tr>
                                                            @else
                                                            <tr>
                                                                <td><span style="color: orange;">Your bank details not updated. Please update your bank details <a href="{{ route('pro-invoices.edit', encrypt($invoice->id)) }}">Here</a></span>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    @endif
                                                </td>
                                                <td style="width: 35%; border: 1px solid gray;  border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                                    <table class="mt-3" style="width: 100%;">
                                                        <tbody>
                                                            @if($settings->show_discounts)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>SUBTOTAL:</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format(($sale->sale_amount-$sale->sale_discount)/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @if($settings->is_vat_registered)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>VAT ({{number_format($settings->tax_rate)}}%):</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format($sale->tax_amount/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <th class="unit" style="text-align: right; border-bottom: 1px solid gray;"><b>GRAND TOTAL ({{$stmtcurr}}):</b></th>
                                                                <th class="total" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)/$ex_rate, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            @else
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>SUBTOTAL:</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format(($sale->sale_amount)/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>DISCOUNT:</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format($sale->sale_discount/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr> 
                                                            @if($settings->is_vat_registered)
                                                            <tr>
                                                                <td class="unit" style="text-align: right;"><b>VAT ({{number_format($settings->tax_rate)}}%):</b></td>
                                                                <td class="total" style="text-align: right;"><b>{{number_format($sale->tax_amount/$ex_rate, 2, '.', ',')}}</b></td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <th class="unit" style="text-align: right; border-bottom: 1px solid gray;"><b>GRAND TOTAL ({{$stmtcurr}}):</b></th>
                                                                <th class="total" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)/$ex_rate, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            @endif
                                                            @if($sale->sale_amount_paid > 0)
                                                            <tr>
                                                                <td class="unit" style="text-align: right; border: none;">CN or Excess Received :</td>
                                                                <td class="total" style="text-align: right; border: none;">{{number_format($oldbalance, 2, '.', ',')}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="unit" style="text-align: right; border-bottom: 1px solid gray;">New Payments :</td>
                                                                <td class="total" style="text-align: right; border-bottom: 1px solid gray;">{{number_format($newpayments, 2, '.', ',')}}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="unit" style="text-align: right; border: none;"><b>Total Paid :</b></th>
                                                                <th class="total" style="text-align: right; border: none;"><b>{{number_format($sale->sale_amount_paid, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            <tr>
                                                                <th class="unit" style="text-align: right; border-bottom: 1px solid black;"><b>Remaining Unpaid:</b></th>
                                                                <th class="total" style="text-align: right; border-bottom: 1px solid black;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)-$sale->sale_amount_paid, 2, '.', ',')}}</b></th>
                                                            </tr>
                                                            <tr>
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
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr style="border-top: 1px solid #037c1e; border-bottom: 2px solid #037c1e; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                                <td style="width: 65%;">
                                                    <div class="notice col-md-12">
                                                        <div><b>DECLARATION</b>:</div>
                                                        <div>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</div>
                                                    </div>
                                                    @if(!is_null($sale->note))
                                                    <div class="notice col-md-12 pt-3">
                                                        <div>NOTE:</div>
                                                        <div>{!! $sale->note !!}</div>
                                                    </div>
                                                    @endif
                                                </td>
                                                <td style="width: 35%; border-left: 1px solid gray;">
                                                    <div class="text-center">
                                                        <span style="font-size: 14px; font-weight: bold;">For {{$shop->name}}</span><br>
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
                                            </tr>
                                            <tr>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if(!is_null($sale->terms_and_conditions))
                                <div class="col-md-12">
                                    <label class="form-label">Terms & Conditions</label>
                                    {!! $sale->terms_and_conditions !!}
                                </div>
                                @endif
                                @if($settings->show_end_note)
                                <div class="col-md-12 text-center" style="border-top: 1px solid gray;">This is an electronic Invoice and is valid without the signature and seal.</div>
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
                            <input id="name" type="number" step="any" name="amount" required value="{{ $netpayable-$sale->sale_amount_paid }}" placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @else
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
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
            var filename = "<?php echo 'Tax Invoice_'.sprintf('%06d', $sale->invoice_no).'_'.$sale->time_created; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},

                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().save();
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
        }

        function saveOrderPdf() {
          const element = document.getElementById("print-order");
          var filename = "<?php echo 'POS_'.sprintf('%06d', $sale->invoice_no).'_'.time(); ?>";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

          // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().save();
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
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
