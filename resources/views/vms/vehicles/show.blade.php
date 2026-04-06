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
                    <li class="breadcrumb-item">Vehicles Management</li>                         
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
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Plate No.</th>
                                        <td>{{$vehicle->plate_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Name</th>
                                        <td>{{$vehicle->vehicle_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Type</th>
                                        <td>{{$vehicle->type}}</td>
                                    </tr>
                                    <tr>
                                        <th>Ownership</th>
                                        <td>{{$vehicle->ownership}}</td>
                                    </tr>
                                    <tr>
                                        <th>Capacity.</th>
                                        <td>{{$vehicle->capacity}} {{$vehicle->uom}}</td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 text-center">
                            <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($vehicle->plate_no, $codetype, 10, 10)}}" alt="barcode" />
                            <hr>
                            <a href="#" class="btn btn-outline-primary font-13" onclick="PrintImage('data:image/png;base64,{{DNS2D::getBarcodePNG($vehicle->plate_no, $codetype, 15, 15)}}'); return false;">
                                                <i class="fa fa-qrcode"></i> PRINT</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Legal Documents (Tanzania)</h6>
                    <a href="{{ route('legal-documents.create') }}?vehicle_id={{ $vehicle->id }}" style="color: #007bff; text-decoration: none;"><i class="fa fa-file-pdf-o"></i> Add Document</a>
                </div>
                <div class="card-body">
                    @php $vehicleDocs = \App\Models\LegalDocument::with('documentType')->where('vehicle_id', $vehicle->id)->orderBy('expire_date')->get(); @endphp
                    @if($vehicleDocs->isEmpty())
                    <p class="text-muted mb-0">No documents. <a href="{{ route('legal-documents.create') }}?vehicle_id={{ $vehicle->id }}">Add documents</a></p>
                    @else
                    <table class="table table-sm">
                        <thead><tr><th>Document</th><th>Expiry</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @foreach($vehicleDocs as $d)
                            <tr>
                                <td>{{ $d->documentType?->dt_name ?? '—' }}</td>
                                <td>{{ optional($d->expire_date)->format('d/m/Y') }}</td>
                                <td>
                                    @if($d->status === 'EXPIRED')<span class="badge bg-danger">Expired</span>
                                    @elseif($d->status === 'EXPIRING_SOON')<span class="badge bg-warning">Soon</span>
                                    @else<span class="badge bg-success">Valid</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('legal-documents.download', encrypt($d->id)) }}" class="btn btn-xs btn-outline-primary"><i class="fa fa-download"></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection