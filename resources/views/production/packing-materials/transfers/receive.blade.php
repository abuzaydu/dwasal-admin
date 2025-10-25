@extends('layouts.prod')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{url('pm-transfers')}}">PM Transfers</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-xl-12 col-md-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="orderform" method="POST" action="{{ url('receive-pm-transfer') }}">
                            @csrf
                            <input type="hidden" name="pm_transfer_id" value="{{$pmt->id}}">
                            <div class="col-md-12 pt-0" style="margin-top: 5px; border-top: 2px solid #BBDEFB;">
                                <div id="msg"></div>
                                <table class="table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="Item">{{trans('navmenu.item_name')}}</th>
                                                <th class="qty">Received {{trans('navmenu.qty')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $key => $item)
                                            <tr id="temps">
                                                <td>{{$key + 1}}</td>
                                                <td class="item">{{$item->name}}</td>
                                                <td class="qty">
                                                    <input class="edit" id="qty_{{$item->id}}" type="number" name="qty" style="text-align: center;">
                                                </td>
                                                <td><a href="#"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">  
                                    <button type="submit" class="btn btn-primary btn-sm mb-1"><i class="fa fa-send" ></i> Comfirm Receive
                                        </button>
                                    <a href="{{ route('pm-transfers.show', encrypt($pmt->id)) }}" class="btn btn-warning btn-sm mr-1 card-subtitle" id="btn-cancel"><i class="fa fa-close"></i> {{trans('navmenu.btn_cancel')}}</a>
                                </div>
                            </div>
                        </form>
                    </div>  
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
                url: "{{ url('update-pm-transfer-item-rec-qty') }}",
                type: 'POST',
                data: { rec_qty: value, id:edit_id },
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
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pm_transfer_date"]')

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
