@extends('layouts.vms')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/expenses.js')}}"></script>

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
                        <form class="needs-validation" id="expenseform" method="POST" action="{{route('requisition-trip-logs.update', $expense->id)}}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="requisition_trip_log_id" value="{{ $tripLog->id }}">
                            <input type="hidden" name="vms_expense_id" value="{{$expense->id}}">
                            <input type="hidden" name="deleted_attachments" id="deleted_attachments" value="">

                            <div class="row mb-1">
                                <div class="col-sm-12" id="ermsg">
                                    @if ($errors->any())
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-sm-4">
                                    <label for="vendor_id" class="form-label">Vendor</label>
                                    <select name="vendor_id" id="vendor_id" class="form-select form-select-sm mb-3">
                                        <option value="">-- Select Vendor --</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{$vendor->id}}" {{ $expense->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                {{$vendor->vendor_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label for="trip_type_id" class="form-label">Trip Type <span style="color: red;">*</span></label>
                                    <select name="trip_type_id" id="trip_type_id" required class="form-select form-select-sm mb-3">
                                        <option value="">-- Select Trip Type --</option>
                                        @foreach($tripTypes as $tt)
                                            <option value="{{$tt->id}}" {{ $expense->trip_type_id == $tt->id ? 'selected' : '' }}>
                                                {{$tt->trip_type}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label for="doc_attachment" class="form-label">Attach Document</label>
                                    <input type="file" name="doc_attachment[]" multiple class="form-control form-control-sm mb-2" placeholder="Attach your document">
                                    
                                    {{-- Existing Attachments --}}
                                    @if($expenseAttachments->count() > 0)
                                        <div id="existing-attachments" style="margin-top: 8px;">
                                            @foreach($expenseAttachments as $attachment)
                                                <div class="attachment-item d-flex align-items-start mb-2 p-2 border rounded" 
                                                    id="attachment-{{ $attachment->id }}" style="background: #f8f9fa;">
                                                    
                                                    {{-- Image Preview --}}
                                                    @if(in_array($attachment->file_type, ['image/jpeg','image/png','image/jpg','image/gif','image/webp']))
                                                        <div class="me-2" style="width:80px; height:80px; flex-shrink:0;">
                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                                                                <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                                                                    alt="attachment"
                                                                    style="width:80px; height:80px; object-fit:cover; border-radius:4px; border:1px solid #dee2e6;">
                                                            </a>
                                                        </div>

                                                    {{-- PDF Preview --}}
                                                    @elseif($attachment->file_type === 'application/pdf')
                                                        <div class="me-2" style="flex-shrink:0;">
                                                            <iframe src="{{ asset('storage/' . $attachment->file_path) }}#toolbar=0&navpanes=0&scrollbar=0"
                                                                    style="width:120px; height:80px; border:1px solid #dee2e6; border-radius:4px;"
                                                                    title="PDF preview">
                                                            </iframe>
                                                        </div>

                                                    {{-- Other Files (word, excel, etc.) - icon only --}}
                                                    @else
                                                        <div class="me-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                                            style="width:80px; height:80px; background:#e9ecef; border-radius:4px; border:1px solid #dee2e6;">
                                                            @if(in_array($attachment->file_type, ['application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document']))
                                                                <i class="fa fa-file-word-o text-primary" style="font-size:32px;"></i>
                                                            @elseif(in_array($attachment->file_type, ['application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']))
                                                                <i class="fa fa-file-excel-o text-success" style="font-size:32px;"></i>
                                                            @else
                                                                <i class="fa fa-file-o text-secondary" style="font-size:32px;"></i>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    {{-- File Info & Remove --}}
                                                    <div class="d-flex flex-column justify-content-between flex-grow-1" style="min-width:0;">
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                        class="text-truncate small text-primary" style="max-width:100%;">
                                                            {{ basename($attachment->file_path) }}
                                                        </a>
                                                        <span class="text-muted" style="font-size:11px;">{{ strtoupper(last(explode('/', $attachment->file_type))) }}</span>
                                                        <a href="#" class="text-danger small remove-attachment mt-1" data-id="{{ $attachment->id }}">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </a>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-1">
                                <div class="col-sm-4">
                                    <label for="date" class="form-label">Expense Date <span style="color: red;">*</span></label>
                                    <input type="date" name="date" id="date" required 
                                        class="form-control form-control-sm mb-3" 
                                        value="{{ old('date', \Carbon\Carbon::parse($expense->date)->format('Y-m-d')) }}">
                                </div>

                                <div class="col-sm-6">
                                    <label for="remarks" class="form-label">Remark</label>
                                    <textarea rows="1" class="form-control form-control-sm mb-3"
                                        name="remarks" id="remarks">{{ old('remarks', $expense->remarks) }}</textarea>
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
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-1">
                                <input type="hidden" id="no_items" value="@{{selectedItems(expItems)}}">
                                <div class="col-sm-12 pt-4">
                                    <button onclick="window.history.back();" type="button" class="btn btn-warning btn-sm float-end" style="margin-left:5px;">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                    <button type="submit" name="myButton" id="btn-submit" class="btn btn-success btn-sm float-end">
                                        <i class="fa fa-save"></i> {{ trans('navmenu.btn_submit') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
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

        // Search expense type
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

        // Clear search
        $('.empty-search').on('click', function () {
            $("#search_expense_key").val('');
            $("#expenseTypeResult").empty();
        });

        // Remove existing attachment
        $(document).on('click', '.remove-attachment', function (e) {
            e.preventDefault();
            var attachmentId = $(this).data('id');
            var current      = $('#deleted_attachments').val();
            var updated      = current ? current + ',' + attachmentId : String(attachmentId);
            $('#deleted_attachments').val(updated);
            $('#attachment-' + attachmentId).fadeOut('fast', function () {
                $(this).remove();
            });
        });

        // Submit validation
        $('#btn-submit').on('click', function (e) {
            e.preventDefault();

            var tripType = document.getElementById('trip_type_id').value;
            var items    = document.getElementById('no_items').value;

            if (tripType === '') {
                showError('Please select a trip type');
            } else if (items == 0) {
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