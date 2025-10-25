@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
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
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmMultipleDelete() {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}",
        }).then((result) => {
            if (result.value) {
                document.getElementById('del-multiple-form').submit();
            }
        })
    }

    function confirmDeleteCat(id) {
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
                document.getElementById('delete-cat-form-' + id).submit();
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

    function validateformModal() {

        var exptype = document.getElementById('exp_type-m');
        if (exptype.value == 'credit') {
            var supp = document.getElementById('supplier-m');
            if (supp.value == 0) {
                $("#newTypeModal").modal('hide');
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

    function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        if (elem == 'show') {
            newform.style.display = 'block';
        } else {
            newform.style.display = 'none';
        }
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                <form class="dashform row g-3" action="{{ url('filter-expenses') }}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="exp_date" id="expensedate" placeholder="{{ trans('navmenu.pick_date') }}" class="form-control form-control-sm mb-1" autocomplete="off">
                        </div>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-8">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.form group -->
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#manage-expenses" role="tab" aria-selected="false">{{ trans('navmenu.expenses') }}</a>
                        </li>
                        @if (Auth::user()->can('create-expense'))
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('expense-categories') }}"> Expense Categories</a>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#items" role="tab" aria-selected="false">
                                Expense Items</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('exp-suppliers') }}">{{ trans('navmenu.supplier_accounts') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('expense-payments') }}">Expense Payments</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="manage-expenses" role="tabpanel">
                            @if (Auth::user()->can('create-expense'))
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm pull-right"><i class="bx bxs-plus-square"></i>{{ trans('navmenu.add_new_expense') }}</a>
                    @endif
                    
                            <div class="table-responsive">
                                <table id="del-multiple" class="table  table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ trans('navmenu.expense_date') }}</th>
                                            <th>{{ trans('navmenu.expense_type') }}</th>
                                            <th>{{ trans('navmenu.category') }}</th>
                                            <th>{{ trans('navmenu.amount') }}</th>
                                            <th>{{ trans('navmenu.paid') }}</th>
                                            <th>{{ trans('navmenu.exp_type') }}</th>
                                            <th>{{ trans('navmenu.status') }}</th>
                                            <th>{{ trans('navmenu.user') }}</th>
                                            <th>{{ trans('navmenu.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach ($expenses as $index => $expense)
                                        <?php $total += $expense->amount; 
                                            $category = App\Models\ExpenseCategory::find($expense->expense_category_id);
                                        ?>
                                        <tr>
                                            <td>{{ $expense->id }}</td>
                                            <td>{{ $expense->created_at }}</td>
                                            <td><a href="{{ route('expenses.show', encrypt($expense->id)) }}">{{ $expense->expense_type }}</a></td>
                                            @if(!is_null($category))
                                            <td>{{ $category->name }}
                                            </td>
                                            @else
                                            <td>None</td>
                                            @endif
                                            <td style="text-align: center;">{{ number_format($expense->amount, 2, '.', ',') }}</td>
                                            <td style="text-align: center;">
                                                {{ number_format($expense->amount_paid, 2, '.', ',') }}
                                            </td>
                                            <td>{{ $expense->exp_type }}</td>
                                            <td>{{ $expense->status }}</td>
                                            <td>{{ $expense->first_name }}</td>
                                            <td>
                                                <a href="{{ route('expenses.show', encrypt($expense->id)) }}"> <span class="fa fa-eye"></span></a> |
                                                @if ($expense->amount_paid < $expense->amount)
                                                <a href="#" onclick="showModal('<?php echo $expense->id; ?>')" data-id="{{ $expense->id }}"><i class="fa fa-money"></i></a> |
                                                @endif
                                                @if (Auth::user()->can('edit-expense'))
                                                <a href="{{ route('expenses.edit', encrypt($expense->id)) }}"> <i class="fa fa-edit" style="color: blue;"></i></a> |
                                                @endif
                                                @if(Auth::user()->can('delete-expense'))
                                                <form id="delete-form-{{ $index }}" method="POST" action="{{ route('expenses.destroy', encrypt($expense->id)) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="button" onclick="confirmDelete('{{ $index }}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>{{ trans('navmenu.total') }}</th>
                                            <th style="text-align: center;">{{ number_format($total) }}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <form id="frm-example" action="{{ url('delete-multiple-expenses') }}" method="POST">
                                @csrf
                                @if (Auth::user()->can('delete-expense'))
                                <button id="submitButton" class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                                @endif
                            </form>
                        </div>
                        <div class="tab-pane fade" id="items" role="tabpanel">
                            <div class="row g-1">
                                <div class="col-md-6 p-4">
                                    <form method="POST" action="{{ route('expense-items.store') }}">
                                        @csrf
                                        <div class="row g-1 align-items-center">
                                            <div class="col-md-12">
                                                <label class="form-label">Expense Category</label>
                                                <select name="expense_category_id" class="form-select form-select-sm mb-1" required>
                                                    <option value="">Select Expense Category</option>
                                                    @foreach ($expcategories as $expcat)
                                                    <option value="{{ $expcat->id }}">{{ $expcat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-12">
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
                                    </form>
                                </div>
                                <div class="col-md-6 p-4">
                                    <table id="exp-items" class="table table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th style="text-align: center;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expitems as $ikey => $item)
                                            <tr>
                                                <td>{{ $item->expense_type }}</td>
                                                <td style="text-align: center;">
                                                    <a href="{{ route('expense-items.edit', encrypt($item->id)) }}"><i class="fa fa-edit"></i></a> |
                                                    <form id="delete-item-form-{{ $ikey }}" method="POST" action="{{ route('expense-items.destroy', encrypt($item->id)) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="#" onclick="confirmDeleteCat('<?php echo $ikey; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                <form method="POST" action="{{ url('store-expenses') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-6 pt-2">
                                <label for="register-username"
                                    class="form-label">{{ trans('navmenu.expense_type') }}</label>
                                <input id="register-username" type="text" name="expense_type" required
                                    placeholder="{{ trans('navmenu.hnt_expense_type') }}"
                                    class="form-control form-control-sm mb-1">

                            </div>
                            <div class="col-md-6 pt-2">
                                <label class="form-label">{{ trans('navmenu.amount') }} </label>
                                <input type="number" name="amount" placeholder="{{ trans('navmenu.hnt_amount') }}"
                                    class="form-control form-control-sm mb-1">
                            </div>
                            <div class=" col-md-6 pt-2">
                                <label for="no_days" class="form-label">{{ trans('navmenu.no_days') }}</label>
                                <input type="number" name="no_days" class="form-control form-control-sm mb-1"
                                    value="1" min="1" placeholder="Enter no of days">
                            </div>
                            {{-- @if ($settings->is_vat_registered)
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">{{ trans('navmenu.has_vat') }}</label>
                                    <select name="has_vat" class="form-control form-control-sm mb-1">
                                        <option value="no">NO</option>
                                        <option value="yes">YES</option>
                                    </select>
                                </div>
                            @endif --}}

                            {{-- @if ($settings->estimate_withholding_tax)
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">Is this Expense contains Withholding Tax</label>
                                    <select onchange="yesnoCheck(this)" class="form-control form-control-sm mb-1">
                                        <option value="no">NO</option>
                                        <option value="yes">YES</option>
                                    </select>
                                </div>
                                <div class="col-md-6 pt-2" id="ifYes" style="display: none;">
                                    <label>Withholding Tax Rate(%): </label>
                                    <input type='number' min="0" id='wtax_rate' name='wht_rate'
                                        class="form-control form-control-sm mb-1"
                                        placeholder="Please Enter the Rate(%) of Withholding Tax">
                                </div>
                            @endif --}}

                            <div class="col-md-6 pt-2">
                                <label for="supplier_id" class="form-label">{{ trans('navmenu.supplier') }}</label>
                                <select name="supplier_id" id="supplier-m" required
                                    class="form-control form-control-sm mb-1" onchange="changeSupplier(this)">
                                    <option value="0">{{ trans('navmenu.unknown') }}</option>
                                    @foreach ($suppliers as $key => $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 pt-2">
                                <label class="form-label">{{ trans('navmenu.exp_type') }}</label>
                                <select name="exp_type" id="exp_type-m" onchange="wegExpTypeModal(this)"
                                    class="form-control form-control-sm mb-1" required>
                                    <option value="">{{ trans('navmenu.select_exp_type') }}</option>
                                    <option value="cash">{{ trans('navmenu.cash_exp') }}</option>
                                    <option value="credit">{{ trans('navmenu.credit_exp') }}</option>
                                </select>
                            </div>
                            {{-- @if ($settings->is_categorized)
                                <div class=" col-md-6 pt-2">
                                    <label class="form-label">{{ trans('navmenu.category') }}</label>
                                    <select name="category_id" id="category" class="form-control form-control-sm mb-1">
                                        <option value="">{{ trans('navmenu.all_categories') }}</option>
                                        @if (!is_null($categories))
                                            @foreach ($categories as $key => $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif --}}
                            <div class=" col-md-6 pt-2" id="account-m" style="display: none;">
                                <label for="account" class="form-label">{{ trans('navmenu.paid_from') }} <span
                                        style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control form-control-sm mb-1" name="account" required>
                                    <option value="Petty Cash">Petty Cash</option>
                                    <option value="Cash">{{ trans('navmenu.cash') }}</option>
                                    <option value="Bank">{{ trans('navmenu.bank') }}</option>
                                    <option value="Mobile Money">{{ trans('navmenu.mobilemoney') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6 pt-2" id="order_no-m" style="display: none;">
                                <label for="total"
                                    class="form-label">{{ trans('navmenu.purchase_order_no') }}</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="ord_no"
                                    placeholder="{{ trans('navmenu.hnt_order_no') }}" name="order_no" />
                            </div>
                            <div class="col-md-6" id="invoice_no-m" style="display: none;">
                                <label for="total" class="form-label">{{ trans('navmenu.invoice_no') }}</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="inv_no"
                                    placeholder="{{ trans('navmenu.hnt_invoice_no') }}" name="invoice_no" />
                            </div>

                            {{-- @if ($settings->is_service_per_device) --}}
                                {{-- <div class="col-md-6 pt-2">
                                    <label class="form-label">{{ trans('navmenu.device_number') }}</label>
                                    <select name="device_id" class="form-control form-control-sm mb-1">
                                        <option value="">{{ trans('navmenu.select_device') }}</option>
                                        @foreach ($devices as $device)
                                            <option value="{{ $device->id }}">{{ $device->device_number }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            {{-- @endif --}}

                        </div>

                        <div class="col-md-12">
                            <label class="form-label">{{ trans('navmenu.description') }}</label>
                            <textarea name="description" class="form-control form-control-sm mb-1"
                                placeholder="{{ trans('navmenu.hnt_description') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn btn-success" onclick="return validateformModal()"
                                name="myButton">{{ trans('navmenu.btn_save') }}</button>
                            <button type="button" class="btn btn-warning"
                                data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
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
                    <div class="modal-body">
                        @csrf
                        <div class="row align-items-center">
                            <div class=" col-md-6 pt-2">
                                <label for="register-username" class="form-label">Supplier Name</label>
                                <input id="register-username" type="text" name="name" required
                                    placeholder="Please enter supplier name" class="form-control form-control-sm mb-1">
                            </div>

                            <div class=" col-md-6 pt-2">
                                <label for="register-username" class="form-label">Phone number</label>
                                <input id="register-username" type="text" name="contact_no"
                                    placeholder="Please enter supplier mobile number"
                                    class="form-control form-control-sm mb-1">
                            </div>

                            <div class=" col-md-6 pt-2">
                                <label for="register-email" class="form-label">Email Address</label>
                                <input id="register-email" type="text" name="email"
                                    placeholder="Please enter supplier email address"
                                    class="form-control form-control-sm mb-1">
                            </div>


                            <div class=" col-md-6 pt-2">
                                <label for="address" class="form-label">Address</label>
                                <input id="address" type="text" name="address"
                                    placeholder="Please enter supplier address" class="form-control form-control-sm mb-1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">Save</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('navmenu.add_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('expense-payments.store') }}" id="add-payment">
                    @csrf
                    <input type="hidden" name="expense_id" id="id_hide">
                    <div class="modal-body row">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.pay_date') }}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="pay_date" id="pay_date"
                                    placeholder="{{ trans('navmenu.pick_date') }}"
                                    class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.amount_paid') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" type="text" name="amount" value="0" required
                                placeholder="{{ trans('navmenu.hnt_amount_paid') }}"
                                class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">{{ trans('navmenu.paid_from') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="account_id" required>
                                <option value="">Petty Cash</option>
                                @foreach($accounts as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit-pay">{{ trans('navmenu.btn_save') }}</button>
                        <button type="button" class="btn btn-orange btn-sm"
                            data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 

    <script src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            $('#example1').DataTable();
            $('#exp-items').DataTable();

            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#frm-example').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

              // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    // Iterate over all selected checkboxes
                    $.each(rows_selected, function(index, rowId) {
                        // Create a hidden element 
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'ids[]')
                            .val(rowId)
                        );
                    });
                }
            });

            $('#expensedate').on('change', function(){
                $('.dashform').submit();
            })
        });
    </script>
@endsection
<script>
    var isChecked = true;

    function selects() {
        var ele = document.getElementsByName('custom_name[]');
        if (isChecked) {
            for (var i = 0; i < ele.length; i++) {
                if (ele[i].type == 'checkbox')
                    ele[i].checked = true;
            }
            isChecked = false;
        } else {
            for (var i = 0; i < ele.length; i++) {
                if (ele[i].type == 'checkbox')
                    ele[i].checked = false;
            }
            isChecked = true;
        }
    }
</script>

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

        $("#add-payment").one("submit", submitFormFunction);
        function submitFormFunction(event) {
            event.preventDefault(); 
            $('#btn-submit-pay').prop("disabled",true);
            $("#add-payment").submit();
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
        var $max = document.querySelector('[name="exp_date"]');
        var $min = document.querySelector('[name="pay_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            // minDate    : new Date(),
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });

        $max.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            // minDate    : new Date(),
            maxDate: new Date()
        });
    });
</script>
