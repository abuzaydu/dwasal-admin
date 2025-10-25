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
            document.getElementById('delete-form-' + id).submit();
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
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form class="form" method="POST" action="{{route('mro.update', encrypt($mro->id))}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <div class="row ms-10">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.mro_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" value="{{$mro->name}}" required placeholder="{{trans('navmenu.mro_name')}}" class="form-control form-control-sm mb-4">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="float-start">
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <a href="{{ url('mro') }}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


