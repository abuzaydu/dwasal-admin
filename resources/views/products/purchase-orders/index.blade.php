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


    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('purchase-orders') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-5 col-md-5 col-sm-12 text-right pt-0">
                <a class="btn btn-primary btn-sm"  href="{{route('purchase-orders.create')}}"><i class='fa fa-plus-circle font-18 me-1'></i>New Purchase Order</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active table-responsive" id="tab_1" role="tabpanel">
                            <table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.supplier')}}</th>
                                        <th>{{trans('navmenu.po_no')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>    
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($porders as $index => $order)
                                    <tr>
                                        <td>{{$order->id}}</td>
                                        <td>{{date('d-m-Y', strtotime($order->created_at))}}</td>
                                        <td><a href="{{ url('poitems/'.encrypt($order->id))}}">{{$order->name}}</a></td>
                                        <td style="text-align: center;"><a href="{{ route('purchase-orders.show', encrypt($order->id))}}">{{ sprintf('%04d', $order->order_no)}}</a></td>
                                        <td>{{number_format($order->amount)}}</td>
                                        <td>{{$order->status}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>
                                            @if($order->status == 'Pending')
                                                <a href="{{ route('purchase-orders.edit', encrypt($order->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a> | 
                                                <form id="delete-form-{{$order->id}}" method="POST" action="{{ route('purchase-orders.destroy', encrypt($order->id))}}" style="display: inline;"> 
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $order->id; ?>')"><i class='fa fa-trash'></i></a>
                                                </form>
                                            @endif     
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-porders')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
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

    <script>
        $(function(){
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            var table = $('#example2').DataTable({
                'scrollX': true,
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');


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

<link rel="stylesheet" href="css/DatePickerX.css">

<script src="js/DatePickerX.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="sale_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
        });
    });

</script>