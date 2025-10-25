@extends('layouts.inv')
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
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


    function showHideForm(elem) {
        var newform = document.getElementById('item-form');
        var itemlist = document.getElementById('pitems');
        if (elem == 'show') {
            newform.style.display = 'block';
            itemlist.style.display = 'none';
        }else{
            itemlist.style.display = 'block';
            newform.style.display = 'none';
        }
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('purchase-orders') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-5 col-md-5 col-sm-12 text-right">
                @if($porder->status == 'Pending')
                <button type="button" class="btn btn-success btn-sm pull-right" onclick="showHideForm('show')">
                    <i class="fa fa-shopping-cart"></i>
                    {{trans('navmenu.add_purchase_item')}}
                </button>
                @endif
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12">
            <div class="card radius-10">
                <div class="card-body" id="item-form" style="display: none;">
                    <form class="form-validate row g-3" method="POST" action="{{url('add-purchase-order-item')}}">
                        @csrf

                        <input type="hidden" name="purchase_order_id" value="{{$porder->id}}">
                        <div class="col-md-4">
                            <label>{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1 select2" id="my-select" name="product_id" required style="width: 100%;">
                                <option value="">Select Product</option>
                                @foreach($products as $key => $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="qty" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-4">
                            <label>{{trans('navmenu.unit_cost')}}</label>
                            <input id="unit_price" type="number" min="0" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal" onclick="showHideForm('hide')">Cancel</button>
                        </div>
                    </form>
                </div>
                <div class="card-body" id="pitems">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                @if(!is_null(App\Models\Supplier::find($porder->supplier_id)))
                                <input type="text" name="" class="form-control form-control-sm mb-3" value="{{App\Models\Supplier::find($porder->supplier_id)->name}}" readonly>
                                @else
                                <input type="text" name="" class="form-control form-control-sm mb-3" value="{{trans('navmenu.unknown')}}" readonly>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="total" class="form-label">{{trans('navmenu.total')}} </label>
                                <input type="text" name="" class="form-control form-control-sm mb-3" value="{{number_format($porder->amount, 2, '.', ',')}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                <textarea  class="form-control form-control-sm mb-3" rows="1" name="comments" id="comments" >{{$porder->comments}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                            <tr>
                                <th>#</th>
                                <th>{{trans('navmenu.product_name')}}</th>
                                <th>{{trans('navmenu.qty')}}</th>
                                <th>{{trans('navmenu.unit_cost')}}</th>
                                <th>{{trans('navmenu.total')}}</th>
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                            @foreach($pitems as $key => $item)
                            <tr id="temps">
                                <td>{{$key + 1}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->qty}}</td>
                                <td>{{$item->unit_cost}}</td>
                                <td>{{($item->qty*$item->unit_cost)}}</td>
                            <td> 
                                <a href="{{ route('poitems.edit', encrypt($item->id))}}">
                                    <i class="fa fa-edit" style="color: blue;"></i>
                                </a> | 
                                <form id="delete-form-{{$item->id}}" method="POST" action="{{ route('poitems.destroy', encrypt($item->id))}}" style="display: inline;"> 
                                    @csrf
                                    @method("DELETE")
                                    <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $item->id; ?>')"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                </form>
                            </td>
                        </tr>                            
                        @endforeach
                    </table>
                </div>
            </div>
        </div>      
    </div>

<div class="modal fade" id="itemModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">�</span></button>
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_purchase_item')}}</h4>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <form class="form-validate row g-3" method="POST" action="{{url('add-purchase-order-item')}}">
                    @csrf

                    <input type="hidden" name="purchase_order_id" value="{{$porder->id}}">
                    <div class="col-md-6">
                        <label>{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-control form-control-sm mb-1 select2" id="product_id" name="product_id" required style="width: 100%;">
                            <option value="">Select Product</option>
                            @foreach($products as $key => $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="qty" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{trans('navmenu.unit_cost')}}</label>
                        <input id="unit_price" type="number" min="0" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-1" required>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection