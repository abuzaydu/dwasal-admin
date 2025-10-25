@extends('layouts.acc')
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


        function showHidedepmethodForm(elem) {
            var newform = document.getElementById('new-depmethod-form');
            var newbtn = document.getElementById('new-depmethod-btn');
            var itemlist = document.getElementById('depmethod-list');
            var newtitle = document.getElementById('new-depmethod-title');
            var listtitle = document.getElementById('depmethod-list-title');
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


        function showHideGradeForm(elem) {
            var newform = document.getElementById('new-grade-form');
            var newbtn = document.getElementById('new-grade-btn');
            var itemlist = document.getElementById('grade-list');
            var newtitle = document.getElementById('new-grade-title');
            var listtitle = document.getElementById('grade-list-title');
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

        function confirmDeletedepmethod(id){

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
                document.getElementById('delete-depmethod-form-'+id).submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmDeleteGrade(id){

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
                document.getElementById('delete-grade-form-'+id).submit();
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
                <a href="{{ url('asset-records')}}" class="btn btn-warning btn-sm">Asset Records</a>
                <button type="button" id="new-depmethod-btn" class="btn btn-primary btn-sm" onclick="showHidedepmethodForm('show')"><i class="bx bxs-plus-square"></i>New Method</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-depmethod-form" style="display: none;">
                        <form class="form row g-3" method="POST" action="{{route('dep-methods.store')}}">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Depreciation Method<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="dep_method" required placeholder="Enter metho name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Abbreviation <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="abbreviation" required placeholder="Enter method Abbreviation" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description </label>
                                <input id="name" type="text" name="description" placeholder="Enter description (Optional)" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideDevceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive" id="depmethod-list">
                        <table id="depmethods" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Depreciation Method</th>
                                    <th>Abbreviation</th>
                                    <th>Description</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depmethods as $key => $depmethod)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$depmethod->dep_method}}</a></td>
                                    <td>{{$depmethod->abbreviation}}</td>
                                    <th>{{$depmethod->description}}</th>
                                    <td>{{$depmethod->created_at}}</td>
                                    <td>
                                        <a href="{{route('dep-methods.edit', encrypt($depmethod->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form method="POST" action="{{route('dep-methods.destroy' , encrypt($depmethod->id))}}" id="delete-depmethod-form-{{$key}}" style="display: inline;"> 
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDeletedepmethod({{$key}})">
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
    <!--end row-->
@endsection