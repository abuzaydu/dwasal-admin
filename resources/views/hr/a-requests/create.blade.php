@extends('layouts.hr')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{ asset('assets/js/angular-1-8-3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/allowance.js') }}"></script>
    <script type="text/javascript">
        function confirmCancel(id) {
            Swal.fire({
                title: 'Are you sure, You want to cancel this arequest?',
                showDenyButton: true,
                confirmButtonText: 'Yes Cancel',
                denyButtonText: `Don't Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href="{{url('cancel-allowance')}}/"+id;
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('arequest not cancelled', '', 'info')
                }
            });
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-12 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('requisitions')}}">Requisitions</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row g-3" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="aRequestTempId('<?php echo $arequest->id; ?>')">
        <form method="POST" id="basic-form" action="{{ route('requisitions.store') }}">
            @csrf
            <input type="hidden" name="request_id" value="{{$arequest->id}}">
            <input type="hidden" name="status" value="Awaitnig for Approval">
            <div class="col-12">
                <div class="card print_invoice pt-2">
                    <div class="block-header p-3">
                        <h6 class="mb-0 text-uppercase">{{$page}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="card p-1">
                            <div class="row g-3">
                                @if ($message = Session::get('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    <i class="fa fa-info-circle"></i> {{$message}}
                                </div>
                                @endif
                                <div class="col-md-3">
                                    <label class="form-label">Department <span style="color: red;">*</span>  </label>
                                    <select class="form-select form-select-sm mb-1 customer-title" name="department_id" required>
                                        <option value=""> ---Select Department---</option>
                                        @foreach($departments as $dept)
                                        <option value="{{$dept->id}}">{{$dept->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mt-0">
                                        <thead>
                                            <tr>
                                                <th style="width:5%"></th>
                                                <th style="width:45%;">Item Description</th>
                                                <th style="width: 10%;">Item Category</th>
                                                <th style="width:10%">No. Of Days</th>
                                                <th style="text-align: center; width: 5%;">Quantity</th>
                                                <th style="text-align: center; width: 10%;">Price</th>
                                                <th style="text-align: center; width: 15%">Sub Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="item-row" ng-repeat="newarequesttemp in arequesttemp ">
                                                <td><a class="text-danger" href="javascript:;" ng-click="removeRequestTemp(newarequesttemp.id)" title="Remove row"><i class="fa fa-close"></i></a> @{{$index + 1}}.</td>
                                                <td class="item-name">
                                                    <textarea type="text" ng-model="newarequesttemp.item_description" ng-blur="updateRequestTemp(newarequesttemp)" placeholder="Enter Item description" rows="2"></textarea>
                                                </td>
                                                <td class="item-name">
                                                    <select ng-model="newarequesttemp.item_category" ng-change="updateRequestTemp(newarequesttemp)">
                                                        <option value="Allowance">Allowance</option>
                                                        <option value="Tools">Tools</option>
                                                        <option value="Transport">Transport</option>
                                                        <option value="Risk Assesment">Risk Assesment</option>
                                                    </select>
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="number" name="no_of_days" min="0" step="any" string-to-number ng-model="newarequesttemp.no_of_days" ng-blur="updateRequestTemp(newarequesttemp)" class="qty" style="text-align: center; width: 80px;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="number" name="quantity" min="0" step="any" string-to-number ng-model="newarequesttemp.quantity" ng-blur="updateRequestTemp(newarequesttemp)" class="qty" style="text-align: center; width: 80px;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="number" name="price" min="0" step="any" string-to-number ng-model="newarequesttemp.price" ng-blur="updateRequestTemp(newarequesttemp)" class="cost" style="text-align: center; width: 100px;">
                                                </td>
                                                <td style="text-align: center;"><span class="price">@{{newarequesttemp.total}}</span></td>
                                            </tr>
                                            <tr class="hiderow">
                                                <td colspan="7"><a href="javascript:;" ng-click="addRequestTemp(arequest)" title="Add a row">Add a row</a></td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" style="text-align: right;">
                                                    <span class="btn btn-sm btn-outline-primary mb-2" ng-click="getData()"><i class="fa fa-refresh"></i> Update</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-8">
                                    
                                </div>
                                <div class="col-md-4">
                                    <table class="items mt-0" style="font-size: 14px;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" class="total-line">Total</td>
                                                <td class="total-value">
                                                    <div id="subtotal">@{{ sum(arequesttemp) | number:2}}</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 text-center text-md-end">
                <button type="submit" class="btn btn-sm btn-primary" ><i class="fa fa-print me-2"></i>Submit Request</button>
                <button type="button" onclick="confirmCancel('<<?php echo encrypt($arequest->id); ?>')" class="btn btn-sm btn-danger"><i class="fa fa-close me-2"></i>Cancel</button>
            </div>
        </form>
    </div> <!-- .row end -->
@endsection