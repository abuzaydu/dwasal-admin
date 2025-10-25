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
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <form class="row g-1 dashform" action="{{url('sales-return-report')}}" method="POST">
                                @csrf
                                <div class="col-md-3">
                                    <select name="user_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                                        <option value="">{{trans('navmenu.select_by_seller')}} </option>
                                        @foreach($users as $user1)
                                        @if(!is_null($user) && $user->id == $user1->id)
                                        <option value="{{$user1->id}}" selected>{{$user1->first_name}} {{$user1->last_name}}</option>
                                        @else
                                        <option value="{{$user1->id}}">{{$user1->first_name}} {{$user1->last_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="customer_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
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
                                <input type="hidden" name="start_date" id="start_input" value="{{ $start_date}}">
                                <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                                <!-- Date and time range -->
                                <div class="col-md-6">  
                                    <div class="form-group">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                                <span><i class="fa fa-calendar"></i></span>
                                                <i class="fa fa-caret-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.form group -->
                                </div>
                            </form>
                        </div>
                        <div class="col-xs-12 table-responsive">
                            <table id="returns" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.user')}}</th>
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.return_date')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.last_updated')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returns as $index => $return)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$return->first_name}} {{$return->last_name}}</td>
                                        <td>{{$return->name}}</td>
                                        <td style="text-align: center;">{{number_format($return->sale_return_amount)}}</td>
                                        <td style="text-align: center;">{{number_format($return->sale_return_discount)}}</td>

                                        @if($settings->is_vat_registered)
                                        <td style="text-align: center;">{{number_format($return->return_tax_amount)}}</td>
                                        @endif
                                        <td style="text-align: center;"> {{$return->created_at}} </td>
                                        <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $return->updated_at)->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th><b>{{trans('navmenu.total')}}</b></th>
                                        <th></th>
                                        <th style="text-align: center;"><b>{{number_format($total_amount)}}</b></th>
                                        <th style="text-align: center;"><b>{{number_format($total_discount)}}</b></th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;"><b>{{$total_tax}}</b></th>
                                        @endif
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
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

            var retable = $('#returns').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_return_report') }}_" + date,
                        title: "{{ trans('navmenu.sales_return_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_return_report') }}_" + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: shop_name + "\n {{ trans('navmenu.sales_return_report') }} \n"+duration,
                    }
                ],
            });
            retable.buttons().container().appendTo('#returns_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection
