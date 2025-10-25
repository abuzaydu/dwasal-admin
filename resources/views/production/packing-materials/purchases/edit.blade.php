@extends('layouts.prod')

@section('content')
<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
          <!-- SELECT2 EXAMPLE -->
          <div class="card radius-10">
            <div class="card-header">
              <h6 class="card-title">
                <ul class="row">
                  <li class="col">{{trans('navmenu.purchase_date')}} : <b>{{date('d M, Y', strtotime($purchase->date))}}</b></li>
                  <li class="col">{{trans('navmenu.total_amount')}} : <b>{{number_format($purchase->total_amount)}}</b></li>
                  <li class="col">{{trans('navmenu.amount_paid')}} : <b>{{number_format($purchase->amount_paid)}}</b></li>
                  <li class="col">{{trans('navmenu.unpaid')}} : <b>{{number_format($purchase->total_amount-$purchase->amount_paid)}}</b></li>
                </ul>
              </h6>
            </div>
            <!-- /.box-header -->
            <div class="card-body">
              <div class="row">
              	<form class="form-validate" method="POST" action="{{route('pm-purchases.update', encrypt($purchase->id))}}">
              		@csrf
                  @method('PUT')
                  <div class="row">
                    <div class="col-md-3" >
                      <label class="form-label">{{trans('navmenu.purchase_date')}}</label>
                      <div class="input-group date"> 
                        <div class="inner-addon left-addon">
                          <i class="myaddon fa fa-calendar"></i>
                          <input type="text" name="date" id="purchase_date" class="form-control form-control-sm mb-3" value="{{$purchase->date}}" >
                        </div> 
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group pt-2">
                        <label class="form-label">{{trans('navmenu.supplier')}}</label>
                        <select name="supplier_id" class="form-control form-control-sm mb-3">
                          @if(!is_null(App\Models\Supplier::find($purchase->supplier_id)))
                          <option value="{{$purchase->supplier_id}}" selected>{{App\Models\Supplier::find($purchase->supplier_id)->name}}</option>
                          @else
                          <option>{{trans('navmenu.unknown')}}</option>
                          @endif
                          @foreach($suppliers as $supplier)
                          <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group pt-2" id="order_no">
                          <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                          <input type="text" class="form-control form-control-sm mb-3" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" value="{{$purchase->order_no}}" />
                      </div> 
                    </div>
                    <div class="col-md-3">
                      <div class="form-group pt-2" id="delivery_note_no">
                        <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                        <input type="text" class="form-control form-control-sm mb-3" id="dn_no" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" value="{{$purchase->delivery_note_no}}" />
                      </div> 
                    </div>
                    <div class="col-md-3">
                      <div class="form-group pt-2" id="invoice_no">
                        <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                        <input type="text"  class="form-control form-control-sm mb-3" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" value="{{$purchase->invoice_no}}" />
                      </div> 
                    </div>  
          
                    <div class="col-md-3">
                      <div class="form-group pt-2">
                        <label class="form-label">{{trans('navmenu.comments')}}</label>
                        <textarea name="comments" class="form-control form-control-sm mb-3">@if($purchase->comments != 'null'){{$purchase->comments}}@endif</textarea>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group pt-2">
                        <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                        <input type="text" name="amount_paid" class="form-control form-control-sm mb-3" value="{{$purchase->amount_paid}}" readonly>
                      </div>
                    </div>
                  </div>

      	            <!-- /.col -->
                    <div class="col-md-3 pt-2">
      	               <button type="submit" class="btn  btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                       <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                    </div>
      	        </form>
              </div>
              <!-- /.row -->
            </div>
          </div>
    </div>
  </div>
      <!-- /.box -->

@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">
<script src="../../js/DatePickerX.min.js"></script>
<script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

        });
</script>
