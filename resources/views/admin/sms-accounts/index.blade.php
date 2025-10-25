@extends('layouts.adm')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-1" method="post" action="{{ route('sms-accounts.store') }}" validate>
                        {{ csrf_field() }}
                        <div class="col-md-3">
                            <label class="form-label">Business</label>
                            <select name="shop_id" required="required"
                                class="form-select form-select-sm mb-1 border-primary mb-1">
                                <option value="">Select Business</option>
                                @foreach ($shops as $key => $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }} ({{$shop->company}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Username</label>
                            <input class="form-control form-control-sm mb-1 border-primary" account="text" name="username"
                                placeholder="Enter Username" id="userinput8" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Password</label>
                            <input class="form-control form-control-sm mb-1 border-primary" type="password" name="password"
                                placeholder="Enter Password" required>
                        </div>
                        <div class="col-md-3 pt-4">
                            <button account="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        </div>
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table id="sms-accounts" class="table table-striped" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>Business name</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sms_accounts as $key => $account)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td> 
                                            {{ $account->name }}  ({{$account->company}})
                                        </td>
                                        <td>{{ $account->username }}</td>
                                        <td>{{ $account->password }} </td>
                                        <td>
                                            <a href="{{ route('sms-accounts.edit', encrypt($account->id)) }}">
                                                <i class="fa fa-edit"></i>
                                            </a> |
                                            <a href="{{ url('admin/sms-accounts/destroy', encrypt($account->id)) }}"
                                                onclick="return confirm('Are you sure you want to delete this record?.')">
                                                <i class="fa fa-trash text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-1" method="post" action="{{ route('sender-ids.store') }}" validate>
                        {{csrf_field()}}
                        <div class="col-md-3">
                            <label class="form-label">SMS Account</label>
                            <select name="sms_account_id" required class="form-select form-select-sm mb-1">
                                <option value="">Select SMS Account</option>
                                @foreach($sms_accounts as $key => $account)
                                <option value="{{$account->id}}">{{$account->username}} ({{$account->name}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sender ID</label>
                            <input class="form-control form-control-sm mb-1" account="text" name="name" placeholder="Enter Sender ID name" id="userinput8" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.auto_sms')}}</label>
                            <select name="auto_sms" class="form-control form-control-sm mb-1">
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                            </select>
                        </div>
                        <div class="col-md-3 pt-4">
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        </div>
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table id="sender-ids" class="table table-striped" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>Sender ID</th>
                                    <th>Account</th>
                                    <th>{{trans('navmenu.auto_sms')}}</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($senderids as $key => $senderid)
                                <tr>
                                    <td>{{ $key+1  }}</td>
                                    <td> {{ $senderid->name }} </td>
                                    <td>{{ $senderid->username }}</td>
                                    <td>
                                        @if($senderid->auto_sms)
                                            {{trans('navmenu.yes')}}
                                        @else
                                            {{trans('navmenu.no')}}
                                        @endif    
                                    </td>
                                    <td>
                                        <a  href="{{  route('sender-ids.edit', Crypt::encrypt($senderid->id)) }}">
                                            <i class="fa fa-edit"></i>
                                        </a> |
                                        <a href="{{ url('admin/sender-ids/destroy', Crypt::encrypt($senderid->id)) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                            <i class="fa fa-trash text-danger"></i>
                                        </a>
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
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            $('#sms-accounts').DataTable({
                "scrollX": true,
            });

            $('#sender-ids').DataTable({
                "scrollX": true,
            });
        });
    </script>
@endsection
