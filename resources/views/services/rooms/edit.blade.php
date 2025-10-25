@extends('layouts.inv')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('rooms') }}">Room Settings</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">

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
                            <div class="p-4 border rounded">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('rooms.update', encrypt($room->id)) }}" enctype="multipart/form-data">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <div class="col-md-3">
                                        <label class="form-label">Room No. <span style="color: red;">*</span></label>
                                        <input type="text" name="room_no" value="{{$room->room_no}}" class="form-control form-control-sm " placeholder="Enter Room number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Name </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" value="{{$room->name}}" placeholder="Enter Room Name" required>
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please provide a room name.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Room Type</label>
                                        <select name="room_type_id" class="form-select form-select-sm mb-1">
                                            @foreach($roomtypes as $type)
                                            @if($room->room_type_id == $type->id)
                                            <option value="{{$type->id}}" selected>{{$type->name}}</option>
                                            @else
                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm px-4 radius-30" type="submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <a href="{{ url('rooms') }}" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection