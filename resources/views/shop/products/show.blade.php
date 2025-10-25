@extends('layouts.app')

@section('page-styles')
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
@endsection
    <script type="text/javascript">
        function confirmDeleteUnit(id) {
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
                    document.getElementById('delete-unit-form-' + id).submit();
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Action not cacelled', '', 'info')
                }
            })
        }

        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            if (elem == 'show') {
                newform.style.display = 'block';
            }else{
                newform.style.display = 'none';
            }
        }

        function showHideImgForm(elem) {
            var newform = document.getElementById('new-img-form');
            if (elem == 'show') {
                newform.style.display = 'block';
            }else{
                newform.style.display = 'none';
            }
        }
        function confirmDeleteImage(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this Image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete',
                cancelButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-img-form-' + id).submit();
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

    <div class="row clearfix product-details">
        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px;">
            <h5 style="font-weight: 200; color: gray;">CODE: {{$product->product_code}}</h5>
            @if($product->is_active)
            <span class="text-success alert-success p-2" style="border-radius: 30px;">Active</span> 
            <span class="text-danger alert-danger p-2" style="border-radius: 30px;">On Sale</span>
            @else
            <span class="text-danger alert-danger"></span>
            @endif
            <div class="action pull-right mb-3">
                <a href="{{ route('products.edit', encrypt($product->id))}}" class="btn btn-outline-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 mb-5">
            <h6 class="mb-0 text-center">Product Unit(s)<a href="#" class=" font-13 btn  btn-primary btn-sm float-end" onclick="showHideImgForm('show')"> New Image</a></h6>
            <hr>
            <div class="preview-pic tab-content border rounded">
                @if(!is_null($product->image_url))
                <div class="tab-pane active" id="pic-1">
                    <img src="{{ asset('storage/'.$product->image_url) }}" />
                </div>
                @endif
            </div>
            <div class="border rounded p-4" id="new-img-form" style="display: none; background: #fefefe;">
                <form class="my-form" method="POST" action="{{ route('product-images.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="form-label">Default Image (Optional)</label>
                            <input name="image" class="form-control form-control-sm mb-1" type="file" />
                        </div>
                        <div class="col-sm-12">
                            <a href="#" onclick="showHideImgForm('hide')" class="btn btn-warning btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Add</button>
                        </div>
                    </div>
                </form>
            </div>    
            <div class="row">
                @foreach($images as $ikey => $pimage)
                <div class="col-sm-6">
                        <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteImage('<?php echo $ikey; ?>')" title="Tap To Remove"><img src="{{ asset('storage/'.$pimage->img_url) }}" /></a>
                    <form id="delete-img-form-{{$ikey}}" method="POST" action="{{ route('product-images.destroy', encrypt($pimage->id))}}"> 
                        @csrf
                        @method("DELETE")
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-12 mb-5">
            <div class="details border rounded p-4">
                <h3 class="product-title">{{$product->name}}</h3>
                <p class="product-description">{{ $product->short_desc }}</p>
                <h5 class="product-units">
                    <h6 class="mb-0 text-center">Product Unit(s) @if($punits->count() > 0)<a href="#" class=" font-13 btn  btn-primary btn-sm float-end" onclick="showHideForm('show')"> New</a>@else <a href="{{ url('create-basic-unit/'.encrypt($product->id)) }}" class="btn btn-primary btn-sm float-end"> Set Basic Unit</a> @endif</h6>
                    <div class="border rounded p-4" id="new-form" style="display: none; background: #fefefe;">
                        <form class="my-form" method="POST" action="{{ route('product-units.store') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{$product->id}}">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-label">Unit <span style="color: red;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="unit_name" required>
                                        <option value=""> ---Select--</option>
                                        @foreach($units as $key => $unit)
                                            <option>{{$unit->unit_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label">Qty equivalent to Basic Unit <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-3" type="number" min="0" step="any" name="qty_equal_to_basic" placeholder="Enter quantity" required>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Unit Price <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-3" type="number" name="unit_price" placeholder="Enter Unit Price" required>
                                </div>
                                <div class="col-sm-6">
                                    <a href="#" onclick="showHideForm('hide')" class="btn btn-warning btn-sm">Cancel</a>
                                    <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-hover " style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align: left;">Unit Name</th>
                                <th style="text-align: center;">Unit Price</th>
                                <th style="text-align: center;">Is Default</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($punits as $key => $punit)
                            <?php $equivalent = '';
                                if (!$punit->is_basic) {
                                    $basic = App\Models\ProductUnit::where('product_id', $punit->product_id)->where('is_basic', true)->first();
                                    if (!is_null($basic)) {
                                        $equivalent = '1 '.$punit->unit_name.' = '.$punit->qty_equal_to_basic.' '.$basic->unit_name;
                                    }
                                }
                            ?>
                            <tr>
                                <td style="text-align: left;">{{$punit->unit_name}} @if($equivalent != '') ({{$equivalent}})@endif</td>
                                <td style="text-align: center;">{{number_format($punit->unit_price, 2, '.', ',') }}</td>
                                <td style="text-align: center;">@if($punit->is_basic) Yes @else No @endif</td>
                                <td style="text-align: center;">
                                    <a href="{{ route('product-units.edit', encrypt($punit->id))}}"><i class="fa fa-edit"></i> Edit</a> | 
                                    <form id="delete-unit-form-{{$key}}" method="POST" action="{{ route('product-units.destroy', encrypt($punit->id))}}" style="display: inline;"> 
                                        @csrf
                                        @method("DELETE")
                                        <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteUnit('<?php echo $key; ?>')">
                                            <i class='fa fa-trash mr-1'></i> Delete</a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </h5>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 mb-5">
            <hr>
            <h3>More Details</h3>
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