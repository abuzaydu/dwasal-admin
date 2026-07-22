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
                    <li class="breadcrumb-item"><a href="{{ url('washing-plants') }}">Washing Plants</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
            @if($wplant->photo_url)
                <img src="{{ asset('storage/'.$wplant->photo_url) }}" width="100%">
            @else
                <img src="{{ asset('assets/images/no-image.png') }}" width="100%">
            @endif
        </div>
        <div class="col-xl-8 col-lg-8 col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="2">Plant Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Plant Name</td>
                                    <td><b>{{$wplant->plant_name}}</b></td>
                                </tr>
                                <tr>
                                    <td>Plant Location</td>
                                    <td><b>{{$wplant->plant_location}}</b></td>
                                </tr>
                                <tr>
                                    <td>Production Capacity per Day</td>
                                    <td><b>{{$wplant->capacity_per_day+0}} {{$wplant->unit_of_measure}}</b></td>
                                </tr>
                                <tr>
                                    <td>Operating Hours</td>
                                    <td><b>{{$wplant->operating_hours}}</b></td>
                                </tr>
                                <tr>
                                    <td>Launch Date</td>
                                    <td>
                                        <b>
                                            @if(!is_null($wplant->launch_date))
                                                {{date('d/m/Y', strtotime($wplant->launch_date))}}
                                            @endif
                                        </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Last Maintenance Date</td>
                                    <td>
                                        <b>
                                            @if(!is_null($wplant->last_maintenance_date))
                                                {{date('d/m/Y', strtotime($wplant->last_maintenance_date))}}
                                            @endif
                                        </b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="window.history.back()">Back</button>
                </div>
            </div>
        </div>
    </div>
@endsection