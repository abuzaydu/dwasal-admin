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

    function confirmDeleteItem(id) {
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
            document.getElementById('delete-form-item'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
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
                                <div class="tab-title">{{trans('navmenu.overhead_expenses_incured')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab"  href="#tab_2-2" role="tab" >
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                </div>
                                <div class="tab-title"> {{trans('navmenu.overhead_expenses')}}</div>
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
                                        <th>#</th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.batch_no')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @foreach($mrouses as $index => $mrouse)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{date('d-m-Y', strtotime($mrouse->date))}}</td>
                                        <td>{{$mrouse->prod_batch}}</td>
                                        <td>{{number_format($mrouse->total_cost, 2, '.', ',')}}</td>
                                        <td>{{$mrouse->created_at}}</td>
                                        <td>
                                            <a href="{{route('mro-uses.show', encrypt($mrouse->id))}}">
                                                <span class="lni lni-eye"></span>
                                            </a> |  
                                            <a href="{{route('mro-uses.edit', encrypt($mrouse->id))}}">
                                                <i class="fa fa-edit" style="color: ble;"></i>
                                            </a> 
                                            | 
                                            <form id="delete-form-{{$index}}" style="display: inline;" method="POST" action="{{route('mro-uses.destroy' , encrypt($mrouse->id))}}">
                                                @csrf
                                                @method('DELETE')
                                                  <a href="#" onclick="confirmDelete('{{$index}}')">
                                                <i class="fa fa-trash" style="color: red;"></i></a>
                                            </form>     
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_2-2" role="tabpanel">
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <th>#</th>
                                    <th>{{trans('navmenu.expense_type')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                   @foreach($mro_used_items as $key => $mro_used_item)
                                   <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$mro_used_item->name}}</td>
                                        <td style="text-align: center;">{{$mro_used_item->qty}}</td>
                                        <td style="text-align: center;">{{number_format($mro_used_item->unit_cost)}}</td>
                                        <td style="text-align: center;">{{number_format($mro_used_item->total)}}</td>
                                        <td style="text-align: center;">{{$mro_used_item->date}}</td>
                                        <td style="text-align: center;">
                                             <a href="{{url('mro-used-items/edit' , encrypt($mro_used_item->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>| 
                                            <form id="delete-form-item-{{$key}}" style="display: inline;" method="POST" action="{{route('mro-used-items.destroy' , encrypt($mro_used_item->id))}}">
                                                @csrf
                                                @method('DELETE')
                                                  <a href="#" onclick="confirmDeleteItem('{{$key}}')">
                                                <i class="fa fa-trash" style="color: red;"></i></a>
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
@endsection


