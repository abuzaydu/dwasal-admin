@extends('layouts.sand')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
    <script>
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
                <a href="{{ route('sand-productions.create') }}" class="btn btn-primary btn-sm"><i class="bx bxs-plus-square"></i> New Production Run</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="prodrun-list">
                        <table id="prod-runs" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PR Bach No.</th>
                                    <th>Washing Plant</th>
                                    <th>Storage Location</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Input Qty</th>
                                    <th>Output Qty</th>
                                    <th>Waste Water Qty</th>
                                    <th>Created By</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prodruns as $key => $prodrun)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td><a href="{{ route('sand-productions.show', encrypt($prodrun->id))}}">{{$prodrun->pr_no}}</a></td>
                                    <td>{{$prodrun->plant_name}}</td>
                                    <td>{{$prodrun->location_name}}</td>
                                    <td>{{$prodrun->start_time}}</td>
                                    <td>{{$prodrun->end_time}}</td>
                                    <td style="text-align: center;">{{$prodrun->input_quantity+0}}</td>
                                    <td style="text-align: center;">{{$prodrun->output_quantity+0}}</td>
                                    <td style="text-align: center;">{{$prodrun->waste_water_quantity+0}}</td>
                                    <td>{{$prodrun->first_name}} {{$prodrun->last_name}}</td>
                                    <td style="text-align: center;">
                                        <a href="{{route('sand-productions.edit', encrypt($prodrun->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form method="POST" action="{{route('sand-productions.destroy' , encrypt($prodrun->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
            $('#prod-runs').DataTable();
        });
    </script>
@endsection