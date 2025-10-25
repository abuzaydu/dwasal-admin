@extends('layouts.prod')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
    function confirmDelete(id) {
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

    function confirmDeleteStage(id) {
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
            document.getElementById('delete-stg-form-'+id).submit();
            
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
    <div class="block-header pt-3">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <a class="btn btn-primary btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#typeModal"><i class="fa fa-plus"></i> New Food Type</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">
                                <table id="food-types" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($foodtypes as $index => $type)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$type->name}}</td>
                                            <td>{{$type->description}}</td>
                                            <td>{{$type->created_at}}</td>
                                            <td>
                                                <a href="{{route('food-types.edit', encrypt($type->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('food-types.destroy' , encrypt($type->id))}}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#"  onclick="confirmDelete('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i>
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
        </div>
    </div>


    <div class="modal fade" id="typeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">New Food Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{route('food-types.store')}}">
                    @csrf
                    <div class="row ms-10">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.name')}}" class="form-control form-control-sm mb-4">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control form-control-sm mb-4" name="description" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="float-start">
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function(){
            $('#food-types').DataTable({
                "scrollX": true,
            })
        });
    </script>
@endsection
