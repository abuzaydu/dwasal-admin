@extends('layouts.inv')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
             document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
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
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <a href="{{url('products')}}" class="btn btn-outline-secondary btn-sm" >{{trans('navmenu.products')}}</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto ">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded" id="new-form">
                        <form class="row g-1 my-form" method="POST" action="{{ route('categories.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.category_name')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_category_name')}}" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.parent_cat')}}</label>
                                <select class="form-select form-select-sm mb-3" name="parent_id" style="width: 100%;">
                                    <option value="">{{trans('navmenu.select_parent_cat')}}</option>
                                    @foreach($categories as $key => $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"> Image (Optional)</label>
                                <input name="image" class="form-control form-control-sm mb-1" type="file" />
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">{{trans('navmenu.description')}}</label>
                                <textarea name="description" rows="1" placeholder="Enter Category Description" class="form-control form-control-sm mb-3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="reset" class="btn btn-info btn-sm">{{trans('navmenu.btn_reset')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="print_invoice" >
                        <table class="items mt-0 " style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Image</th>
                                    <th>{{trans('navmenu.category_name')}}</th>
                                    <th>{{trans('navmenu.description')}}</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $index => $category)
                                <tr>
                                    <td>{{$category->id}}</td>
                                    <td><img class="d-block img-fluid mb-3 mx-auto" src="{{ asset('storage/'.$category->img_url) }}" width="50" alt=""></td>
                                    @if(count($category->parents))
                                    <td><a href="{{ route('categories.show', encrypt($category->id))}}">{{ $category->parents->implode('-') }} <strong>-></strong> {{ $category->name }}</a></td>
                                    @else
                                    <td><a href="{{ route('categories.show', encrypt($category->id))}}">{{$category->name}}</a></td>
                                    @endif
                                    <td>{{$category->description}}</td>
                                    <td>{{$category->created_at}}</td>
                                    <td>
                                        <a href="{{ route('categories.edit', encrypt($category->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                        <form action="{{route('categories.destroy' , encrypt($category->id))}}" method="POST" style=" display : inline;" id="delete-form-{{$index}}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDelete({{$index}})"><i class="fa fa-trash" style="color: red;"></i>
                                            </a> 
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
@endsection