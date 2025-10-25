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
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <a class="btn btn-primary btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#stageModal"><i class="fa fa-plus"></i> New Stage</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Product Stages</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2-2" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">New Product Stage</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_1-2" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Production Stages</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">
                                <table id="pp-stages" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>Product</th>
                                            <th>Stage</th>
                                            <th style="text-align: center;">Is WIP Stage</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pstages as $index => $stage)
                                        <tr>
                                            <td>{{$stage->name}}</td>
                                            <td>{{$stage->stage}}</td>
                                            <td style="text-align: center;">
                                                @if($stage->is_wip_stage)
                                                    Yes
                                                @else
                                                    No
                                                @endif
                                            </td>
                                            <td>{{$stage->created_at}}</td>
                                            <td>
                                                <a href="{{route('pp-stages.edit', encrypt($stage->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('pp-stages.destroy' , encrypt($stage->id))}}" style="display:inline;">
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
                        <div class="tab-pane fade" id="tab_1-2" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>Production Stage</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stages as $index => $stage)
                                        <tr>
                                            <td>{{$stage->stage}}</td>
                                            <td>
                                                <a href="{{route('production-stages.edit', encrypt($stage->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                <form id="delete-stg-form-{{$index}}" method="POST" action="{{route('production-stages.destroy' , encrypt($stage->id))}}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#"  onclick="confirmDeleteStage('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i>
                                                    </a> 
                                                </form>     
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                            <div class="row">
                                <form class="form" method="POST" action="{{route('pp-stages.store')}}">
                                    @csrf
                                    <div class="row ">
                                        <div class="col-sm-4">
                                            <label class="form-label">Stage <span style="color: red; font-weight: bold;">*</span></label>
                                            <select class="form-select form-select-sm mb-1" name="production_stage_id">
                                                <option value="">Select Stage</option>
                                                @foreach($stages as $stage)
                                                <option value="{{$stage->id}}">{{$stage->stage}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Production Stage For </label>
                                            <select class="form-select form-select-sm mb-1" name="product_id" id="product_id">
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Is WIP Stage? </label>
                                            <select class="form-select form-select-sm mb-1" name="is_wip_stage" required>
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group float-end">
                                                <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                                <button type="reset" class="btn btn-warning">{{trans('navmenu.btn_reset')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="stageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">New Stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{route('production-stages.store')}}">
                    @csrf
                    <div class="row ms-10">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="stage" required placeholder="{{trans('navmenu.name')}}" class="form-control form-control-sm mb-4">
                            </div>
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
            $('#pp-stages').DataTable({
                "scrollX": true,
            })
        });
    </script>
@endsection
