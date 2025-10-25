@extends('layouts.inv')
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
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')" style="margin: 5px;"><i class="bx bxs-plus-square"></i>New Room</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-end  px-1 py-1">
                        <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist"  >
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab_0" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-group font-18 me-1'></i></div>
                                        <div class="tab-title">Rooms</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_1" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-ul font-18 me-1'></i></div>
                                        <div class="tab-title">Room Types</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="p-4 border rounded" id="new-form" style="display: none;">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">Room No. <span style="color: red;">*</span></label>
                                        <input type="text" name="room_no" class="form-control form-control-sm " placeholder="Enter Room number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Name </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" placeholder="Enter Room Name" required>
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please provide a room name.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Room Type</label>
                                        <select name="room_type_id" class="form-select form-select-sm mb-1">
                                            <option value=""> -Select Room Type-</option>
                                            @foreach($roomtypes as $type)
                                            <option value="{{$type->id}}">{{$type->name}}</option>
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
                                            <th>Room No.</th>
                                            <th>Name</th>
                                            <th>Room Type</th>
                                            <th style="text-align: center;">Price Per Night ({{$defcurr->code}})</th>
                                            <th style="text-align: center;">Capacity</th>
                                            <th>Description</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rooms as $key => $room)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$room->room_no}}</td>
                                            <td>{{$room->name}}</td>
                                            <td>{{$room->type}}</td>   
                                            <td style="text-align: center;">{{number_format($room->price_per_night)}}</td>
                                            <td style="text-align: center;">{{$room->capacity}}</td>
                                            <td>{{$room->description}}</td>
                                            <td>{{$room->created_at}}</td>
                                            <td>
                                                <a href="{{route('rooms.edit', encrypt($room->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('rooms.destroy' , encrypt($room->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
                        <div class="tab-pane fade" id="tab_1" role="tabpanel">
                            <div class="d-lg-flex align-items-center mb-4 gap-3">
                                <div class="position-relative">
                                    <button type="button" id="new-rtype-btn" class="btn btn-secondary btn-sm" onclick="showHidertypeForm('show')"><i class="bx bxs-plus-square"></i>New Room Type</button>
                                </div>
                            </div>
                            <div class="p-4 border rounded" id="new-rtype-form" style="display: none;">
                                <form class="form row g-3" method="POST" action="{{route('room-types.store')}}">
                                    @csrf
                                    <div class="col-md-4">
                                        <label class="form-label">Room type<span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="name" required placeholder="Enter Room Type name" class="form-control form-control-sm mb-1">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Description <span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="description" required placeholder="Enter Room Type description" class="form-control form-control-sm mb-1">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Price Per Night ({{$defcurr->code}})</label>
                                        <input id="name" type="number" name="price_per_night" placeholder="Enter Price Per Night" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Capacity</label>
                                        <input id="name" type="number" name="capacity" placeholder="Enter Capacity" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideDevceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive" id="rtype-list">
                                <table id="room-types" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Room Type</th>
                                            <th>Descriprion</th>
                                            <th>Price Per Night ({{$defcurr->code}})</th>
                                            <th style="text-align: center;">Capacity</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roomtypes as $key => $rtype)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$rtype->name}}</td>
                                            <td>{{$rtype->description}}</td>
                                            <th>{{$rtype->price_per_night}}</th>
                                            <td style="text-align: center;">{{$rtype->capacity}}</td>
                                            <td>{{$rtype->created_at}}</td>
                                            <td>
                                                <a href="{{route('room-types.edit', encrypt($rtype->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('room-types.destroy' , encrypt($rtype->id))}}" id="delete-rtype-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeletertype({{$key}})">
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
<script type="text/javascript">
     async function AutoCode() {
        let response = await fetch("{{ url('auto-room-code')}} ");
        let data = await response.json();
        document.getElementsByName('code')[0].value = data;
    }
</script>