@extends('layouts.app')
    <script>
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            var itemlist = document.getElementById('item-list');
            var newtitle = document.getElementById('new-title');
            var listtitle = document.getElementById('list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }

        function confirmDelete(id){
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
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')" style="margin: 5px;"><i class="bx bxs-plus-square"></i>New Category</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="cats" role="tabpanel">
                            <div class="p-4 border rounded" id="new-form" style="display: none;">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('customer-categories.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-6">
                                        <label for="validationCustom01" class="form-label">{{trans('navmenu.name')}}</label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" placeholder="Category Name" required>
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please provide a cat name.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="validationCustom03" class="form-label">{{trans('navmenu.description')}}</label>
                                        <input type="tel" class="form-control form-control-sm mb-1" id="validationCustom03" name="description" placeholder="{{trans('navmenu.hnt_cat_desc')}}">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm px-4 radius-30" type="submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive" id="item-list">
                                <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.name')}}</th>
                                            <th>{{trans('navmenu.description')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories as $key => $cat)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$cat->cat_name}}</td>
                                            <td>{{$cat->description}}</td>
                                            <td>{{$cat->created_at}}</td>
                                            <td>
                                                <a href="{{route('customer-categories.edit', encrypt($cat->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('customer-categories.destroy' , encrypt($cat->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{$key}})">
                                                    <i class="fa fa-trash" style="color: red;"></i>
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
        </div>
    </div>
    <!--end row-->
@endsection