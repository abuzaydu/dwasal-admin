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
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-success" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.oe_types')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"  href="{{route('mro-uses.create')}}" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.add_overhead_expenses')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"  href="{{url('mro-items')}}" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.overhead_expenses_incured')}}</div>
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
                                        <th>{{trans('navmenu.expense_type')}}</th>
                                        <th>{{trans('navmenu.date_registered')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mros as $index => $mro)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td><a href="{{route('mro.show', encrypt($mro->id))}}">{{$mro->name}}</a></td>
                                        <td>{{$mro->created_at}}</td>
                                        <td>
                                            <a href="{{route('mro.edit', encrypt($mro->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('mro.destroy' , encrypt($mro->id))}}" style="display:inline;">
                                                 @csrf
                                                 @method('DELETE')
                                                 <a href="#" onclick="confirmDelete('{{$index}}')">
                                                    <i class="fa fa-trash" style="color: red;"></i>
                                                </a> 
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-mros')}}" method="POST">  @csrf <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                        </form>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


