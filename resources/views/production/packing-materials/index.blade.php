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
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.packing_materials')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2-2" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title"> {{trans('navmenu.new_packing_material')}}</div>
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
                                            <th>{{trans('navmenu.packing_name')}}</th>
                                            <th>{{trans('navmenu.basic_uom')}}</th>
                                            <th>{{trans('navmenu.in_stock')}}</th>
                                            <th>
                                            {{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pmaterials as $index => $material)
                                        <tr>
                                            <td></td>
                                            <td><a href="{{route('packing-materials.show', encrypt($material->id))}}">{{$material->name}}</a></td>
                                            <td>{{$material->basic_uom}}</td>
                                            <td>{{number_format($material->pivot->in_store)}}</td>
                                            <td>{{$material->pivot->created_at}}</td>
                                            <td>
                                                <a href="{{route('packing-materials.edit', encrypt($material->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('packing-materials.destroy', encrypt($material->id))}}" style="display:inline;">
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
                                <form id="frm-example" action="{{url('delete-multiple-materials')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                            <div class="row">
                                <form class="form row g-1" method="POST" action="{{route('packing-materials.store')}}">
                                    @csrf
                                    <div class="col-sm-3">
                                        <label class="form-label">Parent Material</label>
                                        <select class="form-select form-select-sm mb-3" name="parent_pm_id" style="width: 100%;">
                                            <option value="">--Select Parent Material--</option>
                                            @foreach($pmaterials->whereNull('parent_pm_id') as $key => $pm)
                                            <option value="{{$pm->id}}">{{$pm->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">{{trans('navmenu.packing_name')}}<span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-3">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                                        <select class="form-select form-select-sm mb-3" name="basic_uom" required style="width: 100%;">
                                            @foreach($units as $key => $unit)
                                            <option>{{$unit->unit_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label">{{trans('navmenu.current_stock')}}</label>
                                        <input id="qty" type="number" min="0" name="qty" step="any" placeholder="{{trans('navmenu.hnt_current_stock')}}" class="form-control form-control-sm mb-3">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                        <input id="unit_price" type="number" min="0" step="any" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-3">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label">{{trans('navmenu.description')}}</label>
                                        <textarea name="description" rows="1" class="form-control form-control-sm mb-3" placeholder="{{trans('navmenu.hnt_product_desc')}}"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="reset" class="btn btn-warning btn-sm">{{trans('navmenu.btn_reset')}}</button>
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


