@extends('layouts.hr')
    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: "Sure you want to delete ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "delete",
                cancelButtonText: "cancel"
            }).then((result) => {
                if (result.value) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire(
                        "Deleted",
                        'success'
                    )
                }else{
                    Swal.fire(
                        "Cancelled",
                        'success'
                    )
                }
            })
        }

        function changeMode(elem) {
            var hourly = document.getElementById('input-hourly');
            var monthly = document.getElementById('input-monthly');
            if (elem.value == '1') {
                monthly.style.display = 'block';
                hourly.style.display = 'none';
            }else{
                hourly.style.display = 'block';
                monthly.style.display = 'none';
            }
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
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card p-2">
                <div class="card-body p-0">
                    <div class="d-lg-flex align-items-center mb-1 gap-0">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="list-title">Employee List</h6>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="fa fa-plus-square"></i>Add Employee</a>
                        </div>
                    </div>

                    <div id="item-list">

                        {{-- Print Selected Button --}}
                        <div class="mb-3">
                            <button id="printSelectedBtn" class="btn btn-primary" disabled>
                                <i class="fa fa-print me-1"></i> Print Selected (<span id="selectedCount">0</span>)
                            </button>
                        </div>

                        <table id="employees" class="table table-striped table-bordered items" style="width:100%; font-size 14px; white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;"><input type="checkbox" id="selectAll" title="Select All"></th>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">Emp ID</th>
                                    <th style="text-align: center;">Photo</th>
                                    <th style="text-align: center;">Full Name</th>
                                    <th style="text-align: center;">Type</th>
                                    <th style="text-align: center;">Salary</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $key => $employee)
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox"
                                               class="employee-checkbox"
                                               value="{{ encrypt($employee->id) }}">
                                    </td>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{$employee->emp_id}}</td>
                                    <td class="width45" style="text-align: center;">
                                        <?php $passport = App\Models\EmployeeDoc::where('employee_id' , $employee->id)->where('type', 'Passport')->first(); ?>
                                        @if(!is_null($passport))
                                        <img src="{{asset('storage/'.$passport->link)}}" class="rounded-circle width35" height="30px" width="30px" alt="">
                                        @endif
                                    </td>
                                    <td>{{ $employee->fname }} {{$employee->mname}} {{ $employee->lname }}</td>
                                    <td>{{$employee->type}}</td>
                                    <td>@if(!$employee->is_paid_monthly){{ number_format($employee->basic_pay_hourly, 2, '.', ',') }} (Per Hour)@else {{ number_format($employee->basic_pay_monthly, 2, '.', ',') }} (Per Month)@endif</td>
                                    <td>
                                        <a href="{{ route('employees.show', encrypt($employee->id)) }}" class="text-secondary"><i class='fa fa-file-text-o mr-1'></i> View</a> | 
                                        <a href="{{ route('employees.edit', encrypt($employee->id)) }}"><i class='fa fa-pencil mr-1'></i> Edit</a> | 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('employees.destroy', encrypt($employee->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger"><i class='fa fa-trash mr-1'></i> Delete</a>
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
@endsection

@section('page-scripts')
    <link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
    <script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
            
        async function AutoID(url) {
            let response = await fetch(url);
            let data = await response.json();
            document.getElementsByName('emp_id')[0].value = data;
        }

        function Allowance(elem){
            var positions = @php echo json_encode($positions) @endphp;
            let position = positions.find(o => o.id == elem.value);
            if(elem.value !== ''){
                document.getElementById('trans_allowance').style.display = 'block';
                document.getElementById('house_allowance').style.display = 'block';        
                document.getElementById('com_allowance').style.display = 'block';

                document.getElementsByName('trans_allowance')[0].value =  position.trans_allowance;
                document.getElementsByName('house_allowance')[0].value =  position.house_allowance;        
                document.getElementsByName('com_allowance')[0].value =  position.com_allowance;
                document.getElementsByName('basic_pay_monthly')[0].value = position.basic_pay_monthly;
            }else{
                document.getElementsByName('trans_allowance')[0].value = 0;
                document.getElementsByName('house_allowance')[0].value = 0;
                document.getElementsByName('com_allowance')[0].value = 0;
                document.getElementsByName('basic_pay_monthly')[0].value = 0;

                document.getElementById('trans_allowance').style.display = 'none';
                document.getElementById('house_allowance').style.display = 'none';        
                document.getElementById('com_allowance').style.display = 'none';
            }
        }

    </script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function() {
            var start = document.querySelector('[name="start_date"]');
            var end = document.querySelector('[name="end_date"]');

            start.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
            });
             
            end.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
            });
        });
    </script>

    <script>
    $(function () {

        // 1. Init DataTable
        const table = $('#employees').DataTable({
            destroy: true,
            columnDefs: [
                { orderable: false, searchable: false, targets: [0, 7] } // disable sort on checkbox & actions cols
            ]
        });

        //  2. Refs 
        const $selectAll    = $('#selectAll');
        const printBtn      = document.getElementById('printSelectedBtn');
        const selectedCount = document.getElementById('selectedCount');

        //  3. Helper: get ALL checked checkboxes across ALL pages
        function getChecked() {
            return $(table.rows().nodes()).find('.employee-checkbox:checked');
        }

        //  4. Helper: sync UI 
        function syncUI() {
            const totalOnPage   = $(table.rows({ page: 'current' }).nodes()).find('.employee-checkbox').length;
            const checkedOnPage = $(table.rows({ page: 'current' }).nodes()).find('.employee-checkbox:checked').length;
            const totalChecked  = getChecked().length;

            if (checkedOnPage === 0) {
                $selectAll.prop('checked', false).prop('indeterminate', false);
            } else if (checkedOnPage === totalOnPage) {
                $selectAll.prop('checked', true).prop('indeterminate', false);
            } else {
                $selectAll.prop('checked', false).prop('indeterminate', true);
            }

            selectedCount.textContent = totalChecked;
            printBtn.disabled = totalChecked === 0;
        }

        //  5. Select All (current page only) 
        $selectAll.on('change', function () {
            $(table.rows({ page: 'current' }).nodes())
                .find('.employee-checkbox')
                .prop('checked', this.checked);
            syncUI();
        });

        //  6. Individual checkbox 
        $('#employees tbody').on('change', '.employee-checkbox', function () {
            syncUI();
        });

        //  7. On DataTable page change / search / sort 
        table.on('draw', function () {
            $selectAll.prop('checked', false).prop('indeterminate', false);
            syncUI();
        });

        //  8. Print button 
        printBtn.addEventListener('click', function () {
            const ids = getChecked().map(function () {
                return 'ids[]=' + encodeURIComponent(this.value);
            }).get();

            if (ids.length === 0) return;

            const url = "{{ route('employees.print.selected-id-card') }}?" + ids.join('&');
            window.open(url, '_blank');
        });

    });
    </script>
@endsection