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
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <form class="row g-1 dashform" id="filter-by-product" action="{{ url('po-item-status-report') }}" method="POST" id="poitemform">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.supplier')}}</label>
                        <select name="supplier_id" id="supplier_id" required class="form-select form-select-sm mb-1 select2">
                            <option value="">All </option>
                            @foreach($suppliers as $key => $supplier)
                            @if($key == $currsupp)
                            <option value="{{$key}}" selected>{{$supplier}}</option>
                            @else
                            <option value="{{$key}}">{{$supplier}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class=" col-md-4">
                        <label class="form-label">Item Name</label>
                        <input id="search_key" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                        <ul id="searchResult2"></ul>
                    </div>
                    <input type="hidden" name="product_id" id="product-id">

                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">

                    <div class="col-md-4 pt-4">
                        <button type="button" class="btn btn-default pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </form>
        </div>
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="po-items" class="table table-striped display nowrap"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>PO No</th>
                                    <th>Invoice No</th>
                                    <th>D. Note</th>
                                    <th>GRN</th>
                                    <th>{{ trans('navmenu.product_name') }}</th>
                                    <th>UOM</th>
                                    <th style="text-align: center;">PO QTY</th>
                                    <th style="text-align: center;">Received QTY</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($poitems as $index => $poitem)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($poitem->date)) }}</td>
                                    <td>{{ $poitem->name }}</td>
                                    <td>{{ sprintf('%05d', $poitem->order_no)}}</td>
                                    <td>{{ $poitem->invoice_no }}</td>
                                    <td>{{ $poitem->delivery_note_no }}</td>
                                    <td>{{ sprintf('%05d', $poitem->grn_no) }}</td>
                                    <td>{{ $poitem->slug }}</td>
                                    <td>{{ $poitem->basic_uom }}</td>
                                    <td style="text-align: center;">{{ $poitem->qty+0 }}</td>
                                    <td style="text-align: center;">{{ $poitem->received_qty+0 }}</td>
                                    <td style="text-align: center;">{{ $poitem->qty-$poitem->received_qty }}</td>
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
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

            var stacktable = $('#po-items').DataTable({
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
                        filename: "{{ $title }}" ,
                        title: "{{ $title }}",
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "{{ $title }}",
                        title: shop_name + "\n {{ $title }} \n"+duration
                    }
                ],
            });
            stacktable.buttons().container().appendTo('#po-items_wrapper .col-md-6:eq(1)');

        })
    </script>
@endsection
    <?php
        $prodID = '';
        if (!is_null($product)) {
            $prodID = $product->id;
        }
    ?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var prodid = "<?php echo $prodID; ?>";
            $('#product-id').val(prodid);
            $('#search_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult2").empty();
                        $("#searchResult2").append("<li value=' '>All</li>");
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchResult2").append("<li value='"+id+"'>"+name+"</li>");
                        }

                        // binding click event to li
                        $("#searchResult2 li").bind("click",function(){
                            searchProduct(this);
                        });

                    }
                })
            });

            $('#supplier_id').on('change', function(){
                $('#filter-by-product').submit(); 
            });
        });

        function searchProduct(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $('#product-id').val(productid);
            $('#filter-by-product').submit(); 
            // $("#search_key").val('');
            $("#searchResult2").empty();  
        }
    </script>