@extends('layouts.inv')
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
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>                         
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
                                        <th>Chassis No.</th>
                                        <td>{{$vehicle->chassis_no}}</td>
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
    <!--end row-->
@endsection