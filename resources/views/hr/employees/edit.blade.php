@extends('layouts.hr')
    <script type="text/javascript">
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
                <h2 class="m-0 fs-5"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> Dashboard</h2>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-1" id="basic-form" novalidate method="POST" action="{{ route('employees.update' , encrypt($employee->id)) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="fname" class="form-label">First Name  <span style="color: red;">*</span></label>
                                    <input type="text" name="fname" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="First Name" value="{{$employee->fname}}" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a First Name.</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="mname" class="form-label">Middle Name</label>
                                  <input type="text" value="{{$employee->mname}}" name="mname" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Middle Name">
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide a Middle Name.</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="lname" class="form-label">Surname Name  <span style="color: red;">*</span></label>
                                  <input type="text" value="{{$employee->lname}}" name="lname" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Surname Name" required>
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide a Surname Name.</div>
                                </div>
                                <div class="col-md-4">
                                    <label  class="form-label">Gender <span style="color: red;">*</span></label>
                                    <div class="d-flex">
                                        @if($employee->gender == "female")
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" value="male" name="gender" required data-parsley-errors-container="#error-radio" id="flexRadioDefault3" />
                                        <label class="form-check-label" for="flexRadioDefault3">Male</label>
                                      </div>
                                      <div class="form-check mx-3">
                                        <input class="form-check-input" type="radio" name="gender" value="female" id="flexRadioDefault4" checked />
                                        <label class="form-check-label" for="flexRadioDefault4">Female</label>
                                      </div>
                                      @else
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" value="male" name="gender" required data-parsley-errors-container="#error-radio" id="flexRadioDefault3" checked />
                                        <label class="form-check-label" for="flexRadioDefault3">Male</label>
                                      </div>
                                      <div class="form-check mx-3">
                                        <input class="form-check-input" type="radio" name="gender" value="female" id="flexRadioDefault4" />
                                        <label class="form-check-label" for="flexRadioDefault4">Female</label>
                                      </div>
                                      @endif
                                    </div>
                                    <p id="error-radio"></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom03" class="form-label">Mobile Number <span style="color: red;">*</span></label>
                                    <input type="tel" class="form-control form-control-sm mb-3" id="validationCustom03" name="mobile" value="{{$employee->mobile}}" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Employee Mobile Number.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom03" class="form-label">NIN <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="validationCustom03" name="nin" value="{{$employee->nin}}" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Employee NIN.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom06" class="form-label">TIN</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="dynamic-mask" name="tin" maxlength="11" placeholder="Enter TIN" data-inputmask='"mask": "999-999-999"' data-mask  value="{{$employee->tin}}" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a valid TIN</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom04" class="form-label">Email Address <span style="color: red;">*</span></label>
                                    <input type="email" class="form-control form-control-sm mb-3" id="validationCustom04" name="email" value="{{$employee->email}}" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide Employee Email Address.</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="marital_status" class="form-label">Marital Status <span style="color: red;">*</span></label>
                                  <select name="marital_status" class="form-select form-select-sm mb-3"  required>
                                        <option value="{{$employee->marital_status}}" selected>{{$employee->marital_status}}</option>
                                      <option value="Single">Single</option>
                                      <option value="Married">Married</option>
                                      <option value="Devoiced">Devoiced</option>
                                      <option value="Widowed">Widowed</option>
                                      <option value="Separated">Separated</option>
                                  </select>
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide a Marital Status.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Health Status</label>
                                    <div class="form-check form-switch">
                                        @if($employee->have_md_condition)
                                        <input class="form-check-input" name="have_md_condition" value="true" type="checkbox" id="flexSwitchCheckDefault" checked />
                                        <label class="form-check-label" for="flexSwitchCheckDefault">Have Medical Condition</label>
                                        @else
                                        <input class="form-check-input" name="have_md_condition" value="true" type="checkbox" id="flexSwitchCheckDefault" />
                                        <label class="form-check-label" for="flexSwitchCheckDefault">Have Medical Condition</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                  <label  for="type" class="form-label">Employee Type <span style="color: red;">*</span></label>
                                  <select name="type" class="form-select form-select-sm mb-3" >
                                      <option selected>{{$employee->type}}</option>
                                      <option>Fulltime</option>
                                      <option>Intern</option>
                                      <option>Volunteer</option>
                                      <option>Field Student</option>
                                      <option>Day Worker</option>
                                      <option>Contractor</option>
                                  </select>
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide a Surname Name.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom03" class="form-label">Residence Address</label>
                                    <textarea  value="{{$employee->address}}"name="address" class="form-control form-control-sm mb-3"></textarea>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Employee Address.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Join Date <span style="color: red;">*</span></label>
                                    <input type="text" name="start_date" class="form-control form-control-sm mb-3" id="start-date" placeholder="Join Date" value="{{$employee->start_date}}" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Employee Start Date.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contract Ends</label>
                                    <input type="text" id="end-date" name="end_date" class="form-control form-control-sm mb-3"  placeholder="End Date" value="{{$employee->end_date}}">
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="emp_id" class="form-label">Employee ID</label>
                                    <div class="input-group input-group-sm mb-3" >
                                        <input type="text" name="emp_id" class="form-control form-control-sm " placeholder="Employee ID" aria-describedby="basic-addon1" value="{{$employee->emp_id}}">
                                        <!-- <button class="input-group-text" id="basic-addon1" >Auto<i class="fa fa-cog"></i></button> -->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom05" class="form-label">Position <span style="color: red;">*</span></label>
                                    <select class="form-select form-select-sm mb-3" name="position_id" required>
                                        <option value="">-Select Position-</option>
                                        @foreach($positions as $value)
                                        @if(!is_null($position) && $position->id == $value->id)
                                        <option value="{{$value->id}}" selected>{{$value->name}}</option>
                                        @else
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please select Roles to assign.</div>
                                </div>
                                @if(!$employee->is_paid_monthly)
                                <div class="col-md-4">
                                    <label for="validationCustom02" class="form-label">Basic Pay (Per Hour)</label>
                                    <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="basic_pay_hourly" value="{{$employee->basic_pay_hourly}}">
                                </div>
                                @else
                                <div class="col-md-4" id="input-monthly">
                                    <label for="validationCustom02" class="form-label">Basic Pay (Per Month)</label>
                                    <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="basic_pay_monthly" value="{{$employee->basic_pay_monthly}}">
                                </div>
                                @endif
                                <div class="col-md-4" id="trans_allowance">
                                    <label for="validationCustom02" class="form-label">Transport Allowance</label>
                                    <input type="number" step="any"  class="form-control form-control-sm mb-3" id="validationCustom02" name="trans_allowance" value="{{$employee->trans_allowance}}">
                                </div>
                                <div class="col-md-4" id="house_allowance">
                                    <label for="validationCustom02" class="form-label">House Allowance</label>
                                    <input type="number" step="any"  class="form-control form-control-sm mb-3" id="validationCustom02" name="house_allowance" value="{{$employee->house_allowance}}">
                                </div>
                                <div class="col-md-4" id="com_allowance">
                                    <label for="validationCustom02" class="form-label">Communication Allowance</label>
                                    <input type="number" step="any" class="form-control form-control-sm mb-3" id="validationCustom02" name="com_allowance" value="{{$employee->com_allowance}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom03" class="form-label">Bank Name</label>
                                    <input type="tel" class="form-control form-control-sm mb-3" id="validationCustom03" name="bank_name" value="{{$employee->bank_name}}">
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide Employee's Bank Name.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom03" class="form-label">Account Number</label>
                                    <input type="tel" class="form-control form-control-sm mb-3" id="validationCustom03" name="account_number" value="{{$employee->account_number}}">
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide Employee's  Bank Acccount number.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom03" class="form-label">Account Name</label>
                                    <input type="tel" class="form-control form-control-sm mb-3" id="validationCustom03" name="account_name" value="{{$employee->account_name}}">
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide Employee's Bank Account Name.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Has Social Security Fund?</label>
                                    <select class="form-select form-select-sm mb-1" name="is_reg_ssf">
                                        @if($employee->is_reg_ssf)
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                        @else
                                        <option value="0">NO</option>
                                        <option value="1">YES</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Has Medical Insurance?</label>
                                    <select class="form-select form-select-sm mb-1" name="is_reg_mif">
                                        @if($employee->is_reg_mif)
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                        @else
                                        <option value="0">NO</option>
                                        <option value="1">YES</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Is Registered WCF?</label>
                                    <select class="form-select form-select-sm mb-1" name="is_reg_wcf">
                                        @if($employee->is_reg_wcf)
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                        @else
                                        <option value="0">NO</option>
                                        <option value="1">YES</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Has HESLB Loan Payment?</label>
                                    <select class="form-select form-select-sm mb-1" name="allow_deduct_heslb">
                                        @if($employee->allow_deduct_heslb)
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                        @else
                                        <option value="0">NO</option>
                                        <option value="1">YES</option>
                                        @endif
                                    </select>
                                </div>
                            </div>   
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a href="{{ url('employees')}}" class="btn btn-warning px-4 radius-30">Cancel</a>
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