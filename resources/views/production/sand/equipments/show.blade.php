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
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>
                    <li class="breadcrumb-item"><a href="{{ url('washing-equipments') }}">Washing Equipments</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row mb-5">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <img src="{{ asset('storage/'.$equipment->photo_url) }}" width="100%">
        </div>
        <div class="col-xl-8 col-lg-8 col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="2">Equipment Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Washing Plant</td>
                                    <td><b>{{$wplant->plant_name}}</b></td>
                                </tr>
                                <tr>
                                    <td>Equipment Code</td>
                                    <td><b>{{$equipment->equipment_code}}</b></td>
                                </tr>
                                <tr>
                                    <td>Equipment Name</td>
                                    <td><b>{{$equipment->equipment_name}}</b></td>
                                </tr>
                                <tr>
                                    <td>Equipment Type</td>
                                    <td><b>{{$equipment->equipment_type}}</b></td>
                                </tr>
                                <tr>
                                    <td>Manufacturer</td>
                                    <td><b>{{$equipment->manufacturer}}</b></td>
                                </tr>
                                <tr>
                                    <td>Model</td>
                                    <td><b>{{$equipment->model}}</b></td>
                                </tr>
                                <tr>
                                    <td>Installation Date</td>
                                    <td><b>{{date('d/m/Y', strtotime($equipment->installation_date))}}</b></td>
                                </tr>
                                <tr>
                                    <td>Maintenance Schedule</td>
                                    <td><b>{{$equipment->maintenance_schedule}}</b></td>
                                </tr>
                                <tr>
                                    <td>Last Maintenance Date</td>
                                    <td><b>{{date('d/m/Y', strtotime($equipment->last_maintenance_date))}}</b></td>
                                </tr>
                                <tr>
                                    <td>Next Maintenance Date Date</td>
                                    <td><b>{{date('d/m/Y', strtotime($equipment->next_maintenance_date))}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
