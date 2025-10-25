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

    function confirmTerminate() {
        Swal.fire({
          title: "Are you sure you want to Terminate this contract",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Terminate It",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('term-form').submit();
            Swal.fire(
              "Terminated",
              "Terminated",
              'success'
            )
          }
        })
    }

    function showHideForm(elem) {
        var newform = document.getElementById('terminate');
        var contract = document.getElementById('contract-view');
        if (elem == 'show') {
            newform.style.display = 'block';
            contract.style.display = 'none';
        }else{
            newform.style.display = 'none';
            contract.style.display = 'block';
        }
    }

    function confirmResume(id) {
        Swal.fire({
          title: "Are you sure you want to Resume this contract",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Resume It",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('resume-contract') }}/"+id;
            Swal.fire(
              "Resumed",
              "Resumed",
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
                    <li class="breadcrumb-item"><a href="{{ url('contracts') }}">contracts</a></li>
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
                <div class="tab-pane fade show active" id="contrat-view" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10">
                                    @if(!$contract->is_deleted)
                                    <a onclick="javascript:savePdf()" class="btn btn-outline-success btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="fa fa-download"></i> {{trans('navmenu.download')}} PDF</a>
                                    @if($contract->status != 'Graduated')
                                    @if($contract->status != 'Terminated')
                                    <a class="btn btn-outline-primary btn-sm" style="margin-left: 2px; padding-top: 5px;" href="{{ route('contracts.edit', encrypt($contract->id)) }}"><i class="fa fa-edit"></i>Update</a>
                                    <a class="btn btn-outline-warning btn-sm" style="margin-left: 2px; padding-top: 5px;" data-bs-toggle="modal" data-bs-target="#terminateModal" data-bs-backdrop="static" data-bs-keyboard="false" ><i class="fa fa-stop"></i> Terminate Contract</a>
                                    <form id="delete-form" method="POST" action="{{ route('contracts.destroy', encrypt($contract->id)) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-outline-danger btn-sm" href="#" href="#" onclick="confirmCancel('<?php echo encrypt($contract->id); ?>')"><i class="fa fa-close" style="color: red;"></i> Cancel contract</a>
                                    </form>
                                    @if(!is_null($sale) && Auth::user()->can('create-sale-payment'))
                                    <a href="#"  class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px; padding-top: 5px"><i class="fa fa-money"></i> {{trans('navmenu.add_payment')}}</a>
                                    @endif
                                    @else
                                    <a class="btn btn-outline-primary btn-sm" style="margin-left: 2px; padding-top: 5px;" href="#" onclick="confirmResume('<?php echo encrypt($contract->id); ?>')"><i class="fa fa-edit"></i>Resume Contract</a>
                                    @endif
                                    @endif
                                    @endif
                                </div>
                                <div class="col-md-2">
                        
                                </div> 
                            </div>
                            <div class="row g-1 print_invoice" id="print-invoice">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="text-align: left; padding-left: 15px;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="200">
                                                </figure>
                                                @endif
                                            </td>
                                            <td>
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">
                                                            <span style="font-size: 18px">{{$company->name}} <br>{{$shop->name}}</span><br>
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
                                                <td style="background: #0459c6; padding-left: 15px;  border-radius: 0px; text-align: center;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;">contract Details</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 customer mb-0" style="padding-left: 50px; padding-right: 50px;">
                                    <table class="table table-bordered" style="width: 100%">
                                        <tr>
                                            <th colspan="4" style="font-size: 14px; border-bottom: 1px solid gray;">Contract ID : <b>{{$contract->cuid}}</b></th>
                                        </tr>
                                        <tr>
                                            <td>Created At</td>
                                            <td><b>{{date('d/m/Y H:i:s A', strtotime($contract->created_at))}}</b></td>
                                            <td>Contract Status : </td>
                                            <td><b>{{$contract->status}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Contract Start Date : </td>
                                            <td><b>{{date('d/m/Y', strtotime($contract->start_date))}}</b></td>
                                            <td>Expected End Date : </td>
                                            <td><b>{{date('d/m/Y', strtotime($contract->end_date))}}</b></td>
                                        </tr>
                                        @if($contract->status == 'Graduated' || $contract->status == 'Terminated')
                                        <tr>
                                            <td>Actual End Date : </td>
                                            <td><b>{{date('d/m/Y', strtotime($contract->actual_end_date))}}</b></td>
                                            <td>Terminated/Closed At : </td>
                                            <td><b>{{date('d/m/Y H:i:s A', strtotime($contract->terminated_at))}}</b></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Plate Number : </td>
                                            <td><b>{{$contract->device_number}}</b></td>
                                            <td>Chasis : </td>
                                            <td><b>{{$contract->device_name}}</b></td>
                                        </tr>
                                        <tr>
                                            <th colspan="4" style="border-bottom: 1px solid gray;"><span>Driver Info</span></th>
                                        </tr>
                                        <tr>
                                            <td>Customer Name :</td>
                                            <td><b>{{$contract->name}}</b></td>
                                            <td>Address : </td>
                                            <td>{{$contract->physical_address}}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile: </td>
                                            <td><a href="tel:{{$contract->phone}}">{{$contract->phone}}</a></td>
                                            <td>Email : </td>
                                            <td><a href="mailto:{{$contract->email}}" style="text-transform: lowercase;">{{$contract->email}}</a></td>
                                        </tr>
                                        <tr>
                                            <th colspan="4" style="border-bottom: 1px solid gray;"><span>Garantors</span></th>
                                        </tr>
                                        @foreach($garantors as $key => $garantor)
                                        <tr>
                                            <td>Garantor {{$key+1}} :</td>
                                            <td><b>{{$garantor->full_name}}</b></td>
                                            <td>Mobile: </td>
                                            <td><a href="tel:{{$garantor->phone}}">{{$garantor->mobile}}</a></td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Initial Costs</label>
                                    <table class="mt-0" style="width: 100%;">
                                        <thead>
                                            <tr style="background: #0459c6; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                <th style="text-align: right; width: 12%; border-left: 1px solid #fff;">Code</th>
                                                <th style="width: 52%;">Item Description</th>
                                                <th style="text-align: center; width: 3%; border-left: 1px solid #fff;">Qty/Days</th>
                                                <th style="text-align: center; width: 15%; border-left: 1px solid #fff;">Price</th>
                                                <th style="text-align: right; width: 15%; border-left: 1px solid #fff;">Amount ({{$defcurr->code}})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total_in = 0; $tdays =0  ?>
                                            @foreach($contractservices->where('is_add_on', 1) as $key => $servitem)
                                            @if($servitem->is_add_on)
                                            <?php
                                                $tdays += $servitem->qty; 
                                                $total_in += $servitem->total;
                                            ?>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                                <td style="text-align: right; border-left: 1px solid gray;">{{$servitem->code}}</td>
                                                <td class="desc" style="">{{$servitem->name}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{$servitem->qty}}</td>
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray;">{{number_format($servitem->unit_price/$ex_rate, 2, '.', ',')}}</td>
                                                <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($servitem->total/$ex_rate, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                                <td colspan="2" class="desc"><b>{{trans('navmenu.total')}}</b></td>
                                                <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b></b></td>
                                                <td class="unit" style="border-left: 1px solid gray;"></td>
                                                <td class="total" style="text-align: right; border-left: 1px solid gray;">
                                                    {{number_format(($total_in)/$ex_rate, 2, '.', ',')}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <label class="form-label">Agreed Charges</label>
                                    <table class="mt-0" style="width: 100%;">
                                        <thead>
                                            <tr style="background: #0459c6; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                <th style="text-align: right; width: 12%; border-left: 1px solid #fff;">Code</th>
                                                <th style="width: 52%;">Item Description</th>
                                                <th style="text-align: center; width: 3%; border-left: 1px solid #fff;">Qty/Days</th>
                                                <th style="text-align: center; width: 15%; border-left: 1px solid #fff;">Price</th>
                                                <th style="text-align: right; width: 15%; border-left: 1px solid #fff;">Amount ({{$defcurr->code}})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0; $tdays =0  ?>
                                            @foreach($contractservices as $key => $servitem)
                                            @if(!$servitem->is_add_on)
                                            <?php
                                                $tdays += $servitem->qty; 
                                                $total += $servitem->total;
                                            ?>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                                <td style="text-align: right; border-left: 1px solid gray;">{{$servitem->code}}</td>
                                                <td class="desc" style="">{{$servitem->name}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{$servitem->qty}}</td>
                                                <td class="unit" style=" text-align: center; border-left: 1px solid gray;">{{number_format($servitem->unit_price/$ex_rate, 2, '.', ',')}}</td>
                                                <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($servitem->total/$ex_rate, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                                <td colspan="2" class="desc"><b>{{trans('navmenu.total')}}</b></td>
                                                <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b></b></td>
                                                <td class="unit" style="border-left: 1px solid gray;"></td>
                                                <td class="total" style="text-align: right; border-left: 1px solid gray;">
                                                    {{number_format(($total)/$ex_rate, 2, '.', ',')}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 70%">
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
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr style="border-top: 1px solid #0459c6; border-bottom: 2px solid #0459c6; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                                <td style="width: 65%;">
                                                    <div class="notice col-md-12">
                                                        <div><b>DECLARATION</b>:</div>
                                                        <div>We declare that this invoice shows the actual price of the goods/service described and that all particulars are true and correct.</div>
                                                    </div>
                                                    @if(!is_null($contract->notes))
                                                    <div class="notice col-md-12 pt-3">
                                                        <div>NOTE:</div>
                                                        <div>{!! $contract->notes !!}</div>
                                                    </div>
                                                    @endif
                                                </td>
                                                <td style="width: 35%; border-left: 1px solid gray;">
                                                    <div class="text-center">
                                                        <span style="font-size: 14px; font-weight: bold;">For {{$company->name}} <br>{{$shop->name}}</span><br>
                                                        @if(!is_null($shop->stamp))
                                                        <figure>
                                                            <img class="invoice-logo" src="{{asset('storage/stamps/'.$shop->stamp)}}" alt="" width="80">
                                                        </figure>
                                                        @else
                                                        <br>
                                                        @endif
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
                                @if(!is_null($contract->terms_and_conditions))
                                <div class="col-md-12">
                                    <label class="form-label">Terms & Conditions</label>
                                    {!! $contract->terms_and_conditions !!}
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

        <div class="col-md-6 mx-auto">
            <label class="form-label">Daily Deposits</label>
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
                                @foreach($deposits as $key => $dep)
                                <?php
                                    $total_dp += $dep->amount;
                                ?>
                                <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                    <td style="text-align: right; border-left: 1px solid gray;">{{date('d-m-Y', strtotime($dep->date))}}</td>
                                    <td class="total" style=" text-align: right; border-left: 1px solid gray;">{{number_format($dep->amount/$ex_rate, 2, '.', ',')}}</td>
                                    <td style="text-align: right; border-left: 1px solid gray;">{{date('d-m-Y H:i:s', strtotime($dep->created_at))}}</td>
                                </tr>
                                @endforeach
                                <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                    <td class="desc"><b>{{trans('navmenu.total')}}</b></td>
                                    <td class="total" style="text-align: right; border-left: 1px solid gray;"><b>{{number_format(($total_dp)/$ex_rate, 2, '.', ',')}}</b></td>
                                    <td style="border-left: 1px solid gray;"></td>
                                </tr>
                                <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                    <td class="desc"><b>{{trans('navmenu.balance')}}</b></td>
                                    <td class="total" style="text-align: right; border-left: 1px solid gray;"><b>{{number_format(($total-$total_dp)/$ex_rate, 2, '.', ',')}}</b></td>
                                    <td style="border-left: 1px solid gray;"></td>
                                </tr>
                            </tbody>
                        </table>
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
                        @if(!is_null($sale))
                        <input type="hidden" name="customer_id" value="{{$sale->customer_id}}">
                        <input type="hidden" name="invoice_id" value="{{$sale->id}}">
                    
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


     <!-- Modal -->
    <div class="modal fade" id="terminateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terminate Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="row g-3" method="POST" id="term-form" action="{{ url('terminate-contract') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="contract_id" value="{{$contract->id}}">
                        <div class="col-md-12">
                            <label class="form-label">Termination Reason <span style="color: red;">*</span></label>
                            <input type="text" name="termination_reason" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-primary btn-sm" onclick="confirmTerminate('<?php echo encrypt($contract->id) ?>')">
                            Terminate Contract</a>
                        <button type="submit" class="btn btn-outline-warning btn-sm" id="btn-submit">{{trans('navmenu.btn_cancel')}}</button>
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
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-invoice");
            var filename = "<?php echo 'Contract_'.$contract->cuid.'_'.$contract->created_at; ?>";
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
