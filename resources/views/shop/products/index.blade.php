@extends('layouts.app')

@section('page-styles')
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
@endsection

    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete',
                cancelButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <a href="{{ route('products.create') }}" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> Add Product</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-6">
                    <div class="card top_counter mb-2">
                        <div class="body">
                            <div class="icon text-info"><i class="fa fa-building-o"></i> </div>
                            <div class="content">
                                <div class="text">In-Store Sales</div>
                                <h5 class="number">Tsh 10,453,251</h5>
                                <small class="displayblock">47% Average <i class="zmdi zmdi-trending-up"></i></small>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-6">
                    <div class="card top_counter mb-2">
                        <div class="body">
                            <div class="icon text-warning"><i class="fa fa-shopping-cart"></i> </div>
                            <div class="content">
                                <div class="text">Online Sales</div>
                                <h5 class="number">Tsh 12,502,560</h5>
                                <small class="displayblock">57% Average <i class="zmdi zmdi-trending-up"></i></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-6">
                    <div class="card top_counter mb-2">
                        <div class="body">
                            <div class="icon text-danger"><i class="icon-present"></i> </div>
                            <div class="content">
                                <div class="text">Discount Sales</div>
                                <h5 class="number">Tsh 4,340,500</h5>
                                <small class="displayblock">5% Average <i class="zmdi zmdi-trending-up"></i></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-6">
                    <div class="card top_counter mb-2">
                        <div class="body">
                            <div class="icon"><i class="icon-support"></i> </div>
                            <div class="content">
                                <div class="text">Affiliate sales</div>
                                <h5 class="number">Tsh 3, 320,217</h5>
                                <small class="displayblock">3% Average <i class="zmdi zmdi-trending-up"></i></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-12">
            <div class="col-lg-12 col-md-12 col-sm-12" id="new-form" style="display: none;">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="border rounded p-4">
                            <h5>New  Product</h5>
                            <hr>
                            <form class="row g-3 needs-validation" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col-sm-3">
                                    <label class="form-label">Product Code </label>
                                    <input id="name" type="text" name="product_code" placeholder="Enter Product code" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Product Name <span style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" required placeholder="Enter Product name" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Basic UOM <span style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                                        @foreach ($units as $key => $unit)
                                        <option>{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Current Stock</label>
                                    <input id="quantity_in" type="number" autocomplete="off" min="0" name="quantity_in" step="any" placeholder="Enter Current Stock Quntity" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Unit Cost</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="unit_cost" placeholder="Enter Unit Cost" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Retail Price</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="retail_price" placeholder="Enter Retail price" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Wholesale Price</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="wholesale_price" placeholder="Enter Wholesale price" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Product Category</label>
                                    <select name="product_id" class="form-select form-select-sm mb-1">
                                        <option value="">--Select Category--</option>
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Barcode Label</label>
                                    <input name="barcode" class="form-control form-control-sm mb-1" placeholder="Scan/Type Barcode number." type="text" />
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Storage Location</label>
                                    <select class="form-select form-select-sm mb-1" name="storage_location_id">
                                        <option value="">Select Location</option>
                                        @foreach($slocations as $loc)
                                        <option value="{{$loc->id}}">{{$loc->location_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Default Image (Optional)</label>
                                    <input name="image" class="form-control form-control-sm mb-1" type="file" />
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label">General Description</label>
                                    <textarea id="description" name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="Please enter Descriptions"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success btn-sm">Add</button>
                                    <button type="reset" onclick="$('#new-form').hide('slow');" class="btn btn-warning btn-sm">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12 col-md-12 col-sm-12">
                @include('flash-message')
                <form class="row g-1 needs-validation" method="POST" action="{{ url('f-products') }}">
                    @csrf
                    <div class="col-md-3">
                        <select class="form-select form-select-sm mb-1" name="category_id" onchange="this.form.submit()">
                            <option value="">Category</option>
                            @foreach($categories as $category)
                            @if($currcatid == $category->id)
                            <option value="{{$category->id}}" selected>{{$category->name}}</option>
                            @else
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm mb-1" name="stock_status" onchange="this.form.submit()">
                            @if($stock_status == 'In Stock')
                            <option value="">Stock</option>
                            <option selected>In Stock</option>
                            <option>Out Of Stock</option>
                            @elseif($stock_status == 'Out Of Stock')
                            <option value="">Stock</option>
                            <option>In Stock</option>
                            <option selected>Out Of Stock</option>
                            @else
                            <option value="">Stock</option>
                            <option>In Stock</option>
                            <option>Out Of Stock</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-0">
                            <input type="text" name="search_key" id="search-key" class="form-control form-control-sm mb-1" placeholder="Please type something to search Product products" autocomplete="off" aria-label="Input Keayword">
                            <button class="btn btn-default btn-sm" type="submit" id="button-addon2"><i class='fa fa-search'></i> Search</button>
                        </div>
                    </div>
                </form>
            </div>
            @if($products->count() > 0)
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-hover js-basic-example dataTable table-custom table-striped m-b-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="2">Product</th>
                                        <th style="text-align: center;">Category</th>
                                        <th style="text-align: center;">Stock</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $key => $product)
                                    <?php $catname = '';
                                        $prodcat = $product->categories()->select('name')->first();
                                        if (!is_null($prodcat)) {
                                            $catname = $prodcat->name;
                                        }

                                        $stock = 'In Stock';
                                        if ($product->available_qty <= 0) {
                                            $stock = 'Out Of Stock';
                                        }
                                    ?>
                                    <tr>
                                        <td class="width45">
                                            <img src="{{ asset('storage/'.$product->image_url) }}" class="product-img" alt="">
                                        </td>
                                        <td>
                                            <h6 class="mb-0"><a href="{{ route('products.show', encrypt($product->id)) }}"> {{$product->name}}</a></h6>
                                            <span>{{ substr($product->short_desc, 0, 50)}} ...</span>
                                        </td>
                                        <td style="text-align: center;"><span>{{$catname}}</span></td>
                                        <td style="text-align: center;"><span>{{$stock}}</span></td>
                                        <td style="text-align: center;">{{$product->available_qty+0}}</td>
                                        <td style="text-align: center;">
                                            <a href="{{ route('products.edit', encrypt($product->id))}}"><i class="fa fa-edit"></i> Edit</a> | 
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('products.destroy', encrypt($product->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')">
                                                    <i class='fa fa-trash mr-1'></i> Delete</a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card mb-4">
                    <div class="card-body text-start pro-img">
                        <img class="d-block img-fluid mb-3 mx-auto" src="{{ asset('img/no-data.webp') }}" alt="">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div> <!-- Row end  -->
@endsection
@section('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            var gArrayFonts = ['sans-serif','Poppins'];

            $('#description').summernote({
                fontNames: gArrayFonts,
                fontNamesIgnoreCheck: gArrayFonts,
                fontSizes: ['8', '9', '10', '11', '12', '13', '14', '15', '16', '18', '20', '22' , '24', '28', '32', '36', '40', '48'],
                followingToolbar: false,
                dialogsInBody: true,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style'],
                    ['style', ['clear', 'bold', 'italic', 'underline']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],       
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['view', ['codeview']]
                ],
                height:200
            });

            // $('.note-editor .note-btn').on('click', function() {
            //     $(this).next().toggleClass("show");
            // });
        });
    </script>

    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.print.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/tables/jquery-datatable.js') }}"></script>
@endsection