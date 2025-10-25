@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/transOrder.js') }}"></script>
<script type="text/javascript">
    
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#sale_date").val('');
      }
    }

</script>

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" >
        <div class="col-md-12">
            <div class="card radius-6">
                <div class="card-body print_invoice">
                    <form class="form row g-1" method="POST" action="{{route('prod-transfer-store')}}">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice" class="form-label">{{trans('navmenu.date')}}</label>
                                <select name="date_set" id="date_set" onchange="weg(this)" class="form-select form-select-sm mb-1">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" id="date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.pick_date')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="order_date" id="datepicker" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}">
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.destin_shop')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control form-control-sm mb-1" name="destin_id"  required>
                                    <option value="{{$shop->id}}" selected>{{$shop->name}}</option>
                                </select>
                            </div>
                        </div>
                        <input type="text" name="production_id" value="{{$production->id}}" hidden="">
                        <div class="col-md-12 order-items">
                            <table class="items mt-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="Item">{{trans('navmenu.item_name')}}</th>
                                        <th class="qty" style="text-align: center;">{{trans('navmenu.transfer_qty')}}</th>
                                        <th class="qty" style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <!-- <th class="qty">{{trans('navmenu.selling_price')}}</th> -->
                                        <!-- <th class="qty">{{trans('navmenu.profit_margin')}}</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prod_cost_items as $key => $pmitem)
                                    <tr id="temps">
                                        <td>{{$key + 1}}</td>
                                        <td class="item">{{$pmitem->name}}</td>
                                        <td class="qty" style="text-align: center;">{{$pmitem->quantity}}</td>
                                        <td class="qty" style="text-align: center;">{{number_format($pmitem->cost_per_unit,2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" >
                            <button class="btn btn-success" id="btn-create"><i class="fa fa-file"></i> {{trans('navmenu.create_order')}}</button>
                            <a href="{{route('prod-costs.index')}}" class="btn btn-warning " id="btn-create" style="margin-right: 5px;"><i class="fa fa-x"></i> {{trans('navmenu.cancel_order')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection