@extends('layouts.adm')
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
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
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Service Payments</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 rounded">
                        <form class="form row g-3" method="post" action="{{ route('modules.update', encrypt($module->id)) }}" validate>
                            {{csrf_field()}}
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Name <span style="color: red;">*</span> </label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" value="{{$module->name}}" placeholder="Enter Module" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Display Name <span style="color: red;">*</span> </label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="display_name" value="{{$module->display_name}}" placeholder="Enter Display Name" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price <span style="color: red;">*</span> </label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="number" step="any" name="price" value="{{$module->price}}" placeholder="Enter Module Price" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="1" class="form-control form-control-sm mb-1 border-primary">{{$module->description}}</textarea>
                            </div>
                            <div class="col-md-12">
                                <a href="{{ url('admin/modules') }}" class="btn btn-warning btn-sm"> Cancel</a>
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection