@extends('layouts.app')
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {

            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
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
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('sales-returns') }}">Sales Returns</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class=" col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" action="{{route('sale-return-items.store')}}" method="POST">
                        @csrf
                        <input type="hidden" name="sale_return_id" value="{{$salereturn->id}}">
                        <div class="form-group col-md-6">
                            <label class="form-label">Select Item Returned</label>
                            <select name="product_id" class="form-select form-select-sm mb-1" onchange='if(this.value != 0) { this.form.submit(); }' required>
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                <option value="{{$item->product_id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div id="inv-content">
                        <div class="print_invoice">
                            <div id="msg">
                                
                            </div>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="desc">Description</th>
                                        <th class="qty" style="text-align: center;">Quantity</th>
                                        <th class="del" style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sritems as $key => $item)
                                    <tr>
                                        <td> {{$key+1}} </td>
                                        <td class="desc">{{$item->name}}</td>
                                        <td class="qty" style="text-align: center;">
                                            <input class="edit" id="qty_{{$item->id}}" type="number" name="quantity" value="{{$item->quantity+0}}" style="text-align: center;">
                                        </td>
                                        <td class="del" style="text-align: center;">
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('sale-return-items.destroy' , encrypt($item->id))}}" style="display: inline;">
                                                @csrf         
                                                @method('DELETE')
                                                <a href="#" class="button" onclick="confirmDelete('{{$key}}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <form class="row g-3" method="POST" action="{{route('sales-returns.update', $salereturn->id)}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="sale_return_id" value="{{$salereturn->id}}">
                        <div class="form-group col-md-2">
                            <label class="form-label">Sales return from::</label>
                            <input type="text" name="customer" class="form-control form-control-sm mb-1" value="{{$salereturn->name}}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Sale Date :</label>
                            <input type="text" name="date" class="form-control form-control-sm mb-1" value="{{date('d, M Y', strtotime($salereturn->time_created))}}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Return Date :</label>
                            <input type="text" name="return_date" value="{{$salereturn->return_date}}" class="form-control form-control-sm mb-1" placeholder="Pick Date" required>
                        </div>
                        <div class="form-group col-md-8">
                            <label class="form-label">Reason for return<span style="color: red;">*</span></label>
                            <input class="form-control form-control-sm mb-1" name="reason" placeholder="Enter reason for issueing this Credit Note" value="{{$salereturn->reason}}">
                        </div>
                        <div class="form-group col-md-12 p-4" style="text-align: right;">
                            <button class="btn btn-primary btn-sm">Create</button>
                            <a href="{{url('delete-sale-return/'.encrypt($salereturn->id))}}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
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
                url: "{{ url('update-sale-return-item') }}",
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
            var $min = document.querySelector('[name="return_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>