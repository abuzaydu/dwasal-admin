@extends('layouts.hr')
    <script type="text/javascript">
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            var importform = document.getElementById('import-form');
            if (elem == 'show') {
                newform.style.display = 'none';
                newbtn.style.display = 'none';
                importform.style.display = 'block';
            }else{
                newform.style.display = 'block';
                newbtn.style.display = 'block';
                importform.style.display = 'none';
            }
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
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" >Add New Employees</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" id="new-btn" class="btn btn-primary" onclick="showHideForm('show')"><i class="fa fa-import"></i>Import From Excel</button>
                        </div>
                    </div>
                    <div class="p-4 border rounded" id="new-form">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('employees.store') }}">
                            @csrf
                            <div class="col-md-3">
                                <label for="emp_id" class="form-label">Employee ID</label>
                                <div class="input-group input-group-sm mb-3" >
                                    <input type="text" name="emp_id" class="form-control form-control-sm " placeholder="Employee ID" aria-describedby="basic-addon1">
                                    <a  onclick="AutoID('{{url('auto-id')}}')"class="input-group-text" id="basic-addon1" >Auto<i class="fa fa-cog"></i></a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="fname" class="form-label">First Name  <span style="color: red;">*</span></label>
                                <input type="text" name="fname" class="form-control form-control-sm mb-3" placeholder="First Name" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a First Name.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="mname" class="form-label">Middle Name</label>
                                <input type="text" name="mname" class="form-control form-control-sm mb-3" placeholder="Middle Name">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Middle Name.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="lname" class="form-label">Surname Name  <span style="color: red;">*</span></label>
                                <input type="text" name="lname" class="form-control form-control-sm mb-3" placeholder="Surname Name" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Surname Name.</div>
                            </div>
                            <div class="col-md-3">
                                <label  class="form-label">Gender <span style="color: red;">*</span></label>
                                <div class="d-flex">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="male" name="gender" required data-parsley-errors-container="#error-radio" id="flexRadioDefault3" />
                                        <label class="form-check-label" for="flexRadioDefault3">Male</label>
                                    </div>
                                    <div class="form-check mx-3">
                                        <input class="form-check-input" type="radio" name="gender" value="female" id="flexRadioDefault4" />
                                        <label class="form-check-label" for="flexRadioDefault4">Female</label>
                                    </div>
                                </div>
                                <p id="error-radio"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mobile Number <span style="color: red;">*</span></label>
                                <input type="tel" class="form-control form-control-sm mb-3" name="mobile" placeholder="Enter Mobile number" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Employee Mobile Number.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">NIN <span style="color: red;">*</span></label>
                                <input type="text" step="any" class="form-control form-control-sm mb-3" name="nin" placeholder="Enter National ID" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Employee NIN.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="validationCustom06" class="form-label">TIN</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="dynamic-mask" name="tin" maxlength="11" placeholder="Enter TIN" data-inputmask='"mask": "999-999-999"' data-mask required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a valid TIN</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Email Address <span style="color: red;">*</span></label>
                                <input type="email" class="form-control form-control-sm mb-3" id="validationCustom04" name="email" placeholder="Enter Email Address" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide Employee Email Address.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="marital_status" class="form-label">Marital Status <span style="color: red;">*</span></label>
                                <select name="marital_status" class="form-select form-select-sm mb-3"  required>
                                    <option value="">--- Select ---</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Devoiced">Devoiced</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Marital Status.</div>
                            </div>
                            <div class="col-md-3">
                                <label  for="type" class="form-label">Employee Type <span style="color: red;">*</span></label>
                                <select name="type" class="form-select form-select-sm mb-3" required>
                                    <option value="">--- Select ---</option>
                                    <option value="Fulltime">Fulltime</option>
                                    <option value="Intern">Intern</option>
                                    <option value="Volunteer">Volunteer</option>
                                    <option value="Field Student">Field Student</option>
                                    <option value="Day Worker">Day Worker</option>
                                    <option value="Contractor">Contractor</option>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Surname Name.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control form-control-sm mb-3"></textarea>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Employee Address.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Join Date <span style="color: red;">*</span></label>
                                <input type="text" name="start_date" class="form-control form-control-sm mb-3" id="start-date" placeholder="Join Date" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Employee Start Date.</div>
                                </div>
                            <div class="col-md-3">
                                <label class="form-label">Contract Ends</label>
                                <input type="text" name="end_date" class="form-control form-control-sm mb-3" id="end-date" placeholder="End Date">
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-md-3">
                                <label for="validationCustom05" class="form-label">Position <span style="color: red;">*</span></label>
                                <select class="form-select form-select-sm mb-3" name="position_id" required onchange="Allowance(this)">
                                    <option value="">--- Select ---</option>
                                    @foreach($positions as $position)
                                    <option value="{{$position->id}}">{{$position->name}}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select Roles to assign.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Mode</label>
                                <select name="is_paid_monthly" class="form-select form-select-sm mb-3" onchange="changeMode(this)">
                                    <option value="1">Paid Monthly</option>
                                    <option value="0">Paid Hourly</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="input-hourly" style="display: none;">
                                <label class="form-label">Basic Pay (Per Hour)</label>
                                <input type="number" class="form-control form-control-sm mb-3" name="basic_pay_hourly">
                            </div>
                            <div class="col-md-3" id="input-monthly">
                                <label class="form-label">Basic Pay (Per Month)</label>
                                <input type="number" class="form-control form-control-sm mb-3" name="basic_pay_monthly">
                            </div>
                            <div class="col-md-3" id="trans_allowance" style="display: none;">
                                <label class="form-label">Transport Allowance</label>
                                <input type="number" step="any"  class="form-control form-control-sm mb-3" name="trans_allowance" value="">
                            </div>
                            <div class="col-md-3" id="house_allowance" style="display: none;">
                                <label class="form-label">House Allowance</label>
                                <input type="number" step="any"  class="form-control form-control-sm mb-3" name="house_allowance" value="">
                            </div>
                            <div class="col-md-3" id="com_allowance" style="display: none;">
                                <label class="form-label">Communication Allowance</label>
                                <input type="number" step="any" class="form-control form-control-sm mb-3" name="com_allowance" value="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bank Name</label>
                                <input type="tel" class="form-control form-control-sm mb-3" name="bank_name" placeholder="Enter Bank name">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide Employee's Bank Name.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Number</label>
                                <input type="tel" class="form-control form-control-sm mb-3" name="account_number" placeholder="Enter Account number">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide Employee's  Bank Acccount number.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Name</label>
                                <input type="tel" class="form-control form-control-sm mb-3" name="account_name" placeholder="Enter Account name">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide Employee's Bank Account Name.</div>
                            </div> 
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a href="{{ url('employees')}}" class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <div class="p-4 border rounded" id="import-form" style="display: none;">
                        <form class="form row g-3" method="POST" action="{{url('import-employees')}}"  enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-6">
                                <h4>Download Sample Excel file</h4>
                                <a href="{{url('employee-sample')}}" class="btn btn-primary btn-sm"><i class="bx bx-download"></i> Download</a>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5 >Choose Update Employee excel file</h5>
                                    <div class="card mx-auto">
                                        <div class="card-body">
                                            <input id="exampleInputFile" class="form-control form-control-sm mb-1 form-control form-control-sm mb-1-sm mb-1" type="file" name="employee_file" accept=".xlsx,.xls" required>
                                        </div>
                                    </div>
                                    @if ($errors->has('file'))
                                    <span class="help-block" style="color: red;">
                                        <strong>{{ $errors->first('file') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn btn-success btn-sm"><i class="bx bx-upload"></i> Upload</button>
                                    <a href="{{ url('employees') }}" type="button" class="btn btn-warning btn-sm mr-1">
                                        <i class="bx bx-x"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function()
        {
            var start = document.querySelector('[name="start_date"]');
            var end = document.querySelector('[name="end_date"]');

             start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });
             
            end.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });
        });
    </script>
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
@endsection