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
                window.location.href = "{{url('vms-expenses')}}";
            }
        });
    }
</script>

@section('content')
<!--breadcrumb-->
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item"><a href="{{ url('vms-expenses') }}">VMS Expenses</a></li>
                <li class="breadcrumb-item active">{{$page}}</li>
            </ul>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<div class="row clearfix" id="expenseController" ng-controller="ExpenseCtrl" ng-init="initExpense('<?php echo $expense->id; ?>')">
    <div class="col-xl-12 mx-auto">
        <div class="card radius-6">
            <div class="card-body">
                <div class="p-4 border rounded">

                    <form class="needs-validation" id="expenseform" method="POST" enctype="multipart/form-data"
                        action="{{route('vms-expenses.update', encrypt($expense->id))}}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="vms_expense_id" value="{{$expense->id}}">

                        {{-- ERROR MESSAGES --}}
                        <div class="row mb-2">
                            <div class="col-sm-12" id="ermsg">
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        {{-- ROW 1: Vendor | Expense Group | Expense Date --}}
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <label for="vendor_id" class="form-label">Vendor</label>
                                <select name="vendor_id" id="vendor_id" class="form-select form-select-sm mb-3">
                                    <option value="">-- Select Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{$vendor->id}}"
                                            {{ (old('vendor_id', $expense->vendor_id) == $vendor->id) ? 'selected' : '' }}>
                                            {{$vendor->vendor_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label for="exp_group" class="form-label">Expense Group <span class="text-danger">*</span></label>
                                <input type="text" name="exp_group" id="exp_group"
                                    placeholder="e.g. Fuel, Maintenance"
                                    value="{{ old('exp_group', $expense->exp_group) }}"
                                    class="form-control form-control-sm mb-3">
                            </div>

                            <div class="col-sm-4">
                                <label for="exp_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="exp_date"
                                    value="{{ old('date', \Carbon\Carbon::parse($expense->date)->format('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm mb-3">
                            </div>
                        </div>

                        {{-- ROW 2: Remarks --}}
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <label for="remarks" class="form-label">Remark</label>
                                <textarea rows="2" class="form-control form-control-sm mb-3"
                                    name="remarks" id="remarks">{{ old('remarks', $expense->remarks) }}</textarea>
                            </div>
                        </div>

                        {{-- ROW 3: Attachments --}}
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <label class="form-label">Attach Document</label>
                                <input type="file" name="doc_attachment[]" id="doc_attachment_input"
                                    multiple class="form-control form-control-sm mb-1">
                                <small class="text-muted d-block mb-2">
                                    Leave empty to keep existing attachments. Accepted: PDF, JPG, JPEG, PNG
                                </small>
                            </div>

                            {{-- Existing Attachments --}}
                            @php
                                $existingAttachments = \App\Models\VmsExpenseAttachment::where('vms_expense_id', $expense->id)->get();
                            @endphp

                            @if($existingAttachments->count() > 0)
                                <div class="col-sm-12">
                                    <div class="border rounded p-2" style="background:#f8f9fa;">
                                        <p class="text-muted mb-2" style="font-size:11px; text-transform:uppercase;">
                                            <i class="fa fa-paperclip me-1"></i> Current Attachments ({{ $existingAttachments->count() }})
                                        </p>
                                        @foreach($existingAttachments as $att)
                                            @php
                                                $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                                                $url = asset('storage/' . $att->file_path);
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between mb-2"
                                                 id="attachment-row-{{ $att->id }}">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                                        <img src="{{ $url }}" alt="attachment"
                                                            style="width:40px; height:40px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                                                    @elseif($ext === 'pdf')
                                                        <span style="font-size:28px;"><i class="fa fa-file-pdf-o text-danger"></i></span>
                                                    @else
                                                        <span style="font-size:28px;"><i class="fa fa-file text-secondary"></i></span>
                                                    @endif
                                                    <span class="text-muted" style="font-size:12px;">{{ strtoupper($ext) }} file</span>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ $url }}" target="_blank"
                                                        class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:11px;">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ $url }}" download
                                                        class="btn btn-outline-success btn-sm py-0 px-2" style="font-size:11px;">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <a href="javascript:;" onclick="confirmDeleteAttachment('{{ $att->id }}')"
                                                        class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size:11px;">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                    {{-- ✅ NO delete form here --}}
                                                </div>
                                            </div>
                                            @if(!$loop->last)<hr class="my-1">@endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-12">
                                    <p class="text-muted mb-0" style="font-size:12px;">
                                        <i class="fa fa-info-circle me-1"></i> No attachments yet.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- ROW 4: Search & Add Expense Type --}}
                        <div class="row mb-2">
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

                        {{-- ROW 5: Expense Items Table --}}
                        <div class="row mb-2">
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
                                                    <input type="number" ng-change="calcTotal(item)" ng-model="item.quantity"
                                                        min="0" step="any" class="form-control form-control-sm text-center"
                                                        style="height:30px;">
                                                </td>
                                                <td>
                                                    <input type="number" ng-change="calcTotal(item)" ng-model="item.unit_price"
                                                        min="0" step="any" class="form-control form-control-sm text-center"
                                                        style="height:30px;">
                                                </td>
                                                <td>
                                                    <input type="number" ng-model="item.total_price" ng-blur="updateExpenseItem(item)"
                                                        readonly class="form-control form-control-sm text-center bg-light"
                                                        style="height:30px;">
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" ng-click="removeExpenseItem(item.id)">
                                                        <span class="fa fa-trash text-danger"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th class="text-center"><b>{{trans('navmenu.total')}}</b></th>
                                                <th class="text-center">@{{sumQty(expItems)}}</th>
                                                <th></th>
                                                <th class="text-center"><b>@{{sumTotal(expItems) | number:2}}</b></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- ROW 6: Action Buttons --}}
                        <div class="row mb-1">
                            <input type="hidden" id="no_items" value="@{{selectedItems(expItems)}}">
                            <div class="col-sm-12 pt-3">
                                <button onclick="confirmCancel('<?php echo encrypt($expense->id); ?>')"
                                        type="button"
                                        class="btn btn-warning btn-sm float-end"
                                        style="margin-left:5px;">
                                    <i class="fa fa-times me-1"></i> {{trans('navmenu.btn_cancel')}}
                                </button>
                                <button type="submit" name="myButton" id="btn-submit"
                                        class="btn btn-success btn-sm float-end">
                                    <i class="fa fa-save me-1"></i> Update Expense
                                </button>
                            </div>
                        </div>

                    </form>
                    {{-- ✅ Main form ends here --}}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ✅ Delete forms OUTSIDE main form — fixes nested form / DELETE bleeding issue --}}
@php
    $attachmentsForDelete = \App\Models\VmsExpenseAttachment::where('vms_expense_id', $expense->id)->get();
@endphp
@foreach($attachmentsForDelete as $att)
    <form id="deleteAttachForm_{{ $att->id }}" method="POST"
        action="{{ route('vms-expense-attachment.destroy', $att->id) }}"
        style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

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

            var vendor        = $('#vendor_id').val();
            var expGroup      = $('#exp_group').val();
            var expDate       = $('#exp_date').val();
            var docAttachment = $('#doc_attachment_input').val();
            var scope         = angular.element(document.getElementById('expenseController')).scope();
            var items         = scope && scope.expItems ? scope.expItems.length : 0;

            if (!vendor) {
                showError('Please select a vendor');
            } else if (!expGroup) {
                showError('Please enter expense group');
            } else if (!expDate) {
                showError('Please enter expense date');
            } else if (docAttachment && !/\.(pdf|jpg|jpeg|png|gif|webp)$/i.test(docAttachment)) {
                showError('Please upload a valid document (PDF, JPG, JPEG, PNG)');
            } else if (items == 0) {
                showError('Please add at least one expense item');
            } else {
                $('#expenseform').submit();
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

    function confirmDeleteAttachment(id) {
        Swal.fire({
            title: 'Remove this attachment?',
            text: 'This file will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/vms-expense-attachment/' + id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function () {
                        $('#attachment-row-' + id).fadeOut('fast', function () {
                            $(this).remove();
                        });
                    },
                    error: function () {
                        Swal.fire('Error', 'Could not delete attachment. Please try again.', 'error');
                    }
                });
            }
        });
    }
</script>