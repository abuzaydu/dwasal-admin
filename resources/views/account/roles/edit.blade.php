@extends('layouts.prof')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                         
                    <li class="breadcrumb-item">My Account</li>    
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card radius-6 p-4" id="print-permission-page">
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('company-roles.update', encrypt($role->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        @if($role->name == 'Admin')
                        <div class="col-sm-6">
                            <label class="form-label">Role Name <span style="color:red">*</span></label>
                            <input id="shopname" type="text" name="name" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.name')}}" value="{{$role->display_name}}" required readonly>
                        </div>
                        @else
                        <div class="col-sm-6">
                            <label class="form-label">Role Name <span style="color:red">*</span></label>
                            <input id="shopname" type="text" name="name" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.name')}}" value="{{$role->display_name}}" required>
                        </div>
                        @endif
                        <div class="col-sm-12">
                            <div class="row g-5 mt-0"  style="page-break-inside:auto" data-masonry='{"percentPosition": true }'>
                                @foreach($fpermissions as $key => $fp)
                                <div class="col-md-4 mt-0">
                                    <h6 class="mb-0"><input type="checkbox" name="sample" id="f-{{$fp['name']}}" onclick="selectAll('<?php echo $fp['name'].'-'.count($fp['permissions']); ?>')" /> {{$fp['name']}}</h6>
                                    <hr>
                                    @foreach ($fp['permissions'] as $pkey => $value)
                                    <label style="padding-bottom: 5px; page-break-inside:avoid; page-break-after:auto; font-weight: normal;">{{ html()->checkbox('permission[]')->value($value['name'])->checked(in_array($value['id'], $currPermissions))->id('perm-'.$fp['name'].'-'.$pkey) }}
                                        {{ $value['display_name'] }}</label><br>
                                    @endforeach
                                    <br>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
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
    
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
    
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