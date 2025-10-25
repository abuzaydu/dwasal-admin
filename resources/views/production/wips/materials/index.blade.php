@extends('layouts.prod')
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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                
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
                                    <div class="tab-title">{{$title}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2-2" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">New {{$title}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th></th>
                                            <th>Title</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wipmaterials as $index => $material)
                                        <tr>
                                            <td>{{$material->id}}</td>
                                            <td><a href="{{route('prod-wip-materials.show', encrypt($material->id))}}">{{$material->title}}</a></td>
                                            <td>{{$material->created_at}}</td>
                                            <td>
                                                <a href="{{route('prod-wip-materials.edit', encrypt($material->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('prod-wip-materials.destroy' , encrypt($material->id))}}" style="display:inline;">
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
                        <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                            <div class="row">
                                <form class="form" method="POST" action="{{route('prod-wip-materials.store')}}">
                                    @csrf
                                    <div class="row ">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="form-label">Title <span style="color: red; font-weight: bold;">*</span></label>
                                                
                                                <input id="name" type="text" name="title" required placeholder="Enter material title" class="form-control form-control-sm mb-3">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
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
@endsection


