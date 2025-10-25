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
                <form class="row g-3 dashform" action="{{url('transfer-received-report')}}" method="POST" id="stockform">
                @csrf
                <div class="col-md-4 pt-1">
                    <select name="store" class="form-select form-select-sm mb-1" onchange='this.form.submit();'>
                        @if (!is_null($currstore))
                            <option value="{{ $currstore->id }}">{{ $currstore->name }}</option>
                        @endif
                        <option value="">All Stores</option>
                        @foreach ($shops as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                <div class="col-md-8 float-md-end">
                    <div class="input-group">
                        <button type="button" class="btn btn-white pull-right mb-3" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <a class="btn btn-success btn-sm pull-right" href="{{ url('transfer-report')}}">{{trans('navmenu.stock_transfered')}}</a>
                </div> -->
            </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    <div class="row">
        <div class="col-md-12 mx-auto pt-2">
            
            <div class="card">
                <div class="card-body" id="transfered">
                    <div class="col-xs-12 table-responsive">
                        <table id="stockreceiver" class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0; text-align: center;">
                                <tr>
                                    <th>#</th>
                                    <th style="text-align: center;">{{trans('navmenu.sto_no')}}</th>
                                    <th style="text-align: center;">Date Time</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                    <th style="text-align: center;">Net Amount</th>
                                    <th>{{trans('navmenu.source_shop')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tqty = 0; $total = 0; $total_vat = 0;?>
                                @foreach($transfers as $index => $transfer)
                                <?php 
                                    $tqty += $transfer->quantity;  $total += ($transfer->source_unit_price*$transfer->quantity); 
                                    $vat = ($transfer->source_unit_price*$transfer->quantity)*($settings->tax_rate/100);
                                    $total_vat += $vat;
                                    $destin = App\Models\Shop::find($transfer->destination_id);
                                ?>
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td style="text-align: center;"><a href="{{ url('sto-value/'.encrypt($transfer->transfer_order_id)) }}"> {{ sprintf('%05d', $transfer->order_no)}}</a></td>
                                    <td style="text-align: center;">{{date('d-m-Y H:i', strtotime($transfer->created_at))}}</td>
                                    <td>{{$transfer->name}}</td>
                                    <td style="text-align: center;">{{$transfer->quantity+0}}</td>
                                    <td style="text-align: center;">
                                      {{number_format($transfer->source_unit_price, 2, '.', ',')}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format($transfer->source_unit_price*$transfer->quantity, 2, '.', ',')}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format($vat, 2, '.', ',')}}
                                    </td>
                                    <td style="text-align: center;">
                                      {{number_format(($transfer->source_unit_price*$transfer->quantity)+$vat, 2, '.', ',')}}
                                    </td>
                                    <td>{{ $destin->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                              <tr>
                                    <th>#</th>
                                    <th></th>
                                    <th></th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{$tqty}}</th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($total_vat, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($total+$total_vat, 2, '.', ',')}}</th>
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