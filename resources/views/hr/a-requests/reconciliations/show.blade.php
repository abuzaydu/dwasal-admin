@extends('layouts.hr')
    
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reconciliations')}}">Reconciliations</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
            </div>
        </div>
    </div>

    
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-reconciliation">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="bg-primary text-light" style="text-align: center;">
                                            <h4 class="mb-0 text-uppercase">Reconciliation Note</h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 border-bottom pb-0">
                            <table class="items mt-0">
                                <tr>
                                    <td style="width: 40%; text-align: right; padding-left: 20px;">
                                        <img id="image" src="{{ asset('assets/img/logo.png') }}" alt="logo" width="100">
                                    </td>
                                    <td style="width: 60%;">
                                        <strong>{{$settings->company_name}}.</strong><br> <small>{{$settings->address}}, {{$settings->poaddress}}<br> Email: <b>{{$settings->email}}</b><br> Tel: <b>{{$settings->tel}}</b> Phone: <b>{{$settings->phone}}</b><br>TIN: <b>{{$settings->tin}}</b> VRN: <b>{{$settings->vrn}}</b></small>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-12 customer mt-2 mb-0">
                            <div class="row g-3">
                                <div class="col-md-8" style="padding-left: 20px; font-size: 11px;">
                                    REQUEST ID : <b>{{ $reconciliation->reconcile_id }}</b><br>
                                    @if(!is_null($project))
                                    PROJECT NAME : <b>{{ $project->project_name }}</b><br>
                                    PROJECT CODE : <b>{{ $project->project_code }}</b><br>
                                    @endif
                                    DEPARTMENT : <b>{{ $department }}</b><br>
                                    NAME : <b>{{ $reconciliation->name }}</b><br>
                                    SIGNATURE : <b>{{ $signature }}</b>
                                </div>
                                <div class="col-md-4">
                                    <table class="meta">
                                        <tbody>
                                            <tr>
                                                <td class="meta-head" style="text-align: right;">Request Date</td>
                                                <td><b id="date" style="text-align: right;">{{ date('d M, Y', strtotime($reconciliation->request_date)) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="meta-head" style="text-align: right;">Status</td>
                                                <td><b id="date" style="text-align: right;">{{ $reconciliation->status }}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="meta-head" style="text-align: right;">PV No.</td>
                                                <td>
                                                    <b class="date" style="text-align: right;">{{ $reconciliation->pv_no }}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- <div style="clear:both"></div> -->
                        <div class="col-md-12">
                            <table class="items mt-3">
                                <tbody>
                                    <tr>
                                        <th style="width: 2%;">#</th>
                                        <th style="width: 48%;">Item Description</th>
                                        <th style="text-align: center; width: 10%;">No. of Days</th>
                                        <th style="text-align: center; width: 5%">Qty</th>
                                        <th style="text-align: center; width: 15%;">Price</th>
                                        <th style="text-align: center; width: 20%;">Total</th>
                                    </tr>
                                    <?php $total_spent = 0; $total_appr = 0; ?>
                                    @foreach($items as $key => $item)
                                    <?php $total_spent += $item->total; ?>
                                    <tr class="item-row">
                                        <td style="width: 5px;">{{$key+1}}</td>
                                        <td class="description">{{$item->item_description}}</td><td class="qty" style="text-align: center;">{{ $item->no_of_days }}</td>
                                        <td class="qty" style="text-align: center;">{{ $item->quantity }}</td>
                                        <td class="qty" style="text-align: center;">{{ $item->price }}</td>
                                        <td class="qty" style="text-align: right;">{{ number_format($item->total, 2, '.', ',') }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" style="text-align: right; text-transform: uppercase;">Amount Spent</td>
                                        <th style="text-align: right;">{{ number_format($total_spent, 2, '.', ',') }}</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" style="text-align: right; text-transform: uppercase;" class="text-primary">Amount Received</td>
                                        <th style="text-align: right;" class="text-primary">{{ number_format($reconciliation->amount_received, 2, '.', ',') }}</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" style="text-align: right; text-transform: uppercase;" class="text-secondary">Amount to Refund</td>
                                        <th style="text-align: right;" class="text-secondary">
                                            @if($reconciliation->amount_received > $total_spent)
                                            {{ number_format(($reconciliation->amount_received - $total_spent), 2, '.', ',') }}
                                             @else
                                            0
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" style="text-align: right; text-transform: uppercase;" class="text-danger">Amount To Claim</td>
                                        <th style="text-align: right;" class="text-danger">
                                            @if($total_spent > $reconciliation->amount_received)
                                            {{ number_format($total_spent-$reconciliation->amount_received, 2, '.', ',') }}
                                            @else
                                            0
                                            @endif
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($reconciliation->status != 'Payment Received')
                        <div class="col-md-12 text-center pt-2">
                            <h6 class="mb-0 text-uppercase" style="font-size: 12px;">Reconciliation Evidence </h6><br><small class="text-warning">(Click to preview)</small>
                            <hr>
                            <div class="row g-1 justify-content-center">
                                @foreach($evidence as $eimg)
                                @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $eimg->file_url))
                                <div class="col-md-6 col-sm-12" style="padding: 0px; border: 1px solid lightgrey; border-radius: 5px; margin: 0px;"><a href="{{ asset('storage/'.$eimg->file_url) }}" target="_blank"><i class="fa fa-image"></i> Evidence Image</a></div>
                                @else
                                <div class="col-md-6 col-sm-12" style="padding: 0px; border: 1px solid lightgrey; border-radius: 5px; margin: 0px;">
                                    <a href="{{ asset('storage/'.$eimg->file_url) }}" target="_blank"><i class="fa fa-file-pdf-o"></i> Evidence PDF File</a>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            <hr>
                        </div>
                        @endif
                        <div class="col-md-12 mt-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <table class="items mt-0">
                                        <thead>
                                            <tr>
                                                <th style="width:5%"></th>
                                                <th style="width:65%;">Comments</th>
                                                <th style="width:30%">Commented By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reccoms as $key => $com)
                                            <tr class="item-row">
                                                <td>{{$key + 1}}</td>
                                                <td>{{ $com->comments }}</td>
                                                <td>{{ $com->name }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6 text-center">
                                    <table class="items mt-0">
                                        <tbody>
                                            <tr>
                                                <td>Cashier Sign</td>
                                                <th>
                                                    @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $reconciliation->cashier_sign))
                                                    <img src="{{ asset('storage/'.$reconciliation->cashier_sign) }}" height="20px;" alt="User Signature">
                                                    @else
                                                    {{$reconciliation->cashier_sign}}
                                                    @endif
                                                </th>
                                                <td>Date</td>
                                                <th>
                                                    @if(!is_null($reconciliation->cashier_sign_time))
                                                        {{ date('d/m/Y h:i A', strtotime($reconciliation->cashier_sign_time))}}
                                                    @endif
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>Received By</td>
                                                <th>
                                                    @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $reconciliation->receiver_sign))
                                                    <img src="{{ asset('storage/'.$reconciliation->receiver_sign) }}" height="20px;" alt="User Signature">
                                                    @else
                                                    {{$reconciliation->receiver_sign}}
                                                    @endif
                                                </th>
                                                <td>Date</td>
                                                <th>
                                                    @if(!is_null($reconciliation->receiver_sign_time))
                                                        {{ date('d/m/Y h:i A', strtotime($reconciliation->receiver_sign_time))}}
                                                    @endif
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    @if(!is_null($project))
                                                    PM Sign
                                                    @else
                                                    HM Sign
                                                    @endif
                                                </td>
                                                <th>
                                                    @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $reconciliation->pm_sign))
                                                    <img src="{{ asset('storage/'.$reconciliation->pm_sign) }}" height="20px;" alt="User Signature">
                                                    @else
                                                    {{ $reconciliation->pm_sign}}
                                                    @endif
                                                </th>
                                                <td>Date</td>
                                                <th>
                                                    @if(!is_null($reconciliation->pm_sign_time))
                                                        {{ date('d/m/Y h:i A', strtotime($reconciliation->pm_sign_time))}}
                                                    @endif
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>TM Sign</td>
                                                <th>
                                                    @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $reconciliation->tm_sign))
                                                    <img src="{{ asset('storage/'.$reconciliation->tm_sign) }}" height="20px;" alt="User Signature">
                                                    @else
                                                    {{ $reconciliation->tm_sign }}
                                                    @endif
                                                </th>
                                                <td>Date</td>
                                                <th>
                                                    @if(!is_null($reconciliation->tm_sign_time))
                                                        {{ date('d/m/Y h:i A', strtotime($reconciliation->tm_sign_time))}}
                                                    @endif
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>TD Sign</td>
                                                <th>
                                                    @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $reconciliation->td_sign))
                                                    <img src="{{ asset('storage/'.$reconciliation->td_sign) }}" height="20px;" alt="User Signature">
                                                    @else
                                                    {{ $reconciliation->td_sign }}
                                                    @endif
                                                </th>
                                                <td>Date</td>
                                                <th>
                                                    @if(!is_null($reconciliation->td_sign_time))
                                                        {{ date('d/m/Y h:i A', strtotime($reconciliation->td_sign_time))}}
                                                    @endif
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    @if($reconciliation->status == 'Payment Received')
                    <div class="row g-3">
                        <div class="col-md-12 text-center pt-2">
                            <h6 class="mb-0 text-uppercase" style="font-size: 12px;">Reconciliation Evidence </h6><br><small class="text-warning">(Click to preview)</small>
                            <hr>
                            <div class="row g-1 justify-content-center">
                                @foreach($evidence as $eimg)
                                @if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $eimg->file_url))
                                <div class="col-md-6 col-sm-12" style="padding: 0px; border: 1px solid lightgrey; border-radius: 5px; margin: 0px;"><a href="{{ asset('storage/'.$eimg->file_url) }}" target="_blank"><i class="fa fa-image"></i> Evidence Image</a></div>
                                @else
                                <div class="col-md-6 col-sm-12" style="padding: 0px; border: 1px solid lightgrey; border-radius: 5px; margin: 0px;">
                                    <a href="{{ asset('storage/'.$eimg->file_url) }}" target="_blank"><i class="fa fa-file-pdf-o"></i> Evidence PDF File</a>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            <hr>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 text-center text-md-end">
            @if($reconciliation->status == 'Payment Received')
                <button type="button" onclick="javascript:savePdf()" class="btn btn-sm btn-secondary mt-2"><i class="fa fa-file-pdf-o me-2"></i>Print Preview</button>
            @else
                @if(Session::get('curr_role') == 'hr manager' && is_null($reconciliation->pm_sign)) 
                <button type="button" class="btn bg-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#reject-modal">Reject</button>
                <a href="{{ url('approve-reconciliation/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Approve</a>
                @elseif(Session::get('curr_role') == 'technical manager' && is_null($reconciliation->tm_sign) && !is_null($reconciliation->pm_sign)) 
                <button type="button" class="btn bg-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#reject-modal">Reject</button>
                <a href="{{ url('approve-reconciliation/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Approve</a>
                @elseif(Session::get('curr_role') == 'technical director' && is_null($reconciliation->td_sign) && !is_null($reconciliation->pm_sign) && !is_null($reconciliation->tm_sign)) 
                <button type="button" class="btn bg-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#reject-modal">Reject</button>
                <a href="{{ url('approve-reconciliation/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Approve</a>
                @elseif(Session::get('curr_role') == 'accountant' && is_null($reconciliation->cashier_sign) && !is_null($reconciliation->pm_sign)  && !is_null($reconciliation->td_sign) && !is_null($reconciliation->md_sign))
                    @if($reconciliation->amount_received > $total_spent)
                    <a href="{{ url('confirm-refund/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Confirm Refund Payment</a>
                    @else
                        @if(!is_null($reconciliation->pv_no))
                        <a href="{{ url('confirm-claim-payment/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Confirm Claim Payment</a>
                        @else
                        <form action="{{ url('reconciliations/edit')}}" method="POST">
                            @csrf
                            <input type="hidden" name="request_id" value="{{$reconciliation->id}}">
                            <button type="submit" class="btn btn-primary btn-sm mt-2"><i class="fa fa-pencil-square-o"></i> Update PV No.</button>
                        </form>
                        @endif
                    @endif
                @elseif(Session::get('curr_role') == 'team leader' && is_null($reconciliation->receiver_sign) && !is_null($reconciliation->pm_sign)  && !is_null($reconciliation->td_sign) && !is_null($reconciliation->tm_sign) && !is_null($reconciliation->cashier_sign))
                    @if($reconciliation->amount_received < $total_spent)
                    <a href="{{ url('confirm-claim-payment-received/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Confirm Payment Received</a>
                    @else
                    <a href="{{ url('confirm-claim-payment-received/'.encrypt($reconciliation->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Complete Reconciliation</a>
                    @endif
                @endif
            @endif
        </div>
    </div> <!-- .row end -->
    <div class="modal fade" id="reject-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Rejection Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="basic-form" method="POST" action="{{ url('reject-reconciliation') }}">
                        @csrf
                        <input type="hidden" name="request_id" value="{{$reconciliation->id}}">
                        <div class="col-md-12">
                            <label class="form-label">Reasons <span style="color: red;">*</span></label>
                            <textarea name="comment" class="form-control form-control-sm mb-1" placeholder="Enter Comments" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Rejection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-reconciliation");
            var filename = "<?php echo $reconciliation->request_id.'_'.$reconciliation->created_at; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                window.open(pdf.output('bloburl'), '_blank');
            });
        }
    </script>