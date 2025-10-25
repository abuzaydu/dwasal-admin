@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#datepicker").val('');
      }
    }

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

    function confirmDeletePayment(id) {
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
            window.location.href="{{url('purchase-payments/destroy/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showEditCost(elem) {
        var editForm = document.getElementById('edit-ci-form');
        if (elem != '') {
            editForm.style.display = 'block';
            document.getElementById('item-id').value = elem.id;
            document.getElementById('item-desc').value = elem.item_desc;
            document.getElementById('amount').value = elem.amount;
        }else{
            editForm.style.display = 'none';
        }
    }
    function confirmDeleteCost(id) {
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
            document.getElementById('delete-ci-form-'+id).submit();
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
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div  class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.supplier')}}</p>
                                    <h4 class="my-1">@if(!is_null($supplier)){{$supplier->name}}@else {{trans('navmenu.unknown')}} @endif</h4>
                                </div>
                                <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="bx bxs-box"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.total_amount')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->total_amount, 2, '.', ',')}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.amount_paid')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->amount_paid, 2, '.', ',')}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.unpaid')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->total_amount-$purchase->amount_paid, 2, '.', ',')}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

              <!-- =========================================================== -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card radius-10">
                        <div class="card-header">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#itemModal">
                                <i class="fa fa-shopping-bag"></i>
                                    Add Item
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">UOM</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchase_date')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($pitems as $index => $stock)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$stock->name}}</td>
                                            <td style="text-align: center;">{{number_format($stock->quantity_in)}}</td>
                                            <td style="text-align: center;"> <span style="color: gray;">{{$stock->basic_uom}}</span></td>
                                            <td style="text-align: center;">{{number_format($stock->unit_cost, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($stock->unit_cost*$stock->quantity_in, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{
$stock->stock_date}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('stocks.edit' , encrypt($stock->id))}}"><i class="fa fa-edit" style="color: blue;"></i>
                                                </a>
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('stocks.destroy' , encrypt($stock->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete({{$index}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{trans('navmenu.purchase_payments')}}</h5>
                        </div>
                        <div class="card-body">
                            @csrf
                            <table id="payments" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center;">{{trans('navmenu.pay_date')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.account')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.record_at')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $index => $payment)
                                        <tr>
                                            <td style="text-align: center;">{{$payment->id}}</td>
                                            <td style="text-align: center;">{{$payment->pay_date}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">
                                                @if($payment->account == 'Cash')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$payment->account}}
                                                    @else
                                                        {{trans('navmenu.cash')}}
                                                    @endif
                                                @elseif($payment->account == 'Mobile Money')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$payment->account}}
                                                    @else
                                                        {{trans('navmenu.mobilemoney')}}
                                                    @endif
                                                @elseif($payment->account == 'Bank')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$payment->account}}
                                                    @else
                                                        {{trans('navmenu.bank')}}
                                                    @endif                           
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{$payment->created_at}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('purchase-payments.edit', encrypt($payment->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a>
                                                <a href="#" onclick="confirmDeletePayment('<?php echo encrypt($payment->id); ?>')">
                                                    <i class="fa fa-trash" style="color: red;"></i>
                                                </a>      
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card radius-10">
                        <div class="card-header">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#costModal">
                                <i class="fa fa-plus"></i>
                                    Add Additional costs
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="p-4 border rounded" id="edit-ci-form" style="display: none;">
                                <form class="form-validate row g-3" method="POST" action="{{ route('cost-items.update', encrypt($purchase->id)) }}">
                                    @csrf
                                    {{ method_field('PATCH')}}
                                    <input type="hidden" name="item_id" id="item-id">
                                    <div class="col-md-8 mb-1">
                                        <label class="form-label">Item Description <span style="color: red; font-weight: bold;">*</span></label>
                                        <input type="text" name="item_desc" id="item-desc" class="form-control form-control-sm mb-1" placeholder="Enter Item description" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                                        <input type="number" name="amount" id="amount" placeholder="Enter Amount" class="form-control form-control-sm mb-1" required>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="showEditCost('')">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            <table id="cost-items" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>Item Description</th>
                                    <th style="text-align: center;">Percent(%)</th>
                                    <th style="text-align: center;">Amount</th>
                                    <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    <?php $totalpercent = 0; $total_cost = 0; ?>
                                    @foreach($costitems as $index => $item)
                                    <?php $totalpercent += $item->percent; $total_cost += $item->amount; ?>
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->item_desc}}</td>
                                        <td style="text-align: center;">{{$item->percent+0}}</td>
                                        <td style="text-align: center;">{{number_format($item->amount, 2, '.', ',')}}</td>
                                        <td style="text-align: center;">
                                            <a href="javascript:;" onclick="showEditCost({{$item}})"><i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            <form id="delete-ci-form-{{$index}}" method="POST" action="{{route('cost-items.destroy' , encrypt($item->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteCost({{$index}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th><b>Total Additional Costs</b></th>
                                        <th style="text-align: center;"><b>{{$totalpercent}}</b></th>
                                        <th style="text-align: center;"><b>{{number_format($total_cost, 2, '.',',')}}</b></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="itemModal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="pull-left" id="myModalLabel">{{trans('navmenu.add_item')}}</h5>
                    <button type="button"  class="close btn btn-danger pull-right" data-bs-dismiss="modal" aria-label="Close"><span class="fa fa-x-circle"></span></button>
                    
                </div>
                <form class="form-validate" method="POST" action="{{url('add-purchase-item')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="purchase_id" value="{{$purchase->id}}">
                        <div class="col-md-6 mb-1">
                            <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="product_id" required style="width: 100%; border: 1px solid gray;">
                                <option value="">Select Product</option>
                                @foreach($products as $key => $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="quantity_in" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                        @if($shop->business_type_id != 1)
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                            <input id="unit_price" type="number" min="0" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                        @endif
                    </div>                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="costModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="pull-left" id="myModalLabel">Add Additional Cost</h5>
                    <button type="button"  class="close btn btn-danger pull-right" data-bs-dismiss="modal" aria-label="Close"><span class="fa fa-x-circle"></span></button>
                    
                </div>
                <form class="form-validate" method="POST" action="{{ route('cost-items.store') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="purchase_id" value="{{$purchase->id}}">
                        <div class="col-md-8">
                            <label class="form-label">Item Description <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="item_desc" class="form-control form-control-sm mb-1" placeholder="Enter Item description" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" name="amount" placeholder="Enter Amount" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>
    <script>
        $(function(){
            $('#example1').DataTable({
                'scrollX': true
            });
            $('#payments').DataTable();
            $('#cost-items').DataTable();
            $('#product-id').select2();
        })
    </script>
@endsection