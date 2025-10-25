@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <span class="mb-0 text-uppercase text-right">{{$reporttime}}</span>               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="stockexpires" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.remain_qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchase_date')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expire_date')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.remain_days')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expired')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expstocks as $index => $stock)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$stock['name']}}</td>
                                    <td style="text-align: center;">
                                      @if(is_numeric( $stock['quantity_in'] ) && floor( $stock['quantity_in'] ) != $stock['quantity_in']) {{$stock['quantity_in']}} @else {{number_format($stock['quantity_in'])}} @endif
                                    </td>
                                    <td style="text-align: center;">
                                      @if(is_numeric( $stock['qty_expired'] ) && floor( $stock['qty_expired'] ) != $stock['qty_expired']) {{$stock['qty_expired']}} @else {{number_format($stock['qty_expired'])}} @endif
                                    </td>
                                    <td style="text-align: center;">{{number_format($stock['unit_cost'])}}</td>
                                    <td style="text-align: center;">{{number_format($stock['qty_expired']*$stock['unit_cost'])}}</td>
                                    <td style="text-align: center;">{{date('d, M Y', strtotime($stock['purchase_date']))}}</td>
                                    <td style="text-align: center;">{{date('d, M Y', strtotime($stock['expire_date']))}}</td>
                                    <td style="text-align: center;">{{$stock['numdays']}}</td>
                                    <td style="text-align: center;">{{$stock['status']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format(0)}}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
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
    
    <script>
        $(function(){
                    
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var shop_name = "<?php echo $shop->name; ?>";

            var expiretable = $('#stockexpires').DataTable({
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.expiration_report') }}_" + date,
                        title: "{{ trans('navmenu.expiration_report') }}",
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.expiration_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.expiration_report') }} \n"+date
                    }
                ],
            });
            expiretable.buttons().container().appendTo('#stockexpires_wrapper .col-md-6:eq(1)');
        })
    </script>
@endsection