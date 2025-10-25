@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/foodprods.js') }}"></script>
<script type="text/javascript">
    function confirmDelete() {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Cancel It",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('cancel-food-production') }}";
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }
</script>
<style type="text/css">
    .gridCard{
        padding-top: 10px;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .gridCard::-webkit-scrollbar {
      display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .gridCard {
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
    }

    .gridScale{
        width : 8rem; 
        height: 5rem;
    }

    .gridName{
         font-size: 18;
         white-space: nowrap; 
         overflow: hidden; 
         text-overflow: ellipsis; 
    }

    .qtySize{
        text-align:center; 
        height: 20px; 
        width: 10px; 
        border: 1px solid #e0e0e0;
        padding-right: 2px;
    }
</style>

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-3">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="print_invoice">
                        <form method="POST" action="{{ route('food-productions.store') }}" class="row g-1">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Food Produced</label>
                                <select id="food-type-id" name ="food_type_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Food Type</option>
                                    @foreach($foodtypes as $key => $ftype)
                                    @if($ftype->id == Session::get('ftype_id'))
                                    <option value="{{$ftype->id}}" selected>{{$ftype->name}}</option>
                                    @else
                                    <option value="{{$ftype->id}}">{{$ftype->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <div id="msg"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="date" value="{{$date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Raw Materials</label>
                                <select ng-model="rm_id" ng-change="addRM()" ng-options="rm.id as rm.name for rm in rms" class="form-select form-select-sm mb-1">
                                    <option value="">---Select Raw Materials---</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <table  class="items mt-0"  style="width: 100%;  display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">{{trans('navmenu.material_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th>UOM</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="rmusedtemp in rmusedtemps" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td>@{{rmusedtemp.name}}</td>
                                            <td><input type="number" name="quantity" ng-blur="updateRMTemp(rmusedtemp)" string-to-number ng-model="rmusedtemp.quantity" min="0" step="any" value="@{{rmusedtemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off"></td>
                                            <td style="text-align: center;">@{{rmusedtemp.basic_uom}}</td>
                                            <td style="text-align:center;">@{{rmusedtemp.unit_cost | number:2}}</td>
                                            <td style="text-align:center;">@{{rmusedtemp.total | number:2}}</td>
                                            <td><a href="#" ng-click="removeRMTemp(rmusedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="4"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumRM(rmusedtemps) | number:2}}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Comments</label>
                                <input type="text" class="form-control form-control-sm mb-1" name="comments">
                            </div>
                            <div class="col-md-12">
                                <input type="submit" name="submit" class="btn btn-success btn-sm">
                                <a href="javascript:;" onclick="return confirmDelete()" class="btn btn-warning btn-sm" style="margin-right: 5px;">Cancel</a>
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
    <script type="text/javascript">
        $('#food-type-id').on('change', function() {
            var typeid = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('set-food-type') }}",
                type: 'POST',
                data: { ftype_id: typeid },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                                window.location.reload();
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
    </script>
@endsection
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>