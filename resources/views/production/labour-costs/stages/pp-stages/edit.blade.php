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
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{ url('production-stages') }}">Product Production Stages</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                
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
                            <form class="form row g-3" method="POST" action="{{route('pp-stages.update', encrypt($stage->id))}}">
                                @csrf
                                {{ method_field('PUT') }}
                                <div class="col-sm-4">
                                    <label class="form-label">Stage <span style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="production_stage_id">
                                        @foreach($stages as $stg)
                                        @if($stage->production_stage_id == $stg->id)
                                        <option value="{{$stg->id}}" selected>{{$stg->stage}}</option>
                                        @else
                                        <option value="{{$stg->id}}">{{$stg->stage}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Production Stage For </label>
                                    <select class="form-select form-select-sm mb-1" name="product_id" id="product_id">
                                        @foreach($products as $product)
                                        @if($stage->product_id == $product->id)
                                        <option value="{{$product->id}}" selected>{{$product->name}}</option>
                                        @else
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Is WIP Stage? </label>
                                    <select class="form-select form-select-sm mb-1" name="is_wip_stage" required>
                                        @if($stage->is_wip_stage)
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                        @else
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <a href="{{ url('production-stages')}}" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection