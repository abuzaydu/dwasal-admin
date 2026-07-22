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
                        <form class="form row g-3" method="POST" action="{{route('raw-material-sources.update', encrypt($rmsource->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Source Name<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="source_name" value="{{$rmsource->source_name}}" required placeholder="Enter source Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Source Location <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="location" type="text" name="source_location" value="{{$rmsource->source_location}}" required placeholder="Enter source Address Or GPS Coordinates" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Material Type <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="material_type" class="form-select form-select-sm mb-1" required>
                                    <option>{{$rmsource->material_type}}</option>
                                    <option>River Sand</option>
                                    <option>Quarry Sand</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Person <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="text" name="contact_person" value="{{$rmsource->contact_person}}" placeholder="Enter Source Contact Person" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Number <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="text" name="contact_number" value="{{$rmsource->contact_number}}" placeholder="Enter Source Contact Number" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Save Changes</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="window.history.back()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection