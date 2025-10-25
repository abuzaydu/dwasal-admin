@extends('layouts.inv')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function confirmDelete(id) {
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
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }
</script>

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
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                <form class="form row g-3" method="POST" action="{{ url('filter-products') }}">
                    @csrf
                    <div class="col-md-4">
                        <select name="category_id" onchange=' return this.form.submit()'
                            class="form-select form-select-sm mb-1">
                            @if ($isSearched)
                                <option>{{ $searchcat->name }}</option>
                                <option value="">All Products</option>
                            @else
                                <option value="">All Products</option>
                            @endif
                            @foreach ($categories as $key => $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($isSearched)
                        @if ($searchcat->children->count() > 0)
                            <div class="col-md-4">
                                <select name="category_id" onchange='if(this.value != 0) { this.form.submit(); }'
                                    class="form-select form-select-sm mb-1">
                                    <option>Select Sub Category</option>
                                    @foreach ($childrens as $key => $child)
                                        <option value="{{ $child->id }}">{{ $child->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endif
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#product_list">{{trans('navmenu.products')}}</a></li>
                        @if (Auth::user()->can('create-product'))
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#new_product">{{trans('navmenu.new_product')}}</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#import_file">{{trans('navmenu.import')}}</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#brands">Brands</a></li>

                        @if (!is_null($settings) && $settings->generate_barcode)
                        <li class="nav-item"><a class="nav-link"  data-bs-toggle="modal" href="#" data-bs-target="#barcode-modal">{{ trans('navmenu.generate_barcode') }}</a></li>
                        @endif
                        @endif
                        @if(Auth::user()->can('deactivate-product'))
                        <li class="nav-item"><a class="nav-link" href="{{ url('deactivated-products') }}"> Deactivated Products</a></li>
                        @endif
                    </ul>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active py-1" id="product_list" role="tabpanel">
                            <div class="table-responsive" id="item-list">
                                <table id="prodTable" class="table table-striped display nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Item Description</th>
                                            <th>{{ trans('navmenu.basic_uom') }}</th>
                                            <th>{{ trans('navmenu.in_stock') }}</th>
                                            <th>{{ trans('navmenu.price') }} ({{ $currency }})</th>
                                            @if($settings->retail_with_wholesale)
                                            <th>{{ trans('navmenu.wholesaleprice') }} ({{ $currency }})</th>
                                            @endif
                                            <th>{{ trans('navmenu.date_registered') }}</th>
                                            <th>{{ trans('navmenu.actions') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            @if (Auth::user()->can('delete-product'))
                            <form id="frm-prod" action="{{ url('delete-multiple-products') }}" method="POST">
                                @csrf
                                <button id="submitDel" class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                            </form>
                            @endif
                        </div>

                        <div class="tab-pane fade py-1" id="new_product" role="tabpanel">
                            <form class="row g-3 needs-validation" method="POST" action="{{ route('products.store') }}"
                            enctype="multipart/form-data">
                                @csrf
                                <div class="col-sm-4">
                                    <label class="form-label">{{ trans('navmenu.product_name') }} <span style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" required
                                        placeholder="{{ trans('navmenu.hnt_product_name') }}"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">{{ trans('navmenu.basic_uom') }} <span
                                            style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="basic_uom" required
                                        style="width: 100%;">
                                        @foreach ($units as $key => $unit)
                                            <option>{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{ trans('navmenu.product_category') }}</label>
                                    <div class="input-group">
                                        <select name="category_id" class="form-select form-select-sm mb-1">
                                            <option value="">{{ trans('navmenu.select_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#category-modal" data-backdrop="static"
                                                data-keyboard="false">New
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{ trans('navmenu.current_stock') }}</label>
                                    <input id="quantity_in" type="number" autocomplete="off" min="0"
                                        name="quantity_in" step="any"
                                        placeholder="{{ trans('navmenu.hnt_current_stock') }}"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label
                                        class="form-label">{{ trans('navmenu.unit_cost') }}({{ $currency }})</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any"
                                        name="unit_cost" placeholder="{{ trans('navmenu.hnt_buying_price') }}"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label
                                        class="form-label">{{ trans('navmenu.selling_price') }}({{ $currency }})</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="retail_price" placeholder="{{ trans('navmenu.hnt_selling_price') }}"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label
                                        class="form-label">{{ trans('navmenu.wholesale_price') }}({{ $currency }})</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any"
                                        name="wholesale_price" placeholder="{{ trans('navmenu.hnt_selling_price') }}"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                @if($settings->enable_exp_date)
                                <div class="col-sm-3">
                                    <label class="form-label">{{ trans('navmenu.expire_date') }}</label>
                                    <input type="date" name="expire_date"
                                        placeholder="{{ trans('navmenu.hnt_expire_date') }}"
                                        class="result form-control form-control-sm mb-1" id="expire_date">
                                </div>
                                @endif
                                <div class="col-sm-3">
                                    <label class="form-label">{{ trans('navmenu.product_code') }} </label>
                                    <input id="name" type="text" name="product_code"
                                        placeholder="{{ trans('navmenu.hnt_product_code') }}"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">{{ trans('navmenu.barcode_label') }}</label>
                                    <input name="barcode" class="form-control form-control-sm mb-1"
                                        placeholder="Scan/Type Barcode number." type="text" />
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">{{ trans('navmenu.location') }}</label>
                                    <input id="unit_price" type="text" name="location"
                                        placeholder="{{ trans('navmenu.hnt_location') }} (Optional)"
                                        class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label">General {{ trans('navmenu.description') }}</label>
                                    <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="{{ trans('navmenu.hnt_product_desc') }}"></textarea>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{ trans('navmenu.product_image') }} (Optional)</label>
                                    <input name="image" class="form-control form-control-sm mb-1" type="file" />
                                </div>

                                @if($settings->allow_more_product_desc)
                                <span>Detailed Descriptions (<span class="text-danger">For Fields that require units Enter value with its unit eg 6mm, 350ml, 10ft e.t.c.</span>)</span>
                                <div class="col-sm-2">
                                    <label class="form-label">Brand</label>
                                    <select class="form-select form-select-sm mb-1" name="brand">
                                        <option value="">Select a Brand</option>
                                        @foreach($brands as $br)
                                        <option>{{$br->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Model</label>
                                    <input type="text" name="model" class="form-control form-control-sm mb-1" placeholder="Enter Model">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Type</label>
                                    <input type="text" name="type" class="form-control form-control-sm mb-1" placeholder="Enter Product Type">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Size</label>
                                    <input type="text" name="size" class="form-control form-control-sm mb-1" placeholder="Enter Size">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Color</label>
                                    <input type="text" name="color" class="form-control form-control-sm mb-1" placeholder="Enter Color Name">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Length</label>
                                    <input type="text" name="length" class="form-control form-control-sm mb-1" placeholder="Enter Length">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Width</label>
                                    <input type="text" name="width" class="form-control form-control-sm mb-1" placeholder="Enter Width">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Thickness</label>
                                    <input type="text" name="thick" class="form-control form-control-sm mb-1" placeholder="Enter Thickness">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Height</label>
                                    <input type="text" name="height" class="form-control form-control-sm mb-1" placeholder="Enter Height">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Volume</label>
                                    <input type="text" name="volume" class="form-control form-control-sm mb-1" placeholder="Enter Volume">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Weight</label>
                                    <input type="text" name="weight" class="form-control form-control-sm mb-1" placeholder="Enter Weight">
                                </div>
                                @endif
                                <div class="col-md-12">
                                    <button type="submit"
                                        class="btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                                    <button type="reset"
                                        class="btn btn-warning btn-sm">{{ trans('navmenu.btn_reset') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade py-1" id="import_file" role="tabpanel">
                            <div class=" row">
                                <div class="col-sm-5">
                                    <form class="form" method="POST" action="{{ url('import-product') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <h4>Download Sample Excel file</h4>
                                        <a href="{{ url('excel-sample') }}" class="btn btn-primary btn-sm"><i
                                                class="fa fa-download"></i> Download</a>
                                        <br>
                                        <br>
                                        <div class="py-5">
                                            <div class="form-group">
                                                <h5>Choose Products excel file</h5>
                                                <div class="card mx-auto">
                                                    <div class="card-body">
                                                        <input id="exampleInputFile"
                                                            class="form-control form-control-sm mb-1 form-control form-control-sm mb-1-sm mb-1"
                                                            type="file" name="file" accept=".xlsx,.xls" required>
                                                    </div>
                                                </div>
                                                @if ($errors->has('file'))
                                                    <span class="help-block" style="color: red;">
                                                        <strong>{{ $errors->first('file') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn btn-success btn-sm"><i
                                                        class="fa fa-upload"></i> Upload</button>
                                                <a href="{{ url('products') }}" type="button"
                                                    class="btn btn-warning btn-sm mr-1">
                                                    <i class="fa fa-x"></i>Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-7">
                                    <h4>Instruction to Import Products</h4>
                                    <p>Please download the sample excel file below then use it to create your products excel
                                        file then upload and save.</p>
                                    <p>The following are the meaning of the basic_uom abriviations</p>
                                    <p><b>Note: </b>Make sure your units match these abriviations to ensure importing success.
                                    </p>
                                    <div class="row">
                                        @foreach($units as $unit)
                                        <span class="col-sm-2">{{$unit->unit_name}}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="brands" role="tabpanel">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <form class="row g-3 needs-validation" method="POST" action="{{ route('brands.store') }}">
                                        @csrf
                                        <div class="col-sm-12">
                                            <label class="form-label">Name <span style="color: red; font-weight: bold;">*</span></label>
                                            <input id="name" type="text" name="name" required placeholder="Enter Brand name" class="form-control form-control-sm mb-1">
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($brands as $key => $brand)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$brand->name}}</td>
                                                <td>
                                                    <a href="{{route('brands.edit' , encrypt($brand->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                    <form id="delete-brand-form-{{$key}}" method="POST" action="{{route('brands.destroy' , encrypt($brand->id))}}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteBrand({{$key}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="category-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{ trans('navmenu.new_category') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('categories.store') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="in_products" value="1">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.category_name') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" required
                                placeholder="{{ trans('navmenu.hnt_category_name') }}"
                                class="form-control form-control-sm mb-1 form-control form-control-sm mb-1-sm"
                                id="cat_name">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.parent_cat') }}</label>
                            <select class="form-control form-control-sm mb-1 form-select form-select-sm mb-1-sm"
                                name="parent_id" style="width: 100%;" id="cat_parent_id">
                                <option value="">{{ trans('navmenu.select_parent_cat') }}</option>
                                @foreach ($categories as $key => $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('navmenu.description') }}</label>
                            <textarea name="description" placeholder="Enter Category Description" class="form-control form-control-sm mb-1"
                                id="cat_descrption"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                        <button type="reset" class="btn btn-warning btn-sm">{{ trans('navmenu.btn_reset') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <!-- Generate barcode modal -->
    <div class="modal" id="barcode-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Generate Barcodes</h4>
                </div>
                <div class="modal-body" style="position: static">
                    <div class="text-center">
                        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($code, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}" alt="barcode" />
                    </div>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">Barcode Type  <a href="#" class="pull-right"><strong>{{$bsetting->code_type}}</strong></a></li>
                        <li class="list-group-item">Barcode Legnth <a href="#" class="pull-right"><strong>{{$bsetting->code_length}}</strong></a></li>
                        <li class="list-group-item">Barcode Height <a href="#" class="pull-right"><strong>{{$bsetting->height}}</strong></a></li>
                        <li class="list-group-item">Barcode Width <a href="#" class="pull-right"><strong>{{$bsetting->width}}</strong></a></li>
                        @if($bsetting->showcode)
                        <li class="list-group-item">Show Code <a href="#" class="pull-right"><strong>Yes</strong></a></li>
                        @else
                        <li class="list-group-item">Show Code <a href="#" class="pull-right"><strong>No</strong></a></li>
                        @endif
                    </ul>
                </div>
                <div class="modal-footer">
                    <a href="{{url('settings')}}" class="btn btn-warning btn-sm">Update Settings</a>
                    <a href="{{ url('generate-barcodes') }}" class="btn btn-primary btn-sm">Generate</a>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
@endsection
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "assets/vendor/libs/English.json";
            } else {
                languageUrl = "assets/vendor/libs/Swahili.json";
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var isrw = "<?php echo $settings->retail_with_wholesale; ?>";
            var tbcolumns = [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'basic_uom'
                    },
                    {
                        data: 'in_stock'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: "date"
                    },
                    {
                        data: 'action'
                    }
                ];
            if (isrw == 1) {
                tbcolumns = [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'basic_uom'
                    },
                    {
                        data: 'in_stock'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'wholesale_price'
                    },
                    {
                        data: "date"
                    },
                    {
                        data: 'action'
                    }
                ];
            }
            // DataTable
            var prodtable = $('#prodTable').DataTable({
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
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.getShopProducts') }}",
                    type: 'POST'
                },
                columns: tbcolumns
            });

            var counterCheckedProd = 0;
            $('#submitDel').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterCheckedProd++ : counterCheckedProd--;
                counterCheckedProd > 0 ? $('#submitDel').prop("disabled", false) : $('#submitDel').prop(
                    "disabled", true);
            });

            $('#submitDel').on('click', function(e) {
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
                        $('#frm-prod').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            })

            // Handle form submission event 
            $('#frm-prod').on('submit', function(e) {
                var form = this;
                var rows_selected = prodtable.column(0).checkboxes.selected();

                // Iterate over all selected checkboxes
                $.each(rows_selected, function(index, rowId) {
                    // Create a hidden element 
                    $(form).append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                    );
                });
            });
        });
    </script>