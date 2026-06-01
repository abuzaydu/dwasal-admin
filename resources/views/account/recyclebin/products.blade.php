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
                window.location.href = "{{ url('del-recy-product/') }}/" + id;
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
                window.location.href = "{{ url('recycle-product/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
                    'success'
                )
            }
        })
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
                <form class="dashform row g-3" action="{{ url('recycle-products') }}" method="get" id="stockform">
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

    <div class="card">
        <div class="card-body row">
            <ul class="nav nav-tabs nav-tabs-new2 col-md-8" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="{{ url('recyclebin') }}" role="tab"
                        aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.products') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url('recycle-purchases') }}">
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
                <form action="{{ url('empty-recycle-products') }}" method="POST" id="empty-recycle-products">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger " id="submitdr"> <i class="fa fa-trash"></i> Empty
                        product's Recyclebin</button>
                </form>
            </div>
            <div class="col-md/-12 tab-content py-3">
                <div class="tab-pane fade show active" id="manage-products" role="tabpanel">
                    <div class="table-responsive">
                        <table id="empty-multiple" class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th></th>
                                    <th>name</th>
                                    <th>{{ trans('navmenu.basic_uom') }}</th>
                                    <th>{{ trans('navmenu.in_stock') }}</th>
                                    <th>{{ trans('navmenu.price') }} </th>
                                    {{-- @if($settings->retail_with_wholesale) --}}
                                    <th>{{ trans('navmenu.wholesaleprice') }} </th>
                                    {{-- @endif --}}
                                    <th>{{ trans('navmenu.date_registered') }}</th>
                                    <th>{{ trans('navmenu.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->basic_uom}}</td>
                                    <td>{{ $product->in_stock }}</td>
                                    <td>{{ $product->unit_cost }}</td>
                                    <td>{{ $product->wholesale_price }}</td>
                                    <td>{{ $product->created_at }}</td>
                                    <td>
                                        <a href="#" class="button" onclick="confirmRecycle('<?php echo encrypt($product->id); ?>')">
                                        <i class="fa fa-recycle"></i> Restore
                                        </a> | <a href="#" class="button"
                                            onclick="confirmDelete('<?php echo encrypt($product->id); ?>')"><i class="fa fa-trash" style="color: red;"></i> Delete Parmanently</a>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                       <div class="d-flex gap-2">
                        <form id="del-products" action="{{ url('del-multiple-recycle-products') }}" method="POST">
                            @csrf
                            <button id="submitDelButton" type="button" class="btn btn-danger">
                                Delete Selected
                            </button>
                       </form>

                        <form id="restore-products" action="{{ url('recycle-multiple-products') }}" method="POST">
                            @csrf
                            <button id="submitResButton" type="button" class="btn btn-primary">
                                Restore Selected
                            </button>
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

        $('#submitDelButton').on('click', function () {

            let rows = table.column(0).checkboxes.selected();

            if (rows.length === 0) return;

            Swal.fire({
                title: "Are you sure?",
                icon: 'warning',
                showCancelButton: true,
            }).then((result) => {

                if (result.value) {

                    let form = $('#del-products');

                    form.find('input[name="ids[]"]').remove();

                    rows.each(function (id) {
                        form.append(`<input type="hidden" name="ids[]" value="${id}">`);
                    });

                    form.submit();
                }
            });
        });
          
        $('#submitResButton').prop("disabled", true);
        $('body').on('change', 'input[type="checkbox"]', function() {
            this.checked ? counterChecked++ : counterChecked--;
            counterChecked > 0 ? $('#submitResButton').prop("disabled", false): $('#submitResButton').prop("disabled", true);
        });

        $('#submitResButton').on('click', function () {

            let rows = table.column(0).checkboxes.selected();

            if (rows.length === 0) return;

            Swal.fire({
                title: "Restore selected products?",
                icon: 'info',
                showCancelButton: true,
            }).then((result) => {

                if (result.value) {

                    let form = $('#restore-products');

                    form.find('input[name="ids[]"]').remove();

                    rows.each(function (id) {
                        form.append(`<input type="hidden" name="ids[]" value="${id}">`);
                    });

                    form.submit();
                }
            });
        });
          
        // Handle form submission event 
        $('#del-products').on('submit', function(e){
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