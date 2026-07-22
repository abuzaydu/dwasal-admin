@extends('layouts.sand')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
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
                <form class="dashform row mb-0" action="{{url('f-rm-sourcings')}}" method="POST" id="dashform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">

                    <div class="col-sm-7">
                        <div class="input-group mb-0">
                            <button type="button" class="btn btn-default mb-0 pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')"><i class="bx bxs-plus-square"></i>New Raw Material Sourcing</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="form row g-3" method="POST" action="{{route('rm-sourcings.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Raw Material Source Name<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="raw_material_source_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($rmsources as $key => $source)
                                    @if($rmsources->count() == 1)
                                    <option value="{{$source->id}}" selected>{{$source->source_name}}</option>
                                    @else
                                    <option value="{{$source->id}}">{{$source->source_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Storage Location<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="storage_location_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($slocations as $key => $location)
                                    @if($slocations->count() == 1)
                                    <option value="{{$location->id}}" selected>{{$location->location_name}}</option>
                                    @else
                                    <option value="{{$location->id}}">{{$location->location_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sourcing Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="sourcing_date" value="{{$today}}" id="sourcing-date" placeholder="Enter Raw Material Sourcing Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity Received <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="number" step="any" min="0" name="qty_received" placeholder="Enter Quantity Received" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">UOM <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="unit_of_measure" class="form-select form-select-sm mb-1" required>
                                    @foreach ($units as $key => $unit)
                                    @if($key < 3)
                                    <option>{{ $unit->unit_name }}</option>
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
                    <div class="table-responsive" id="rmsourcing-list">
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
                                    <td>
                                        <a href="{{ route('rm-sourcings.show', encrypt($rmsourcing->id)) }}">{{$rmsourcing->source_name}} </a>
                                    </td>
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
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>
    <script type="text/javascript">

        $(document).ready(function(){
            //Exportable table
            $('#rm-sourcings').DataTable();
        });
    </script>
@endsection
<link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
<script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="sourcing_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>