@extends('layouts.vms')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}"><i class="fa fa-file-text-o"></i></a></li>
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}">Field Operations</a></li>
                    <li class="breadcrumb-item active">{{ $record->record_no }}</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                @can('edit-fo-record')
                <a href="{{ route('field-operations.edit', $record) }}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="fa fa-edit"></i> Update</a>
                @endcan
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body p-4">
                    @include('vms.field-operations.report', ['record' => $record])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-fo");
            var filename = "<?php echo 'Field Operations Record_'.$record->record_no.'_'.$record->record_date; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
    </script>
@endsection
