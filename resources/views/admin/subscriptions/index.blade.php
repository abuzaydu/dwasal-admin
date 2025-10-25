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
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3 mt-2 text-uppercase text-center">New Subscription Type</h6>
                    <form class="form row g-3" method="post" action="{{ route('subscriptions.store') }}" validate>
                        {{csrf_field()}}
                        <div class="col-md-12">
                            <label class="form-label">Subscription Title <span style="color: red;">*</span> </label>
                            <input class="form-control form-control-sm mb-1 border-primary" subs="text" name="title" placeholder="Enter Subscription Title" id="userinput8" required>
                        </div>
                        <div class="col-md-12">
                            <label for="userinput8">Description <span style="color: red;">*</span> </label>
                            <textarea name="description" class="form-control form-control-sm mb-1 border-primary" placeholder="Please Enter subs- description" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <button subs="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="my-3 text-uppercase text-center">{{$title}}</h6>
                    <table class="table table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $key => $subs)
                            <tr>
                                <td>{{ $key+1  }}</td>
                                <td>{{ $subs->title }}</td>
                                <td>{{ $subs->description }} </td>
                                <td>
                                    <a  href="{{  route('subscriptions.edit', encrypt($subs->id)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a> |
                                    <form id="delete-form-{{ $key }}" method="POST" action="{{ route('subscriptions.destroy', encrypt($subs->id)) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" class="button" onclick="confirmDelete('{{ $key }}')"><i class="fa fa-trash" style="color: red;"></i></a>
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
@endsection
