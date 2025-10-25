@extends('layouts.pr')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{ asset('js/angular-1-8-3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/payroll.js') }}"></script>
    <script>
        function confirmCancel() {Swal.fire({
                title: 'Are you sure, You want to cancel this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Cancel',
                denyButtonText: `Don't Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href="{{url('cancel-payroll')}}";
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Payroll not cancelled', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('payrolls')}}">Payrolls</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body pt-0">
                    <div class="p-2 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="payrollform" method="POST" action="{{ url('payrolls/create') }}">
                            @csrf
                            <div class="col-md-6">
                                <label for="validationCustom09" class="form-label">Month of Payment</label>
                                <select class="form-select form-select-sm" name="month" required onchange="this.form.submit()">
                                    @foreach($data as $d)
                                    @if($d['month'].' '.$d['year'] == $curmonth)
                                    <option selected value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                                    @else
                                    <option value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </form>
                            <div class="col-md-12">
                                <table class="table table-striped" style="width: 100%; white-space: nowrap;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: left;">Employee Name</th>
                                        <th style="text-align: center;">Basic Salary</th>
                                        <th style="text-align: center;">Bonuses</th>
                                        <th style="text-align: center;">Penalty</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newpayrolltemp in payrolltemp" id="temps">
                                        <td style="padding: 5; text-align: center;">@{{$index + 1}}</td>
                                        <td style="padding: 5">@{{newpayrolltemp.fname}} @{{newpayrolltemp.lname}}</td>
                                        <td style="text-align: center; padding: 5;">@{{newpayrolltemp.basic_pay_monthly | number:2}}</td>
                                        <td style="text-align: center;"><input type="number" name="bonuses" min="0" step="any" string-to-number ng-model="newpayrolltemp.bonuses" ng-blur="updatePayrollTemp(newpayrolltemp)" style="text-align:center;" autocomplete="off"></td>
                                        <td style="text-align: center;"><input type="number" name="bonuses" min="0" step="any" string-to-number ng-model="newpayrolltemp.penalty" ng-blur="updatePayrollTemp(newpayrolltemp)" style="text-align:center;" autocomplete="off"></td>
                                        <td style="text-align: center;"><a href="#" ng-click="removePayrollTemp(newpayrolltemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a></td>
                                    </tr>
                                </table>
                            </div>

                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('payrolls.store') }}">
                            @csrf
                                <input type="hidden" name="month" value="{{$curmonth}}">
                                <div class="col-sm-6">
                                    <button type="submit" name="myButton" class="btn btn-primary btn-sm">Create</button>
                                    <button onclick="confirmCancel()" type="button" class="btn btn-outline-danger btn-sm">Cancel</button>
                                </div>
                            </form>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection