@extends('layouts.app')

<script>
  function confirmEditItem() {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_edit')}}",
          text: "{{trans('navmenu.will_affect')}}",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.yes_update')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('edititem').submit();
            Swal.fire(
              "{{trans('navmenu.updated')}}",
              "{{trans('navmenu.update_success')}}",
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
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-3 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form id="edititem" class="row g-3 form-validate" method="POST" action="{{ route('sale-items.update', encrypt($saleitem->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="prod_detail" value="{{$prod_detail}}">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.product_name')}}</label>
                                <select class="form-select form-select-sm mb-1" name="product_id" required>
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                    <option value="">{{trans('navmenu.select_product')}}</option>
                                    @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                <input type="number" step="any" name="quantity_sold" class="form-control form-control-sm mb-1" value="{{$saleitem->quantity_sold}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                <input type="number" step="any" name="unit_cost" class="form-control form-control-sm mb-1" value="{{$saleitem->unit_cost}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                                <input type="number" step="any" name="retail_price" class="form-control form-control-sm mb-1" value="{{$saleitem->retail_price}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.unit_discount')}}</label>
                                <input type="number" step="any" name="discount" class="form-control form-control-sm mb-1" value="{{$saleitem->discount}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.total')}}</label>
                            <input type="number" name="" value="{{$saleitem->price}}" class="form-control form-control-sm mb-1" readonly>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.discount')}}</label>
                                <input type="number" step="any" name="total_discount" class="form-control form-control-sm mb-1" value="{{$saleitem->total_discount}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Add VAT</label>
                            <select class="form-select form-select-sm mb-1" name="with_vat">
                                @if($saleitem->with_vat == 'yes')
                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                <option value="no">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="no">{{trans('navmenu.no')}}</option>
                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-12">
                            <a href="#" class="btn btn-success btn-sm" onclick="confirmEditItem()" id="btn-submit">{{trans('navmenu.btn_save')}}
                            </a>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection