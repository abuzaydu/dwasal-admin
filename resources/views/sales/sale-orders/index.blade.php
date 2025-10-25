@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
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
            }else{
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        }else{
            b.style.display = 'none';
            m.style.display = 'none';
        }
    }
</script>

@section('content')
     <!--breadcrumb-->
    <div class="block-header pt-4" style="margin-bottom: 0px;">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <form class="dashform row" action="{{url('f-sale-orders')}}" method="POST" id="stockform">
                    @csrf
                    <div class="form-group col-sm-4">
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                        </div>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="form-group col-sm-8">
                        <div class="input-group">
                            <button type="button" class="btn btn-default pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                        </div>
                    </div>
                    <!-- /.form group -->
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="card" style="padding-top: 0px;">
            <div class="card-body">
                <div class="tab-content py-0">
                    <a href="{{ route('sale-orders.create') }}" class="btn btn-primary btn-sm">New Sales Order</a>
                    <div class="tab-pane fade show active py-2" id="manage-invoices" role="tabpanel">
                        <form id="frm-example" action="{{url('delete-multiple-invoices')}}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%;">
                                    <thead style="font-weight: bold;">
                                        <tr>
                                            <th>#</th>
                                            <th>Order No.</th>
                                            <th>Created BY</th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>Order Date</th>
                                            <th>Order Amount</th>
                                            <th>{{trans('navmenu.status')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.last_updated')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($saleorders as $index => $order)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td><a href="{{ route('sale-orders.show', encrypt($order->id)) }}"> {{ sprintf('%04d', $order->order_no)}}</a></td>
                                            <td>{{$order->first_name}} {{$order->last_name}}</td>
                                            <td>{{$order->name}}</a></td>
                                            <td>{{date('d-m-Y', strtotime($order->order_date))}}</td>
                                            <td>{{number_format($order->order_amount, 2, '.',',')}}</td>
                                            <td>{{$order->status}}</td>
                                            <td>{{$order->created_at}}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->updated_at)->diffForHumans() }}</td>
                                            @if($order->status == 'Closed')
                                            <td></td>
                                            @else
                                            <td>
                                                <a href="{{ route('sale-orders.show', encrypt($order->id)) }}" title="">
                                                <i class="fa fa-detail"></i></a> 
                                                @if(!$order->is_approved) |
                                                <form method="POST" action="{{route('sale-orders.destroy' , encrypt($order->id))}}" id="delete-form-{{$index}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{$index}})">
                                                    <i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>  
                                </table>
                            </div>
                            <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>
    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('side/assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('side/assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            var exporttable = $('#export-customers').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf'
                ]
            });

            exporttable.buttons().container().appendTo('#export-customers_wrapper .col-md-6:eq(1)');

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
        });
    </script>
@endsection
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $max = document.querySelector('[name="sale_date"]');

        $max.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            // minDate    : new Date(),
            maxDate: new Date()
        });
    });
</script>