@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/labourcost.js') }}"></script>
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
                window.location.href = "{{ url('cancel-labourcost') }}";
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
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row print_invoice">
                        <div class="col-md-4">
                            <select ng-model="production_stage_id" ng-change="addItem()" ng-options="stage.id as stage.stage for stage in stages" class="form-select form-select-sm mb-1">
                                <option value="">---Select Cost Item---</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 border rounded">
                                <table class="items mt-0"  style="width: 100%;  display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">Item Name</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="labourcosttemp in labourcosttemps" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td style="text-align: left;">@{{labourcosttemp.stage}}</td>
                                            <td style="text-align: center;"><input type="number" name="quantity" string-to-number ng-blur="updateItemTemp(labourcosttemp)" ng-model="labourcosttemp.quantity" min="0" step="any" value="@{{labourcosttemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;"><input type="number" name="unit_cost" string-to-number ng-blur="updateItemTemp(labourcosttemp)" ng-model="labourcosttemp.unit_cost" min="0" step="any" value="@{{labourcosttemp.unit_cost}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;"><input type="number" name="total" string-to-number ng-blur="updateItemTemp(labourcosttemp)" ng-model="labourcosttemp.total" min="0" step="any" value="@{{labourcosttemp.total}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;"><a href="#" ng-click="removeItemTemp(labourcosttemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="3"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumItems(labourcosttemps) | number:2}}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('prod-labour-costs.store') }}" class="row g-3 pt-3">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label">Date </label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="date" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="remarks" placeholder="Enter Your Remarks" class="form-control form-control-sm mb-3" required>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                    <a href="javascript:;" onclick="return confirmDelete()" class="btn btn-warning btn-sm float-end" style="margin-right: 5px;">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
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
