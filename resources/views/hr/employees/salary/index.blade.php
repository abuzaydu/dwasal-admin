@extends('layouts.hr')
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
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-3 col-sm-12 text-md-end">
            
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form class="row g-3 report-form" action="{{ url('f-hr-salaries') }}" method="POST">
                @csrf
                <div class="col-md-9">
                    
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
            </form>
            <div class="card">
                <div class="card-body">
                    <table id="pay_salary" class="table table-striped align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Action</th>
                                <th>Name</th>
                                <th>Employee ID</th>
                                <th>Gross Salary</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Phone</th>
                                <th>Join Date</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $key => $payroll)
                            <tr>
                                <td class="width45">
                                    {{$key+1}}
                                </td>
                                <td>
                                    <a href="{{ route('hr-salaries.show', encrypt($payroll['id']))}}" class="btn btn-sm btn-success" title="Preview salary slip"><i class="fa fa-file-pdf-o me-2"></i>Slip</a>
                                </td>
                                <td>
                                    <h6 class="mb-0">{{ $payroll['name'] }}</h6>
                                    <span>{{ $payroll['email'] }}</span>
                                </td>
                                <td><span>{{ $payroll['emp_id'] }}</span></td>
                                <td>{{ number_format($payroll['gross_income'], 2, '.', ',') }}</td>
                                <td>{{ number_format($payroll['deduction'], 2, '.', ',') }}</td>
                                <td>{{ number_format($payroll['net_pay'], 2, '.', ',') }}</td>
                                <td><span>{{ $payroll['mobile'] }}</span></td>
                                <td>{{ date('d M, Y', strtotime($payroll['join_date'])) }}</td>
                                <td>{{ $payroll['position'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection