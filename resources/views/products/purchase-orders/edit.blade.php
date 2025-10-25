@extends('layouts.inv')
<script type="text/javascript">
    function confirmDelete(id){
        document.getElementById('delete-form-'+id).submit();
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
                    <li class="breadcrumb-item"><a href="{{ url('purchase-orders') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12">
          
            <div class="card radius-10">

                <!-- /.box-header -->
                <div class="card-body">
                    <form class="form row g-3x" name="orderform" method="POST" action="{{ route('purchase-orders.update', encrypt($porder->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                                                
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier" required class="form-select form-select-sm mb-3" onchange="changeSupplier(this)">
                                    @foreach($suppliers as $supplier)
                                    @if($porder->supplier_id == $supplier->id)
                                    <option value="{{$supplier->id}}" selected>{{$supplier->name}}</option>
                                    @else
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PFI No.</label>
                            <input type="text" name="pfi_no" class="form-control form-control-sm mb-1" value="{{$porder->pfi_no}}" placeholder="Enter PFI/Quote No.">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label">{{trans('navmenu.status')}}</label>
                                <select name="status" id="status" required class="form-select form-select-sm mb-3" onchange="changeSupplier(this)">
                                    @foreach($statuses as $status)
                                    @if($porder->status == $status['value'])
                                    <option selected>{{$status['value']}}</option>
                                    @else
                                    <option>{{$status['value']}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" rows="1">{{$porder->comments}}</textarea>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>      
    </div>
@endsection