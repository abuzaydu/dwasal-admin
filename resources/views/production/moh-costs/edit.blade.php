@extends('layouts.prod')
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
            document.getElementById('delete-form-' + id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }
</script>
<style type="text/css">
    .gridCard{
        padding-top: 10px;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .gridCard::-webkit-scrollbar {
      display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .gridCard {
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
    }

    .gridScale{
        width : 8rem; 
        height: 5rem;
    }

    .gridName{
         font-size: 18;
         white-space: nowrap; 
         overflow: hidden; 
         text-overflow: ellipsis; 
    }

    .qtySize{
        text-align:center; 
        height: 20px; 
        width: 10px; 
        border: 1px solid #e0e0e0;
        padding-right: 2px;
    }
</style>

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                <a href=""></a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row print_invoice">
                        <div class="col-md-4">
                            <form class="row g-3" action="{{route('moh-items.store')}}" method="POST">
                                @csrf
                                <input type="hidden" name="moh_cost_id" value="{{$moh->id}}">
                                <div class="col-md-12">
                                    <label class="form-label">Add Item </label>
                                    <select name="mro_id" class="form-select form-select-sm mb-1" onchange='if(this.value != 0) { this.form.submit(); }' required>
                                        <option value="">Select Labour Cost Item </option>
                                        @foreach($mros as $mro)
                                        <option value="{{$mro->id}}">{{$mro->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 border rounded">
                                <table class="items mt-0"  style="width: 100%;  display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">Item Name</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{$key + 1}}</td>
                                            <td style="text-align: left;">{{$item->name}}</td>
                                            <td style="text-align: center;"><input id="quantity_{{$item->id}}" type="number" name="quantity" min="0" step="any" value="{{$item->quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm edit"></td>
                                            <td style="text-align: center;"><input id="unitcost_{{$item->id}}" type="number" name="unit_cost" min="0" step="any" value="{{$item->unit_cost}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm edit"></td>
                                            <td style="text-align: center;"><input id="total_{{$item->id}}" type="number" name="total" min="0" step="any" value="{{$item->total}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm edit"></td>
                                            <td style="text-align: center;">
                                                <form id="delete-form-{{$key}}" method="POST" action="{{route('moh-items.destroy' , encrypt($item->id))}}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" onclick="confirmDelete('{{$key}}')">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a> 
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="3"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>{{ number_format($moh->amount, 2, '.', ',') }}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="msg">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('moh-costs.update', encrypt($moh->id)) }}" class="row g-3 pt-3">
                                @csrf
                                {{ method_field('PATCH') }}
                                <div class="col-md-3">
                                    <label class="form-label">Date </label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="date" value="{{$moh->date}}" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="remarks" value="{{$moh->remarks}}" placeholder="Enter Your Remarks" class="form-control form-control-sm mb-3" required>
                                </div>
                                <div class="col-md-12">
                                    <input type="submit" name="submit" class="btn btn-success btn-sm float-end">
                                    <a href="{{ url('moh-costs') }}" class="btn btn-warning btn-sm float-end" style="margin-right: 5px;">Cancel</a>
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
                url: "{{ url('update-moh-item') }}",
                type: 'POST',
                data: { field_name: field_name, value: value, id:edit_id },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                                location.reload();
                            });
                        }, 300);
                    }else{
                        $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                                location.reload();
                            });
                        }, 1300);
                    }
                }
            });
        });
    });
</script>

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

        });
    </script>
