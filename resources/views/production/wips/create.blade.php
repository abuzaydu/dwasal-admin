@extends('layouts.prod')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/wip.js') }}"></script>
    <script>
        function confirmCancel() {Swal.fire({
                title: 'Are you sure, You want to cancel this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Cancel',
                denyButtonText: `Don't Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href="{{url('cancel-wip')}}";
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('wip not cancelled', '', 'info')
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
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body pt-0">
                    <div class="p-2 border rounded print_invoice">
                        <form class="row g-3 needs-validation" novalidate name="wipform" method="POST" action="{{ route('prod-wips.store') }}">
                            @csrf
                            <div class="col-sm-3">
                                <label class="form-label">Date</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="date" id="date" value="{{$date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <label class="form-label">Search Item Here for Easy updating</label>
                                <input ng-model="searchKeyword" placeholder="Ente key word to search" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <table class="items mt-0" style="width: 100%; white-space: nowrap;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: left;">Item Name</th>
                                        <th style="text-align: center;">Opening WIP Qty</th>
                                        <th style="text-align: center;">Produced</th>
                                        <th style="text-align: center;">Moved to Finished</th>
                                        <th style="text-align: center;">WIP Loss/Wastage</th>
                                        <th style="text-align: center;">Closing Qty</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newwiptemp in wiptemp | filter: searchKeyword" id="temps">
                                        <td style="padding: 5; text-align: center;">@{{$index + 1}}</td>
                                        <td style="padding: 5">@{{newwiptemp.name}}</td>
                                        <td style="text-align: center; padding: 5;">@{{newwiptemp.bf_balance}}</td>
                                        <td style="text-align: center;"><input type="number" min="0" step="any" string-to-number ng-model="newwiptemp.produced" ng-blur="updatewipTemp(newwiptemp)" style="text-align:center; width: 60px;" autocomplete="off"></td>
                                        <td style="text-align: center;"><input type="number" min="0" step="any" string-to-number ng-model="newwiptemp.finished_qty" ng-blur="updatewipTemp(newwiptemp)" style="text-align:center; width: 60px;" autocomplete="off"></td>
                                        <td style="text-align: center;"><input type="number" min="0" step="any" string-to-number ng-model="newwiptemp.wip_damage" ng-blur="updatewipTemp(newwiptemp)" style="text-align:center; width: 60px;" autocomplete="off"></td>
                                        <td style="text-align: center; padding: 5;">@{{newwiptemp.closing_qty}}</td>
                                        <td style="text-align: center;"><a href="#" ng-click="removewipTemp(newwiptemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" name="myButton" class="btn btn-primary btn-sm">Create</button>
                                <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');
    
            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>