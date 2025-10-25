@extends('layouts.inv')

@section('content')

      <!-- SELECT2 EXAMPLE -->
      <div class="card">
        
        <!-- /.box-header -->
        <div class="card-body">
          <div class="row">

            <form class="form-validate" method="POST" action="{{ route('poitems.update', encrypt($item->id))}}">
                @csrf
                {{ method_field('PATCH') }}
                <input type="hidden" name="id" value="{{$item->id}}">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="product_id" class="form-label">{{trans('navmenu.product_name')}}</label>
                    <select class="form-control select2" name="product_id" required>
                      <option value="{{$product->id}}">{{$product->name}}</option>
                        <option value="">{{trans('navmenu.select_product')}}</option>
                        @foreach($products as $product)
                        <option value="{{$product->id}}">{{$product->name}}</option>
                        @endforeach
                  </select>
                  </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="qty" class="form-label">{{trans('navmenu.quantity')}}</label>
                        <input type="number" step="any" name="qty" class="form-control" value="{{$item->qty}}">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="unit_cost" class="form-label">{{trans('navmenu.buying_price')}}</label>
                        <input type="number" step="any" name="unit_cost" class="form-control" value="{{$item->unit_cost}}">
                    </div>
                </div>
                <!-- /.col -->
              <div class="col-md-12">
                <div class="mb-3">
                   <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                 <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
               </div>
              </div>
            </form>
          </div>
          <!-- /.row -->
        </div>
      </div>
      <!-- /.box -->
@endsection