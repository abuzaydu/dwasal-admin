@extends('layouts.app')
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
            window.location.href = "{{ url('remove-rm-use-item')}}/" + id;
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
    <div class="block-header pt-0">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('food-productions') }}">Food Productions</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#itemModal"> <i class="fa fa-shopping-bag"></i> Add Item </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-voucher">
                        <form method="POST" action="{{ route('food-productions.update', encrypt($rmuse->id)) }}" class="row g-1">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-6">
                                <label class="form-label">Food Produced <span style="color: red;">*</span></label>
                                <select id="food-type-id" name ="food_type_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Food Type</option>
                                    @foreach($foodtypes as $key => $ftype)
                                    @if($ftype->id == $rmuse->food_type_id)
                                    <option value="{{$ftype->id}}" selected>{{$ftype->name}}</option>
                                    @else
                                    <option value="{{$ftype->id}}">{{$ftype->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <div id="msg"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="date" value="{{$rmuse->date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="msg"></div>
                                <table class="list-items" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.description')}}</th>
                                            <th style="text-align: center;">UOM</th>
                                            <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $tqty = 0; ?>
                                        @foreach($uitems as $key => $item)
                                        <?php $tqty += $item->quantity; ?>
                                        <tr>
                                            <td class="no">{{$key+1}}</td>
                                            <td class="text-left">{{$item->name}}</td>
                                            <td style="text-align: center;">{{$item->basic_uom}}</td>
                                            <td class="qty" style="text-align: center;">
                                                <input class="edit" id="qty_{{$item->id}}" type="number" name="quantity" value="{{$item->quantity+0}}" style="text-align: center;">
                                            </td>
                                            <td class="del" style="text-align: center;">
                                                <a href="#" class="button" onclick="confirmDelete('{{encrypt($item->id)}}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td><b>{{trans('navmenu.total')}}</b></td>
                                            <td></td>
                                            <td style="text-align: center;"><b>{{$tqty}}</b></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Comments</label>
                                <input type="text" class="form-control form-control-sm mb-1" name="comments" value="{{$rmuse->comments}}">
                            </div>
                            <div class="col-md-12">
                                <input type="submit" name="submit" class="btn btn-success btn-sm">
                                <a href="{{ url('food-productions') }}" class="btn btn-warning btn-sm" style="margin-right: 5px;">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="itemModal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="pull-left" id="myModalLabel">Add Item</h5>
                    <button type="button"  class="close btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="fa fa-x-circle"></span></button>
                    
                </div>
                <form class="form-validate" method="POST" action="{{url('add-rm-use-item')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="rm_use_id" value="{{$rmuse->id}}">
                        <div class="col-md-6 mb-1">
                            <label class="form-label">Name <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="raw_material_id" required style="width: 100%; border: 1px solid gray;">
                                <option value="">Select Product</option>
                                @foreach($materials as $key => $material)
                                <option value="{{$material->id}}">{{$material->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
         // Save data
        $(".edit").focusout(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // $(this).removeClass("editMode");
            var id = this.id;
            var split_id = id.split("_");
            var field_name = split_id[0];
            var edit_id = split_id[1];
            var value = $(this).val();

            $.ajax({
                url: "{{ url('update-rm-use-item') }}",
                type: 'POST',
                data: { quantity: value, id:edit_id },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 1300);
                    }else{
                        $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                                // location.reload();
                                
                            });
                        }, 1300);
                    }
                }
            });
        });
    });
</script>

    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>