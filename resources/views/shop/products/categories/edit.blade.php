@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products</li>
                    <li class="breadcrumb-item"><a href="{{ url('categories') }}">Product Categories</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#new-product-form').show('fast');">New Category
                </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="border rounded p-4">
                        <h6>New Product Category</h6>
                        <form class="row g-1 needs-validation" method="POST" action="{{ route('categories.update', encrypt($category->id)) }}"
                            enctype="multipart/form-data">
                                @csrf
                                {{ method_field('PATCH') }}
                                <div class="col-md-3">
                                    <label class="form-label">Category Name <span  style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" value="{{$category->name}}" required placeholder="Enter Category Name" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Parent Category</label>
                                    <select class="form-select form-select-sm mb-3" name="parent_id" style="width: 100%;">
                                        <option value="">Select Parent</option>
                                        @foreach($parentcategories as $key => $cat)
                                        @if($category->parent_id == $cat->id)
                                        <option value="{{$category->id}}" selected>{{$category->name}}</option>
                                        @else
                                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label"> Image (Optional)</label>
                                    <input name="image" class="form-control form-control-sm mb-1" type="file" />
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="5" placeholder="Enter Category Description" class="form-control form-control-sm mb-3">{{$category->description}}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                                    <a href="{{ url('categories') }}" class="btn btn-warning btn-sm">Cancel</a>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Row end  -->
@endsection