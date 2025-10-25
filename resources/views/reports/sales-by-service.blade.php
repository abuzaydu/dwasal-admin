@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="dashform row g-1" action="{{url('sales-by-service')}}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <select name="customer_id" id="customer-id" class="form-select form-select-sm mb-1 select2" onchange="this.form.submit()">
                            <option value="">{{trans('navmenu.select_by_customer')}} </option>
                            @foreach($customers as $cust)
                            @if(!is_null($customer) && $customer->id == $cust->id)
                            <option value="{{$cust->id}}" selected>{{$cust->name}}</option>
                            @else
                            <option value="{{$cust->id}}">{{$cust->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="service_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                            <option value="">Select Service</option>
                            @foreach($services as $serv)
                            @if(!is_null($service) && $service->id == $serv->id)
                            <option value="{{$service->id}}" selected>{{$service->name}}</option>
                            @else
                            <option value="{{$serv->id}}">{{$serv->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class=" col-md-4">
                        <div class="input-group">
                            <button type="button" class="btn btn-white pull-right" id="reportrange">
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
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="salesservice" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('navmenu.date')}}</th>
                                    <th>{{trans('navmenu.service')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                <tr>
                                    <td>{{date('d/m/Y', strtotime($sale->created_at))}}</td>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity}}</td>
                                    <td style="text-align: center;">{{number_format($sale->price-$sale->discount, 0, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->total-$sale->total_discount, 0, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_selling)}}/=</strong></th>
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
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

            var sptable = $('#salesservice').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                "bInfo": true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_service') }}_" + date,
                        title: "{{ trans('navmenu.sales_by_service') }}",
                        messageTop: duration,
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_service') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.sales_by_service') }} \n" +duration,
                    }
                ],
            });
            sptable.buttons().container().appendTo('#salesservice_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection