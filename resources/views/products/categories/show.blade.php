@extends('layouts.inv')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
    <style>

        #filterInput {
            margin-bottom: 20px;
            padding: 10px;
            width: calc(100% - 20px);
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #filterInput-2 {
            margin-bottom: 20px;
            padding: 10px;
            width: calc(100% - 20px);
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .listItem {
            padding-top: 5px;
/*            margin: 2px 0;*/
            border-radius: 5px;
            border-bottom: 1px solid gray;
            transition: background-color 0.3s;
        }
        .listItem-2 {
            padding-top: 5px;
/*            margin: 2px 0;*/
            border-radius: 5px;
            border-bottom: 1px solid gray;
            transition: background-color 0.3s;
        }

        /*.listItem:hover {
            background-color: #e9e9e9;
        }
*/
        .hidden {
            display: none;
        }
    </style>
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{url('categories')}}">{{trans('navmenu.categories')}}</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card ">
                <div class="card-body">
                    <table class="table table-striped" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td><img class="d-block img-fluid mb-3 mx-auto" src="{{ asset('storage/'.$category->img_url) }}" width="50" alt=""></td>
                            
                                <td>{{trans('navmenu.category_name')}} :<br> <strong>{{$category->name}}</strong></td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.description')}} :</td>
                                <td><strong>{{$category->description}}</strong></td>
                                <td> <a href="{{ route('categories.edit', encrypt($category->id))}}"><i class="fa fa-edit"></i><b>{{trans('navmenu.edit_category')}}</b></a></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="nav-tabs">
                        <ul class="nav nav-tabs nav-tabs-new2">
                            <li class="nav-item"><a class="nav-link active" href="#product" data-bs-toggle="tab">{{trans('navmenu.cat_products')}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#add-product" data-bs-toggle="tab">{{trans('navmenu.add_products')}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#remove-product" data-bs-toggle="tab">{{trans('navmenu.remove_products')}}</a></li>
                        </ul>
                        <div class="tab-content pt-3">
                            <div class="active tab-pane" id="product">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{trans('navmenu.product_name')}}</th>
                                                <th style="text-align: center;">In Stock</th>
                                                <th style="text-align: center;">{{trans('navmenu.basic_uom')}}</th>
                                                <th>{{trans('navmenu.actions')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cat_products as $index => $product)
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td><a href="{{ route('products.show' , encrypt($product->id)) }}">@if(!is_null($product->product_code)){{$product->product_code}} - @endif{{$product->name}}</a></td>
                                                <td style="text-align: center;">{{$product->in_stock+0}}</td>
                                                <td style="text-align: center;">{{$product->basic_uom}}</td>
                                                <td>
                                                    <a href="{{ route('products.edit' , encrypt($product->id)) }}">
                                                        <i class="fa fa-edit" style="color: blue;"></i>
                                                    </a>
                                                    <a href="{{ route('products.destroy' , encrypt($product->id)) }}" onclick="return confirm('Are you sure you want to delete this record')">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>      
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>                
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="add-product">
                                <div class="col-md-12">
                                    <label for="product_id" class="form-label">{{trans('navmenu.select_products_to_add')}}</label>
                                    <input type="text" id="filterInput" placeholder="Filter list...">
                                </div>

                                <form class="my-form row g-1" method="POST" action="{{url('add-product')}}">
                                    @csrf
                                    <input type="hidden" name="category_id" value="{{$category->id}}">
                                    <div class="col-md-9">
                                        <ul id="list">
                                            @foreach ($products as $pkey => $product)
                                            <li class="listItem"><label style="page-break-inside:avoid; page-break-after:auto; font-weight: normal;">{{ html()->checkbox('product_id[]')->value($product->id)->checked(in_array($product->id, $currProducts))->id('perm-'.$pkey) }}
                                                @if(!is_null($product->product_code)){{$product->product_code}}@endif {{ $product->name }}</label></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-3 pt-0">
                                        <button class="btn btn-success btn-sm">{{trans('navmenu.btn_add')}} Selected</button>
                                    </div>
                                </form>
                              </div>
                              <!-- /.tab-pane -->

                              <!-- /.tab-pane -->
                              <div class="tab-pane" id="remove-product">
                                <div class="col-md-12">
                                    <label for="product_id" class="form-label">{{trans('navmenu.select_products_to_add')}}</label>
                                    <input type="text" id="filterInput-2" placeholder="Filter list...">
                                </div>
                                <form class="row g-1" method="POST" action="{{url('remove-product')}}">
                                    @csrf
                                    <input type="hidden" name="category_id" value="{{$category->id}}">
                                    <div class="col-md-9">
                                        <ul id="list">
                                            @foreach ($products as $pkey => $product)
                                            <li class="listItem-2"><label style="page-break-inside:avoid; page-break-after:auto; font-weight: normal;">{{ html()->checkbox('product_id[]')->value($product->id)->checked(in_array($product->id, $currProducts))->id('perm-'.$pkey) }}
                                                @if(!is_null($product->product_code)){{$product->product_code}}@endif {{ $product->name }}</label></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-3 pt-4">
                                        <button class="btn btn-warning btn-sm">{{trans('navmenu.btn_remove')}}</button>
                                        <a class="btn btn-danger btn-sm" href="{{ url('remove-all-prods-from-category/'.$category->id)}}">{{trans('navmenu.btn_remove_all')}}</a>
                                    </div>
                                  </div>
                                </form>
                              </div>
                          <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('#example1').DataTable();

            $('#add-products').select2({
                theme: 'bootstrap4',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });

            $('#remove-products').select2({
                theme: 'bootstrap4',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });
        });
    </script>
@endsection
    <script>
        window.addEventListener("load", () => {
            // Fully loaded!
            // JavaScript Filter Functionality

            const filterInput =
                document.getElementById('filterInput');
            const listItems =
                document.querySelectorAll('.listItem');

            filterInput.addEventListener('input', function () {
                const filterValue = filterInput.value.toLowerCase();
                if (filterValue.length > 2) {
                    listItems.forEach(function (item) {
                        const text = item.innerText.toLowerCase();
                        if (text.includes(filterValue)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                }
            });

            const filterInput2 =
                document.getElementById('filterInput-2');
            const listItems2 =
                document.querySelectorAll('.listItem-2');

            filterInput2.addEventListener('input', function () {
                const filterValue = filterInput2.value.toLowerCase();
                if (filterValue.length > 2) {
                    listItems2.forEach(function (item) {
                        const text = item.innerText.toLowerCase();
                        if (text.includes(filterValue)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                }
            });
        });
    </script>