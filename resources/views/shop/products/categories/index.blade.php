@extends('layouts.app')
    
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
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#new-form').show('fast');">New Category
                </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12" id="new-form" style="display: none;">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="border rounded p-4">
                        <h6>New Product Category</h6>
                        <hr>
                        <form class="row g-1 needs-validation" method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label">Category Name <span  style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" required placeholder="Enter Category Name" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Parent Category</label>
                                    <select class="form-select form-select-sm mb-3" name="parent_id" style="width: 100%;">
                                        <option value="">Select Parent</option>
                                        @foreach($categories as $key => $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label"> Image (Optional)</label>
                                    <input name="image" class="form-control form-control-sm mb-1" type="file" />
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="5" placeholder="Enter Category Description" class="form-control form-control-sm mb-3"></textarea>
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
            <form class="row g-1 needs-validation" method="POST" action="{{ url('f-categories') }}">
                @csrf
                <div class="col-md-12">
                    <div class="input-group mb-0">
                        <input type="text" name="search_key" id="search-key" class="form-control form-control-sm mb-1" placeholder="Please type something to search Product categories" autocomplete="off" aria-label="Input Keayword">
                        <button class="btn btn-default btn-sm" type="submit" id="button-addon2"><i class='fa fa-search'></i> Search</button>
                    </div>
                </div>
            </form>
        </div>
        @if($categories->count() > 0)
        @foreach($categories as $key => $category)
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="card mb-4">
                <div class="card-body text-start pro-img">
                    <a href="{{ route('categories.show', encrypt($category->id)) }}"><img class="d-block img-fluid mb-3 mx-auto" src="{{ asset('storage/'.$category->img_url) }}" alt="">
                    <h6 class="project-title text-primary mb-3">{{ $category->name }}</h6></a>
                    <p>{{ substr($category->description, 0, 100) }}</p>
                    <div class="progress mb-3" style="height: 5px;">
                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row g-1 text-center">
                        <div class="col-sm-6"><a href="{{ route('categories.edit', encrypt($category->id))}}"><i class="fa fa-edit"></i><br> Edit</a></div>
                        <div class="col-sm-6">
                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('categories.destroy', encrypt($category->id))}}" style="display: inline;"> 
                                @csrf
                                @method("DELETE")
                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')">
                                    <i class='fa fa-trash mr-1'></i><br> Delete</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card mb-4">
                <div class="card-body text-start pro-img">
                    <img class="d-block img-fluid mb-3 mx-auto" src="{{ asset('img/no-data.webp') }}" alt="">
                </div>
            </div>
        </div>
        @endif
    </div> <!-- Row end  -->
@endsection