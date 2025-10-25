@extends('layouts.acc')
@section('content')
    <script type="text/javascript">
        function confirmApproval(id) {
            Swal.fire({
                title: 'Are you sure to approve the requested Petty Cash?',
                showDenyButton: true,
                confirmButtonText: 'Yes Approve',
                denyButtonText: `Don't Approve`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'approve-petty-cash/'+id;
                    Swal.fire('Approved!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Approved', '', 'info')
                }
            })
        }

        function confirmReceived(id) {
            Swal.fire({
                title: 'Are you sure to confirm this Petty Cash Received?',
                showDenyButton: true,
                confirmButtonText: 'Yes Confirm',
                denyButtonText: `Don't Confirm`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'confirm-petty-cash-receive/'+id;
                    Swal.fire('Received!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Received', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item"><a href="{{ url('petty-cash') }}">Petty Cash Requests </a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row g-3">
        <div class="col-xl-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table class="table table-striped display nowrap" style="width: 100%; font-size: 14px;">
                                <tbody>
                                    <tr>
                                        <th>Branch</th>
                                        <td>{{ App\Models\Shop::find($petty->shop_id)->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Request Date</th>
                                        <td>{{ date('d M Y H:i:s', strtotime($petty->request_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Requested By</th>
                                        <td>{{$petty->first_name}} {{$petty->last_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td colspan="3">{{$petty->description}}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>{{number_format($petty->amount, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{$petty->status}}</td>
                                    </tr>
                                    @if($petty->status == 'Rejected')
                                    <tr>
                                        <th>Rejected By</th>
                                        <td>{{$petty->approver}}</td>
                                    </tr>
                                    <tr>
                                        <th>Rejected At</th>
                                        <td>@if(!is_null($petty->approved_at)){{ date('d M Y H:i:s', strtotime($petty->approved_at)) }}@endif</td>
                                    </tr>
                                    <tr>
                                        <th>Reject Reason</th>
                                        <td><span class="text-danger">{{$petty->reject_reason}}</span></td>
                                    </tr>
                                    @else
                                    <tr>
                                        <th>Approved By</th>
                                        <td>{{$petty->approver}}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved At</th>
                                        <td>@if(!is_null($petty->approved_at)){{ date('d M Y H:i:s', strtotime($petty->approved_at)) }}@endif</td>
                                    </tr>
                                    <tr>
                                        <th>Issued By</th>
                                        <td>{{$petty->issued_by}}</td>
                                    </tr>
                                    <tr>
                                        <th>Issued At</th>
                                        <td>@if(!is_null($petty->issued_date)){{ date('d M Y H:i:s', strtotime($petty->issued_date)) }}@endif</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Received Date</th>
                                        <td>@if(!is_null($petty->received_date)){{ date('d M Y H:i:s', strtotime($petty->received_date)) }}@endif</td>
                                    </tr>
                                    <tr>
                                        <th>Reference</th>
                                        <td>{{$petty->ref_no}}</td>
                                    </tr>
                                    @if(!is_null($account))
                                    <tr>
                                        <td colspan="2" class="bg-primary text-light">Received From</td>
                                    </tr>
                                    <tr>
                                        <th>Account Type</th>
                                        <td>{{$account->type}}</td>
                                    </tr>
                                    <tr>
                                        <th>Account Name</th>
                                        <td>{{ $account->account_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Account</th>
                                        <td>{{$account->account_number}}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">

                        @if(!$petty->is_approved)
                        <a href="{{ route('petty-cash.edit', encrypt($petty->id))}}" class="btn btn-sm btn-secondary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        @if(Auth::user()->can('approve-petty-cash'))
                        <button type="button" data-bs-toggle="modal" data-bs-target="#rejectModal" class="btn btn-sm btn-warning btn-sm"><i class="fa fa-check"></i> Reject</button>
                        <button type="button" onclick="confirmApproval('<?php echo encrypt($petty->id); ?>')" class="btn btn-sm btn-primary btn-sm"><i class="fa fa-check"></i> Approve</button>
                        @endif
                        @endif
                        @if($petty->is_approved && $petty->status != 'Issued' && $petty->status != 'Received' && Auth::user()->can('confirm-petty-cash-issue'))
                        <a href="#"  class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#pettyModal" data-backdrop="static" data-keyboard="false" style="margin-right: 2px;"><i class="fa fa-money"></i>Confirm Petty Cash Issued</a>
                        @endif
                        @if($petty->is_approved && $petty->status == 'Issued' && Auth::user()->can('confirm-petty-cash-receive'))
                        <a href="#"  class="btn btn-success btn-sm" onclick="confirmReceived('<?php echo encrypt($petty->id); ?>')" style="margin-right: 2px;"><i class="fa fa-money"></i>Confirm Petty Cash Received</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="addLeaveModal" aria-hidden="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Petty Cash rejection reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="basic-form" method="POST" action="{{ url('reject-petty-cash') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" value="{{$petty->id}}">
                            <div class="col-md-12 mb-3">
                                <textarea name="reject_reason" class="form-control form-control-sm" placeholder="Enter rejection reason" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="submit()" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Default Size -->
    <div class="modal fade" id="pettyModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Issue Petty Cash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('petty-cash.update', encrypt($petty->id)) }}">
                    <div class="modal-body row">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="status" value="Issued" class="form-control form-control-sm mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" step="any" name="amount" value="{{$petty->amount}}" required placeholder="Enter Request Amount" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Received from <span  style="color: red; font-weight: bold;">*</span> </label>
                            <select name="account_id" class="form-select form-select-sm mb-3" required>
                                @if($petty->is_from_hq)
                                <option value="hq">From HQ</option>
                                @else
                                <option value="">Select Account</option>
                                @foreach($accounts as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference Number</label>
                            <input id="name" type="text" name="ref_no" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description</label>
                            <textarea class="form-control form-control-sm mb-3" name="description" placeholder="Enter petty Description (Optional)....">{{$petty->description}}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
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