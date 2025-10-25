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
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="slocation-list">
                        <table id="slocations" class="table table-bordered display nowrap" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <th>Location Name</th>
                                    <td>{{$slocation->location_name}}</td>
                                </tr>
                                <tr>
                                    <th>Latitude</th>
                                    <td>{{$slocation->latitude}}</td>
                                </tr>
                                <tr>
                                    <th>Longitude</th>
                                    <td>{{$slocation->longitude}}</td>
                                </tr>
                                <tr>
                                    <th>Storage For</th>
                                    <td>{{$slocation->storage_for}}</td>
                                </tr>
                                <tr>
                                    <th>Capacity</th>
                                    <td>{{$slocation->capacity}}</td>
                                </tr>
                                <tr>
                                    <th>Unit</th>
                                    <td>{{$slocation->unit}}</td>
                                </tr>
                                <tr>
                                    <th colspan="2" style="border-top: 2px solid gray; border-bottom: 2px solid gray;">Stock Movement Summary</th>
                                </tr>
                                @if($slocation->storage_for == 'Raw Material')
                                <tr>
                                    <td>Total Qty Received</td>
                                    <th>{{$total_received+0}}</th>
                                </tr>
                                <tr>
                                    <td>Total Qty Used/Sold</td>
                                    <th>{{$total_used+0}}</th>
                                </tr>
                                <tr>
                                    <td>In Stock</td>
                                    <th>{{$total_received-$total_used}}</th>
                                </tr>
                                @else
                                <tr>
                                    <td>Total Qty Produced</td>
                                    <th>{{$total_in+0}}</th>
                                </tr>
                                <tr>
                                    <td>Total Qty Sold</td>
                                    <th>{{$total_sold+0}}</th>
                                </tr>
                                <tr>
                                    <td>In Stock</td>
                                    <th>{{$total_in-$total_sold}}</th>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($slocation->storage_for == 'Raw Material')
        <div class="col-xl-8">
            <div class="card mt-0">
                <div class="card-body">
                    <ul class="nav nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_0-0"><i class='fa fa-list'></i> Raw Materail Received </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_1-1"><i class='fa fa-list-alt'></i> Moved to Production</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0-0" role="tabpanel">
                            <div class="table-responsive">
                                <table id="rm-sourcings" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Sourcing Date</th>
                                            <th>Source Name</th>
                                            <th>Qty Received</th>
                                            <th>UOM</th>
                                            <th>Record By</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rmsourcings as $key => $rmsourcing)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$rmsourcing->sourcing_date}}</a></td>
                                            <td>{{$rmsourcing->source_name}}</td>
                                            <td>{{$rmsourcing->qty_received+0}}</td>
                                            <td>{{$rmsourcing->unit_of_measure}}</td>
                                            <td>{{$rmsourcing->first_name}} {{$rmsourcing->last_name}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('rm-sourcings.edit', encrypt($rmsourcing->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('rm-sourcings.destroy' , encrypt($rmsourcing->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
                        <div class="tab-pane fade" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        @endif
    </div>
    <!--end row-->
@endsection