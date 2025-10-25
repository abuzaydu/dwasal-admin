@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function confirmDelete(id) {
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
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function createRequest() {
        document.getElementById('st-req-form').submit();
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto ">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="d-flex align-items-end px-1 py-1">
                                <ul class="nav nav-tabs nav-tabs-new2" role="tablist"  >
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1" role="tab" aria-selected="false">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i></div>
                                                <div class="tab-title"> Stock Corrections</div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tab_2" role="tab" aria-selected="false">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='fa fa-download font-18 me-1'></i></div>
                                                <div class="tab-title"> Export</div>
                                            </div>
                                        </a>
                                    </li>
                                    @if(Auth::user()->can('create-stock-correction'))
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" href="{{ route('stock-corrections.create')}}" aria-selected="false">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='fa fa-plus font-18 me-1'></i></div>
                                                <div class="tab-title"> New Correction</div>
                                            </div>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <form class="dashform" action="{{ url('f-stock-corrections') }}" method="POST" id="stockform">
                                @csrf
                                <input type="hidden" name="start_date" id="start_input" value="">
                                <input type="hidden" name="end_date" id="end_input" value="">
                                <!-- Date and time range -->
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-default pull-right" id="reportrange">
                                        <span><i class="fa fa-calendar"></i></span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active table-responsive" id="tab_1" role="tabpanel">
                            <table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th>{{trans('navmenu.in_stock')}}</th>
                                        <th>Correction QTy</th>
                                        <th>Diff Qty</th>
                                        <th>Corrected BY</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockcorrections as $key => $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->time_created))}}</td>
                                        <td><a href="{{ route('products.show', encrypt($item->product_id)) }}">{{$item->name}}</a></td>
                                        <td>{{$item->in_stock+0}}</td>
                                        <td>{{$item->correction_qty+0}}</td>
                                        <td>{{$item->diff_qty+0}}</td>
                                        <td>{{$item->first_name}} {{$item->last_name}}</td>
                                        <td> 
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('stock-corrections.destroy', encrypt($item->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')"><i class='fa fa-trash'></i></a>
                                            </form>  
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-corrections')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
                        <div class="tab-pane fade table-responsive" id="tab_2" role="tabpanel">
                            <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th>{{trans('navmenu.in_stock')}}</th>
                                        <th>Physical QTy</th>
                                        <th>Variation</th>
                                        <th>Corrected BY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockcorrections as $key => $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->time_created))}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->in_stock+0}}</td>
                                        <td>{{$item->correction_qty+0}}</td>
                                        <td>{{$item->diff_qty+0}}</td>
                                        <td>{{$item->first_name}} {{$item->last_name}}</td>
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
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function(){
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

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

            var table = $('#example2').DataTable({
                'scrollX': true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection