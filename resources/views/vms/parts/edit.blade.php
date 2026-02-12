@extends('layouts.vms')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">Vehicle Managment</li>
                    <li class="breadcrumb-item"><a href="{{ url('parts') }}">Parts</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form row g-3" method="POST" action="{{route('parts.update', encrypt($part->id))}}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-4">
                            <label class="form-label">Part Number <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="part_no" value="{{$part->part_no}}" required placeholder="Enter Part Number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Part Name <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="part_name" value="{{$part->part_name}}" placeholder="Enter Part Name" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Part Category <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="part_category_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Category --</option>
                                @foreach($partcategories as $category)
                                @if($category->id == $part->part_category_id)
                                <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                @else
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Part Locations <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="part_location_id" class="form-select form-select-sm mb-1" required>
                                <option value="">--Select--</option>
                                @foreach($partlocations as $key => $location)
                                @if($part->part_location_id == $location->id)
                                <option value="{{ $location->id }}" selected>{{$location->name}}</option>
                                @else
                                <option value="{{ $location->id }}">{{$location->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">UOM  <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="uom" class="form-select form-select-sm mb-1" required>
                                <option selected>{{$part->uom}}</option>
                                <option value="">Select Unit</option>
                                <option>pc</option>
                                <option>box</option>
                                <option>set</option>
                                <option>roll</option>
                                <option>gal</option>
                                <option>bottle</option>
                                <option>ft</option>
                                <option>ltr</option>
                                <option>mtr</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-sm mb-1">
                                @if($part->status == 'Active')
                                <option>Active</option>
                                <option>Inactive</option>
                                @else
                                <option>Inactive</option>
                                <option>Active</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description </label>
                            <textarea type="text" rows="1" name="description" placeholder="Enter Description" class="form-control form-control-sm mb-1">{{$part->description}}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks </label>
                            <textarea type="text" rows="1" name="remarks" placeholder="Enter Remarks" class="form-control form-control-sm mb-1">{{$part->remarks}}</textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Save Changes</button>
                            <a href="javascript:history()" class="btn btn-warning btn-sm">{{ trans('navmenu.btn_cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection