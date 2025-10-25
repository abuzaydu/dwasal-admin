@extends('layouts.prof')
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

    function confirmDeleteRole(id) {
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
            document.getElementById('delete-form-role-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmShopDelete(id){
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
            document.getElementById('delete-form-shop-'+id).submit();
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
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Account & Settings</li>    
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                 <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="my-businesses" role="tabpanel">
                            <a href="{{ route('shops.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>Add New Shop/Business</a>
                                
                            <a href="{{ url('add-store') }}" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i>Add New Warehouse/Store</a>
                            <div class="table-responsive">
                                <table id="example" class="table table-striped display nowrap " style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>{{trans('navmenu.business_name')}}</th>
                                            <th>Is A Warehouse</th>
                                            <th>Company</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shops as $index => $shop)
                                        <tr>
                                            <td>{{ $index+1  }}</td>
                                            <td><a href="{{route('shops.show' , encrypt($shop->id))}}">{{ $shop->name }}</a></td>
                                            <td>@if($shop->is_warehouse) YES @else NO @endif </td>
                                            <td>{{$shop->company}}</td>
                                            <td>{{ $shop->created_at}} </td>
                                            <td>@if(Auth::user()->roles[0]['name'] != 'manager')
                                                <form id="delete-form-shop-{{$index}}" method="POST" action="{{ route('shops.destroy' , encrypt($shop->id)) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a onclick="return confirmShopDelete({{$index}})" ><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                                @endif
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
@endsection