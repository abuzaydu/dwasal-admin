@extends('layouts.prod')
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
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('delete-rmitem/')}}/"+id;
            Swal.fire(
              'Deleted!',
              'Your Product has been deleted.',
              'success'
            )
          }
        })
    }

    function updateProdCost(){
        var qty = document.getElementById('prod_qty').value;
        var total_cost = document.getElementById('total_cost').innerHTML;
        if(isNaN(qty)){
            alert('wrong Input , Enter a Number quantity');
        }else{
            var prod_cost = parseFloat(total_cost) / Number(qty) ;
            
            document.getElementById('prod_cost').textContent = prod_cost.toFixed(3) ;
            document.getElementById('unit_cost').value = prod_cost.toFixed(3);
        }

    }

    function setProdProfit(){
        var profit_margin = document.getElementById('profit_margin').value;
        var prod_cost = document.getElementById('prod_cost').innerHTML;
        if(isNaN(profit_margin)){
            alert('wrong Input , Enter a Number Amount');
        }else{
            var product_price = parseFloat(prod_cost) + Number(profit_margin) ;
            // document.getElementById('product_price').textContent = product_price.toFixed(3) ;
            document.getElementById('prod_price').value = product_price.toFixed(2);
        }

    }


    function setProdPrice() {
        var price = document.getElementById('prod_price').value;
        var prod_cost = document.getElementById('prod_cost').innerHTML;
        if(isNaN(price)){
            alert('wrong Input , Enter a Number Amount');
        }else{
            var profit_margin = Number(price)-parseFloat(prod_cost);
            // document.getElementById('profit_margin').value = profit_margin.toFixed(3) ;
            document.getElementById('profit_margin').value = profit_margin.toFixed(2);
        }

    }

    function getProductInfo(elem){
        var tt_p = elem.value;

        var unit_packed = [];
        var sum = 0;
        

        var unit_p = document.querySelectorAll('#unit_p');
        var ratio = document.querySelectorAll('#ratio');
         for (var i = unit_p.length -1; i >= 0; i--) {

             unit_packed.push(unit_p[i].value);

             sum += parseFloat(unit_p[i].value);
         }

         for (var i = ratio.length - 1; i >= 0; i--) {
             ratio[i].textContent = ((parseFloat(unit_p[i].value)/parseFloat(tt_p)) * 100).toFixed(2);
         }
        
       

    }

    function prof(elm){
        var profit_margin = elm.value;
        var id = elm.id;
        var idInt = parseInt(id);
        var unit_cost_id = idInt+" unit_cost_p";
        var price_id = idInt+" price_p"
        var unit_cost_s = document.getElementById(unit_cost_id).value;
        var unit_cost = unit_cost_s.replace(/,/g , '');
        var price =parseFloat(profit_margin) + parseFloat(unit_cost);
        document.getElementById(price_id).value = parseFloat(price).toFixed(2);

        var key = document.getElementById("tot_key").value;
        var total = 0;
        var totalprice = 0;
        for (var i = key - 1; i >= 0; i--) {
            var qty_id = i+" qty_p";
            var qty = document.getElementById(qty_id).value;

            var profit_id = i+" prof_margin_p";
            var prof_m = document.getElementById(profit_id).value;
            if (isNaN(prof_m)) {
                total  += 0;
            } else {
                total  += parseFloat(prof_m)*parseFloat(qty);
            }

            var pri_id = i+" price_p";
            var mprice = document.getElementById(pri_id).value;
            if (isNaN(mprice)) {
                totalprice  += 0;
            } else {
                totalprice  += parseFloat(mprice)*parseFloat(qty);
            }
         }

         // alert("Total Margin"+total+" Total Price "+totalprice);
         document.getElementById("total_prof_margin").innerHTML = total.toFixed(2);
         document.getElementById("total_price").innerHTML = totalprice.toFixed(2);
    }


    function myPrice(elm){
        var my_price = elm.value;
        var id = elm.id;
        var idInt = parseInt(id);
        var unit_cost_id = idInt+" unit_cost_p";
        var profit_id = idInt+" prof_margin_p"
        var unit_cost_s = document.getElementById(unit_cost_id).value;
        var unit_cost = unit_cost_s.replace(/,/g , '');
        var profit =parseFloat(my_price) - parseFloat(unit_cost);
        document.getElementById(profit_id).value = parseFloat(profit).toFixed(2);

        var key = document.getElementById("tot_key").value;
        var total = 0;
        var totalprice = 0;
        for (var i = key - 1; i >= 0; i--) {
            var qty_id = i+" qty_p";
            var qty = document.getElementById(qty_id).value;

            var prof_id = i+" prof_margin_p";
            var prof_m = document.getElementById(prof_id).value;
            if (isNaN(prof_m)) {
                total  += 0;
            } else {
                total  += parseFloat(prof_m)*parseFloat(qty);
            }

            var pri_id = i+" price_p";
            var mprice = document.getElementById(pri_id).value;
            if (isNaN(mprice)) {
                totalprice  += 0;
            } else {
                totalprice  += parseFloat(mprice)*parseFloat(qty);
            }
         }

         // alert("Total Margin"+total+" Total Price "+totalprice);
         document.getElementById("total_prof_margin").innerHTML = total.toFixed(2);
         document.getElementById("total_price").innerHTML = totalprice.toFixed(2);
    }


</script>
@section('content')

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{trans('navmenu.mro_cost')}}</p>
                        <h4 class="my-1">{{number_format($mrouse->sum('total_cost'))}} </h4>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{trans('navmenu.rm_cost')}}</p>
                        <h4 class="my-1">{{number_format($rmuse->sum('total_cost'))}}</h4>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto">
                        <i class="bx bxs-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">{{trans('navmenu.pm_cost')}}</p>
                        <h4 class="my-1">{{ number_format($pmuse->sum('total_cost'))}}</h4>
                    </div>
                    <div class="widgets-icons bg-light-dark text-dark ms-auto">
                        <i class="bx bxs-alarm-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
       <div class="card radius-10 ">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">{{trans('navmenu.batch_no')}}</p>
                       <h4 class="my-1">{{$prod_batch}}
                       </h4>
                   </div>
                   <div class="widgets-icons bg-light-danger text-danger ms-auto">
                       <i class="bx bxs-trash"></i>
                   </div>
               </div>
               <div class="text-center">
                  <span class="">{{date('d M, Y', strtotime($prod_date))}}</span>
               </div>
           </div>
       </div>
    </div>
</div>

<div class="row pb-8" >
    <div class="col-md-12">
        <div class="card radius-6">
            <div class="card-header" >
                <div class="card-title">Production Section</div> 
            </div>
            <div class="card-body"> 
                <div class="row">
                    <form method="POST" action="{{url('production/createOld')}}" class="form ">
                          
                        @csrf
                        <div class="form-group col-md-5 " >
                            <label class="form-label" for="prod_qty">{{trans('navmenu.choose_different_batch')}}</label>
                            <select name="prod_batch" class="form-control select2" onchange="this.form.submit()">
                                <option>{{trans('navmenu.select_batch_no')}}</option>
                                @foreach($old_prod_batch as $old_batch)
                                <option value="{{$old_batch}}">{{$old_batch}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>    
                </div>  
                <hr border="3"> 
                @if(count($pms_grouped) >= 1)
                <form method="POST" action="{{route('prod-costs.store')}}">
                    @method('POST')                      
                    @csrf

                    <input type="text" name="prod_batch" value="{{$prod_batch}}" hidden="">
                       
                    <div class="row">
                        <div class="col-md-6">

                        <div class="p-2 border rounded float-start">
                             <div>
                                 <label class="form-label">Total MROs</label> <span>: {{number_format($mrouse->sum('total_cost'))}}</span>
                             </div> 
                             <div>
                                  <label>Total Raw Materials</label> <span>: {{number_format($rmuse->sum('total_cost'))}}</span>
                             </div>
                             <div>
                                 
                              <label>Total Packing Materials</label> <span>: {{number_format($pmuse->sum('total_cost'))}}</span>
                             </div>
                            </div>  

                            <div class="col-md-3 float-end">
                                <label for="product_v" class="form-label" >{{trans('navmenu.total_production_volume')}}</label>
                                <input type="text" name="product_v" value="{{$sum_unit_packed}}" class="form-control form-control-sm mb-3" >
                            </div>  

                        </div>
                    </div> 
                    

                             
                    <div class="row">
                           <div class="col-md-6">
                            <h6>{{trans('navmenu.total_production_cost')}}<span > : {{number_format(
                                  $mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost')
                              )}}</span>
                            </h6>
                            <input type="text" value="{{
                                  $mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost')
                              }}" name="total_cost"  hidden="">
                          </div>  
                    </div>   
                    <div class="row">
                         <table class="table table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                <thead style="text-align: center;">
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th>Quantity</th>
                                    <th>{{trans('navmenu.ratio')}}</th>
                                    <th>{{trans('navmenu.unit_packed')}}</th>
                                    <th>{{trans('navmenu.unit_cost')}}</th>
                                    <th>{{trans('navmenu.profit_margin')}}</th>
                                    <th>{{trans('navmenu.product_price')}}</th>
                                </thead>
                                <tbody>
                                    <?php $v= 0 ;?>
                                    @foreach($pms_grouped as $ky => $pms_group)
                                    <tr>
                                        <?php ++$v ;?>
                                        <td>{{$pms_group['name']}}</td>
                                            <input type="text" name="product_id[]"  value="{{$pms_group['product_id']}}" hidden="">
                                            <input type="text" name="package_id[]"  value="{{$pms_group['packing_material_id']}}" hidden="">
                                        <td><input type="text" name="qty_p[]" id="{{$ky}} qty_p" value="{{$pms_group['quantity']}}"  class="form-control form-control-sm mb-3" placeholder="product quantity" readonly="" style="text-align:center; width: 140px;"></td>

                                        <td><span id="ratio">{{number_format((floatval($pms_group['unit_packed']*$pms_group['quantity'])/$sum_unit_packed)*100 , 2, '.' , '')}}</span> %</td>

                                        <td><input type="text" name="unit_p[]" id="unit_p" value="{{$pms_group['unit_packed']}}"  class="form-control form-control-sm mb-3" placeholder="product quantity" style="text-align:center; width: 140px;"></td>


                                        <td ><input type="text" class="form-control form-control-sm mb-3"  name="unit_cost_p[]" id="{{$ky}} unit_cost_p" value="{{number_format((($mrouse->sum('total_cost')+ $rmuse->sum('total_cost'))*($pms_group['unit_packed']/$sum_unit_packed))+$pms_group['unit_cost'], 2, '.' , '' )}}" readonly="" style="text-align:center; width: 140px;"></td>

                                        <td ><input type="number" step="any" name="prof_margin_p[]" id="{{$ky}} prof_margin_p" onblur="prof(this)"  class="form-control form-control-sm mb-3" placeholder="0.00" required="" value="0" style="text-align:center; width: 140px;"></td> 
                                        <td><input type="number"  step="any" class="form-control form-control-sm mb-3" name="price_p[]" id="{{$ky}} price_p" placeholder="0.00" value="0" onblur="myPrice(this)" style="text-align:center; width: 140px;">
                                        </td>
                                    </tr>
                                    @endforeach
                                    <input type="hidden" name="" id="tot_key" value="{{$v}}">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{trans('navmenu.total')}}</td>
                                        <td><span id="total_prof_margin">0</span></td>
                                        <td><span id="total_price">0</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <div class="row col-md-3" style="padding-top: 10px;" align="center">
                        <button type="submit"  class="btn btn-success btn-block  ">{{trans('navmenu.btn_submit')}}</button>
                    </div>
                </form>
                @elseif($pms_grouped->isEmpty())  
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{route('prod-costs.store')}}">
                            @csrf
                            <input type="number" name="prod_batch" step="any" value="{{$prod_batch}}" hidden="">
                            <input type="number" name="no_pack" step="any" value="1" hidden="">        
                            <div class="row col-md-12"> 
                                <div><h6>{{trans('navmenu.how_many_prod')}} ?</h6></div>
                                <div class="col-md-4">
                                    <label class="form-label" for="prod_qty">{{trans('navmenu.product_quantity')}}</label>
                                    <input type="number" name="product_v" id="prod_qty" class="form-control form-control-sm mb-3" placeholder="product quantity" onblur="updateProdCost()" required="" step="any"> 
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="product">{{trans('navmenu.product_name')}}</label>
                                    <select name="product" class="form-control select2" required="">
                                        <option>{{trans('navmenu.select_product')}}</option>
                                        @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="total_cost">{{trans('navmenu.total_cost')}}</label>
                                    <div> 
                                        <span id="total_cost">{{ $mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost')}}</span>
                                    </div>
                                    <input type="number" name="total_cost"  hidden="" value="{{ $mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost')}}" step="any">
                                </div>
                            </div>

                            <div class="row" style="padding-top:8px;">
                                <h6 class="col mb-2">{{trans('navmenu.cost_per_product')}} :</h6> 
                                <span class="col pl-2" id="prod_cost">@if($pm_qty != 0){{($mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost'))/$pm_qty}} @else 0 @endif  </span>
                                <input type="number" name="prod_cost" id="unit_cost" @if($pm_qty != 0) value="{{($mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost'))/$pm_qty}}"  @else value="0" @endif hidden="" step="any" >
                            </div>
                            <div class="row col-md-12">
                                <div><h6>{{trans('navmenu.set_profit')}} </h6></div>
                                <div class="col-md-4">
                                    <label class="form-label" for="profit_margin">{{trans('navmenu.profit_margin')}}</label>
                                    <input type="number" name="profit_margin" id="profit_margin" class="form-control form-control-sm mb-3" placeholder="{{trans('navmenu.profit_margin')}}"  onblur="setProdProfit()" required="" step="any"> 
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="product_price">{{trans('navmenu.product_price')}}</label>
                                    <input type="number" name="prod_price" id="prod_price" value="0" class="form-control form-control-sm mb-3" onblur="setProdPrice()" step="any">
                                </div>
                            </div>

                            <div class=" row col-md-12 ">
                                <div class="form-group">
                                    <div class="col-sm-6" style="padding-top: 10px;">
                                        <button type="submit"  class="btn btn-success btn-block">{{trans('navmenu.btn_submit')}}</button>
                                    </div>
                                </div> 
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="form-label">Total MROs : </label> 
                            <span>{{number_format($mrouse->sum('total_cost'))}}</span>
                        </div> 
                        <div>
                            <label>Total Raw Materials : </label> 
                            <span>{{number_format($rmuse->sum('total_cost'))}}</span>
                        </div>
                        <div>
                            <label>Total Cost of Production : </label>
                            <span > {{number_format($mrouse->sum('total_cost')+ $rmuse->sum('total_cost')+ $pmuse->sum('total_cost'))}}</span>
                        </div>
                    </div>
                </div> 
                @endif  
            </div>
        </div>
    </div>    
</div>

<!-- =========================================================== -->

<div class="row">
    <div class="col-md-12">
        <div class="card radius-10">
            <div class="card-body">   
                <ul class="nav nav-tabs nav-success " role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tab_1-1" class="nav-link active" role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15">{{trans('navmenu.mro_items')}}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_2-2" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15"> 
                                        {{trans('navmenu.raw_materials')}}  
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_3-3" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15">
                                        {{trans('navmenu.packing_materials')}} 
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                    
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                        <table id="mro_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.mro_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                            </thead>
                            <tbody>
                            @foreach($mros as  $mro)
                                @foreach($mro as $key => $mroitem)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$mroitem->name}}</td>
                                        <td style="text-align: center;">{{$mroitem->quantity}}</td>
                                        <td><span style="color: gray; text-align: center;">
                                                {{$mroitem->basic_uom}}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">{{number_format($mroitem->unit_cost)}}</td>
                                        <td style="text-align: center;">{{number_format($mroitem->total)}}</td>
                                        <td style="text-align: center;">{{$mroitem->date}}</td>
                                    </tr>
                                @endforeach
                            @endforeach 
                            </tbody>
                        </table>
                    </div>
                        
                    <div class="tab-pane fade " id="tab_2-2" role="tabpanel">
                        <table id="rm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.material_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                            </thead>
                            <tbody>
                            @foreach($rms as  $rm)
                                @foreach($rm as $ref => $rmitem)
                                    <tr>
                                        <td>{{$ref+1}}</td>
                                        <td>{{$rmitem->name}}</td>
                                        <td style="text-align: center;">{{$rmitem->quantity}}</td>
                                        <td><span style="color: gray; text-align: center;">
                                            {{$rmitem->basic_uom}}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">{{number_format($rmitem->unit_cost)}}</td>
                                        <td style="text-align: center;">{{number_format($rmitem->total)}}</td>
                                        <td style="text-align: center;">{{$rmitem->date}}</td>
                                    </tr>
                                @endforeach
                            @endforeach 
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade " id="tab_3-3" role="tabpanel">
                        <table id="pm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.packing_name')}}</th>
                                <th>{{trans('navmenu.product_packed')}}</th>
                                <th>{{trans('navmenu.product_basic_uom')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                            </thead>
                            <tbody>
                            @foreach($pms as  $pmv)
                                @foreach($pmv as $index => $pm )
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$pm->name}}</td>
                                        <td>{{$pm->product_name}}</td>
                                        <td>{{$pm->basic_uom}}</td>
                                        <td>{{$pm->quantity}}</td>
                                        <td>{{$pm->package_unit}}</td>
                                        <td>{{$pm->unit_cost}}</td>
                                        <td>{{$pm->total}}</td>
                                        <td>{{$pm->date}}</td>
                                    </tr> 
                                @endforeach
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

