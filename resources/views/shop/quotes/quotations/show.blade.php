@extends('layouts.app')
<script type="text/javascript">
    function confirmCreateProforma(url) {
        Swal.fire({
            title: "Create a Proforma Invoice from this quotation?",
            text: "This cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, Create",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        })
        return false;
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-3">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotes</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="mb-0 text-uppercase">{{ $quotation->quote_number }}</h5>

                        <div class="d-flex flex-wrap gap-2">

                            <a href="{{ route('quotations.index') }}" class="btn btn-default btn-sm text-nowrap">
                                <i class="fa fa-list"></i> Back to List
                            </a>

                            @if ($quotation->status === 'Draft')
                                <a href="{{ route('quotations.edit', encrypt($quotation->id)) }}" class="btn btn-primary btn-sm text-nowrap">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('quotations.send', encrypt($quotation->id)) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-sm text-nowrap">
                                        <i class="fa fa-paper-plane"></i> Mark as Sent
                                    </button>
                                </form>
                            @endif

                            @if ($quotation->status === 'Sent')
                                <form action="{{ route('quotations.accept', encrypt($quotation->id)) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm text-nowrap">
                                        <i class="fa fa-check"></i> Accept
                                    </button>
                                </form>

                                <form action="{{ route('quotations.reject', encrypt($quotation->id)) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm text-nowrap">
                                        <i class="fa fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            @if ($quotation->status === 'Accepted' && $quotation->is_proinvoice_created === 0)
                                <a href="javascript:;" class="btn btn-success btn-sm text-nowrap" data-url="{{ route('proforma.from-quotation', encrypt($quotation->id)) }}" onclick="confirmCreateProforma(this.dataset.url)">
                                    <i class="fa fa-file-invoice"></i> Create Proforma
                                </a>
                            @endif

                        </div>
                    </div>

                    <hr class="mt-0">

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Customer</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $quotation->customer_name ?? '-' }}
                        </div>

                        <div class="col-md-3">
                            <strong>Email</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $quotation->email }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Mobile</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $quotation->phone }}
                        </div>

                        <div class="col-md-3">
                            <strong>Address</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $quotation->address ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Status</strong>
                        </div>
                        <div class="col-md-3">
                            @php
                                $badgeClass = match ($quotation->status) {
                                    'Draft' => 'bg-secondary',
                                    'Sent' => 'bg-info',
                                    'Accepted' => 'bg-success',
                                    'Rejected' => 'bg-danger',
                                    'Expired' => 'bg-warning',
                                    default => 'bg-primary',
                                };
                            @endphp

                            <span class="badge {{ $badgeClass }}">
                                {{ $quotation->status }}
                            </span>
                        </div>

                        <div class="col-md-3">
                            <strong>Valid Until</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $quotation->valid_until ?? '-' }}
                        </div>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotation->items as $item)
                                    <tr>
                                        <td>{{ $item->item_description }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            No quotation items found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-4">

                            <div class="row">
                                <div class="col-6">Subtotal</div>
                                <div class="col-6 text-end">
                                    {{ number_format($quotation->subtotal, 2) }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">Discount</div>
                                <div class="col-6 text-end">
                                    {{ number_format($quotation->discount, 2) }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">Tax</div>
                                <div class="col-6 text-end">
                                    {{ number_format($quotation->tax_amount, 2) }}
                                </div>
                            </div>

                            <div class="row fw-bold">
                                <div class="col-6">Total</div>
                                <div class="col-6 text-end">
                                    {{ number_format($quotation->total, 2) }}
                                </div>
                            </div>

                        </div>
                    </div>

                    @if ($quotation->notes)
                        <hr>
                        <strong>Notes</strong>
                        <p>{{ $quotation->notes }}</p>
                    @endif

                    @if ($quotation->is_proinvoice_created === 1)
                        <hr>
                        <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="font-35 text-white"><i class="fa fa-check-circle"></i></div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-white">Proforma Created</h6>
                                    <div class="text-white">The Pro Invoice for this quotation has already been created.
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection