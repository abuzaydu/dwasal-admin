@extends('layouts.hr')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{ asset('assets/js/angular-1-8-3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/reconcile.js') }}"></script>
    <script type="text/javascript">
        function confirmCancel(id) {
            Swal.fire({
                title: 'Are you sure, You want to cancel this reconciliation?',
                showDenyButton: true,
                confirmButtonText: 'Yes Cancel',
                denyButtonText: `Don't Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href="{{url('cancel-allowance')}}/"+id;
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('reconciliation not cancelled', '', 'info')
                }
            });
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-12 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reconciliations')}}">Requisitions</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row g-3" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="reconcileTempId('<?php echo $reconciliation->id; ?>')">
        <div class="col-12">
            <div class="card print_invoice pt-2">
                <div class="block-header p-3">
                    <h6 class="mb-0 text-uppercase">{{$page}}</h6>
                </div>
                <div class="card-body">
                    <div class="card p-1">
                        <div class="row g-3">
                            @if ($message = Session::get('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <i class="fa fa-info-circle"></i> {{$message}}
                            </div>
                            @endif
                            <div class="col-md-12">
                                <table class="items mt-0">
                                    <thead>
                                        <tr>
                                            <th style="width:5%"></th>
                                            <th style="width:45%;">Item Description</th>
                                            <th style="width: 10%;">Item Category</th>
                                            <th style="width:10%">No. Of Days</th>
                                            <th style="text-align: center; width: 5%;">Quantity</th>
                                            <th style="text-align: center; width: 10%;">Price</th>
                                            <th style="text-align: center; width: 15%">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="item-row" ng-repeat="newreconciletemp in reconciletemp ">
                                            <td><a class="text-danger" href="javascript:;" ng-click="removeReconcileTemp(newreconciletemp.id)" title="Remove row"><i class="fa fa-close"></i></a> @{{$index + 1}}.</td>
                                            <td class="item-name">
                                                <textarea type="text" ng-model="newreconciletemp.item_description" ng-blur="updateReconcileTemp(newreconciletemp)" placeholder="Enter Item description" rows="2"></textarea>
                                            </td>
                                            <td class="item-name">
                                                <select ng-model="newreconciletemp.item_category" ng-change="updateReconcileTemp(newreconciletemp)">
                                                    <option value="Allowance">Allowance</option>
                                                    <option value="Tools">Tools</option>
                                                    <option value="Transport">Transport</option>
                                                    <option value="Risk Assesment">Risk Assesment</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" name="no_of_days" min="0" step="any" string-to-number ng-model="newreconciletemp.no_of_days" ng-blur="updateReconcileTemp(newreconciletemp)" class="qty" style="text-align: center; width: 80px;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" name="quantity" min="0" step="any" string-to-number ng-model="newreconciletemp.quantity" ng-blur="updateReconcileTemp(newreconciletemp)" class="qty" style="text-align: center; width: 80px;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" name="price" min="0" step="any" string-to-number ng-model="newreconciletemp.price" ng-blur="updateReconcileTemp(newreconciletemp)" class="cost" style="text-align: center; width: 100px;">
                                            </td>
                                            <td style="text-align: center;"><span class="price">@{{newreconciletemp.total}}</span></td>
                                        </tr>
                                        <tr class="hiderow">
                                            <td colspan="7"><a href="javascript:;" ng-click="addReconcileTemp(reconciliation)" title="Add a row">Add a row</a></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right;">
                                                <span class="btn btn-sm btn-outline-primary mb-2" ng-click="getData()"><i class="fa fa-refresh"></i> Update</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <table class="items mt-0" style="font-size: 14px;">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" class="total-line">Total</td>
                                            <td class="total-value">
                                                <div id="subtotal">@{{ sum(reconciletemp) | number:2}}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 text-center">
                                <h6 class="mb-0 text-uppercase" style="font-size: 12px;">Reconciliation Evidence </h6><br><small class="text-warning">(Click An Image to remove if addaed by mistake)</small>

                                <hr>
                                <div class="row g-1">
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12"  ng-repeat="evidence in evidencetemp">
                                        <a title="Tap to Remove Image" ng-click="removeImage(evidence.id)"  ng-if="(evidence.file_url.substring(evidence.file_url.lastIndexOf('.') + 1) === ('pdf'))">
                                            <img class="thumbnail rounded img-fluid"src="{{ asset('assets/img/PDF_file_icon.svg.png') }}" style="width: 60%; height: 150px;"><br>
                                            <small>Evidence File</small>
                                        </a>
                                        <a title="Tap to Remove Image" ng-click="removeImage(evidence.id)"  ng-if="!(evidence.file_url.substring(evidence.file_url.lastIndexOf('.') + 1) === ('pdf'))">
                                            <img class="thumbnail rounded img-fluid" ng-src="{{asset('storage')}}/@{{ evidence.file_url }}" style="width: 100%; height: 150px;">
                                        </a>
                                    </div>
    
                                </div>
                                <hr>
                                <div class="row g-1">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div id="result"></div>
                                        <form class="row g-3" id="basic-form" method="POST"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
                                            @csrf
                                            <div class="col-md-12 text-center mt-3">
                                                <label class="form-label">Add Pictures(Maximu 4 Images at a time) <span class="text-danger">*</span></label>
                                                <input type="file" id="img-files" name="images[]" multiple class="form-control" required>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <button type="submit" class="btn btn-primary" id="submitFiles">Upload</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('reconciliations.store') }}">
                @csrf
                <input type="hidden" name="request_id" value="{{$reconciliation->id}}">
                <input type="hidden" name="status" value="Awaitnig for Approval">
                
                <div class="col-12 text-center text-md-end">
                    <button type="submit" class="btn btn-sm btn-primary" ><i class="fa fa-print me-2"></i>Reconcile</button>
                    <button type="button" onclick="confirmCancel('<<?php echo encrypt($reconciliation->id); ?>')" class="btn btn-sm btn-danger"><i class="fa fa-close me-2"></i>Cancel</button>
                </div>
            </form>
        </form>
    </div> <!-- .row end -->
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#basic-form').submit(function(e){
                e.preventDefault();
                var reconciliation_id = "<?php echo $reconciliation->id; ?>";                    
                let formData = new FormData(this);
                formData.append('reconciliation_id', reconciliation_id);
                const totalImages = $("#img-files")[0].files.length;
                let images = $("#img-files")[0];
                if (parseInt(totalImages) > 4){
                    Swal.fire({
                        icon: 'info',
                        title: 'Oops...',
                        text: 'You can only upload a maximum of Four(4) images at a time!'
                    })
                    $('#basic-form').trigger("reset");
                }else{
                    for (let i = 0; i < totalImages; i++) {
                        formData.append('images' + i, images.files[i]);
                    }
                    formData.append('totalImages', totalImages);
                    $.ajax({
                        url: "{{ route('reconciliation-evidence.store') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        cache: false,
                        contentType: false,
                        success: function(response) {
                            $('#basic-form').trigger("reset");
                            if (response && response.status === 'success') {
                                $("#result").append(`<div class='alert alert-success alert-dismissible fade show' role='alert'>${response.message}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>`);
                                setTimeout(() => {
                                    $(".alert").remove();
                                }, 5000);
                            }else if(response.status === 'failed') {
                                $("#result").append(`<div class='alert alert-success alert-dismisisble fade show' role='alert'>${response.message}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>`);
                                    
                                setTimeout(() => {
                                    $(".alert").remove();
                                }, 5000);
                            }
                            angular.element(document.getElementById('mycontroller')).scope().getData();
                        }            
                    });
                }
            });
        });
    </script>
@endsection