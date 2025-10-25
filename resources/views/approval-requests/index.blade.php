@extends('layouts.app')
@section('page-styles')
  <!-- Application Vendor CSS URL -->
  <link rel="stylesheet" href="{{ asset('assets/cssbundle/summerdiscapp.min.css') }}">
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script>
        function showHidediscappForm(elem) {
            var newform = document.getElementById('new-discapp-form');
            var newbtn = document.getElementById('new-discapp-btn');
            var itemlist = document.getElementById('discapp-list');
            var newtitle = document.getElementById('new-discapp-title');
            var listtitle = document.getElementById('discapp-list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }

        function confirmDelete(id){
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
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#discount-requests"><i class='fa fa-list'></i> Discount Approval Requests</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#invoice-requests"><i class='fa fa-list-alt'></i> Proforma Approval Requests</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="discount-requests" role="tabpanel">
                            <div class="table-responsive" id="discapp-list">
                                <table id="discounts" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item Description</th>
                                            <th>Discount (%)</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Requested By</th>
                                            <th>Approved By</th>
                                            <th>Approved At</th>
                                            <th>Comments</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($discapprovals as $key => $discapp)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$discapp->slug}}</a></td>
                                            <td style="text-align: center;">{{$discapp->disc_percent+0}}</td>
                                            <td style="text-align: center;">{{number_format($discapp->discount) }}</td>
                                            <td>{{$discapp->status}}</td>
                                            <td>{{$discapp->user}}</td>
                                            <td>{{$discapp->approver}}</td>
                                            <td>{{$discapp->approved_time}}</td>
                                            <td>{{$discapp->comments}}</td>
                                            <td>
                                                @if($discapp->status == 'Awaiting for Approval')
                                                <a href="{{ url('approve-discount/'.encrypt($discapp->id))}}" class="text-primary"><i class="fa fa-check"></i> Approve</a> | 
                                                <a href="{{ route('approval-requests.edit', encrypt($discapp->id)) }}" class="text-danger"><i class="fa fa-close"></i> Reject</a> 
                                                @endif  
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="invoice-requests" role="tabpanel">
                            
                            <div class="table-responsive" id="discapp-list">
                                <table id="invoices" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PFI No</th>
                                            <th>Status</th>
                                            <th>Requested By</th>
                                            <th>Approved By</th>
                                            <th>Approved At</th>
                                            <th>Comments</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invapprovals as $key => $invapp)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$invapp->invoice_no}}</a></td>
                                            <td>{{$invapp->status}}</td>
                                            <td>{{$invapp->user}}</td>
                                            <td>{{$invapp->approver}}</td>
                                            <td>{{$invapp->approved_at}}</td>
                                            <td>{{$invapp->comments}}</td>
                                            <td>
                                                @if($invapp->status == 'Awaiting for Approval')
                                                <a href="{{ route('pro-invoices.show', encrypt($invapp->pro_invoice_id))}}" class="text-primary"><i class="fa fa-check"></i> View Invoice To Approve</a>
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
        </div>
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#discounts').DataTable({
                'scrollX': true
            });
            $('#invoices').DataTable({
                'scrollX': true
            });
        });
    </script>
@endsection