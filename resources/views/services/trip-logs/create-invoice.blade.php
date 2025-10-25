@extends('layouts.inv')
@section('page-styles')

    <!-- CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
@endsection
@section('content')
    
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{ url('create-invoice-for-trips/'.encrypt($customer->id))}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-sm-12">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded print_invoice">
                        <form class="row g-3"  name="saleform" method="POST" action="{{ url('trips-invoice') }}">
                            @csrf
                            <div class="col-sm-4">
                                <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                <select name="customer_id" id="customer_id" required class="form-select form-select-sm mb-1">
                                    <option value="{{$customer->id}}">{{$customer->name}}</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label"> Invoice Date <span style="color: red;">*</span></label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="sale_date" id="trip-start-date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label">Due Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="due_date" id="trip-end-date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                </div>
                            </div>
                            @if($settings->is_vat_registered)
                            <div class="col-md-2">
                                <label class="form-label">Add VAT</label>
                                <select class="form-select form-select-sm mb-1" name="with_vat">
                                    <option value="no">{{trans('navmenu.no')}}</option>
                                    <option value="yes">{{trans('navmenu.yes')}}</option>
                                </select>
                            </div>
                            @endif
                            <div class="col-sm-12">
                                <label class="form-label">Select Trips to Invoice <span style="color: red;">*</span></label>
                                <h6 class="mb-0"><input type="checkbox" name="sample" id="check-all" onclick="selectAll('<?php echo $triplogs->count(); ?>')"/> All Trips</h6>
                                <hr>
                                @foreach ($triplogs as $pkey => $value)
                                <label style="padding-bottom: 5px; page-break-inside:avoid; page-break-after:auto; font-weight:normal;">
                                    <input type="checkbox" name="trips[]" value="{{$value->id}}" 
                                    @if(in_array($value->id, $currTrips)) checked="checked" @endif tabindex="0" id="{{'trip-'.$pkey}}">
                                    {{ $value->trip_title }} ({{$value->from}} - {{$value->to}} Date : {{$value->trip_date}}}}) Price ({{$currency}}) {{number_format($value->trip_price)}}</label><br>
                                @endforeach
                                <br>
                            </div>
                            <div class="col-sm-12">
                                <label for="employee" class="form-label">{{trans('navmenu.comments')}}</label>
                                <input type="text" class="form-control form-control-sm mb-1" name="comments" placeholder="Enter Comments/Note to invoice" />
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" name="myButton" class="btn btn-success btn-sm">Create Invoice</button>
                                <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- datetimepicker jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function(){
            $("#trip-start-date").each(function () {
                $(this).datetimepicker({
                    timepicker:false,
                    formatDate:'Y-m-d',
                    // minDate:'-1970/01/02',
                    maxDate:'+1970/01/01'
                });
            });

            $("#trip-end-date").each(function () {
                $(this).datetimepicker({
                    timepicker:false,
                    formatDate:'Y/m/d',
                    minDate:'-1970/01/01',
                    // maxDate:'+1970/01/01'
                });
            });
        });
    </script>
@endsection


    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function selectAll(elem) {
            var feature = document.getElementById('check-all');
            var trips = elem;
            if (feature.checked) {
                for(i=0; i<trips; i++) {
                    var item = document.getElementById('trip-'+i);
                    item.checked = true;
                }
            }else{
                for(i=0; i<trips; i++) {
                    var item = document.getElementById('trip-'+i);
                    item.checked = false;
                }
            }
        }
        
        function savePdf() {
          const element = document.getElementById("print-permission-page");
          var filename = "User Permissions";
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