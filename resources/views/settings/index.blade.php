@extends('layouts.prof')
<script type="text/javascript">
    function showHideForm(elem) {
        var newform = document.getElementById('add-currency');
        var curlist = document.getElementById('currencies');
        var newbtn = document.getElementById('new-btn');
        if (elem == 'show') {
            newform.style.display = 'block';
            newbtn.style.display = 'none';
            curlist.style.display = 'none';
        }else{
            newform.style.display = 'none';
            newbtn.style.display = 'block';
            curlist.style.display = 'block';
        }
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-9 col-md-9 col-sm-12">
                <!-- <a target="_blank" href="{{ route('settings.edit', encrypt($settings->id)) }}" class="btn btn-danger btn-sm mb-1">Daily Closing Time Settings</a> -->
                <a target="_blank" href="{{ url('invoice-notes') }}" class="btn btn-success btn-sm mb-1">Invoice Note Settings</a>
                <a target="_blank" href="{{ url('invoice-template-settings')}}" class="btn btn-primary btn-sm mb-1">Invoice Template Settings</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div>
                        <h5 class="card-title">Currencies <a href="#" onclick="showHideForm('show')" class="btn btn-success btn-sm float-end" id="new-btn" style="margin: 2px;"><i class="bx bxs-plus-square"></i> Add Currency</a></h5>
                    </div>
                    <hr/>
                    <div id="add-currency" style="display: none;">
                        <form class="row g-3" method="POST" action="{{ url('set-currency') }}">
                            @csrf
                            <div class="col-md-6">
                                <label>{{trans('navmenu.currency')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="select2 form-select form-select-sm " name="code" onchange="this.form.submit()" required style="width: 100%;">
                                    <option value="">Select Currency</option>
                                    @foreach($list as $key => $cur)
                                    <option value="{{$key}}">{{$cur['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-warning btn-sm float-end" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="row row-cols-auto g-3" id="currencies">
                        @foreach($shopcurrencies as $sc)
                        @if($sc->is_default)
                        <div class="col">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm">{{$sc->code}} (Default)</button>
                            </div>
                        </div>
                        @else
                        <div class="col">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <button type="button" class="btn btn-secondary">{{$sc->code}}</button>
                                
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item" href="{{ url('make-default-currency/'.encrypt($sc->id))}}">Make Default</a>
                                    <a class="dropdown-item" href="{{ url('rem-currency/'.encrypt($sc->id))}}">Remove Currency</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach    
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form class="form" method="POST" action="{{url('change-btype')}}">
                        @csrf
                        <div class="form-group">
                            <label for="inputName" class="form-label">{{trans('navmenu.business_type')}}</label>
                            <select name="business_type_id"  onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-1">
                              @foreach($btypes as $key => $type)
                              @if($btype->id == $type->id)
                              <option value="{{$type->id}}" selected>{{$type->id}}. {{$type->type}}</option>
                              @else
                              <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                              @endif
                              @endforeach
                            </select>
                        </div> 
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" action="{{ route('settings.update', encrypt($settings->id)) }}" method="POST">
                        <!-- Horizontal Form -->
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="is_inv_temp_update" value="0">
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_multi_currency')}}</label>
                            <select name="allow_multi_currency" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                                @if($settings->allow_multi_currency)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.enable_cpos')}}</label>
                            <select name="enable_cpos" class="form-select form-select-sm mb-1 ">
                                @if($settings->enable_cpos)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.sp_mindays')}}</label>
                            <input type="number" name="sp_mindays" class="form-control form-control-sm mb-1" value="{{$settings->sp_mindays}}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Allow More Product Descriptions</label>
                            <select name="allow_more_product_desc" class="form-select form-select-sm mb-1 ">
                                @if($settings->allow_more_product_desc)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Allow to change Item Seling Price For All Stores</label>
                            <select name="change_price_for_all_store" class="form-select form-select-sm mb-1 ">
                                @if($settings->change_price_for_all_store)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Enable Sales/Invoice Approvals</label>
                            <select name="enable_sale_approval" class="form-select form-select-sm mb-1 ">
                                @if($settings->enable_sale_approval)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Enable Sales With Low Stock</label>
                            <select name="sale_with_low_stock" class="form-select form-select-sm mb-1 ">
                                @if($settings->sale_with_low_stock)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Allow to initiate STO Request after Invoice Created</label>
                            <select name="allow_initiate_sto_from_invoice" class="form-select form-select-sm mb-1 ">
                                @if($settings->allow_initiate_sto_from_invoice)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_sp_less_bp')}}</label>
                            <select name="allow_sp_less_bp" class="form-select form-select-sm mb-1 ">
                                @if($settings->allow_sp_less_bp)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.always_sell_old_first')}}</label>
                            <select name="always_sell_old" class="form-select form-select-sm mb-1 ">
                                @if($settings->always_sell_old)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_retail_with_wholesale')}}</label>
                            <select name="retail_with_wholesale" class="form-select form-select-sm mb-1 ">
                                @if($settings->retail_with_wholesale)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_categorized')}}</label>
                            <select name="is_categorized" class="form-select form-select-sm mb-1 ">
                                @if($settings->is_categorized)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Record Discount By Percentage</label>
                            <select name="discount_by_percent" class="form-select form-select-sm mb-1 ">
                                @if($settings->discount_by_percent)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_unit_discount')}}</label>
                            <select name="allow_unit_discount" class="form-select form-select-sm mb-1 ">
                                @if($settings->allow_unit_discount)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.allow_exp_date')}}</label>
                            <select name="enable_exp_date" class="form-select form-select-sm mb-1 ">
                                @if($settings->enable_exp_date)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_service_per_device')}}</label>
                            <select name="is_service_per_device" class="form-select form-select-sm mb-1">
                                @if($settings->is_service_per_device)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Is Rental Service?</label>
                            <select name="is_rental_service" class="form-select form-select-sm mb-1">
                                @if($settings->is_rental_service)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Is Hotel Or Lodge?</label>
                            <select name="is_hotel" class="form-select form-select-sm mb-1">
                                @if($settings->is_hotel)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Enable to Record Trip Logs</label>
                            <select name="enable_trip_logs" class="form-select form-select-sm mb-1">
                                @if($settings->enable_trip_logs)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Show Service QTY & Price in Statement</label>
                            <select name="show_qty_in_stmt" class="form-select form-select-sm mb-1">
                                @if($settings->show_qty_in_stmt)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.show_discounts')}}</label>
                            <select name="show_discounts" class="form-select form-select-sm mb-1">
                                @if($settings->show_discounts)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.show_bd')}}</label>
                            <select name="show_bd" class="form-select form-select-sm mb-1">
                                @if($settings->show_bd)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Show Declaration</label>
                            <select name="show_declaration" class="form-select form-select-sm mb-1">
                                @if($settings->show_declaration)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Show Authorization Signatory</label>
                            <select name="show_authorization_sign" class="form-select form-select-sm mb-1">
                                @if($settings->show_authorization_sign)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.show_end_note')}}</label>
                            <select name="show_end_note" class="form-select form-select-sm mb-1">
                                @if($settings->show_end_note)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Always Print Invoice/Order </label>
                            <select name="always_print_invoice" class="form-select form-select-sm mb-1">
                                @if($settings->always_print_invoice)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_filling_station')}}</label>
                            <select name="is_filling_station" class="form-select form-select-sm mb-1">
                                @if($settings->is_filling_station)
                                <option value="1">Yes</option>
                                <option value="0">No</option>                
                                @else
                                <option value="0">No</option>  
                                <option value="1">Yes</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Enable Expense Payment Approval</label>
                            <select name="enable_exp_pay_approval" class="form-select form-select-sm mb-1">
                                @if($settings->enable_exp_pay_approval)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.is_vat_registered')}}</label>
                            <select name="is_vat_registered" class="form-select form-select-sm mb-1">
                                @if($settings->is_vat_registered)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="tax_rate" class="form-label">{{trans('navmenu.tax_rate')}}(%)</label>
                            <input type="tel" class="form-control form-control-sm mb-1" id="tax_rate" name="tax_rate" value="{{$settings->tax_rate}}" placeholder="{{trans('navmenu.hnt_tax_rate')}}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Always Set VAT by Default</label>
                            <select name="set_vat_by_default" class="form-select form-select-sm mb-1">
                                @if($settings->set_vat_by_default)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group  col-md-6">
                            <label class="form-label">{{trans('navmenu.allow_to_estmate_wht')}}</label>
                            <select name="estimate_withholding_tax" class="form-select form-select-sm mb-1">
                                @if($settings->estimate_withholding_tax)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.enable_barcode')}}</label>
                            <select name="use_barcode" class="form-select form-select-sm mb-1">
                                @if($settings->use_barcode)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.generate_barcode')}}</label>
                            <select name="generate_barcode" class="form-select form-select-sm mb-1">
                                @if($settings->generate_barcode)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Is Manufacturing With Merchandizing?</label>
                            <select name="is_manuf_with_merch" class="form-select form-select-sm mb-1">
                                @if($settings->is_manuf_with_merch)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Is Livestock?</label>
                            <select name="is_livestock" class="form-select form-select-sm mb-1">
                                @if($settings->is_livestock)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Is Manufacturing With Service?</label>
                            <select name="is_manufacturing_with_service" class="form-select form-select-sm mb-1">
                                @if($settings->is_manufacturing_with_service)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Use Production Module</label>
                            <select name="use_production_module" class="form-select form-select-sm mb-1">
                                @if($settings->use_production_module)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Enable Packaging</label>
                            <select name="enable_packaging" class="form-select form-select-sm mb-1">
                                @if($settings->enable_packaging)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Enable HR & Payroll Module</label>
                            <select name="enable_hr_payroll_module" class="form-select form-select-sm mb-1">
                                @if($settings->enable_hr_payroll_module)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Is Health Center</label>
                            <select name="is_health_center" class="form-select form-select-sm mb-1">
                                @if($settings->is_health_center)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-12 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.btype_desc')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    @foreach($btypes as $type)
                      <div class="col-md-12">
                        @if(app()->getLocale() == 'en')
                        <h6 class="mb-0 text-uppercase">{{$type->type}}</h6>
                        <p>{{$type->description}}</p>
                        @else
                        <h6 class="mb-0 text-uppercase">{{$type->type_sw}}</h6>
                        <p>{{$type->description_sw}}</p>
                        @endif
                      </div>
                      @endforeach
                </div>
            </div>
            @if($settings->use_barcode)
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.barcode_settings')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <form class="form" method="POST" action="{{ url('update-bsettings')}}">
                        @csrf
                        <div class="form-body">
                            <div class="form-group col-md-12 text-center">
                                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($code, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}" alt="barcode" />
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_type')}} </label>
                                <select class="form-select form-select-sm mb-1" name="code_type">
                                    <option>{{$bsetting->code_type}}</option>
                                    <option value="">{{trans('navmenu.select_barcode_type')}}</option>
                                    <option>C39</option>
                                    <option>C39+</option>
                                    <option>C39E</option>
                                    <option>C39E+</option>
                                    <option>I25</option>
                                    <option>I25+</option>
                                    <option>C128</option>
                                    <option>C128A</option>
                                    <option>C128B</option>
                                    <option>EAN8</option>
                                    <option>EAN13</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_number_length')}}</label>
                                <input type="number" name="code_length" value="{{$bsetting->code_length}}" class="form-control form-control-sm mb-1">
                            </div>

                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_width')}}</label>
                                <input type="number" min="1" max="2" name="width" value="{{$bsetting->width}}" class="form-control form-control-sm mb-1">
                            </div>

                            <div class="form-group col-md-12">
                                <label class="form-label">{{trans('navmenu.barcode_height')}}</label>
                                <input type="number" name="height" value="{{$bsetting->height}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="form-group">
                                <div class="col-md-6"> 
                                    @if($bsetting->showcode)
                                    <div class="checkbox icheck">
                                        <label>
                                            <input type="checkbox" name="showcode" value="1" checked> {{trans('navmenu.show_code')}}
                                        </label>
                                    </div>
                                    @else
                                    <div class="checkbox icheck">
                                        <label>
                                            <input type="checkbox" name="showcode" value="1"> {{trans('navmenu.show_code')}}
                                        </label>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                </div>
                            </div>
                        </div>
                      </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection