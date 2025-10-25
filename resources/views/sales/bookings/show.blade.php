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
          title: "Are you sure you want to cancel this booking",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, cancel It",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('cancel-booking') }}/"+id;
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
                    <li class="breadcrumb-item">bookings & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('bookings') }}">Bookings</a></li>
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
                            <div class="row">
                                <div class="col-md-10">
                                    @if(!$booking->is_deleted)
                                    <a onclick="javascript:savePdf()" class="btn btn-outline-success btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="fa fa-download"></i> {{trans('navmenu.download')}} PDF</a>
                                    <a class="btn btn-outline-primary btn-sm" style="margin-left: 2px; padding-top: 5px;" href="{{ route('bookings.edit', encrypt($booking->id)) }}"><i class="fa fa-edit"></i>Update</a>
                                    <form id="delete-form" method="POST" action="{{ route('bookings.destroy', encrypt($booking->id)) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <a class="btn btn-outline-danger btn-sm" href="#" href="#" onclick="confirmCancel('<?php echo encrypt($booking->id); ?>')"><i class="fa fa-x" style="color: red;"></i> Cancel Booking</a>
                                    </form>
                                    @if(!is_null($sale) && Auth::user()->can('create-sale-payment') && $booking->status != 'Paid')
                                    <a href="#"  class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px; padding-top: 5px"><i class="fa fa-money"></i> {{trans('navmenu.add_payment')}}</a>
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
                                                @if(!is_null($shop->logo_location))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
                                                </figure>
                                                @endif
                                            </td>
                                            <td>
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">
                                                            <span style="font-size: 18px">{{$shop->name}}</span><br>
                                                            <small>{{$shop->short_desc}}</small><br> 
                                                            <p>
                                                                {{$shop->postal_address}} {{$shop->physical_address}}<br> @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} {{$shop->country}} <br>  Tel: <b>{{$shop->tel}}</b> | <b>{{$shop->mobile}}</b> WhatsApp : <b>{{$shop->whatsapp}}</b><br> Email: <b>{{$shop->email}}</b> Website: <b>{{$shop->website}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="background: #037c1e; padding-left: 15px;  border-radius: 30px; text-align: center;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;">Booking Details</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 customer mb-0" style="padding-left: 50px; padding-right: 50px;">
                                    <table class="table table-bordered" style="width: 100%">
                                        <tr>
                                            <th colspan="4" style="font-size: 14px; border-bottom: 1px solid gray;">Booking ID : <b>{{$booking->buid}}</b></th>
                                        </tr>
                                        <tr>
                                            <td>Created At</td>
                                            <td><b>{{date('d/m/Y H:i:s A', strtotime($booking->created_at))}}</b></td>
                                            <td>Booking Status : </td>
                                            <td><b>{{$booking->status}}</b></td>
                                        </tr>
                                        @if(!is_null($payment))
                                        <tr>
                                            <td>Payment Date: </td>
                                            <td><b>{{date('d/m/Y', strtotime($payment->pay_date))}}</b></td>
                                            <td>Payment Method : </td>
                                            <td><b>{{$payment->pay_mode}}</b></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Check In Date : </td>
                                            <td><b>{{date('d/m/Y', strtotime($booking->check_in_date))}}</b></td>
                                            <td>Check Out Date : </td>
                                            <td><b>{{date('d/m/Y', strtotime($booking->check_out_date))}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Booking Type</td>
                                            <td><b>{{$booking->booking_type}}</b></td>
                                            <td>Agent</td>
                                            <td><b>@if(!is_null($bagent)) {{$bagent->name}}@endif</b></td>
                                        </tr>
                                        <tr>
                                            <th colspan="4" style="border-bottom: 1px solid gray;"><span>Customer Info</span></th>
                                        </tr>
                                        <tr>
                                            <td>Customer Name :</td>
                                            <td><b>{{$customer->name}}</b></td>
                                            <td>Address : </td>
                                            <td>{{$customer->physical_address}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.cust_id_type')}} :</td>
                                            <td><b>
                                                @foreach($custids as $cid)
                                                @if($cid['id'] == $customer->cust_id_type)
                                                    {{$cid['name']}}
                                                @endif
                                                @endforeach
                                                </b>
                                            </td>
                                            <td>{{trans('navmenu.id_number')}} : </td>
                                            <td>{{$customer->custid}}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile: </td>
                                            <td><a href="tel:{{$customer->phone}}">{{$customer->phone}}</a></td>
                                            <td>Email : </td>
                                            <td><a href="mailto:{{$customer->email}}" style="text-transform: lowercase;">{{$customer->email}}</a></td>
                                        </tr>
                                        <tr>
                                            <td>TIN : </td><td><b>{{$customer->tin}}</b></td>
                                            <td>VRN : </td><td><b>{{$customer->vrn}}</b></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12" style="padding-left: 50px; padding-right: 50px;">
                                    <label class="form-label">Booked Rooms</label>
                                    <table class="items mt-0" style="width: 100%;">
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th>Room</th>
                                            <th>Room Type</th>
                                            <th>Description</th>
                                            <!-- <th style="text-align: center;">Capacity</th> -->
                                        </tr>
                                        @foreach($bookedrooms as $index => $broom)
                                        <tr>
                                            <td style="text-align: center;">{{$index + 1}}</td>
                                            <td>{{$broom->room_no}} @if(!is_null($broom->name))- {{$broom->name}} @endif</td>
                                            <td>{{$broom->type}}</td>
                                            <td>{{$broom->description}}</td>
                                            <!-- <td style="text-align: center;">{{$broom->capacity}}</td> -->
                                        </tr>
                                        @endforeach
                                    </table>

                                    <label class="form-label">Booked Services</label>
                                    <table class="mt-0" style="width: 100%;">
                                        <thead>
                                            <tr style="background: #037c1e; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                <th style="text-align: center; width: 3%;">#</th>
                                                <th style="text-align: right; width: 12%; border-left: 1px solid #fff;">Code</th>
                                                <th style="width: 49%;">Item Description</th>
                                                <th style="width: 3%; text-align: center; border-left: 1px solid #fff;">No. Persons/Rooms</th>
                                                <th style="text-align: center; width: 3%; border-left: 1px solid #fff;">No. Nights</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $tpersons = 0; $tdays =0  ?>
                                            @foreach($bookedservices as $key => $servitem)
                                            <?php
                                                $tpersons += $servitem->persons; 
                                                $tdays += $servitem->quantity/$servitem->persons; 
                                            ?>
                                            <tr style="border-bottom: 1px solid gray; border-left: 1px solid #037c1e; border-right: 1px solid #037c1e;">
                                                <td style="text-align: center;"> {{$key+1}} </td>
                                                <td style="text-align: right; border-left: 1px solid gray;">{{$servitem->code}}</td>
                                                <td class="desc" style="">{{$servitem->name}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{$servitem->persons}}</td>
                                                <td class="qty" style=" text-align: center; border-left: 1px solid gray;">{{$servitem->quantity/$servitem->persons}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    @if(!is_null($booking->remarks))
                                    <div>Remarks:</div>
                                    <div>{!! $booking->remarks !!}</div>
                                    @endif
                                </div>

                                <div class="col-md-12 text-center">
                                    <span style="font-size: 14px; font-weight: bold;"></span><br>
                                    <b>-----------------------------------------</b><br>
                                    <b>Customer Signatory</b>
                                </div>
                                @if(!is_null($booking->terms_and_conditions))
                                <div class="col-md-12">
                                    <label class="form-label">Terms & Conditions</label>
                                    {!! $booking->terms_and_conditions !!}
                                </div>
                                @endif
                                @if($settings->show_end_note)
                                <div class="col-md-12 text-center" style="border-top: 1px solid gray;"></div>
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
                        @if($sale->sale_type == 'cash')
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" step="any" name="amount" required value="{{ $booking->total_price }}" placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @else
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @endif
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
            var filename = "<?php echo 'Booking_'.$booking->buid.'_'.$booking->created_at; ?>";
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
