@extends('layouts.vms')
    <script>
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');

            if (elem == 'show') {
                newform.style.display = 'block';
                newbtn.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newbtn.style.display = 'block';
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


        function ImagetoPrint(source)
        {
            return "<html><head><scri"+"pt>function step1(){\n" +
                    "setTimeout('step2()', 10);}\n" +
                    "function step2(){window.print();window.close()}\n" +
                    "</scri" + "pt></head><body onload='step1()'>\n" +
                    "<img src='" + source + "' /></body></html>";
        }

        function PrintImage(source)
        {
            var Pagelink = "about:blank";
            var pwa = window.open(Pagelink, "_new");
            pwa.document.open();
            pwa.document.write(ImagetoPrint(source));
            pwa.document.close();
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">parts Management</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
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
                    <div class="row g-3 p-4 border rounded">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Part No.</th>
                                        <td>{{$part->part_no}}</td>
                                        <th>Part Name</th>
                                        <td>{{$part->part_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Description.</th>
                                        <td>{{$part->av_qty}} {{$part->uom}}</td>
                                        <th>Status</th>
                                        <td>
                                            @if($part->active)
                                            <span class="badge rounded-pill bg-success">Active</span>
                                            @else
                                            <span class="badge rounded-pill bg-danger">In Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{$part->name}}</td>
                                        <th>Description</th>
                                        <td>{{$part->description}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <ul class="nav nav-tabs nav-tabs-new2">
                                <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Part Purchases</a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_1">Part Usage</a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_2">Damages</a></li>
                            </ul>
                            <div class="tab-content pt-2">
                                <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                                    <div class="table-responsive" id="vehicle-list">
                                        <table id="vehicles" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ppitems as $key => $item)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$vehicle->date}}</a></td>
                                                    <td>{{$vehicle->pp_qty}}</td>
                                                    <td>{{number_format($vehicle->unit_price, 2, '.', ',') }}</td>
                                                    <td>{{number_format($vehicle->total_price, 2, '.', ',') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_1">
                                    <div class="table-responsive">
                                        <table id="vehicle-types" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Quantity</th>
                                                    <th>Created</th>
                                                    <th>Updated</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($puitems as $key => $uitem)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$uitem->date}}</a></td>
                                                    <td>{{$uitem->pu_qty}}</td>
                                                    <th>{{$uitem->created_at}}</th>
                                                    <th>{{$uitem->updated_at}}</th>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab_2">
                                    <div class="table-responsive">
                                        <table id="ownerships" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Qty</th>
                                                    <th>Reason</th>
                                                    <th>Created</th>
                                                    <th>Updated</th>
                                                    <th style="text-align: center;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection