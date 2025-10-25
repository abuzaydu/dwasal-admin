@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function setValue(elm){
        // alert('test');
        var sms = document.getElementById('dmsg');
        
        if (elm.value != '') {
            sms.value += elm.value;
        }

    }

    function setValueTemp(elm){
        // alert('test');
        var sms = document.getElementById('tempmsg');
        
        if (elm.value != '') {
            sms.value += elm.value;
        }

    }

    function useTemplateStatic(message){
        var msg = document.getElementById('message');
        msg.value = message;
    }
    function useTemplateDynamic(message) {
        var dmsg = document.getElementById('dmsg');
        dmsg.value = message;
    }

    function weg(elem){
        var temp = document.getElementById('temp-options');

        if (elem.value == '1') {
            temp.style.display = 'block';
        }else{
            temp.style.display = 'none';
        }
    }
</script>
@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#static-sms" role="tab" aria-selected="true">
                                {{trans('navmenu.static_sms')}}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#dynamic-sms" role="tab" aria-selected="true">{{trans('navmenu.dynamic_sms')}}</a>
                        </li>
                        @if(Auth::user()->can('manage-sms-templates'))
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#sms-templates" role="tab" aria-selected="false">{{trans('navmenu.sms_templates')}}</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('edit-settings'))
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#sms-setting" role="tab" aria-selected="false">SMS Settings</a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="static-sms" role="tabpanel">
                            <form class="row g-1" method="post" action="{{ route('sms-notifications.store') }}" validate style="margin-top: 5px;">
                                {{csrf_field()}}
                                <div class="col-md-4">
                                    <label class="form-label">Sender ID <span style="color: red;">*</span></label>
                                    <select name="sender" class="form-select form-select-sm mb-1" required>
                                        @if(!is_null($senderids))
                                            @if($senderids->count() == 1)
                                                @foreach($senderids as $senderid)
                                                <option>{{$senderid->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">Select Sender ID</option>
                                                @foreach($senderids as $senderid)
                                                <option>{{$senderid->name}}</option>
                                                @endforeach
                                            @endif
                                        @else
                                            <option value="">No Sender Id registered for this Account</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Send SMS to: <span style="color: red;">*</span> </label>
                                    <select name="customers" class="form-select form-select-sm mb-1" required>
                                        <option value="">Select List of Customers</option>
                                        <option value="all">All Customers</option>
                                        <option value="15-30">Debtors in 15 to 30 days</option>
                                        <option value="31-60">Debtors in 31 to 60 days</option>
                                        <option value="61-90">Debtors in 61 to 90 days</option>
                                        <option value="91-180">Debtors in 91 to 180 days</option>
                                        <option value="180+">Debtors in more than 180 days</option>
                                    </select>
                                </div>
                                @if(Auth::user()->can('manage-sms-templates'))
                                <div class="col-md-5">
                                    <label class="form-label">Template name (If you want to save as Template)</label>
                                    <input class="form-control form-control-sm mb-1" subs="text" name="title" placeholder="Enter Template name" id="userinput8">
                                </div>
                                @endif
                                <div class="col-md-7">
                                    <label class="form-label">Message <span style="color: red;">*</span> </label>
                                    <textarea name="message" id="message" class="form-control form-control-sm mb-1" placeholder="Please Type her Your Message" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button subs="submit" class="btn btn-primary btn-sm">
                                        <i class="icon-check2"></i> Submit
                                    </button>
                                    <button type="reset" class="btn btn-outline-warning btn-sm">Reset</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="dynamic-sms" role="tabpanel">
                            <form class="form row g-1" method="post" action="{{ url('sms-dynamic') }}" validate>
                                {{csrf_field()}}
                                <div class="col-md-6">
                                    <label class="form-label">Select Automatic values to set in your SMS</label>
                                    <select onchange="setValue(this)" class="form-select form-select-sm mb-1">
                                        <option value="">Select a Value</option>
                                        <option value="{customer_name}">Customer Name</option>
                                        <option value="{invoice_no}">Invoice Number</option>
                                        <option value="{invoice_date}">Invoice date</option>
                                        <option value="{amount_due}">Unpaid Amount</option>
                                        <option value="{due_date}">Due Date</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sender ID</label>
                                    <select name="sender" class="form-select form-select-sm mb-1" required>
                                    @if(!is_null($senderids))
                                        @if($senderids->count() == 1)
                                            @foreach($senderids as $senderid)
                                            <option>{{$senderid->name}}</option>
                                            @endforeach
                                        @else
                                            <option value="">Select Sender ID</option>
                                            @foreach($senderids as $senderid)
                                            <option>{{$senderid->name}}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        <option value="">No Sender Id registered for this Account</option>
                                    @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Send SMS to:</label>
                                    <select name="customers" id="customers" onchange="getData(this)" class="form-select form-select-sm mb-1" required>
                                        <option value="">Select List of Customers</option>
                                        <option value="all">All Customers</option>
                                        <option value="15-30">Debtors in 15 to 30 days</option>
                                        <option value="31-60">Debtors in 31 to 60 days</option>
                                        <option value="61-90">Debtors in 61 to 90 days</option>
                                        <option value="91-180">Debtors in 91 to 180 days</option>
                                        <option value="180+">Debtors in more than 180 days</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Message</label>
                                    <textarea name="message" id="dmsg" class="form-control form-control-sm mb-1" placeholder="Please Type her Your Message" required></textarea>
                                </div>
                                @if(Auth::user()->can('manage-sms-templates'))
                                <div class="col-md-6">
                                    <label class="form-label">Template name (If you want to save as Template)</label>
                                    <input class="form-control form-control-sm mb-1" subs="text" name="title" placeholder="Enter Template name" id="userinput8">
                                </div>
                                @endif
                                <div class="form-actions right">
                                    <button subs="submit" class="btn btn-primary btn-sm">Submit</button>
                                    <button type="reset" class="btn btn-outline-warning btn-sm">Reset</button>
                                </div>
                            </form>
                        </div>  
                        <div class="tab-pane fade" id="sms-templates" role="tabpanel">
                            <div class="row g-1">
                                <div class="col-md-12">
                                    <form class="form row g-1" action="{{route('sms-notifications.store')}}" method="post">
                                        @csrf
                                        <div class="col-md-4">
                                            <label class="form-label">Template name <span style="color: red;">*</span></label>
                                            <input class="form-control form-control-sm mb-1" subs="text" name="title" placeholder="Enter Template name" id="userinput8" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Use as Auto SMS</label>
                                            <select name="is_auto_sms" onchange="weg(this)" class="form-select form-select-sm mb-1">
                                                <option value="0">{{trans('navmenu.no')}}</option>
                                                <option value="1">{{trans('navmenu.yes')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6" id="temp-options" style="display: none;">
                                            <label class="form-label">{{trans('navmenu.temp_used_when')}}</label>
                                            <select name="temp_for" class="form-select form-select-sm mb-1">
                                                <option value=""> --Select--</option>
                                                @foreach($tempuses as $key => $tempuse)
                                                <option value="{{$key}}">{{$tempuse}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Select Automatic values</label>
                                            <select onchange="setValueTemp(this)" class="form-control form-control-sm mb-1">
                                                <option value="">Select a Value</option>
                                                <option value="{customer_name}">Customer Name</option>
                                                <option value="{invoice_no}">Invoice Number</option>
                                                <option value="{invoice_date}">Invoice date</option>
                                                <option value="{amount_due}">Unpaid Amount</option>
                                                <option value="{due_date}">Due Date</option>
                                                <option value="{first_name}">User First Name</option>
                                                <option value="{shop_name}">Shop Name</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Message <span style="color: red;">*</span></label>
                                            <textarea name="message" id="tempmsg" class="form-control form-control-sm mb-1" placeholder="Please Type her Your Message" required></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <button subs="submit" class="btn btn-primary btn-sm">Save</button>
                                            <button type="reset" class="btn btn-outline-warning btn-sm">Reset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="sms-setting" role="tabpanel">
                            <form class="form row g-1" method="POST" action="{{ url('sms-settings') }}" validate>
                                {{csrf_field()}}
                                <input type="hidden" name="id" value="{{$smssetting->id}}">
                                <div class="col-md-4">
                                    <label class="form-label">Days before Product Expire Date</label>
                                    <input class="form-control form-control-sm mb-1" type="number" name="days_before_expire" placeholder="Enter Days before Expire" id="userinput8" required value="{{$smssetting->days_before_expire}}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Days before Invoice Due Date</label>
                                    <input class="form-control form-control-sm mb-1" type="number" name="days_before_due" placeholder="Enter Days before Due Date" id="userinput8" required value="{{$smssetting->days_before_due}}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Repeat</label>
                                    <input class="form-control form-control-sm mb-1" type="number" name="repeat" placeholder="Enter SMS repeatition" id="userinput8" required value="{{$smssetting->repeat}}">
                                </div>
                                <div class="col-md-12">                                      
                                    <a href="javascript:history.back()" class="btn btn-outline-warning btn-sm">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </div>
                            </form>
                        </div>                    
                    </div>
                </div>
            </div>
        </div> 
        <hr>
        <div class="col-xl-12 mx-auto">
            <h6>My Templates</h6>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-striped" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>Temlate Name</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sms_templates as $key => $temp)
                                <tr>
                                    <td>{{ $key+1  }}</td>
                                    <td>{{ $temp->title }}</td>
                                    <td>{{ $temp->message }} </td>
                                    <td>
                                        <a href="#" class="btn btn-outline-secondary btn-sm" onclick="useTemplateStatic('{{$temp->message}}')" data-message="">Use In Static</a> | 
                                        <a href="#" class="btn btn-outline-success btn-sm" onclick="useTemplateDynamic('{{$temp->message}}')" data-message="">Use In Dynamic</a> 
                                        @if(Auth::user()->can('manage-sms-templates')) | 
                                        <a  href="{{  route('sms-notifications.edit', encrypt($temp->id)) }}"><i class="bx bx-edit"></i>Edit</a> | 
                                        <form id="delete-form-{{ $key }}" method="POST"action="{{ route('sms-notifications.destroy', encrypt($temp->id)) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" title="Cancel Invoice" onclick="confirmDelete('<?php echo $key; ?>')"><i class="bx bx-x-circle" style="color: red;"></i> Delete</a>
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
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
    <script src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script>
        $(function () {
            $('#example1').DataTable({
                'scrollX': true,
            });
        });
    </script>
@endsection