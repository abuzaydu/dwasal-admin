@extends('layouts.vms')
    <script>
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');

            if (elem == 'show') {
                newform.style.display = 'block';
                newbtn.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newbtn.style.display = 'block';
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

         function confirmDeletepartType(id){
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
                document.getElementById('delete-vt-form-'+id).submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmDeletelocationship(id){
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
                document.getElementById('delete-ot-form-'+id).submit();
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
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">parts Managment</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#partModal"><i class="fa fa-plus-square"></i> New Part</button>
                <button type="button" class="btn btn-success btn-sm px-1" data-bs-toggle="modal" data-bs-target="#newTypeModal">
                    <i class="fa fa-plus mr-1"></i>
                    Add Part Category
                </button>
                <button type="button" class="btn btn-secondary btn-sm px-1" data-bs-toggle="modal" data-bs-target="#newOwnModal">
                    <i class="fa fa-plus mr-1"></i>
                    Add Part Location
                </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Parts List</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_1">Part Categories</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_2">Part Locations</a></li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="table-responsive" id="part-list">
                                <table id="parts" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Part Number</th>
                                            <th>Part Name</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($parts as $key => $part)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('parts.show', encrypt($part->id))}}">{{$part->part_no}}</a></td>
                                            <td>{{$part->part_name}}</td>
                                            <td>{{$part->status}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('parts.edit', encrypt($part->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('parts.destroy' , encrypt($part->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
                        <div class="tab-pane fade" id="tab_1">
                            <div class="table-responsive">
                                <table id="part-categories" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($partcategories as $key => $type)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('part-categories.show', encrypt($type->id))}}">{{$type->name}}</a></td>
                                            <td>
                                                @if($type->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <th>{{$type->created_at}}</th>
                                            <th>{{$type->updated_at}}</th>
                                            <td style="text-align: center;">
                                                <a href="{{route('part-categories.edit', encrypt($type->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('part-categories.destroy' , encrypt($type->id))}}" id="delete-vt-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeletepartType({{$key}})">
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
                        <div class="tab-pane fade" id="tab_2">
                            <div class="table-responsive">
                                <table id="part-locations" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Room</th>
                                            <th>Self</th>
                                            <th>Drawer</th>
                                            <th>Dimension</th>
                                            <th>Capacity</th>
                                            <th>Status</th>
                                            <th>Description</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($partlocations as $key => $location)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('part-locations.show', encrypt($location->id))}}">{{$location->name}}</a></td>
                                            <td style="text-align: center;">{{$location->room}}</td>
                                            <td style="text-align: center;">{{$location->self}}</td>
                                            <td style="text-align: center;">{{$location->drawer}}</td>
                                            <td style="text-align: center;">{{$location->dimension}}</td>
                                            <td style="text-align: center;">{{$location->capacity}}</td>
                                            <td style="text-align: center;">{{$location->description}}</td>
                                            <td style="text-align: center;">
                                                @if($location->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">In Active</span>
                                                @endif
                                            </td>
                                            <th>{{$location->created_at}}</th>
                                            <th>{{$location->updated_at}}</th>
                                            <td style="text-align: center;">
                                                <a href="{{route('part-locations.edit', encrypt($location->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('part-locations.destroy' , encrypt($location->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeletelocationship({{$key}})">
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


    <!-- Modal -->
    <div class="modal animated zoomIn" id="partModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form row g-3" method="POST" action="{{route('parts.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Part Number <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="part_no" required placeholder="Enter Part Number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Name <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="part_name" placeholder="Enter Part Name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Category <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="part_category_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Category --</option>
                                @foreach($partcategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Locations <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="part_location_id" class="form-select form-select-sm mb-1" required>
                                <option value="">--Select--</option>
                                @foreach($partlocations as $key => $location)
                                <option value="{{ $location->id }}">{{$location->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Quantity </label>
                            <input type="number" name="av_qty" placeholder="Enter Current Quantity" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">UOM </label>
                            <select id="unit" name="uom" class="form-select form-select-sm mb-1" required>
                                <option value="">Select Unit</option>
                                <option>pc</optio<>
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
                        <div class="col-md-6">
                            <label class="form-label">Description </label>
                            <textarea type="text" name="description" placeholder="Enter Description" class="form-control form-control-sm mb-1"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks </label>
                            <textarea type="text" name="remarks" placeholder="Enter Remarks" class="form-control form-control-sm mb-1"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Add</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="newTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Part Categor</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('part-categories.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <div class="col-md-12 pt-2">
                                <label class="form-label">Name <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="name" required placeholder="Enter part type name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newOwnModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Part Location</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('part-locations.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <div class="col-md-6 pt-2">
                                <label class="form-label">Location Name <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="name" required placeholder="Enter location name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room</label>
                                <input type="text" name="room" class="form-control form-control-sm mb-1" placeholder="Enter Room number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Self</label>
                                <input type="text" name="self" class="form-control form-control-sm mb-1" placeholder="Enter Self Number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Drawer</label>
                                <input type="text" name="drawer" class="form-control form-control-sm mb-1" placeholder="Enter Drawer number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dimension</label>
                                <input type="text" name="drawer" class="form-control form-control-sm mb-1" placeholder="Enter Drawer number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Capacity</label>
                                <input type="text" name="capacity" class="form-control form-control-sm mb-1" placeholder="Enter Capacity">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dimension</label>
                                <input type="text" name="dimension" class="form-control form-control-sm mb-1" placeholder="Enter Dimension">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css')}}">
<script src="{{ asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="reg_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
