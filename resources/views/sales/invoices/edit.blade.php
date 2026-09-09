@extends('layouts.app')
@section('page-styles')
  <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('an-sales') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{ route('an-sales.update', encrypt($sale->id))}}" id="pos-form">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="id" value="{{$sale->id}}">
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.customer_name')}}</label>
                            <select class="form-select form-select-sm mb-1" name="customer_id" required>
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.saledate')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="sale_date" id="sale_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{$sale->time_created}}" class="form-control form-control-sm mb-1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{trans('navmenu.sales_type')}}</label>
                            <select name="sale_type" id="sale_type" class="form-select form-select-sm mb-1" required>
                                @if($sale->sale_type == 'cash')
                                <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                @else
                                <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                @endif
                            </select>
                        </div>
                        @if($sale->sale_type == 'credit')
                        <div class="col-md-3">
                            <label class="form-label">Due date <span style="color: red; font-weight: bold;">*</span></label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="due_date" placeholder="Choose Due date" class="form-control form-control-sm mb-1" value="{{$sale->due_date}}">
                            </div>
                        </div>
                        <div class="col-md-4" id="bank-name">
                            <label class="form-label">Payment Account </label>
                            <select name="account_id" class="form-select form-select-sm mb-1">
                                <option value="">Any</option>
                                @foreach($accounts->where('type', '!=', 'Cash') as $acc)
                                @if($sale->account_id == $acc->id)
                                <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}} @endif @if(!is_null($acc->currency)) - {{$acc->currency}}@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                                @else
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}} @endif @if(!is_null($acc->currency)) - {{$acc->currency}}@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if($settings->is_filling_station)
                        <div class="col-md-3">
                            <label for="total" class="form-label">{{trans('navmenu.vehicle_no')}}</label>
                            <input type="text" class="form-control" id="vehicle_no" placeholder="{{trans('navmenu.vehicle_no')}}" name="vehicle_no" value="{{$sale->vehicle_no}}" />
                        </div>
                        @endif

                        @if($settings->is_service_per_device)
                            @if(!is_null($dsale))
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.device_number')}}</label> 
                                <select name="device_id" class="form-select form-select-sm mb-1">
                                    @foreach($devices as $device)
                                    @if($dsale->device_id == $device->id)
                                    <option value="{{$device->id}}" selected>{{$device->device_number}} - {{$device->device_name}}</option>
                                    @else
                                    <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.device_number')}}</label>
                                <select name="device_id" class="form-select form-select-sm mb-1">
                                    <option value="">{{trans('navmenu.select_device')}}</option>
                                    @foreach($devices as $device)
                                    <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if($settings->is_rental_service)
                            <div class="col-sm-3">
                                <label class="control-label">Rent End Date</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="rent_end_date" value="{{$sale->rent_end_date}}" id="rent_end_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            @endif
                        @endif
                        <div class="col-md-7">
                            <label>{{trans('navmenu.comments')}}</label>
                            <textarea name="comments" rows="1" class="form-control form-control-sm mb-1">{{$sale->comments}}</textarea>
                        </div>

                         <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">
                                    Invoice note
                                    <span style="color: red; font-weight: bold;">*</span>
                                </label>
                                <div class="summernote" id="note-content">
                                    @if(!empty($sale->note))
                                        {!! $sale->note !!}
                                    @elseif(isset($notes) && !empty($notes->content))
                                        {!! $notes->content !!}
                                    @endif
                                </div>
                                <input type="hidden" name="note" id="note" value="{{ old('note', $sale->note) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a> 
                        </div>
                    </form>
                </div>
            </div>                
        </div>
    </div>
    <!--end row-->
@endsection
@section('page-scripts')
    <script src="{{ asset('side/assets/js/bundle/summernote.bundle.js') }}"></script>
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            if (typeof $.fn.summernote === 'undefined') {
                console.error('Summernote plugin failed to load - check the <script> src path for summernote.bundle.js');
                return;
            }

            $('#note-content').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        $('#note').val(contents);
                    },
            
                    onInit: function() {
                        $('#note').val($('#note-content').summernote('code'));
                    }
                }
            });

            $('.note-editor .note-btn').on('click', function() {
                $(this).next().toggleClass("show");
            });

            $('#pos-form').on('submit', function() {
                $('#note').val($('#note-content').summernote('code'));
            });
        });

        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');
            var $red = document.querySelector('[name="rent_end_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            var iscred = "<?php echo $sale->sale_type; ?>";
            if (iscred == 'credit') {
                var $max = document.querySelector('[name="due_date"]');
                $max.DatePickerX.init({
                    mondayFirst: true,
                    format     : 'yyyy-mm-dd',
                    minDate    : new Date(),
                    // maxDate    : new Date()
                });
            }

            var isrental = "<?php echo $settings->is_rental_service; ?>";
            if (isrental) {
                $red.DatePickerX.init({
                    mondayFirst: true,
                    format     : 'yyyy-mm-dd',
                    // minDate    : new Date(),
                });
            }
        });
    </script>
@endsection