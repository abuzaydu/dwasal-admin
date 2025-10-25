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
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="form row g-3" method="POST" action="{{route('storage-locations.update', encrypt($slocation->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Location Name<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="location_name" value="{{$slocation->location_name}}" required placeholder="Enter Location Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Latitude <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="latitude" type="text" name="latitude" value="{{$slocation->latitude}}" required placeholder="Enter Location Latitude" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Longitude <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="longitude" type="text" name="longitude" value="{{$slocation->longitude}}" required placeholder="Enter Location Longitude" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Storage For</label>
                                <select name="storage_for" class="form-select form-select-sm mb-1" required>
                                    @if($slocation->storage_for == 'Raw Material')
                                    <option>Raw Material</option>
                                    <option>End Products</option>
                                    @else
                                    <option>End Products</option>
                                    <option>Raw Material</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Storage Capacity <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="number" min="0" step="any" name="capacity" value="{{$slocation->capacity}}" placeholder="Enter Storage Capacity" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">UOM <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="unit" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $key => $unit)
                                    @if($key < 3)
                                    @if($slocation->unit == $unit->unit_name)
                                    <option selected>{{ $unit->unit_name }}</option>
                                    @else
                                    <option>{{ $unit->unit_name }}</option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Add</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection