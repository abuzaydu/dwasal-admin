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
                        <form class="form row g-3" method="POST" action="{{route('washing-plants.update', encrypt($wplant->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Plant Name<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="plant_name" value="{{ $wplant->plant_name }}" required placeholder="Enter Plant Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Plant Location <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="location" type="text" name="plant_location" value="{{$wplant->plant_location}}" required placeholder="Enter Plant Address Or GPS Coordinates" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Production Capacity per Day <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="text" name="capacity_per_day" value="{{$wplant->capacity_per_day}}" placeholder="Enter Production Capacity (Per Day)" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Unit <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="unit_of_measure" class="form-select form-select-sm mb-1" required>
                                    @if($wplant->unit_of_measue == 't')
                                    <option value="t" selected>Tonnes (t)</option>
                                    <option value="m³">Cubic meters (m³)</option>
                                    <option value="yd³">cubic yards (yd³)</option>
                                    @elseif($wplant->unit_of_measure == 'm³')
                                    <option value="t">Tonnes (t)</option>
                                    <option value="m³" selected>Cubic meters (m³)</option>
                                    <option value="yd³">cubic yards (yd³)</option>
                                    @else
                                    <option value="t">Tonnes (t)</option>
                                    <option value="m³">Cubic meters (m³)</option>
                                    <option value="yd³" selected>cubic yards (yd³)</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Operating Hours</label>
                                <input id="op-hours" type="text" name="operating_hours" value="{{$wplant->operating_hours}}" placeholder="Enter Plant Operating Hours" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Launch Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="launch_date" value="{{$wplant->launch_date}}" id="launch-date" placeholder="Enter Plant Launch Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Maintenance Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="last_maintenance_date" value="{{$wplant->last_maintenance_date}}" id="last-mdate" placeholder="Enter Last Maintenance Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Plant Photo</label>
                                <input type="file" name="image" class="form-control form-control-sm mb-1">
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

<link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
<script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="launch_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>