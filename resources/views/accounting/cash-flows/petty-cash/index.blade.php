@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to Delete/Cancel this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }

        function confirmDeleteCancelled(id) {
            Swal.fire({
                title: 'Are you sure, You want to Delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-c-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }

        function detailUpdate(elem) {
            var bank = document.getElementById('bank');
            var slip = document.getElementById('slip');
            if (elem.value === 'Bank') {
                slip.style.display = 'block'
                bank.style.display = 'block';
            }else{
                slip.style.display = "none";
                bank.style.display = "none";
            }
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
               <a href="#"  class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#expModal" data-backdrop="static" data-keyboard="false" style="margin-right: 2px;"><i class="fa fa-money"></i> Request Petty Cash</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body p-2">
                    <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_0" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Petty Cashes</div>
                                </div>
                            </a>
                        </li>
                        @if($shop->is_hq)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_1" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-alt font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Branch Requests</div>
                                </div>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-close font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Cancelled Petty Cashes</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="p-4 border rounded tab-pane fade show active" id="tab_0" role="tabpanel">
                            <table  id="petty-cash" class="table table-striped display nowrap" style="width:100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request Date</th>
                                        <th>Requested By</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Approved By</th>
                                        <th>Received Date</th>
                                        <th>Reference</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pettycashs as $key => $petty)
                                    <tr>
                                        <th scope="row">{{$key+1}}</th>
                                        <td>{{ date('d M Y H:i:s', strtotime($petty->request_date)) }}</td>
                                        <td>{{$petty->first_name}} {{$petty->last_name}}</td>
                                        <td>{{ number_format($petty->amount, 2, '.', ',') }}</td>
                                        <td>{{$petty->status}}</td>
                                        <td>{{$petty->approver}}</td>
                                        <td>@if(!is_null($petty->received_date)){{ date('d M Y H:i:s', strtotime($petty->received_date)) }}@endif</td>
                                        <td>{{$petty->ref_no}}</td>
                                        <td>{{$petty->description}}</td>
                                        <td>
                                            <a class="text-success" href="{{ route('petty-cash.show', encrypt($petty->id)) }}"><i class='fa fa-file-text-o mr-1'></i> Detail</a> | 
                                            @if(!$petty->is_approved)
                                            @if(Auth::user()->can('delete-petty-cash'))
                                            <a class="text-primary" href="{{ route('petty-cash.edit', encrypt($petty->id)) }}"><i class='fa fa-pencil mr-1'></i> Edit</a> | 
                                            @endif
                                            @endif
                                            @if(Auth::user()->can('delete-petty-cash'))
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('petty-cash.destroy', encrypt($petty->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')">
                                                    @if($petty->status == 'Received')
                                                    <i class='fa fa-close mr-1'></i> Cancel</a>
                                                    @else
                                                    <i class='fa fa-trash mr-1'></i> Delete</a>
                                                    @endif
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border rounded tab-pane fade" id="tab_1" role="tabpanel">
                            <table  id="branch-petty-cash" class="table table-striped display nowrap" style="width:100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request Date</th>
                                        <th>Requested By</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Approved By</th>
                                        <th>Received Date</th>
                                        <th>Reference</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branch_pettycashes as $key => $petty)
                                    <tr>
                                        <th scope="row">{{$key+1}}</th>
                                        <td>{{ date('d M Y H:i:s', strtotime($petty->request_date)) }}</td>
                                        <td>{{$petty->first_name}} {{$petty->last_name}}</td>
                                        <td>{{ number_format($petty->amount, 2, '.', ',') }}</td>
                                        <td>{{$petty->status}}</td>
                                        <td>{{$petty->approver}}</td>
                                        <td>@if(!is_null($petty->received_date)){{ date('d M Y H:i:s', strtotime($petty->received_date)) }}@endif</td>
                                        <td>{{$petty->ref_no}}</td>
                                        <td>{{$petty->description}}</td>
                                        <td>
                                            <a class="text-success" href="{{ route('petty-cash.show', encrypt($petty->id)) }}"><i class='fa fa-file-text-o mr-1'></i> Detail</a> | 
                                            @if(!$petty->is_approved)
                                            @if(Auth::user()->can('delete-petty-cash'))
                                            <a class="text-primary" href="{{ route('petty-cash.edit', encrypt($petty->id)) }}"><i class='fa fa-pencil mr-1'></i> Edit</a> | 
                                            @endif
                                            @endif
                                            @if(Auth::user()->can('delete-petty-cash'))
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('petty-cash.destroy', encrypt($petty->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')"><i class='fa fa-trash mr-1'></i> Delete</a>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border rounded tab-pane fade" id="tab_2" role="tabpanel">
                            <table  id="cancel-petty-cash" class="table table-striped display nowrap" style="width:100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request Date</th>
                                        <th>Requested By</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Approved By</th>
                                        <th>Received Date</th>
                                        <th>Reference</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cancelpettycashs as $ckey => $petty)
                                    <tr>
                                        <th scope="row">{{$ckey+1}}</th>
                                        <td>{{ date('d M Y H:i:s', strtotime($petty->request_date)) }}</td>
                                        <td>{{$petty->first_name}} {{$petty->last_name}}</td>
                                        <td>{{ number_format($petty->amount, 2, '.', ',') }}</td>
                                        <td>{{$petty->status}}</td>
                                        <td>{{$petty->approver}}</td>
                                        <td>@if(!is_null($petty->received_date)){{ date('d M Y H:i:s', strtotime($petty->received_date)) }}@endif</td>
                                        <td>{{$petty->ref_no}}</td>
                                        <td>{{$petty->description}}</td>
                                        <td>
                                            <a class="text-success" href="{{ route('petty-cash.show', encrypt($petty->id)) }}"><i class='fa fa-file-text-o mr-1'></i> Detail</a> | 
                                            @if(!$petty->is_approved)
                                            @if(Auth::user()->can('delete-petty-cash'))
                                            <a class="text-primary" href="{{ route('petty-cash.edit', encrypt($petty->id)) }}"><i class='fa fa-pencil mr-1'></i> Edit</a> | 
                                            @endif
                                            @endif
                                            @if(Auth::user()->can('delete-petty-cash'))
                                            <form id="delete-c-form-{{$ckey}}" method="POST" action="{{ route('petty-cash.destroy', encrypt($petty->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteCancelled('<?php echo $ckey; ?>')">
                                                    <i class='fa fa-trash mr-1'></i> Delete</a>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Size -->
    <div class="modal fade" id="expModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Petty Cash Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="my-form" method="POST" action="{{ route('petty-cash.store') }}">
                    <div class="modal-body row">
                        @csrf
                        @if(!$shop->is_hq)
                        <div class="col-md-6">
                            <label class="form-label">Request From Main Branch/HQ? <span  style="color: red; font-weight: bold;">*</span></label>
                            <select name="is_from_hq" class="form-select form-select-sm mb-3"required>
                                <option value="">--Select--</option>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="is_from_hq" value="0">
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Amount <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" step="any" name="amount" required placeholder="Enter Request Amount" value="0" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description <span  style="color: red; font-weight: bold;">*</span></label>
                            <textarea class="form-control form-control-sm mb-3" rows="1" name="description" placeholder="Enter Request Description (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">Send Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $( document ).ready(function() {
        inputamt = $("#inputAmount");
        var n = inputamt.val();
        var output = getCommaSeparatedTwoDecimalsNumber(n);
        inputamt.val(output);

        inputamt.on('focus', function(){
            var n = $(this).val();
            let output = parseFloat(n.replace(/,/g, ''));
            $(this).val(output);
        });

        inputamt.on('blur', function(){
            var n = $(this).val();
            var output = getCommaSeparatedTwoDecimalsNumber(n);
            $(this).val(output);
        });
    });

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#petty-cash').DataTable({
                'scrollX': true,
            });

            $('#cancel-petty-cash').DataTable({
                'scrollX': true,
            });
            $('#branch-petty-cash').DataTable({
                'scrollX': true,
            });
        });
    </script>
@endsection