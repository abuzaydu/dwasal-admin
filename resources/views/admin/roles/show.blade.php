@extends('layouts.adm')

@section('content')
<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-9 mx-auto">
            <div class="card">
                <div class="card radius-6 p-4" id="print-permission-page">
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('roles.update', encrypt($role->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-sm-6">
                            <label class="form-label">Role Name <span style="color:red">*</span></label>
                            <input id="shopname" type="text" name="name" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.name')}}" value="{{$role->display_name}}" required>
                        </div>
                        <div class="col-sm-12">
                            <div class="row g-5 mt-0"  style="page-break-inside:auto" data-masonry='{"percentPosition": true }'>
                                @foreach($fpermissions as $key => $fp)
                                <div class="col-md-4 mt-0">
                                    <h6 class="mb-0">{{$fp['name']}}</h6>
                                    <hr>
                                    @foreach ($fp['permissions'] as $value)
                                    <label style="padding-bottom: 5px; page-break-inside:avoid; page-break-after:auto">{{ Form::checkbox('permission[]', $value['name'], in_array($value['id'], $currPermissions) ? true : false, ['class' => 'name']) }}
                                        {{ $value['display_name'] }}</label><br>
                                    @endforeach
                                    <br>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-sm">Add</button>
                            <a href="{{ url('user-profile')}}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a href="#" onclick="javascript:savePdf()" class="btn btn-outline-warning btn-sm float-end" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF /Print </a>
                </div>
            </div>
        </div>
    </div>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
          const element = document.getElementById("print-permission-page");
          var filename = "User Permissions";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

          // New Promise-based usage:
          html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                window.open(pdf.output('bloburl'), '_blank');
            });
          
        }
    </script>