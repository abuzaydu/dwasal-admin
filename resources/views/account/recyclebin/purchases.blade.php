@extends('layouts.prof')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
    function confirmDelete(id) {
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
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmRecycle(id) {
        Swal.fire({
            title: "{{ trans('navmenu.sure_restore') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.yes_restore') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('recycle-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
                    'success'
                )
            }
        })
    }

    function confirmDeletePurchase(id) {
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
                window.location.href = "{{ url('del-recy-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmRecyclePurchase(id) {
        Swal.fire({
            title: "{{ trans('navmenu.sure_restore') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.yes_restore') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('recycle-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
                    'success'
                )
            }
        })
    }


    function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');

        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            if (elem.value === 'Bank') {
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            } else {
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        } else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        } else {
            b.style.display = 'none';
            m.style.display = 'none';
        }
    }
</script>
@section('content')
    
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Recyclebin</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{ url('recycle-purchases') }}" method="get" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-7">
                        <button type="button" class="btn btn-white btn-xs pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="card">
            <div class="card-body row">
                <ul class="nav nav-tabs nav-tabs-new2 col-md-8" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="{{ url('recyclebin') }}">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{ trans('navmenu.sales') }}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#" role="tab"
                            aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{ trans('navmenu.purchases') }}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="{{ url('recycle-expenses') }}">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{ trans('navmenu.expenses') }}</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="col-md-4 d-flex justify-content-end">
                    <form action="{{ url('empty-recycle-purchases') }}" method="POST" id="empty-recycle-sales">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger " id="submitdr"> <i class="fa fa-trash"></i> Empty Purchases's Recyclebin</button>
                    </form>
                </div>
                <div class="col-md-12 tab-content py-3">
                    <div class="tab-pane fade show active" id="purchases" role="tabpanel">
                        <div class="table-responsive">
                            <table id="empty-multiple" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ trans('navmenu.purchase_date') }}</th>
                                        <th>{{ trans('navmenu.supplier') }}</th>
                                        <th>{{ trans('navmenu.grn_no') }}</th>
                                        <th>{{ trans('navmenu.invoice_no') }}</th>
                                        <th>{{ trans('navmenu.amount') }}</th>
                                        <th>{{ trans('navmenu.amount_paid') }}</th>
                                        <th>{{ trans('navmenu.unpaid') }}</th>
                                        <th>{{ trans('navmenu.del_by') }}</th>
                                        <th>{{ trans('navmenu.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchases as $index => $purchase)
                                    <tr>
                                        <td>{{ $purchase->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($purchase->time_created)) }}</td>
                                        <td>{{ $purchase->name }}</td>
                                        <td>{{ sprintf('%04d', $purchase->grn_no) }}</td>
                                        <td>{{ $purchase->invoice_no }}</td>
                                        <td>{{ number_format($purchase->total_amount, 2, '.', ',') }}</td>
                                        <td>{{ number_format($purchase->amount_paid, 2, '.', ',') }}</td>
                                        <td>{{ number_format($purchase->total_amount - $purchase->amount_paid, 2, '.', ',') }}</td>
                                        <td>{{ $purchase->del_by }} </td>
                                        <td>
                                            <a href="#" class="button" onclick="confirmRecyclePurchase('<?php echo encrypt($purchase->id); ?>')"> <i class="fa fa-recycle"></i> Restore
                                                </a> | 
                                            <a href="#" class="button" onclick="confirmDeletePurchase('<?php echo encrypt($purchase->id); ?>')"><i class="fa fa-trash" style="color: red;"></i> Delete Parmanently</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="del-purchases" action="{{ url('del-multiple-recycle-purchases') }}" method="POST">
                                @csrf
                                <button id="submitDelButton" class="btn btn-danger">{{ trans('navmenu.delete_selected') }}</button>
                                <button id="submitResButton" class="btn btn-primary">{{ trans('navmenu.restore_selected') }}</button>
                            </form>
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

    <script type="text/javascript">
        var userlang = "<?php echo app()->getLocale(); ?>";
        var languageUrl = "";
        if (userlang === 'en') {
            languageUrl = "plugins/libs/English.json";
        }else{
          languageUrl = "plugins/libs/Swahili.json"; 
        }
        
        var table = $('#empty-multiple').DataTable({
            "scrollX": true,
            language: {
                url: languageUrl
            },
            'columnDefs': [
                {
                    'targets': 0,
                    'checkboxes': {
                       'selectRow': true
                    }
                }
            ],
            'select': {
                'style': 'multi'
            },
            // 'order': [[1, 'asc']]
        })

        var is_restore = 0;
        var counterChecked = 0;
        $('#submitDelButton').prop("disabled", true);
        $('body').on('change', 'input[type="checkbox"]', function() {
            this.checked ? counterChecked++ : counterChecked--;
            counterChecked > 0 ? $('#submitDelButton').prop("disabled", false): $('#submitDelButton').prop("disabled", true);
        });

        $('#submitDelButton').on('click', function(e){
            e.preventDefault();
            is_restore = 0;
            Swal.fire({
              title: "{{trans('navmenu.are_you_sure_delete')}}",
              text: "{{trans('navmenu.no_revert')}}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{trans('navmenu.cancel_it')}}",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                $('#del-purchases').submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        })
          
        $('#submitResButton').prop("disabled", true);
        $('body').on('change', 'input[type="checkbox"]', function() {
            this.checked ? counterChecked++ : counterChecked--;
            counterChecked > 0 ? $('#submitResButton').prop("disabled", false): $('#submitResButton').prop("disabled", true);
        });

        $('#submitResButton').on('click', function(e){
            e.preventDefault();
            is_restore = 1;
            Swal.fire({
              title: "Are you sure want to restore?",
              icon: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes, Restore It",
              cancelButtonText: "No"
            }).then((result) => {
              if (result.value) {
                $('#del-purchases').submit();
                Swal.fire(
                  "Restored",
                  'success'
                )
              }
            })
        })
          
        // Handle form submission event 
        $('#del-purchases').on('submit', function(e){
            var form = this;
            var rows_selected = table.column(0).checkboxes.selected();
            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                // Create a hidden element 
                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'ids[]')
                    .val(rowId)
                );
                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'is_restore')
                    .val(is_restore)
                );
            });      
        });
</script>
@endsection