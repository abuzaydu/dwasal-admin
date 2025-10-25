@extends('layouts.pr')
    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
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

        function confirmDeletePayroll(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = "payroll-delete/"+id;
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-4 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-5">
            </div>
            <div class="col-md-3 col-sm-12 text-md-end">
                <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus-square"></i>Create New Payroll</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <form class="row g-3 report-form" action="{{ url('payroll-list') }}" method="POST">
                @csrf
                <div class="col-md-5">
                    <select class="form-select form-select-sm mb-1" name="employee_id" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($employees as $emp)
                        @if(!is_null($employee) && $emp->id == $employee->id)
                        <option value="{{$emp->id}}" selected>{{$emp->fname}} {{$emp->lname}}</option>
                        @else
                        <option value="{{$emp->id}}">{{$emp->fname}} {{$emp->lname}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm mb-1 col-md-6" name="month" onchange="this.form.submit()">
                        @foreach($data as $d)
                        @if($d['month'].' '.$d['year'] == $curmonth)
                        <option selected value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                        @else
                        <option value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body pt-0">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#emp-payrolls">Employees Payroll List</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#total-monthly">Monthly Total Payroll</a></li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade" id="total-monthly" role="tabpanel">
                            <table  id="total-payrolls" class="table table-striped table-bordered" style="width:100%; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Month</th>
                                        <th>Basic Salaries</th>
                                        <th>House Allowance</th>
                                        <th>Transport Allowance</th>
                                        <th>Com Allowance</th>
                                        <th>Bonuses</th>
                                        <th>PAYE</th>
                                        <th>SSF</th>
                                        <th>HIS</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mpayrolls as $key => $payroll)
                                    <tr>
                                        <th scope="row">{{$key+1}}</th>
                                        <td><a href="{{ url('view-payroll/'. encrypt($payroll->id)) }}">{{ $payroll->month }}</a></td>
                                        <td>{{ number_format($payroll->basic_salaries, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->house_allowance, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->trans_allowance, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->com_allowance, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->bonuses, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->paye, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->ssf, 2, '.', ',') }}</td>
                                        <td>{{ number_format($payroll->mif, 2, '.', ',') }}</td>
                                        <td>
                                            <a href="{{ url('view-payroll/'. encrypt($payroll->id)) }}" class="text-secondary"><i class='bx bx-detail mr-1'></i>View</a> | 
                                            <a href="{{ url('payroll-edit/'.encrypt($payroll->id)) }}"><i class='bx bx-pencil mr-1'></i>Edit</a> | 
                                            <a href="javascript:;" class="text-danger" onclick="return confirmDeletePayroll('<?php echo encrypt($payroll->id); ?>')" class="text-danger"><i class='bx bx-trash mr-1'></i>Delete</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade active show" id="emp-payrolls" role="tabpanel">
                            <table  id="payrolls" class="table table-striped table-bordered display nowrap" style="width:100%; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Payroll ID</th>
                                        <th>Employee Name</th>
                                        <th>Payroll Created.</th>
                                        <th>Payroll Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payrolls as $key => $payroll)
                                    <tr>
                                        <th scope="row">{{$key+1}}</th>
                                        <td><a href="{{ route('payrolls.show', encrypt($payroll->id)) }}">{{ $payroll->payid }}</a></td>
                                        <td>{{ $payroll->fname }} {{ $payroll->lname }}</td>
                                        <td>{{ $payroll->created_at }}</td>
                                        <td>{{ $payroll->updated_at }}</td>
                                        <td>
                                            <a href="{{ route('payrolls.show', encrypt($payroll->id)) }}" class="text-secondary"><i class='bx bx-detail mr-1'></i>View</a> | 
                                            <a href="{{ route('payrolls.edit', encrypt($payroll->id)) }}"><i class='bx bx-pencil mr-1'></i>Edit</a> | 
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('payrolls.destroy', encrypt($payroll->id)) }}" style="display: inline;">
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger"><i class='bx bx-trash mr-1'></i>Delete</a>
                                            </form>
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
@endsection