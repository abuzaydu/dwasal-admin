@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="../js/rmuse.js"></script>
<script type="text/javascript">

    

    function validateform(form) {
        var items = document.rmusedform.no_items.value;
        if (items == 0) {
            // alert('Please select at least one item to continue.');
            Swal.fire(
              'Nothing To Submit!',
              'Please select at least one item to continue.',
              'info'
            )
            return false;
        }

        form.myButton.disabled = true;
        form.myButton.value = "Please wait...";
        return true;
        
    }

    function confirmCancel() {
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
            window.location.href="{{url('cancel-rmused')}}";
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function weg(elem) {
      var x = document.getElementById("rmused_date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#rmused_date").val('');
      }
    }

    function updateProdCost() {
        var qty = document.getElementById('prod_qty').value;
        
        var total_cost = document.getElementById('total_cost').innerHTML;
        alert(total_cost);
        
        if(isNaN(qty)){
            alert('wrong Input , Enter a Number quantity');
        }else{
            var prod_cost = parseFloat(total_cost) / Number(qty) ;
            
            document.getElementById('cost_per_product').textContent = prod_cost.toFixed(2) ;
        }
    }

    function batch(elem) {
        var b = document.getElementById("prod_batch");
        if(elem.value !== "auto") {
          b.readOnly = false; 
        } else {
          b.readOnly = true;
    }

    

    
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
    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-xl-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_raw_material')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded"> 
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_raw_material')}}" class="form-control form-control-sm mb-3">
                        </div> 
                        <div class="col-sm-12">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addStockTemp(item, newrmusedtemp)">
                                    <div class="col-sm-11">
                                        @{{item.name}}
                                        <span style="color: blue;" ng-if="item.in_store > 0">(@{{item.in_store}})</span>
                                        <span style="color: red;" ng-if="item.in_store == 0">(@{{item.in_store}})</span>
                                    </div>
                                    <div class="col-sm-1">
                                        <span class="badge bg-success rounded-pill"><span class="fa fa-arrow-right" aria-hidden="true"></span></span>
                                    </div>
                                </li>
                            </ul>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.new_use_of_rm')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        @if(!$settings->disable_prod_panel) 
                        <form class="row g-3"  name="rmusedform" method="POST" action="{{route('rm-uses.store')}}" onsubmit="return validateform(this)">
                        @else 
                        <form class="row g-3"  name="rmusedform" method="POST" action="{{url('rm-uses-store')}}"  onsubmit="return validateform(this)">
                        @endif
                            @csrf
                            <div class="col-sm-3">
                                <label for="batch_no_set" class="form-label">{{trans('navmenu.batch_no')}}</label>
                                <select name="batch_no_set" id="batch_no_set" onchange="batch(this)" class="form-control form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="prod_batch" class="form-label">{{trans('navmenu.batch_no')}}</label>
                                <input type="text" name="prod_batch" class="form-control form-control-sm mb-3" id="prod_batch" value="{{$prod_batch}}" readonly/>
                            </div>

                            <div class="col-sm-3">
                                <label for="invoice" class="form-label">{{trans('navmenu.date')}}</label>
                                <select name="date_set" id="date_set" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>

                            <div class="col-sm-3" id="rmused_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                <div class="date">
                                    <input type="text" name="rmused_date" id="rmused_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-select-sm mb-3" readonly/>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <table id="discount_field" class="table table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.material_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newrmusedtemp in rmusedtemp" id="temps">
                                        
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newrmusedtemp.name}}</td>
                                        <td><input type="number" name="quantity" ng-blur="updateStockTemp(newrmusedtemp)" ng-model="newrmusedtemp.quantity" min="0" step="any" value="@{{newrmusedtemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                        <td style="text-align:center;">@{{newrmusedtemp.unit_cost | number:0}}</td>
                                        <td style="text-align:center;">@{{newrmusedtemp.total | number:0}}</td>
                                        <td><a href="#" ng-click="removeStockTemp(newrmusedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align:center;">{{trans('navmenu.total')}}</td>
                                        <td style="text-align:center;"><p class="form-control-static" ><b id="total_cost">@{{sum(rmusedtemp) | number:0}}</b></p></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                @if($settings->disable_prod_panel)
                                <div class="col-sm-12 p-2"><span style="font-weight: bold;">{{trans('navmenu.how_many_prod')}} ?</span></div>

                                <div class="col-sm-4">
                                    <label class="form-label" for="product">{{trans('navmenu.product_name')}}</label>
                                    <select name="product" class="form-select form-select-sm select2 " required="">
                                        <option>{{trans('navmenu.select_product')}}</option>
                                        @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label" for="prod_qty">{{trans('navmenu.quantity')}}</label>
                                    <input type="number" name="product_v" id="prod_qty" ng-model="prod_qty"ng-blur="updateProdCost(prod_qty , rmusedtemp)"  class="form-control form-control-sm mb-3" placeholder="Quantity"  required="" step="any"> 
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Cost Per Product</label>
                                    <input type="number" name="cost_per_product" id="cost_per_product" ng-model="cost_per_product" class="form-control form-control-sm mb-3" value="@{{cost_per_unit | number:2}}" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Profit Margin</label>
                                    <input type="number" name="profit_margin" id="profit_margin" class="form-control form-control-sm mb-3" placeholder="Profit Margin"  step="any">
                                </div>
                                @endif
                                <div class="col-md-8">
                                    <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                    <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" ></textarea>
                                </div>
                                <div class="col-sm-4" style="margin-top: 5px;">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                    <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!--end row-->

@endsection
    
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="rmused_date"]'),
                $max = document.querySelector('[name="due_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>