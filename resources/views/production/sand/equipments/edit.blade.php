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
                        <form class="form row g-3" method="POST" action="{{route('washing-equipments.update', encrypt($equipment->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Washing Plant<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="washing_plant_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Plant</option>
                                    @foreach($wplants as $plant)
                                    @if($plant->id == $equipment->washing_plant_id)
                                    <option value="{{$plant->id}}" selected>{{$plant->plant_name}}</option>
                                    @else
                                    <option value="{{$plant->id}}">{{$plant->plant_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Equipment Code<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="equipment_code" value="{{$equipment->equipment_code}}" required placeholder="Enter equipment Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Equipment Name<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="equipment_name" value="{{$equipment->equipment_name}}" required placeholder="Enter equipment Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Equipment Type <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="location" type="text" name="equipment_type" value="{{$equipment->equipment_type}}" required placeholder="Enter equipment type" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Manufacturer <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="text" name="manufacturer" value="{{$equipment->manufacturer}}" placeholder="Enter Manufacturer" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Model <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="text" name="model" value="{{$equipment->model}}" placeholder="Enter Model number" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Installation Date <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input id="op-hours" type="text" name="installation_date" value="{{$equipment->installation_date}}" placeholder="Enter Equipment Installation date" class="form-control form-control-sm mb-1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Maintenance Schedule</label>
                                <select name="maintenance_schedule" class="form-select form-select-sm mb-1">
                                    @if($equipment->maintenance_schedule == 'Weekly')
                                    <option>Weekly</option>
                                    <option>Monthly</option>
                                    @else
                                    <option>Monthly</option>
                                    <option>Weekly</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Maintenance Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="last_maintenance_date" value="{{$equipment->last_maintenance_date}}" id="last-mdate" placeholder="Enter Last Maintenance Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Next Maintenance Date Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="next_maintenance_date" value="{{$equipment->next_maintenance_date}}" id="next-date" placeholder="Enter Next Maintenance Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Equipment Photo</label>
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
        var $min = document.querySelector('[name="installation_date"]');
        var $last = document.querySelector('[name="last_maintenance_date"]');
        var $next = document.querySelector('[name="next_maintenance_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });

        $last.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });

        $next.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            minDate: new Date()
        });
    });
</script>