@extends('layouts.hr')
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <h2 class="m-0 fs-5"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> Dashboard</h2>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('employees') }}">Employees</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border-bottom p-2">
                            <div class="row g-3">
                                <div class="col-md-1">
                                    @if($user_photo)
                                    <img src="{{asset('storage/'.$user_photo)}}" alt="" class="rounded-2" width="60px" height="70px">
                                    @else
                                    <div width="80px" height="90px" align="center" class="pt-2" class="text-primary">
                                        No <br>
                                        Passport
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-3 pt-3">
                                    <h6 class="mb-1 fw-light">{{$employee->fname}} {{$employee->mname}}  {{$employee->lname}} <a href="{{ route('employees.edit' , encrypt($employee->id))}}" class="fa fa-pencil-square-o fs-6 ms-2" title="Edit Profile"></a></h6>
                                    <p>{{$employee->email}}</p>
                                    <span class="text-muted"></span>
                                </div>
                                <div class="col-md-7 row pt-3">
                                    <div class="col-3 card py-2 px-2 mt-2">
                                        <small class="text-muted">Postion</small>
                                        <div class="fs-6">
                                            @if(!is_null($position))
                                                {{$position->name}}
                                            @else
                                                Not Assigned Position
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-3 card py-2 px-2 mt-2">
                                        <small class="text-muted">Montyl Pay</small>
                                        <div class="fs-6">{{$employee->basic_pay_monthly}}</div>
                                    </div>
                                    <div class="col-3 card py-2 px-2 mt-2">
                                        <small class="text-muted">Type</small>
                                        <div class="fs-6">{{$employee->type}}</div>
                                    </div>
                                    <div class="col-3 card py-2 px-2 mt-2">
                                        <small class="text-muted">Start Date</small>
                                        <div class="fs-6">{{ date('d, M Y', strtotime($employee->start_date)) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="nav nav-tabs tab-card border-bottom-0 pt-2 fs-6 justify-content-center justify-content-md-start" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#profile_post" role="tab"><span>Overview</span></a></li>
                            <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#profile_groups" role="tab"><i class="fa fa-address-card-o"></i><span class="d-none d-sm-inline-block ms-2">Groups</span></a></li> -->
                            <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#profile_project" role="tab"><i class="fa fa-list-alt"></i><span class="d-none d-sm-inline-block ms-2">Projects</span></a></li> -->
                            <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#profile_campaigns" role="tab" id="tab_profile_campaigns"><i class="fa fa-area-chart"></i><span class="d-none d-md-inline-block ms-2">Campaigns</span></a></li> -->
                            <!-- <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#profile_activity" role="tab"><i class="fa fa-font"></i><span class="d-none d-md-inline-block ms-2">Activities</span></a></li> -->
                        </ul>
                    </div>

                    <div class="tab-content mt-3">
                        <!-- Tab: Overview -->
                        <div class="tab-pane fade show active" id="profile_post" role="tabpanel">
                            <div class="row-title mb-1">
                                <h5 class="px-4">Profile Overview</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Personal Information</h6>
                                            <p class="card-text text-muted"></p>
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">First Name:</span></td>
                                                        <td>{{$employee->fname}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">Middle Name:</span></td>
                                                        <td>{{$employee->mname}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w92 d-inline-block">Surname Name:</span></td>
                                                        <td>{{$employee->lname}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">E-mail:</span></td>
                                                        <td>{{$employee->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">Phone:</span></td>
                                                        <td>{{$employee->mobile}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">Location:</span></td>
                                                        <td>{{$employee->address}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">Gender:</span></td>
                                                        <td>{{$employee->gender}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="text-muted me-2 w90 d-inline-block">Marital Status:</span></td>
                                                        <td>{{$employee->marital_status}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Leave Roster</h6>
                                            <p class="text-muted">Days Remain:</p>
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>From - To</th>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($leave_rosters as $leave_roster)
                                                    <tr>
                                                        <td><small class="text-uppercase">{{$leave_roster->type}}</small></td>
                                                        <td><small class="text-muted">{{Carbon\Carbon::parse($leave_roster->start_date)->format('d-m-Y')}}</small> <b>To</b> <small class="text-muted">{{Carbon\Carbon::parse($leave_roster->end_date)->format('d-m-Y')}}</small>
                                                        <b>{{Carbon\Carbon::parse($leave_roster->start_date)->diffInDays(Carbon\Carbon::parse($leave_roster->end_date))}}day</b>
                                                        </td>
                                                        <td>
                                                        @if($leave_roster->approved_by)
                                                        <i class="fa fa-check" style="color: green;"></i>
                                                        @else
                                                        <i class="fa fa-times" style="color: red;"></i>
                                                        @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-xl-8 col-lg-8 col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Next Of Kins<button id="new-med" class="mb-3 btn btn-sm btn-primary float-end" onclick="showHideForm('show' , 'add-nextkin-card');"><i class="fa fa-plus" ></i>Add</button></h6>
                                            <div class="p-3 border rounded " style="display : none;"  id="add-nextkin-card">
                                                <h5>Add Next of Kin</h5>
                                                <form id="nextkin-form" class="row" method="POST" action="{{route('next-of-kins.store')}}">
                                                    @csrf
                                                    <input type="text" value="{{$employee->id}}" name="id" hidden>
                                                    <div class="col-md-4">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" name="f_name" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Middle Name</label>
                                                        <input type="text" name="m_name" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Surname Name</label>
                                                        <input type="text" name="l_name" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Relationship</label>
                                                        <input type="text" class="form-control form-control-sm" name="relationship" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Occupation</label>
                                                        <input type="text" class="form-control form-control-sm" name="occupation" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" class="form-control form-control-sm" name="address" >
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Resident</label>
                                                        <input type="text" class="form-control form-control-sm" name="residence" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Mobile 1</label>
                                                        <input type="text" class="form-control form-control-sm" name="f_phone" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Mobile 2</label>
                                                        <input type="text" class="form-control form-control-sm" name="s_phone" >
                                                    </div>
                                                    <div class="col-md-4 float-end">
                                                        <button class="px-3-3 btn  btn-warning">Save</button> 
                                                        <a onclick="showHideForm('hide' , 'add-nextkin-card');" class="btn  btn-secondary">Cancel</a> 
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="p-3 border rounded " style="display: none;" id="edit-nextkin-card">
                                                <h5>Edit Next of Kin</h5>
                                                <form id="nextkin-form" class="row" method="POST" action="{{route('next-of-kins.update' , encrypt($employee->id))}}">
                                                    @method('PUT')
                                                    @csrf
                                                    <input type="text" id="nextkin-id" name="id" hidden>
                                                    <div class="col-md-4">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" name="e_f_name" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Middle Name</label>
                                                        <input type="text" name="e_m_name" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Surname Name</label>
                                                        <input type="text" name="e_l_name" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Relationship</label>
                                                        <input type="text" class="form-control form-control-sm" name="e_relationship" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Occupation</label>
                                                        <input type="text" class="form-control form-control-sm" name="e_occupation" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" class="form-control form-control-sm" name="e_address" >
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Resident</label>
                                                        <input type="text" class="form-control form-control-sm" name="e_residence" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Mobile 1</label>
                                                        <input type="text" class="form-control form-control-sm" name="e_f_phone" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Mobile 2</label>
                                                        <input type="text" class="form-control form-control-sm" name="e_s_phone" >
                                                    </div>
                                                    <div class="col-md-4 float-end">
                                                        <button class="px-3-3 btn  btn-primary">Update</button> 
                                                        <a onclick="showHideForm('hide' , 'edit-nextkin-card');" class="btn  btn-secondary">Cancel</a> 
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Relationship</th>
                                                    <th>Mobile</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($next_of_kins as $key => $next_of_kin)
                                                    <tr>
                                                        <td>{{$next_of_kin->f_name}} {{$next_of_kin->l_name}}</td>
                                                        <td>{{$next_of_kin->relationship}}</td>
                                                        <td>{{$next_of_kin->f_phone}}, {{$next_of_kin->s_phone}} </td>
                                                        <td><a href="{{route('next-of-kins.show' , encrypt($next_of_kin->id))}}"><i style="color: blue ;"  class="fa fa-eye"></i></a> |
                                                        <a onclick="EditKins({{$next_of_kin}})"><i style="color: black ;"  class="fa fa-pencil"></i></a> |
                                                        <form id="delete-nextkin-form-{{$key}}" method="POST" action="{{route('next-of-kins.destroy' , encrypt($next_of_kin->id))}}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a onclick="confirmDeleteNextKin({{$key}})"><i style="color:red ;" class="fa fa-trash"></i></a>
                                                        </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                        </div> <!-- .Card End -->
                                        <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Medical Information <button id="new-med" class="mb-3 btn btn-sm btn-primary float-end" onclick="showHideForm('show' , 'add-med-card')" ><i class="fa fa-plus" ></i>Add</button></h6>
                                            <div class="p-2 border rounded mb-3" style="display:none;" id="add-med-card">
                                                <form id="med-info" method="POST" action="{{route('medical-infos.store')}}" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="text" value="{{$employee->id}}" name="id" hidden>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">Conditions Name</label>
                                                            <input type="text" name="conditions_name" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select form-select-sm" name="status">
                                                                <option value="">--- Select ---</option>
                                                                <option>Healthy</option>
                                                                <option>Critical</option>
                                                                <option>Avarage</option>
                                                                <option>Permanet</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row pt-3 mb-2">
                                                        <div class="col">
                                                            <label class="form-label">Medical Form</label>
                                                            <input type="file" name="attachment" >
                                                        </div>
                                                        <div class="col float-end">
                                                        <button class="px-3-3 btn  btn-warning">Save</button> 
                                                            <a onclick="showHideForm('hide' , 'add-med-card')" class="btn  btn-secondary">Cancel</a> 
                                                        </div>
                                                            
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="p-2 border rounded mb-3" style="display:none;" id="edit-med-card">
                                                <form id="edit-med-info" method="POST" action="{{route('medical-infos.update', encrypt($employee->id))}}" enctype="multipart/form-data">
                                                    @method('PUT')
                                                    @csrf
                                                    <input type="text" id="med-id"  name="id" hidden>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">Conditions Name</label>
                                                            <input type="text" name="e_conditions_name" class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select form-select-sm" name="e_status">
                                                                <option>Healthy</option>
                                                                <option>Critical</option>
                                                                <option>Avarage</option>
                                                                <option>Permanet</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row pt-3 mb-2">
                                                        <div class="col">
                                                            <label class="form-label">Medical Form</label>
                                                            <input type="file" name="e_attachment" >
                                                        </div>
                                                        <div class="col float-end">
                                                        <button class="px-3-3 btn  btn-warning">Save</button> 
                                                            <a onclick="showHideForm('hide' , 'edit-med-card')" class="btn  btn-secondary">Cancel</a> 
                                                        </div>
                                                            
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Condition Name</th>
                                                    <th>Status</th>
                                                    <th>Attachement</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($medical_infos as $key => $medical_info)
                                                    <tr>
                                                        <td>{{$medical_info->conditions_name}}</td>
                                                        <td>{{$medical_info->status}}</td>
                                                        <td><a target="_blank" href="{{asset('storage/'.$medical_info->attachment)}}"><i class="fa fa-eye"></i> Preview</a> | 
                                                        <a onclick="alert('downlod')"><i style="color : lightblue;" class="fa fa-download"></i></a> |
                                                        <a onclick="EditMedicalInfo({{$medical_info}})"><i style="color: black ;"  class="fa fa-pencil"></i></a>|
                                                        <form id="delete-med-form-{{$key}}" method="POST" action="{{route('medical-infos.destroy' , encrypt($medical_info->id))}}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a onclick="confirmDeleteMed({{$key}})"><i style="color:red ;" class="fa fa-trash"></i></a>
                                                        </form>
                                                        
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                        </div> <!-- .Card End -->
                                        <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Academic Information <button onclick="showHideForm('show' , 'add-academic-card')" class="mb-3 btn btn-sm btn-primary float-end"><i class="fa fa-plus" ></i> New</button></h6>
                                                
                                            <div class="p-2 border rounded mb-3" id="add-academic-card" style="display: none;">
                                                <form id="academic-info" method="POST" action="{{route('academic-infos.store')}}"  enctype="multipart/form-data">
                                                    @csrf
                                                        <input type="text" value="{{$employee->id}}" name="id" hidden>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">Level</label>
                                                            <select class="form-select form-select-sm" name="level">
                                                                <option value="">--- Select ---</option>
                                                                <option>Primary</option>
                                                                <option>O-level</option>
                                                                <option>A-level</option>
                                                                <option>Certificate</option>
                                                                <option>Diploma</option>
                                                                <option>Bachelor Degree</option> 
                                                                <option>Masters Degree</option>
                                                                <option>Phd</option>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Institution/School</label>
                                                            <input type="text" name="institution" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Title/Subject</label>
                                                            <input type="text" name="title" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                    <div class="row pt-3 mb-2">
                                                        <div class="col">
                                                            <label class="form-label">Certificate Attachement</label>
                                                            <input type="file" name="certificate_link" >
                                                        </div>
                                                        <div class="col float-end">
                                                        <button type="submit" class="px-3-3 btn  btn-warning">Save</button> 
                                                            <a onclick="showHideForm('hide' , 'add-academic-card')" class="btn  btn-secondary">Cancel</a> 
                                                        </div>
                                                            
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="p-2 border rounded mb-3" id="edit-academic-card" style="display: none;">
                                                <form id="edit-academic-info" method="POST" action="{{route('academic-infos.update' , encrypt($employee->id))}}"  enctype="multipart/form-data">
                                                    @method('PUT')
                                                    @csrf
                                                        <input type="text" id="academic-id" name="id" hidden>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">Level</label>
                                                            <select class="form-select form-select-sm" name="e_level">
                                                                <option>Primary</option>
                                                                <option>O-level</option>
                                                                <option>A-level</option>
                                                                <option>Certificate</option>
                                                                <option>Diploma</option>
                                                                <option>Bachelor Degree</option> 
                                                                <option>Masters Degree</option>
                                                                <option>Phd</option>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Institution/School</label>
                                                            <input type="text" name="e_institution" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Title/Subject</label>
                                                            <input type="text" name="e_title" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                    <div class="row pt-3 mb-2">
                                                        <div class="col">
                                                            <label class="form-label">Certificate Attachement</label>
                                                            <input type="file" name="e_certificate_link" >
                                                        </div>
                                                        <div class="col float-end">
                                                        <button type="submit" class="px-3-3 btn  btn-warning">Save</button> 
                                                            <a onclick="showHideForm('hide' , 'edit-academic-card')" class="btn  btn-secondary">Cancel</a> 
                                                        </div>
                                                            
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Level</th>
                                                    <th>Institution</th>
                                                    <th>Title/Subject</th>
                                                    <th>File Attachment</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($academic_infos as $key => $academic_info)
                                                    <tr>
                                                        <td>{{$academic_info->level}}</td>
                                                        <td>{{$academic_info->institution}}</td>
                                                        <td>{{$academic_info->title}}</td>
                                                        <td><a target="_blank" href="{{asset('storage/'.$academic_info->certificate_link)}}"><i class="fa fa-eye"></i> Preview</a> | <a onclick="alert('downlod')"><i style="color : lightblue;" class="fa fa-download"></i></a> |
                                                        <a onclick="EditAcademicInfo({{$academic_info}})"><i style="color: black ;"  class="fa fa-pencil"></i></a>|
                                                        <form id="delete-academic-form-{{$key}}" method="POST" action="{{route('academic-infos.destroy' , encrypt($academic_info->id))}}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a onclick="confirmDeleteAcademic({{$key}})"><i style="color:red ;" class="fa fa-trash"></i></a>
                                                        </form>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                        </div> <!-- .Card End -->

                                        <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">Other Documents <button onclick="showHideForm('show' , 'add-docs-card')" class="mb-3 btn btn-sm btn-primary float-end"><i class="fa fa-plus" ></i> New</button></h6>
                                                
                                            <div class="p-2 border rounded mb-3" id="edit-docs-card" style="display: none;">
                                                <form id="edit-docs-info" method="POST" action="{{route('employee-docs.update' , encrypt($employee->id))}}"  enctype="multipart/form-data">
                                                    @method('PUT')
                                                    @csrf
                                                    <input type="text" id="docs-id" name="id" hidden>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">Type</label>
                                                            <select class="form-select form-select-sm" name="e_type">
                                                                <option>Passport</option>
                                                                <option>CV</option>
                                                                <option>Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" name="e_name" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                    <div class="row pt-3 mb-2">
                                                        <div class="col">
                                                            <label class="form-label"> Attachement</label>
                                                            <input type="file" name="e_link" >
                                                        </div>
                                                        <div class="col float-end">
                                                        <button type="submit" class="px-3-3 btn  btn-warning">Save</button> 
                                                            <a onclick="showHideForm('hide', 'edit-docs-card')" class="btn  btn-secondary">Cancel</a> 
                                                        </div>
                                                            
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="p-2 border rounded mb-3" id="add-docs-card" style="display: none;">
                                                <form id="add-docs-info" method="POST" action="{{route('employee-docs.store')}}"  enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="text" id="doc-emp-id" name="id" value="{{$employee->id}}"  hidden>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">Type</label>
                                                            <select class="form-select form-select-sm" name="type">
                                                                <option>Passport</option>
                                                                <option>CV</option>
                                                                <option>Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" name="name" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                    <div class="row pt-3 mb-2">
                                                        <div class="col">
                                                            <label class="form-label"> Attachement</label>
                                                            <input type="file" name="link" >
                                                        </div>
                                                        <div class="col float-end">
                                                        <button type="submit" class="px-3-3 btn  btn-warning">Save</button> 
                                                            <a onclick="showHideForm('hide' , 'add-docs-card')" class="btn  btn-secondary">Cancel</a> 
                                                        </div>
                                                            
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($docs as $key => $doc)
                                                    <tr>
                                                        <td>{{$doc->name}}</td>
                                                        <td>{{$doc->type}}</td>
                                                        <td><a target="_blank" href="{{asset('storage/'.$doc->link)}}"><i class="fa fa-eye"></i> Preview</a> | <a onclick="alert('downlod')"><i style="color : lightblue;" class="fa fa-download"></i></a> |
                                                        <a onclick="EditDocsInfo({{$doc}})"><i style="color: black ;"  class="fa fa-pencil"></i></a>|
                                                        <form id="delete-docs-form-{{$key}}" method="POST" action="{{route('employee-docs.destroy' , encrypt($doc->id))}}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a onclick="confirmDeleteDocs({{$key}})"><i style="color:red ;" class="fa fa-trash"></i></a>
                                                        </form>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                        </div> <!-- .Card End -->

                                        <div>

                    </div>
                        <!-- Employee Identity Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-4">Employee Identity Card</h6>
                            
                            <div class="row">
                                <!-- Front Side -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3 text-center">Front Side</h6>
                                    <div id="id-card-front" class="position-relative bg-white border mx-auto" style="width: 350px; height: 500px; border-radius: 15px; overflow: hidden; font-family: 'Arial', sans-serif; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                        
                                        <div class="text-center pt-4 pb-2">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div style="background: #d4a017; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                                <div class="text-start" style="line-height: 1;">
                                                    <h5 class="m-0 fw-bold" style="letter-spacing: 1px; color: #d4a017;">{{ $employee->company->name ?? 'COMPANY' }}</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center my-3">
                                            <div class="d-inline-block p-1 rounded-circle" style="border: 3px solid #d4a017;">
                                                @if($user_photo)
                                                    <img src="{{asset('storage/'.$user_photo)}}" alt="Employee Photo" class="rounded-circle" width="130px" height="130px" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 130px; height: 130px;">
                                                        <i class="fa fa-user fa-3x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-center px-3">
                                            <h3 class="fw-bold mb-0 text-uppercase" style="color: #222;">{{$employee->fname}} {{$employee->lname}}</h3>
                                            <p class="text-uppercase fw-bold mb-4" style="color: #d4a017; font-size: 0.9rem;">{{$position ? $position->name : 'DESIGNATION'}}</p>
                                            
                                            <div class="text-start mx-auto" style="width: fit-content; font-size: 0.85rem; color: #444;">
                                                <p class="mb-1"><strong>ID NUMBER:</strong> &nbsp;&nbsp;&nbsp; {{$employee->emp_id ?? '987654321'}}</p>
                                                <p class="mb-1"><strong>VALID THROUGH:</strong> &nbsp;&nbsp;&nbsp; {{ $employee->end_date ?? 'Not Set' }}</p>
                                            </div>
                                        </div>

                                        <div class="position-absolute bottom-0 w-100" style="background: #d4a017; height: 100px; border-radius: 0 0 15px 15px;">
                                            <div class="position-absolute bg-white p-1" style="bottom: 15px; right: 20px; border-radius: 5px; line-height: 0;">
                                                @php
                                                    $fullName = $employee->fname . ' ' . $employee->lname;
                                                    $posName = $position ? $position->name : 'Not Assigned';
                                                    $empID = $employee->id_number ?? $employee->id;
                                                    $qrContent = "FULL NAME: $fullName\nPOSITION: $posName\nID NUMBER: $empID";
                                                @endphp
                                                {!! QrCode::size(70)->margin(1)->generate($qrContent) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Back Side -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3 text-center">Back Side</h6>
                                    <div id="id-card-back" class="position-relative bg-white border mx-auto" style="width: 350px; height: 500px; border-radius: 15px; overflow: hidden; font-family: 'Arial', sans-serif; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                        
                                        <div style="background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%); height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 30px; text-align: center;">
                                            
                                            <!-- Company Logo Section -->
                                            <div class="mb-4">
                                                @if($employee->company && $employee->company->logo_url)
                                               
                                                    <img src="{{asset('storage/clogos'.$employee->company->logo_url)}}" alt="Company Logo" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                                
                                                    @else
                                                    <div style="width: 100px; height: 100px; background: rgba(255,255,255,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fa fa-building fa-3x" style="color: white;"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Company Information -->
                                            <div class="mb-4" style="border-bottom: 2px solid rgba(255,255,255,0.5); padding-bottom: 20px;">
                                                <h4 class="fw-bold mb-2">{{ $employee->company->name ?? 'COMPANY NAME' }}</h4>
                                                <p class="mb-1" style="font-size: 0.9rem;">{{ $employee->company->industry ?? 'Industry' }}</p>
                                            </div>

                                            <!-- Contact Information -->
                                            <div style="font-size: 0.85rem; text-align: left; width: 100%;">
                                                <p class="mb-2"><i class="fa fa-map-marker me-2"></i> {{ $employee->company->address ?? 'Address Not Available' }}</p>
                                                <p class="mb-2"><i class="fa fa-phone me-2"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</p>
                                                <p class="mb-2"><i class="fa fa-envelope me-2"></i> {{ $employee->company->email ?? 'Email Not Available' }}</p>
                                                @if($employee->company && $employee->company->website)
                                                    <p class="mb-0"><i class="fa fa-globe me-2"></i> {{ $employee->company->website }}</p>
                                                @endif
                                            </div>

                                            <!-- Security Feature -->
                                            <div class="mt-4 pt-2 border-top border-white" style="width: 100%; font-size: 0.75rem;">
                                                <p class="m-0">Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</p>
                                                <p class="m-0">Authorized by Company</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Button -->
                            <div class="text-center mt-4">
                                <a href="{{ route('employees.download_id', $employee->id) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-file-pdf"></i> Download PDF Card
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- End of Employee Identity Card -->
            </div>
      </div> <!-- Row end  -->
    </div>
                          <!-- Tab: Groups -->
                          <div class="tab-pane fade" id="profile_groups" role="tabpanel">
                            <div class="row-title mb-2">
                              <h5>Groups</h5>
                              <div>
                              </div>
                            </div>
                          </div>
                          <!-- Tab: Project -->
                          <div class="tab-pane fade" id="profile_project" role="tabpanel">
                            <div class="row-title mb-2">
                              <h5>My Projects</h5>
                            </div>
                          </div>
                          <!-- Tab: Campaigns -->
                          <div class="tab-pane fade" id="profile_campaigns" role="tabpanel">
                            <div class="row-title mb-2">
                              <h5>Campaigns</h5>
                            </div>
                          </div>
                          <!-- Tab: Activity -->
                          <div class="tab-pane fade" id="profile_activity" role="tabpanel">
                            <div class="row-title mb-2">
                              <h5>User Activity</h5>
                            </div>
                          </div>
                        </div>
                      </div>
    </div>
@endsection
@section('page-scripts')
<script type="text/javascript">
  
    function showHideForm(id,form){
        
        if(id == 'show'){
            if(form == 'add-nextkin-card'){
               document.getElementById('add-nextkin-card').style.display = 'block';
               document.getElementById('edit-nextkin-card').style.display = 'none'; 
            }

            if(form == 'add-med-card'){
                document.getElementById('add-med-card').style.display = 'block'; 
            }

            if(form == 'add-academic-card'){
                document.getElementById('add-academic-card').style.display = 'block';
            }

            if(form == 'add-docs-card'){
                document.getElementById('add-docs-card').style.display = 'block';
            }
        }else{

           if(form == 'add-nextkin-card'){
               document.getElementById('add-nextkin-card').style.display = 'none';
               document.getElementById('edit-nextkin-card').style.display = 'none'; 
            }
            if(form == 'add-med-card'){
                document.getElementById('add-med-card').style.display = 'none'; 
            }

            if(form == 'add-academic-card'){
                document.getElementById('add-academic-card').style.display = 'none';
            }
            
            if(form == 'add-docs-card'){
                document.getElementById('add-docs-card').style.display = 'none';
            }
            if (form == 'edit-academic-card') {
                document.getElementById('edit-academic-card').style.display = 'none';
            }

            if (form == 'edit-med-card') {
                document.getElementById('edit-academic-card').style.display = 'none';
            }

            if (form == 'edit-nextkin-card') {
                document.getElementById('edit-nextkin-card').style.display = 'none';
            }

            if(form == 'edit-docs-card'){
                document.getElementById('edit-docs-card').style.display = 'none';
            }
        }
    }

    function EditKins(elem){

        document.getElementById('edit-nextkin-card').style.display = 'block';
        document.getElementById('add-nextkin-card').style.display = 'none';

        let kin = elem;
        document.getElementById('nextkin-id').value = kin.id;
        document.getElementsByName('e_f_name')[0].value = kin.f_name;
        document.getElementsByName('e_m_name')[0].value = kin.m_name;
        document.getElementsByName('e_l_name')[0].value = kin.l_name;
        document.getElementsByName('e_relationship')[0].value = kin.relationship;
        document.getElementsByName('e_occupation')[0].value = kin.occupation;
        document.getElementsByName('e_address')[0].value = kin.address;
        document.getElementsByName('e_residence')[0].value = kin.residence;
        document.getElementsByName('e_f_phone')[0].value = kin.f_phone;
        document.getElementsByName('e_s_phone')[0].value = kin.s_phone;
    }

    function EditAcademicInfo(elem){

        document.getElementById('edit-academic-card').style.display = 'block';
        document.getElementById('add-academic-card').style.display = 'none';

        let info = elem;
        document.getElementById('academic-id').value = info.id;
        document.getElementsByName('e_institution')[0].value = info.institution;
        document.getElementsByName('e_title')[0].value = info.title;

        var select = document.getElementsByName('e_level')[0];
        var option;
        for (var i=0; i<select.options.length; i++) {
          option = select.options[i];

          if (option.value === info.level) {
             option.setAttribute('selected', true);
             return; 
          } 
        }
      
    }

    function EditMedicalInfo(elem){
        document.getElementById('edit-med-card').style.display = 'block';
        document.getElementById('add-med-card').style.display = 'none';

        let  med = elem;

        document.getElementById('med-id').value = med.id;
        document.getElementsByName('e_conditions_name')[0].value = med.conditions_name;
        document.getElementsByName('e_status')[0].value =med.status;
    }

    function EditDocsInfo(elem){
        document.getElementById('edit-docs-card').style.display = 'block';
        document.getElementById('add-docs-card').style.display = 'none';

        let  doc = elem;

        document.getElementById('docs-id').value = doc.id;
        document.getElementsByName('e_name')[0].value = doc.name;
        document.getElementsByName('e_type')[0].value =doc.type;
    }

    function confirmDeleteNextKin(id) {
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
                    document.getElementById('delete-nextkin-form-' + id).submit();
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


    function confirmDeleteDocs(id) {
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
                    document.getElementById('delete-docs-form-' + id).submit();
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


    function confirmDeleteAcademic(id){
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
                    document.getElementById('delete-academic-form-' + id).submit();
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

     function confirmDeleteMed(id){
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
                    document.getElementById('delete-med-form-' + id).submit();
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

</script>
@endsection