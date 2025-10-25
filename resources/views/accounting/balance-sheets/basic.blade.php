@extends('layouts.acc')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting & Finance</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <form class="row g-1" method="POST" action="{{ url('f-balance-sheets') }}">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm mb-1" name="year" onchange="this.form.submit()">
                            @foreach($months as $d)
                            @if($d->date == $year)
                            <option selected value="{{$d->date}}">{{ date('M Y', strtotime($d->date)) }}</option>
                            @else
                            <option value="{{$d->date}}">{{ date('M Y', strtotime($d->date)) }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9">
                        @if($mbs == 0)
                        <a href="{{ url('create-bs') }}" class="btn btn-primary btn-sm">Generate Balance Sheet for Last Year</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row mt">
        <div class="col-md-12 mx-auto">
            @if($mbs > 0)
            <div class="card">
                <div class="card-body row g-1 pt-0">
                    <div class="col-md-12" id="msg">
                    
                    </div>
                    <div class="col-md-6 print_invoice mt-0">
                        <table class="items mt-1" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="2" class="Item">CURRENT ASSETS</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($current_assets as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->item_desc}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="amt_{{$item->id}}" value="{{$item->amount+0}}" style="text-align: center;">
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="items mt-1" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="2" class="Item">FIXED (LONG TERM) ASSETS</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fixed_assets as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->item_desc}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="amt_{{$item->id}}" value="{{$item->amount+0}}" style="text-align: center;">
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="items mt-1" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="2" class="Item">OTHER ASSETS</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($other_assets as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->item_desc}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="amt_{{$item->id}}" value="{{$item->amount+0}}" style="text-align: center;">
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 print_invoice mt-0">
                        <table class="mt-1" style="width: 100%;">
                            <thead>
                                <tr style="background: #faee96;">
                                    <th colspan="2" class="Item">CURRENT LIABILITIES</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($current_liabilities as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->item_desc}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="amt_{{$item->id}}" value="{{$item->amount+0}}" style="text-align: center;">
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="mt-1" style="width: 100%;">
                            <thead>
                                <tr style="background: #faee96;">
                                    <th colspan="2" class="Item">LONG TERM LIABILITIES</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($long_term_liabilities as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->item_desc}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="amt_{{$item->id}}" value="{{$item->amount+0}}" style="text-align: center;">
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="mt-1" style="width: 100%;">
                            <thead>
                                <tr style="background: #faee96;">
                                    <th colspan="2" class="Item">OWNER'S EQUITY</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($owners_equity as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->item_desc}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="amt_{{$item->id}}" value="{{$item->amount+0}}" style="text-align: center;">
                                    </td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-5">
                        <a href="javascript:;" class="btn btn-primary btn-sm float-end"  onclick="confirmUpdates()">Confirm & Close</a>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info hideit alertSuc">No Balance sheet records found</div>
            @endif
        </div>
    </div> 
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    function confirmUpdates() {
        Swal.fire({
            title: "Are you sure you want to confirm changes",
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, Confirm",
            cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
            if (result.value) {
                window.location.href="{{ url('balance-sheet') }}";
            }
        })
    }
    
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
                url: "{{url('update-bs-item')}}",
                type: 'POST',
                data: { amount: value, id:edit_id },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div>');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 1300);
                    }else{
                        $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div>');
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