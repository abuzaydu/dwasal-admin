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


        function showHidertypeForm(elem) {
            var newform = document.getElementById('new-rtype-form');
            var newbtn = document.getElementById('new-rtype-btn');
            var itemlist = document.getElementById('rtype-list');
            var newtitle = document.getElementById('new-rtype-title');
            var listtitle = document.getElementById('rtype-list-title');
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

        function confirmDeletertype(id){

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
                document.getElementById('delete-rtype-form-'+id).submit();
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
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')" style="margin: 5px;"><i class="bx bxs-plus-square"></i>New Booking Agent</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="p-4 border rounded" id="new-form" style="display: none;">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('booking-agents.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">Name <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" placeholder="Enter agent Name" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="mobile" placeholder="Enter Agent Mobile ">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="email" placeholder="Enter Agent Email address">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Address </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="address" placeholder="Enter Agent Addres">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">{{trans('navmenu.tin')}}</label>
                                        <input id="tin" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "999-999-999"' data-mask>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">{{trans('navmenu.vrn')}}</label>
                                        <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label">{{trans('navmenu.currency')}}</label>
                                        <select name="currency" id="currency" class="form-select form-select-sm mb-1" required>
                                            @foreach($currencies as $curr)
                                            <option>{{$curr->code}}</option>
                                            @endforeach
                                        </select>
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
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bagents as $key => $agent)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('booking-agents.show', encrypt($agent->id))}}">{{$agent->name}}</a></td>
                                            <td>{{$agent->mobile}}</td>   
                                            <td>{{$agent->email}}</td>
                                            <td>{{$agent->address}}</td>
                                            <td>{{$agent->created_at}}</td>
                                            <td>
                                                <a href="{{route('booking-agents.edit', encrypt($agent->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('booking-agents.destroy' , encrypt($agent->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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