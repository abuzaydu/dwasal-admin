@extends('layouts.sand')
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
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')"><i class="bx bxs-plus-square"></i>New Storage Location</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="form row g-3" method="POST" action="{{route('storage-locations.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-2">
                                <label class="form-label">Location Name<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="location_name" required placeholder="Enter Location Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Latitude <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="latitude" type="text" name="latitude" required placeholder="Enter Location Latitude" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Longitude <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="longitude" type="text" name="longitude" required placeholder="Enter Location Longitude" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Storage For</label>
                                <select name="storage_for" class="form-select form-select-sm mb-1" required>
                                    <option>--Select--</option>
                                    <option>Raw Material</option>
                                    <option>End Products</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Storage Capacity <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="number" min="0" step="any" name="capacity" placeholder="Enter Storage Capacity" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">UOM <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="unit" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $key => $unit)
                                    @if($key < 3)
                                    <option>{{ $unit->unit_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Add</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">Cancel</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive" id="slocation-list">
                        <table id="slocations" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Location Name</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Storage For</th>
                                    <th>Capacity</th>
                                    <th>Unit</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slocations as $key => $slocation)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td><a href="{{ route('storage-locations.show', encrypt($slocation->id))}}">{{$slocation->location_name}}</a></td>
                                    <td>{{$slocation->latitude}}</td>
                                    <td>{{$slocation->longitude}}</td>
                                    <td>{{$slocation->storage_for}}</td>
                                    <td>{{$slocation->capacity}}</td>
                                    <td>{{$slocation->unit}}</td>
                                    <td style="text-align: center;">
                                        <a href="{{route('storage-locations.edit', encrypt($slocation->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form method="POST" action="{{route('storage-locations.destroy' , encrypt($slocation->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
    <!--end row-->
@endsection