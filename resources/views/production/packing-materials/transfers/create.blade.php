@extends('layouts.prod')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{url('pm-transfers')}}">PM Transfers</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-xl-12 col-md-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="orderform" method="POST" action="{{route('pm-transfers.store')}}">
                            @csrf
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="bx bx-cart"></i> Add Item</button>
                            </div>
                            <input type="hidden" name="id" value="{{$pmt->id}}">
                            <div class="col-sm-3">
                                <label for="shop_id" class="form-label">{{trans('navmenu.source_shop')}} <span style="color: red; font-weight: bold;">*</span> </label>
                                <select name="shop_id" class="form-select form-select-sm mb-1">
                                    <option value="{{$shop->id}}">{{$shop->name}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="destin_id" class="form-label"> {{trans('navmenu.destin_shop')}}<span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="destin_id" required>
                                    <option value="">{{trans('navmenu.select_destin_shop')}}</option>
                                    @foreach($shops as $cshop)
                                    <option value="{{$cshop->id}}">{{$cshop->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="date_set" class="form-label">{{trans('navmenu.date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i> 
                                    <input type="text" name="pm_transfer_date" value="{{date('Y-m-d', strtotime($pmt->pm_transfer_date))}}" id="datepicker" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}"  aria-describedby="calendar">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="reason" class="form-label">{{trans('navmenu.reason')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <textarea name="reason" rows="1" class="form-control form-control-sm mb-1" required placeholder="{{trans('navmenu.hnt_transfer_reason')}}"></textarea>
                            </div>
                            <div class="col-md-12 pt-0" style="margin-top: 5px; border-top: 2px solid #BBDEFB;">
                                <div id="msg"></div>
                                <table border="0" cellspacing="0" cellpadding="0" class="table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="Item">{{trans('navmenu.item_name')}}</th>
                                                <th class="qty">{{trans('navmenu.qty')}}</th>
                                                <th class="qty">{{trans('navmenu.unit_cost')}}</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $key => $item)
                                            <tr id="temps">
                                                <td>{{$key + 1}}</td>
                                                <td class="item">{{$item->name}}</td>
                                                <td class="qty">
                                                    <input class="edit" id="qty_{{$item->id}}" type="number" name="qty" value="{{$item->qty+0}}" style="text-align: center;">
                                                </td>
                                                <td class="qty" style="text-align: center;">{{$item->unit_cost+0}}</td>
                                                <td><a href="#"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">  
                                    <button type="submit" class="btn btn-primary btn-sm mb-1"><i class="fa fa-send" ></i> {{trans('navmenu.btn_submit')}}
                                        </button>
                                    <a href="{{url('cancel-pmt/'.encrypt($pmt->id))}}" class="btn btn-warning btn-sm mr-1 card-subtitle" id="btn-cancel"><i class="fa fa-close"></i> {{trans('navmenu.btn_cancel')}}</a>
                                </div>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>      
    </div>


    <!-- Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('pm-transfer-items.store') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="pm_transfer_id" value="{{$pmt->id}}">
                        <div class="col-md-6">
                            <label>{{trans('navmenu.name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select name="packing_material_id" class="form-select form-select-sm mb-1" required>
                                <option value="">--Select Packing Material</option>
                                @foreach($materials as $material)
                                <option value="{{$material->id}}">{{$material->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" min="0" name="qty" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
         // Save data
        $(".edit").focusout(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // $(this).removeClass("editMode");
            var id = this.id;
            var split_id = id.split("_");
            var field_name = split_id[0];
            var edit_id = split_id[1];
            var value = $(this).val();

            $.ajax({
                url: "{{ url('update-pm-transfer-item') }}",
                type: 'POST',
                data: { qty: value, id:edit_id },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 1300);
                    }else{
                        $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                                // location.reload();
                                
                            });
                        }, 1300);
                    }
                }
            });
        });
    });
</script>
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pm_transfer_date"]')

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
