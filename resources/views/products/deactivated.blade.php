@extends('layouts.inv')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
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
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-new2">
                    <li class="nav-item"><a class="nav-link" href="{{ url('products') }}">{{trans('navmenu.products')}}</a></li>
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#new_product">Deactivated Products</a></li>
                </ul>
                <div class="tab-content py-1">
                    <div class="tab-pane fade show active table-responsive" id="product_list" role="tabpanel">
                        <div class="table-responsive" id="item-list">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.product_name')}}</th>
                                        <th>{{trans('navmenu.basic_uom')}}</th>
                                        <th>{{trans('navmenu.in_stock')}}</th>
                                        <th>{{trans('navmenu.price')}} ({{$currency}})</th>
                                        <th>{{trans('navmenu.date_registered')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $index => $product)
                                    <tr>
                                        <td>{{$product['id']}}</td>
                                        <td><a href="{{ route('products.show', encrypt($product['id']))}}">{{$product['name']}}</a></td>
                                        <td style="text-align: center;">{{$product['basic_uom']}}</td>
                                        <td style="text-align: center;">{{$product['in_stock']+0}}</td>
                                        <td style="text-align: center;">{{number_format($product['retail_price'], 2, '.', ',')}}</td>
                                        <td>{{$product['created_at']}}</td>
                                        <td style="text-align: center;">
                                            <a href="{{url('edit-product/'.Crypt::encrypt($product['id']))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            <a href="#" onclick="confirmDelete('<?php echo Crypt::encrypt($product['id']); ?>')">
                                                <i class="fa fa-trash" style="color: red;"></i>
                                            </a>      
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form id="frm-prod" action="{{url('delete-multiple-products')}}" method="POST">
                            @csrf
                            <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                        </form>
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

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
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
        })
    </script>
@endsection