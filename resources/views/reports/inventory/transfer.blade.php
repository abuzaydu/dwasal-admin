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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="row g-3 dashform" action="{{url('transfer-report')}}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-4">
                        <a class="btn btn-primary btn-sm" href="{{ url('transfer-received-report')}}">{{trans('navmenu.stock_received')}}</a>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <div class="col-md-8 float-md-end">
                        <div class="input-group">
                            <button type="button" class="btn btn-white pull-right mb-3" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body" id="transfered">
                    <div class="col-xs-12 table-responsive">
                        <table id="stocktransfer" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.sto_no')}}</th>
                                    <th>{{trans('navmenu.destin_shop')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.source_unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.destin_unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.transfer_type')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tqty = 0; $tscost = 0; $tdcost = 0; $tprofit = 0; $total = 0; ?>
                                @foreach($transfers as $index => $transfer)
                                <?php $tqty += $transfer->quantity; $tscost += $transfer->source_unit_cost; $tdcost += $transfer->destin_unit_cost; $tprofit = 0; $total += (($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity); ?>
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$transfer->name}}</td>
                                    <td style="text-align: center;">{{$transfer->quantity}}</td>
                                    <?php 
                                       $order =App\Models\TransferOrder::find($transfer->transfer_order_id);
                                       $destin = App\Models\Shop::find($order->destination_id);
                                       if($order->is_transfomation_transfer) {
                                          $transfer_type_en = "Transformation";
                                          $transfer_type_sw = "Kubadilisha";
                                       }else{
                                          $transfer_type_en = "Normal";
                                          $transfer_type_sw = "Kawaida";
                                       }
                                     ?>
                                    <td style="text-align: center;"><a href="{{route('transfer-orders.show', encrypt($order->id))}}"> {{ sprintf('%05d', $order->order_no)}}</a></td>
                                    <td>{{ $destin->name }}</td>
                                    <td style="text-align: center;">
                                      {{$transfer->source_unit_cost}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{$transfer->destin_unit_cost}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format($transfer->destin_unit_cost-$transfer->source_unit_cost)}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format(($transfer->destin_unit_cost-$transfer->source_unit_cost)*$transfer->quantity)}}
                                    </td >
                                    @if($order->is_transfomation_transfer)
                                      <td style="text-align: center;">
                                        <span class="badge bg-warning">
                                          @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                        </span>
                                      </td>
                                    @else
                                    <td style="text-align: center;">
                                      <span class="badge bg-success">
                                        @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                      </span></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                              <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{$tqty}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($tscost)}}</th>
                                    <th style="text-align: center;">{{number_format($tdcost)}}</th>
                                    <th style="text-align: center;"></th>
                                    <th style="text-align: center;">{{number_format($total)}}</th>
                                    <th style="text-align: center;"></th>
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

            var stranstable = $('#stocktransfer').DataTable({
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
                        filename: "{{ trans('navmenu.transfer_report') }}_" + date,
                        title: "{{ trans('navmenu.transfer_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.transfer_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.transfer_report') }} \n Date: " + date
                    }
                ],
            });
            stranstable.buttons().container().appendTo('#stocktransfer_wrapper .col-md-6:eq(1)');

            var srectable = $('#stockreceiver').DataTable({
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
                        filename: "{{ trans('navmenu.stock_received_report') }}_" + date,
                        title: "{{ trans('navmenu.stock_received_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_received_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.stock_received_report') }} \n Date: " + date
                    }
                ],
            });
            srectable.buttons().container().appendTo('#stockreceiver_wrapper .col-md-6:eq(1)');
        })
    </script>
@endsection