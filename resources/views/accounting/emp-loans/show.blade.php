@extends('layouts.acc')

@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('emp-loans')}}">Employee Loans</a> </li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body p-2">
                    <div class="p-4 border rounded">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <td>Employee ID</td>
                                    <td>{{$emploan->emp_id}}</td>
                                </tr>
                                <tr>
                                    <td>Full Name</td>
                                    <td>{{$emploan->fname}}{{$emploan->lname}}</td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>{{ date('d F Y', strtotime($emploan->loan_date))}}</td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td>{{ number_format($emploan->amount, 1, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>Amount Paid</td>
                                    <td>{{ number_format($emploan->amount_paid, 1, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td><td>{{$emploan->status}}</td>
                                </tr>
                                <tr>
                                    <td>Approved By</td><td>{{$emploan->approved_by}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        @if($emploan->is_approved)
                        @if($emploan->status != 'Issued')
                        <a href="{{ url('issue-loan/'.encrypt($emploan->id)) }}" class="btn btn-secondary btn-sm"><i class="fa fa-money"></i> Issue Loan</a>
                        @endif
                        @else
                        <a href="{{ url('approve-loan/'.encrypt($emploan->id)) }}" class="btn btn-info btn-sm"><i class="fa fa-check"></i> Approve Loan</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body p-2">
                    <h6 class="mb-0">Loan Returns</h6>
                    <div class="p-4 border rounded">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>MDate</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emploan_returns as $key => $return)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ date('d M, Y', strtotime($return->return_date)) }}</td>
                                    <td>{{$return->amount}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection