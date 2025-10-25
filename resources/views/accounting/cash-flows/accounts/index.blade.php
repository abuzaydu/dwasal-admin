@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
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
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#actxModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus-circle"></i>New Account</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">  
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                            <table id="example1" class="table table-striped display nowrap" style="width:100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Account Type</th>
                                        <th>Account Name</th>
                                        <th>Account Number</th>
                                        <th>Currency</th>
                                        <th>Bank Name</th>
                                        <th>Branch Name</th>
                                        <th>Swift Code</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accounts as $index => $acc)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$acc->type}}</td>
                                        <td>{{$acc->account_name}}</td>
                                        <td>{{$acc->account_number}}</td>
                                        <td>{{$acc->currency}}</td>
                                        <td>{{$acc->bank_name}}</td>
                                        <td>{{$acc->branch_name}}</td>
                                        <td>{{$acc->swift_code}}</td>
                                        <td>
                                            <a href="{{route('accounts.edit', encrypt($acc->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>  |
                                            <form id="delete-form-{{$index}}" action="{{route('accounts.destroy' , encrypt($acc->id) )}}" method="POST" style="display: inline-block;">
                                                @method('DELETE')
                                                 @csrf
                                                <a href="#" class="button" onclick="confirmDelete('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i></a>
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
<!-- Modal -->
<div class="modal fade" id="actxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel">New Account</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form class="my-form" method="POST" action="{{route('accounts.store')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="row pt-2">
                            <label for="account" class="col-sm-4 form-label">Account Type <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm mb-1" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row pt-2">
                            <label for="account" class="col-sm-4 form-label">Account Number <br>(<span class="text-primary">If it is a bank Account</span>)</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" placeholder="Account Number">
                            </div>
                        </div>

                        <div class="row pt-2">
                            <label for="account" class="col-sm-4 form-label">Account Name <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" placeholder="Account Name" required>
                            </div>
                        </div>
                        <div class="row pt-2">
                            <label for="bank_name" class="col-sm-4 form-label">Bank Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" placeholder="Bank Name">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <label for="bank_name" class="col-sm-4 form-label">Branch Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" placeholder="Branch Name">
                            </div>
                        </div>
                    
                        <div class="row pt-2">
                            <label for="swift_code" class="col-sm-4 form-label">Swift Code</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" placeholder="Swift Code">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <label for="account" class="col-sm-4 form-label">Currency </label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm mb-1" name="currency">
                                    <option value="">-Select</option>
                                    @foreach($currencies as $curr)
                                    @if($dfcurr->code == $curr->code)
                                    <option selected>{{$curr->code}}</option>
                                    @else
                                    <option>{{$curr->code}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#example1').DataTable({
                'scrollX': true
            }); 
        });
    </script>
@endsection