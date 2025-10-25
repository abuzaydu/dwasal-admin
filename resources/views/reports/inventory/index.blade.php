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
                <form class="row g-3" id="filter-form" action="{{url('stock-reports')}}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Shops</label>
                        <select name="store" class="form-select form-select-sm mb-1" onchange='this.form.submit();'>
                            @if(!is_null($currstore))
                            <option value="{{$currstore->id}}">{{$currstore->name}}</option>
                            @endif
                            <option value="">All Stores</option>
                            @foreach($shops as $store)
                            <option value="{{$store->id}}">{{$store->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(!is_null($currstore))
                    @if(!is_null($locations))
                    <div class="col-md-3">
                        <label class="form-label">Location</label>
                        <select name="location" id="location" class="form-select form-select-sm mb-1">
                            <option value="">All</option>
                            @foreach($locations as $prodloc)
                            @if($location == $prodloc->location)
                            <option value="{{$prodloc->location}}" selected>{{$prodloc->location}}</option>
                            @else
                            <option value="{{$prodloc->location}}">{{$prodloc->location}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(!is_null($categories))
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" id="category" class="form-select form-select-sm mb-1">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            @if($categoryid == $cat->id)
                            <option value="{{$cat->id}}" selected>{{$cat->name}}</option>
                            @else
                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @endif
                    <div class="col-md-3">
                        <label class="form-label">Stock Status</label>
                        <select name="status" class="form-select form-select-sm mb-1" onchange='if(this.value != "") { this.form.submit(); }'>
                            @foreach($statuses as $st)
                            @if($st['value'] == $currstatus)
                            <option selected>{{$st['value']}}</option>
                            @else
                            <option>{{$st['value']}}</option>
                            @endif
                            @endforeach
                        </select>
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
                    <div class="table-responsive">
                        <table id="stockstatus" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    @if(is_null($currstore))
                                    @foreach($shops as $store)
                                    <th style="text-align: center;">{{$store->name}}</th>
                                    @endforeach
                                    @endif
                                    <th style="text-align: center;">{{trans('navmenu.total')}} {{trans('navmenu.in_stock')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.status')}}</th>  
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockstatus as $index => $stock)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$stock['name']}}</td>
                                    @if(is_null($currstore))
                                    @foreach($shops as $key => $store)
                                    <td style="text-align: center;">{{$stock[$key][$store->name]+0}}</td>
                                    @endforeach
                                    @endif
                                    <td style="text-align: center;">
                                        {{$stock['in_stock']+0}}
                                    </td>
                                    <td style="text-align: center;">
                                    @if($stock['status'] == 'In Stock')
                                    <span class="badge  bg-success">{{$stock['status']}}</span>
                                    @elseif($stock['status'] == 'Low Stock')
                                    <span class="badge  bg-warning">{{$stock['status']}}</span>
                                    @else
                                    <span class="badge  bg-danger">{{$stock['status']}}</span>
                                    @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
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

            var stocktable = $('#stockstatus').DataTable({
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
                        filename: "{{ trans('navmenu.stock_status_report') }}_" + date,
                        title: "{{ trans('navmenu.stock_status_report') }} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_status_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.stock_status_report') }} \n Date : " + date
                    }
                ],
            });
            stocktable.buttons().container().appendTo('#stockstatus_wrapper .col-md-6:eq(1)');


            $('#location').on('change', function(){
                $('#category').val('');
                $('#filter-form').submit();
            });

            $('#category').on('change', function(){
                $('#location').val('');
                $('#filter-form').submit();
            });
        });


    </script>
@endsection