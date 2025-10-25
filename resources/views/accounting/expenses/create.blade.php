@extends('layouts.acc')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{ asset('js/angular-1-8-3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/expense.js') }}"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('delete-expense/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function yesnoCheck(elem) {
        var x = document.getElementById("ifYes");
        if (elem.value !== "no") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#wtax_rate").val('');
        }

    }

    function validateform(form) {
        var items = document.expenseform.no_items.value;
        if (items == 0) {
            // alert('Please select at least one item to continue.');
            Swal.fire(
                'Nothing To Submit!',
                'Please select at least one item to continue.',
                'info'
            )
            return false;
        }

        var exptype = document.getElementById('exp_type');
        if (exptype.value == 'credit') {
            var supp = document.getElementById('supplier');
            if (supp.value == 0) {
                // alert('Please select at least one item to continue.');
                Swal.fire(
                    'No Supplier selected!',
                    'Please select a supplier for credit expense.',
                    'info'
                )
                return false;
            }
        }
        form.myButton.disabled = true;
        form.myButton.value = "Please wait...";
        return true;

    }

    function confirmCancel() {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('cancel-expense') }}";
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function weg(elem) {
        var x = document.getElementById("expense_date_field");
        if (elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#stock_date").val('');
        }
    }

    function wegExpType(elem) {
        var acc = document.getElementById('account');

        var sbscr = "<?php echo $shop->subscription_type_id; ?>";
        if (sbscr == 2) {
            var or = document.getElementById('order_no');
            var inv = document.getElementById('invoice_no');
            if (elem.value === "credit") {
                var supp = document.getElementById('supplier');
                acc.style.display = "none";
                if (supp.value != 0) {
                    or.style.display = "block";
                    inv.style.display = " block";
                }
            } else {
                acc.style.display = "block";
                or.style.display = "none";
                inv.style.display = "none";
            }
        } else {
            if (elem.value === "credit") {
                acc.style.display = "none";
            } else {
                acc.style.display = "block";
            }
        }
    }

    function wegExpTypeModal(elem) {
        var acc = document.getElementById('account-m');

        var sbscr = "<?php echo $shop->subscription_type_id; ?>";
        if (sbscr == 2) {
            var or = document.getElementById('order_no-m');
            var inv = document.getElementById('invoice_no-m');
            acc.style.display = "none";
            if (elem.value === "credit") {
                or.style.display = "block";
                inv.style.display = " block";
            } else {
                acc.style.display = "block";
                or.style.display = "none";
                inv.style.display = "none";
            }
        } else {
            if (elem.value === "credit") {
                acc.style.display = "none";
            } else {
                acc.style.display = "block";
            }
        }
    }

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
    }

</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item"><a href="{{ url('expenses') }}">Expenses </a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-4 mx-auto">            
            <div class="card radius-6">
                <div class="card-body p-2">
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <div class="ms-auto">
                            <button type="button" class="btn btn-primary btn-sm px-1" data-bs-toggle="modal"
                                data-bs-target="#newTypeModal">
                                <i class="fa fa-plus mr-1"></i>
                                {{ trans('navmenu.new_type') }}
                            </button>
                        </div>
                    </div>
                    <div class="p-2 border rounded">
                        <div class="form-group">
                            <label class="form-label">{{ trans('navmenu.search_tap') }}</label>
                            <input ng-model="searchKeyword" placeholder="{{ trans('navmenu.search_expense_type') }}"
                                class="form-control form-control-sm mb-1">
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items | filter: searchKeyword | limitTo:10" ng-click="addExpenseTemp(item, newexpensetemp)" style="cursor: pointer;">@{{ item.expense_type }}  <span class="badge bg-success rounded-pill"><span class="fa fa-arrow-right" aria-hidden="true"></span></span></li>
                        </ul>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xl-8 mx-auto">
            <div class="card radius-6">
                <!-- /.box-header -->
                <div class="card-body p-2">
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fa fa-user-plus"></i>{{ trans('navmenu.new_supplier') }}
                        </button>
                    </div>
                    <div class="p-1 border rounded print_invoice">
                        <form class="row g-3 needs-validation" novalidate name="expenseform" method="POST"
                            action="{{ route('expenses.store') }}" onsubmit="return validateform(this)">
                            @csrf
                            <div class="col-sm-3">
                                <label for="supplier_id" class="form-label">{{ trans('navmenu.supplier') }}</label>
                                <select name="supplier_id" id="supplier" required class="form-select form-select-sm mb-1" onchange="changeSupplier(this)">
                                    @foreach ($suppliers as $key => $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{ trans('navmenu.expense_date') }}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="expense_date" id="expense_date" placeholder="{{ trans('navmenu.pick_date') }}" value="{{$today}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            @if ($settings->is_service_per_device)
                            <div class="col-sm-3">
                                <label class="form-label">{{ trans('navmenu.device_number') }}</label>
                                <select name="device_id" class="form-select form-select-sm mb-1">
                                    <option value="">{{ trans('navmenu.select_device') }}</option>
                                    @if (!is_null($devices))
                                        @foreach ($devices as $device)
                                        <option value="{{ $device->id }}">{{ $device->device_number }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @endif

                            <div class="col-sm-3">
                                <div id="purchase_type_field">
                                    <label class="form-label">Payment ype</label>
                                    <select name="exp_type" id="exp_type" onchange="wegExpType(this)"
                                        class="form-select form-select-sm mb3" required>
                                        <option value="">{{ trans('navmenu.select_exp_type') }}</option>
                                        <option value="cash">{{ trans('navmenu.cash_exp') }}</option>
                                        <option value="credit">{{ trans('navmenu.credit_exp') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div id="account" style="display: none;">
                                    <label for="account" class="form-label">{{ trans('navmenu.paid_from') }} <span style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb3" name="account_id">
                                        <option value="">Petty Cash</option>
                                        @foreach($accounts as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div id="order_no" style="display: none;">
                                    <label for="total"
                                        class="form-label">{{ trans('navmenu.purchase_order_no') }}</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="ord_no"
                                        placeholder="{{ trans('navmenu.hnt_order_no') }}" name="order_no" />
                                </div>
                            </div>

                            <div class="col-md-3" id="invoice_no" style="display: none;">
                                <label for="total" class="form-label">{{ trans('navmenu.invoice_no') }}</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="inv_no" placeholder="{{ trans('navmenu.hnt_invoice_no') }}" name="invoice_no" />
                            </div>
                            
                            <div class="col-md-12">
                                <!-- <span class="text-center" style="color: red;">{{ trans('navmenu.exp_note') }}</span> -->
                                <table class="items mt-0"
                                    style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{ trans('navmenu.expense_type') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.qty') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.unit_cost') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.amount') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.description') }}</th>
                                        
                                        @if ($settings->estimate_withholding_tax)
                                            <th style="text-align: center;">{{ trans('navmenu.wht_rate') }}</th>
                                        @endif
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newexpensetemp in expensetemp" id="temps">
                                        <td>@{{ $index + 1 }}</td>
                                        <td>@{{ newexpensetemp.expense_type }}</td>
                                        <td>
                                            <input type="number" name="qty" ng-blur="updateExpenseTemp(newexpensetemp)" ng-model="newexpensetemp.qty" min="0" step="any" value="@{{ newexpensetemp.qty }}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1">
                                        </td>
                                        <td>
                                            <input type="number" name="unit_cost" ng-blur="updateExpenseTemp(newexpensetemp)" ng-model="newexpensetemp.unit_cost" min="0" step="any" value="@{{ newexpensetemp.unit_cost }}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1">
                                        </td>
                                        <td>
                                            <input type="number" name="amount" oninput="seprator(this)" ng-blur="updateExpenseTemp(newexpensetemp)" string-to-number ng-model="newexpensetemp.amount" min="0" step="any" value="@{{ newexpensetemp.amount }}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1">
                                        </td>
                                        <td>
                                            <input type="text" name="description" ng-model="newexpensetemp.description" ng-blur="updateExpenseTemp(newexpensetemp)" class="form-control form-control-sm mb-1" value="@{{ newexpensetemp.description }}">
                                        </td>
                                        
                                        @if ($settings->estimate_withholding_tax)
                                        <td><input type="number" name="wht_rate"ng-blur="updateExpenseTemp(newexpensetemp)" ng-model="newexpensetemp.wht_rate" min="0" step="any" value="@{{ newexpensetemp.wht_rate }}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1"></td>
                                        @endif
                                        <td><a href="#" ng-click="removeExpenseTemp(newexpensetemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>{{ trans('navmenu.total') }}</td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: center;"><b>@{{ sum(expensetemp) | number: 2 }}</b></td>
                                        <td></td>
                                        @if ($settings->estimate_withholding_tax)
                                        <td></td>                               @endif
                                        <td></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" name="myButton"
                                    class="btn btn-success btn-sm">{{ trans('navmenu.btn_submit') }}</button>
                                <button onclick="confirmCancel()" type="button"
                                    class="btn btn-warning btn-sm">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">{{ trans('navmenu.new_type') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('expense-items.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <div class="col-md-12 pt-2">
                                <label class="form-label">Expense Category</label>
                                <div class="input-group mb-0">
                                    <select name="expense_category_id" class="form-select form-select-sm mb-1" required>
                                        <option value="">Select Expense Category</option>
                                        @foreach ($expcategories as $expcat)
                                        <option value="{{ $expcat->id }}">{{ $expcat->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-primary btn-sm mb-1"  data-bs-toggle="modal" data-bs-target="#catModal"><i class='fa fa-plus'></i> New</button>
                                </div>
                            </div>
                            <div class="col-md-12 pt-2">
                                <label for="register-username" class="form-label">{{ trans('navmenu.expense_type') }}
                                    <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="expense_type" required placeholder="{{ trans('navmenu.hnt_expense_type') }}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Is Cost Of Sale?</label>
                                <select name="is_cost_of_sale" class="form-select form-select-sm mb-1">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Supplier</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <form class="form-validate" method="POST" action="{{ route('suppliers.store') }}">
                    <div class="modal-body row g-1">
                        @csrf
                        <input type="hidden" name="supplier_for" value="Expense">
                        <div class="col-md-6">
                            <label for="register-username" class="form-label">Supplier Name <span style="color: red;">*</span></label>
                            <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label for="register-username" class="form-label">Phone number</label>
                            <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm mb-1">
                        </div>

                        <div class="col-md-6">
                            <label for="register-email" class="form-label">Email Address</label>
                            <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input id="address" type="text" name="address" placeholder="Please enter supplier address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" placeholder="Bank Name">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" placeholder="Branch Name">
                        </div>
                        <div class="col-md-6">
                            <label for="swift_code" class="form-label">Swift Code</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" placeholder="Swift Code">
                        </div>
                        <div class="col-md-6">
                            <label for="account" class="form-label">Account Number</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" placeholder="Account Number">
                        </div>
                        <div class="col-md-6">
                            <label for="account" class="form-label">Account Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" placeholder="Account Name">
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

    <!-- Modal -->
    <div class="modal fade" id="catModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Expense Category</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body row g-1">
                    <form method="POST" action="{{ route('expense-categories.store') }}">
                        @csrf
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('navmenu.name') }}</label>
                            <input class="form-control form-control-sm mb-3" type="text" name="name" placeholder="Enter Category name" required>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('navmenu.description') }}</label>
                            <textarea name="description" class="form-control form-control-sm mb-3" placeholder="Enter Category Description"></textarea>
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                            <a href="#" onclick="showHideForm('hide')" class="btn btn-primary btn-sm">{{ trans('navmenu.btn_cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $( document ).ready(function() {
        inputamt = $("#inputAmount");
        var n = inputamt.val();
        var output = getCommaSeparatedTwoDecimalsNumber(n);
        inputamt.val(output);

        inputamt.on('focus', function(){
            var n = $(this).val();
            let output = parseFloat(n.replace(/,/g, ''));
            $(this).val(output);
        });

        inputamt.on('blur', function(){
            var n = $(this).val();
            var output = getCommaSeparatedTwoDecimalsNumber(n);
            $(this).val(output);
        });

        $("#new-type-form").one("submit", submitFormFunction);
        function submitFormFunction(event) {
            event.preventDefault(); 
            $('#btn-submit-new').prop("disabled",true);
            $("#new-type-form").submit();
        }
    });

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="expense_date"]');

        var mind = "<?php echo $settings->sp_mindays; ?>";
        var d = new Date();
        d.setDate(d.getDate() - mind);
        $min.DatePickerX.init({
            mondayFirst: true,
            minDate: d,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>
