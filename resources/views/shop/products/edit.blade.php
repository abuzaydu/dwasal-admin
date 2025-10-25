@extends('layouts.app')

@section('page-styles')
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
@endsection
    <script type="text/javascript">
        function comnfirmCancel() {
            Swal.fire({
                title: 'Are you sure, You want to cancel?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete',
                cancelButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href="{{ url('products') }}";
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Action not cacelled', '', 'info')
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

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="border rounded p-4">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('products.update', encrypt($product->id)) }}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-sm-3">
                                <label class="form-label">Product Code </label>
                                <input id="name" type="text" name="product_code" value="{{$product->product_code}}" placeholder="Enter Product code" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Product Name <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" value="{{$product->name}}" required placeholder="Enter Product name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Basic UOM <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                                    @foreach ($units as $key => $unit)
                                    @if($product->basic_uom == $unit->unit_name)
                                    <option selected>{{ $unit->unit_name }}</option>
                                    @else
                                    <option>{{ $unit->unit_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Retail Price</label>
                                <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="retail_price" value="{{$product->retail_price}}" placeholder="Enter Retail price" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Wholesale Price</label>
                                <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="wholesale_price" value="{{$product->wholesale_price}}" placeholder="Enter Wholesale price" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Barcode Label</label>
                                <input name="barcode" value="{{$product->barcode}}" class="form-control form-control-sm mb-1" placeholder="Scan/Type Barcode number." type="text" />
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Storage Location</label>
                                <select class="form-select form-select-sm mb-1" name="storage_location_id">
                                    <option value="">Select Location</option>
                                    @foreach($slocations as $loc)
                                    @if($product->storage_location_id == $loc->id)
                                    <option value="{{$loc->id}}" selected>{{$loc->location_name}}</option>
                                    @else
                                    <option value="{{$loc->id}}">{{$loc->location_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Default Image (Optional)</label>
                                <input name="image" class="form-control form-control-sm mb-1" type="file" />
                            </div>
                            <div class="col-sm-12">
                                <label class="form-label">Short Description <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="short-desc" name="short_desc" value="{{$product->short_desc}}" class="form-control form-control-sm mb-1" placeholder="Please enter Short Description" required>
                            </div>
                            <div class="col-sm-12">
                                <label class="form-label">General Description</label>
                                <textarea id="description" name="description" class="form-control form-control-sm mb-1" placeholder="Please enter Descriptions">{!! $product->description !!}</textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm">Update Product</button>
                                <button type="button" onclick=" comnfirmCancel()" class="btn btn-warning btn-sm">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Row end  -->
@endsection
@section('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            var gArrayFonts = ['sans-serif', 'Poppins'];

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
@endsection