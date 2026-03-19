@extends('layouts.vms')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/expenses.js')}}"></script>
    <script>
        function confirmCancel(id) {
            Swal.fire({
                title: "{{trans('navmenu.are_you_sure')}}",
                text: "{{trans('navmenu.no_revert')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{trans('navmenu.cancel_it')}}",
                cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
                if (result.value) {
                    window.location.href = "{{url('cancel-vms-expense')}}/" + id;
                    Swal.fire(
                        "{{trans('navmenu.deleted')}}",
                        "{{trans('navmenu.cancelled')}}",
                        'success'
                    );
                }
            });
        }

        function submitPending(index) {
            document.getElementById('pexp-form-' + index).submit();
        }
    </script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a href="{{ url('vms-expenses') }}">VMS Expenses</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
           
             <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <div class="d-flex flex-wrap gap-1 justify-content-md-end justify-content-start">
                
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseTypeModal">
                        <i class="fa fa-plus me-1"></i> Add Expense Type
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addTripTypeModal">
                        <i class="fa fa-plus me-1"></i> Add Trip Type
                    </button>
                     
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

        <div class="row clearfix" id="expenseController" ng-controller="ExpenseCtrl" ng-init="initExpense('<?php echo $expense->id; ?>')">
            <div class="col-xl-12 mx-auto">
                <div class="card radius-6">
                    <div class="card-body">
                        <div class="p-4 border rounded">
                            <form class="needs-validation" id="expenseform" method="POST" action="{{route('vms-expenses.store')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="vms_expense_id" value="{{$expense->id}}">

                                <div class="row mb-1">
                                    <div class="col-sm-12" id="ermsg">
                                        @if ($errors->any())
                                            @foreach ($errors->all() as $error)
                                                <div class="alert alert-danger">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="vehicle_id" class="form-label">Vehicle <span style="color: red;">*</span></label>
                                        <select name="vehicle_id" id="vehicle_id" required class="form-select form-select-sm mb-3">
                                            <option value="">-- Select Vehicle --</option>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{$vehicle->id}}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                    {{$vehicle->plate_no}} - {{$vehicle->vehicle_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="employee_id" class="form-label">Employee <span style="color: red;">*</span></label>
                                        <select name="employee_id" id="employee_id" required class="form-select form-select-sm mb-3">
                                            <option value="">-- Select Employee --</option>
                                            @foreach($employees as $emp)
                                                <option value="{{$emp->id}}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                                    {{$emp->fname}} {{$emp->lname}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="vendor_id" class="form-label">Vendor</label>
                                        <select name="vendor_id" id="vendor_id" class="form-select form-select-sm mb-3">
                                            <option value="">-- Select Vendor --</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{$vendor->id}}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                    {{$vendor->vendor_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="trip_type_id" class="form-label">Trip Type <span style="color: red;">*</span></label>
                                        <select name="trip_type_id" id="trip_type_id" required class="form-select form-select-sm mb-3">
                                            <option value="">-- Select Trip Type --</option>
                                            @foreach($tripTypes as $tt)
                                                <option value="{{$tt->id}}" {{ old('trip_type_id') == $tt->id ? 'selected' : '' }}>
                                                    {{$tt->trip_type}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-1">
                                    <div class="col-sm-3">
                                        <label for="exp_group" class="form-label">Expense Group <span style="color: red;">*</span></label>
                                        <input type="text" name="exp_group" id="exp_group"
                                            placeholder="e.g. Fuel, Maintenance"
                                            value="{{ old('exp_group', $expense->exp_group) }}"
                                            class="form-control form-control-sm mb-3">
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="date" class="form-label">Expense Date <span style="color: red;">*</span></label>
                                        <div class="inner-addon left-addon">
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="date" id="exp_date"
                                                placeholder="{{trans('navmenu.pick_date')}}"
                                                value="{{$expense->date}}"
                                                class="form-control form-control-sm mb-3">
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="odometer_mileage" class="form-label">Odometer / Mileage</label>
                                        <input type="number" min="1" step="any" name="odometer_mileage" id="odometer_mileage"
                                            value="{{ old('odometer_mileage', $expense->odometer_mileage) }}"
                                            class="form-control form-control-sm mb-3">
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="vehicle_rent" class="form-label">Vehicle Rent</label>
                                        <input type="number" min="1" step="any" name="vehicle_rent" id="vehicle_rent"
                                            value="{{ old('vehicle_rent', $expense->vehicle_rent) }}"
                                            class="form-control form-control-sm mb-3">
                                    </div>
                                </div>

                                <div class="row mb-1">
                                    <div class="col-sm-6">
                                        <label for="remarks" class="form-label">Remark</label>
                                        <textarea rows="1" class="form-control form-control-sm mb-3"
                                                name="remarks" id="remarks">{{ old('remarks', $expense->remarks) }}</textarea>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="remarks" class="form-label">Attach Document <span style="color:red">*</span></label>
                                        <input type="file" name="doc_attachment[]" multiple class="form-control form-control-sm mb-3" placeholder="Attach your documment">
                                        
                                    </div>
                                </div>

                                <div class="row mb-1">
                                    <div class="col-sm-8">
                                        <label class="form-label">Search & Add Expense Type</label>
                                        <div class="input-group mb-0">
                                            <input type="text" class="form-control form-control-sm mb-1"
                                                id="search_expense_key"
                                                placeholder="Search expense type..."
                                                autocomplete="off">
                                            <a class="btn btn-danger btn-sm empty-search mb-1"><i class="fa fa-close"></i></a>
                                        </div>
                                        <ul id="expenseTypeResult" class="list-group"></ul>
                                    </div>
                                </div>
                                
                                <div class="row mb-1">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:5%;">#</th>
                                                        <th style="width:35%;">Expense Type</th>
                                                        <th class="text-center" style="width:15%;">{{trans('navmenu.qty')}}</th>
                                                        <th class="text-center" style="width:20%;">{{trans('navmenu.unit_price')}}</th>
                                                        <th class="text-center" style="width:20%;">{{trans('navmenu.total')}}</th>
                                                        <th style="width:5%;"></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr ng-repeat="item in expItems">
                                                        <td class="text-center">@{{$index + 1}}</td>

                                                        <td>@{{item.expense_type}}</td>

                                                        <td>
                                                            <input type="number"
                                                                ng-change="calcTotal(item)"
                                                                ng-model="item.quantity"
                                                                min="0" step="any"
                                                                class="form-control form-control-sm text-center"
                                                                style="height:30px;">
                                                        </td>

                                                        <td>
                                                            <input type="number"
                                                                ng-change="calcTotal(item)"
                                                                ng-model="item.unit_price"
                                                                min="0" step="any"
                                                                class="form-control form-control-sm text-center"
                                                                style="height:30px;">
                                                        </td>

                                                        <td>
                                                            <input type="number"
                                                                ng-model="item.total_price"
                                                                ng-blur="updateExpenseItem(item)"
                                                                readonly
                                                                class="form-control form-control-sm text-center bg-light"
                                                                style="height:30px;">
                                                        </td>

                                                        <td class="text-center">
                                                            <a href="#" ng-click="removeExpenseItem(item.id)">
                                                                <span class="fa fa-trash text-danger"></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-1">
                                    <input type="hidden" id="no_items" value="@{{selectedItems(expItems)}}">
                                    
                                    <div class="col-sm-12 pt-4">
                                        <button onclick="confirmCancel('<?php echo encrypt($expense->id); ?>')"
                                                type="button"
                                                class="btn btn-warning btn-sm float-end"
                                                style="margin-left:5px;">
                                            {{trans('navmenu.btn_cancel')}}
                                        </button>
                                        <button type="submit" name="myButton" id="btn-submit"
                                                class="btn btn-success btn-sm float-end">
                                            {{trans('navmenu.btn_submit')}}
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addExpenseTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-tags me-1"></i> Add Expense Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('expense-type.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Expense Type <span class="text-danger">*</span></label>
                                    <input type="text" name="type" class="form-control form-control-sm"
                                        placeholder="Enter expense type name" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addTripTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-road me-1"></i> Add Trip Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('trip-types.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Trip Type <span class="text-danger">*</span></label>
                                    <input type="text" name="trip_type" class="form-control form-control-sm"
                                        placeholder="Enter trip type name" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function () {
            angular.bootstrap(document.getElementById('expenseController'), ['ExpenseApp']);
        });

        $(document).ready(function () {

            $('#search_expense_key').on('keyup', function () {
                var query = $(this).val();
                $.ajax({
                    url: "{{ url('search-expense-type') }}",
                    type: 'GET',
                    data: { search_key: query },
                    success: function (response) {
                        var len = response.length;
                        $("#expenseTypeResult").empty();
                        for (var i = 0; i < len; i++) {
                            var id   = response[i]['id'];
                            var type = response[i]['type'];  
                            $("#expenseTypeResult").append(
                                "<li class='list-group-item d-flex justify-content-between align-items-center' value='" + id + "'>" +
                                "<div class='col-sm-11'>" + type + "</div>" +
                                "<div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right'></span></span></div>" +
                                "</li>"
                            );
                        }
                        $("#expenseTypeResult li").bind("click", function () {
                            addExpenseItem(this);
                        });
                    }
                });
            });

            $('.empty-search').on('click', function () {
                $("#search_expense_key").val('');
                $("#expenseTypeResult").empty();
            });

            $('#btn-submit').on('click', function (e) {
                e.preventDefault();
                var vehicle  = document.getElementById('vehicle_id').value;
                var employee = document.getElementById('employee_id').value;
                var tripType = document.getElementById('trip_type_id').value;
                var expGroup = document.getElementById('exp_group').value;
                var odometer = document.getElementById('odometer_mileage').value;
                var rent     = document.getElementById('vehicle_rent').value;
                var items    = document.getElementById('no_items').value;
                
                if (vehicle === '') {
                    showError('Please select a vehicle');
                } else if (employee === '') {
                    showError('Please select an employee');
                } else if (tripType === '') {
                    showError('Please select a trip type');
                }else if (expGroup === '') {
                    showError('Please enter expense group');
                } else if (odometer === '' || odometer <= 0) {
                    showError('Please enter valid odometer mileage');
                } else if (rent === '' || rent <= 0) {
                    showError('Please enter valid vehicle rent');
                }
                else if (items == 0) {
                    showError('Please add at least one expense item');
                } else {
                    document.getElementById('expenseform').submit();
                }
            });

            function showError(msg) {
                $('#ermsg').append('<div class="alert alert-danger hideit">' + msg + '</div>');
                setTimeout(function () {
                    $('.hideit').fadeOut('slow', function () { $(this).remove(); });
                }, 1300);
            }
        });

        function addExpenseItem(element) {
            var expTypeId = $(element).val();
            $.ajax({
                url: "{{ url('fetch-expense-type') }}",
                type: 'GET',
                data: { expense_type_id: expTypeId },
                success: function (response) {
                    var scope = angular.element(document.getElementById('expenseController')).scope();
                    scope.$apply(function () {
                        scope.addExpenseItem(response);
                    });
                    setTimeout(function () {
                        $("#search_expense_key").val('');
                        $("#expenseTypeResult").empty();
                    }, 500);
                }
            });
        }
    </script>

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            var $dateInput = document.querySelector('[name="date"]');
            var mind = "<?php echo $settings->sp_mindays ?? 30; ?>";
            var d = new Date();
            d.setDate(d.getDate() - mind);
            $dateInput.DatePickerX.init({
                mondayFirst: true,
                minDate: d,
                format: 'yyyy-mm-dd',
                maxDate: new Date()
            });
        });
    </script>