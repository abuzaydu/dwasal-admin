@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>

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
                <a href="#" class=" font-13 btn  btn-warning btn-sm mb-3 float-end" onclick="showHideForm('show')">New Expense Category</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="categories" role="tabpanel">
                            <div class="border rounded p-2" id="new-form" style="display: none;">
                                <form class="row g-1" method="POST" action="{{ route('expense-categories.store') }}">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">Transaction Account <span class="tex">*</span></label>
                                        <select name="transaction_account_id" class="form-select form-select-sm mb-3" required>
                                            <option value="">Select Account</option>
                                            @foreach($traccounts as $account)
                                            <option value="{{$account->id}}">{{$account->account_number}} - {{$account->account_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ trans('navmenu.name') }} <span class="tex">*</span></label>
                                        <input class="form-control form-control-sm mb-3" type="text" name="name" placeholder="Enter Category name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">{{ trans('navmenu.description') }}</label>
                                        <input name="description" class="form-control form-control-sm mb-3" placeholder="Enter Category Description">
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="submit" class="btn btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                                        <a href="#" onclick="showHideForm('hide')" class="btn btn-primary btn-sm">{{ trans('navmenu.btn_cancel') }}</a>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expcategories as $index => $cat)
                                        <tr>
                                            <td>{{ $cat->name }}</td>
                                            <td>{{ $cat->description }}</td>
                                            <td>
                                                <a href="{{ route('expense-categories.edit', encrypt($cat->id)) }}"><i class="fa fa-edit"></i></a> |
                                                <form id="delete-cat-form-{{ $index }}" method="POST" action="{{ route('expense-categories.destroy', encrypt($cat->id)) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" onclick="confirmDeleteCat('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
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
