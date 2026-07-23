@extends('layouts.sand')

@section('page-styles')
    <style>
        :root {
            --sand-primary: #d98c3b;
            --sand-primary-dark: #b56f28;
            --sand-accent: #2c7a4b;
            --sand-info: #2f80c9;
            --sand-danger: #e0483e;
            --sand-bg: #f5f6f8;
            --sand-card-radius: 14px;
        }

        #main-content {
            background: var(--sand-bg);
        }

        .page-header-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .page-header-row h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
            color: #2b2b2b;
        }

        .page-header-row p {
            margin: 2px 0 0;
            color: #8a8a8a;
            font-size: .875rem;
        }

        #reportrange {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .85rem;
            color: #444;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
        }

        .stat-card {
            background: #fff;
            border-radius: var(--sand-card-radius);
            border: 1px solid #edeef0;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(20, 20, 43, .04);
            transition: transform .15s ease, box-shadow .15s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(20, 20, 43, .08);
        }

        .stat-card .stat-label {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9a9a9a;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #26282c;
            line-height: 1.1;
        }

        .stat-card .stat-sub {
            font-size: .78rem;
            color: #9a9a9a;
            margin-top: 6px;
        }

        .stat-card .stat-sub.up {
            color: var(--sand-accent);
        }

        .stat-card .stat-sub.down {
            color: var(--sand-danger);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            color: #fff;
        }

        .bg-sand {
            background: linear-gradient(135deg, #e0a35c, var(--sand-primary));
        }

        .bg-green {
            background: linear-gradient(135deg, #3ea16b, var(--sand-accent));
        }

        .bg-blue {
            background: linear-gradient(135deg, #4d9be0, var(--sand-info));
        }

        .bg-red {
            background: linear-gradient(135deg, #e8756c, var(--sand-danger));
        }

        .panel-card {
            background: #fff;
            border-radius: var(--sand-card-radius);
            border: 1px solid #edeef0;
            box-shadow: 0 2px 6px rgba(20, 20, 43, .04);
            padding: 22px;
            height: 100%;
        }

        .panel-card .panel-title {
            font-weight: 600;
            font-size: 1rem;
            color: #2b2b2b;
            margin-bottom: 4px;
        }

        .panel-card .panel-subtitle {
            font-size: .8rem;
            color: #9a9a9a;
            margin-bottom: 16px;
        }

        .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #eee;
            text-decoration: none;
            color: #333;
            font-size: .875rem;
            font-weight: 500;
            transition: background .15s ease, border-color .15s ease;
            margin-bottom: 10px;
        }

        .quick-action:hover {
            background: #faf6f0;
            border-color: var(--sand-primary);
            color: var(--sand-primary-dark);
            text-decoration: none;
        }

        .quick-action .qa-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #f3ede3;
            color: var(--sand-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .table-modern thead th {
            border-top: none;
            border-bottom: 1px solid #eee;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9a9a9a;
            font-weight: 600;
        }

        .table-modern td {
            vertical-align: middle;
            font-size: .875rem;
            color: #333;
        }

        .badge-status {
            font-size: .72rem;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .badge-status.pass {
            background: #e6f6ec;
            color: #1f8a4c;
        }

        .badge-status.fail {
            background: #fdeceb;
            color: #cf3f34;
        }

        .badge-status.pending {
            background: #fff4e2;
            color: #b3781c;
        }

        .section-gap {
            margin-bottom: 22px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header-row">
        <div>
            <h2>Sand Production Dashboard</h2>
            <p>Overview of washed sand production, quality, and plant activity</p>
        </div>

        <form class="dashform d-flex align-items-center" method="POST" action="{{ url('sand-prod-dash') }}">
            @csrf
            <input type="hidden" id="start_input" name="start_date" value="{{ $start_date ?? '' }}">
            <input type="hidden" id="end_input" name="end_date" value="{{ $end_date ?? '' }}">
            <div id="reportrange">
                <i class="fa fa-calendar"></i>
                <span>{{ $duration ?? 'Select date range' }}</span>
                <i class="fa fa-caret-down"></i>
            </div>
            <a href="{{ route('sand-productions.create') }}" class="btn btn-sm ms-2"
                style="background:var(--sand-primary); color:#fff; border-radius:10px; padding:8px 16px; font-weight:600;">
                <i class="fa fa-plus"></i>&nbsp; New Production Run
            </a>
        </form>
    </div>

    <div class="row section-gap g-3">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3 mb-lg-0">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Production Runs</div>
                    <div class="stat-value">{{ $totalProductionRuns ?? 0 }}</div>
                    <div class="stat-sub up"><i class="fa fa-arrow-up"></i> this period</div>
                </div>
                <div class="stat-icon bg-sand"><i class="fa fa-industry"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3 mb-lg-0">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Washed Sand (Tons)</div>
                    <div class="stat-value">{{ number_format($totalWashedSandTons ?? 0, 1) }}</div>
                    <div class="stat-sub">total output</div>
                </div>
                <div class="stat-icon bg-blue"><i class="fa fa-tint"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3 mb-lg-0">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Quality Pass Rate</div>
                    <div class="stat-value">{{ number_format($qualityPassRate ?? 0, 1) }}%</div>
                    <div class="stat-sub {{ ($qualityPassRate ?? 0) >= 90 ? 'up' : 'down' }}">
                        <i class="fa fa-{{ ($qualityPassRate ?? 0) >= 90 ? 'check-circle' : 'exclamation-circle' }}"></i>
                        {{ ($qualityPassRate ?? 0) >= 90 ? 'On target' : 'Needs attention' }}
                    </div>
                </div>
                <div class="stat-icon bg-green"><i class="fa fa-check-square"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Active Washing Plants</div>
                    <div class="stat-value">{{ $activeWashingPlants ?? 0 }}</div>
                </div>
                <div class="stat-icon bg-red"><i class="fa fa-cogs"></i></div>
            </div>
        </div>
    </div>

    <div class="row section-gap g-3">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="panel-card">
                <div class="panel-title">Production Trend</div>
                <div class="panel-subtitle">Washed sand output over the selected period</div>
                <canvas id="productionTrendChart" height="110"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel-card">
                <div class="panel-title">Quality Test Breakdown</div>
                <div class="panel-subtitle">Pass / fail / pending results</div>
                <canvas id="qualityBreakdownChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row section-gap g-3">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="panel-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <div class="panel-title">Recent Production Runs</div>
                        <div class="panel-subtitle">Latest entries across all washing plants</div>
                    </div>
                    <a href="{{ url('sand-productions') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern" id="recentProductionsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Plant</th>
                                <th>Batch/Prod Number</th>
                                <th>Output (Tons)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($recentProductions ?? []) as $run)
                                <tr>
                                    <td>{{ optional($run->created_at)->format('d M Y') ?? '-' }}</td>
                                    <td>{{ $run->washingPlant->plant_name ?? '-' }}</td>
                                    <td>{{ $run->pr_no ?? '-' }}</td>
                                    <td>{{ number_format($run->output_quantity ?? 0, 1) }}</td>
                                    <td>
                                        @php $status = strtolower($run->status ?? 'pending'); @endphp
                                        <span class="badge-status {{ $status }}">{{ ucfirst($status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No production runs recorded for
                                        this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel-card">
                <div class="panel-title">Quick Actions</div>
                <div class="panel-subtitle">Jump straight to common tasks</div>

                <a href="{{ route('sand-productions.create') }}" class="quick-action">
                    <span class="qa-icon"><i class="fa fa-plus"></i></span>
                    New Production Run
                </a>
                <a href="{{ url('quality-tests') }}" class="quick-action">
                    <span class="qa-icon"><i class="fa fa-flask"></i></span>
                    Log Quality Test
                </a>
                <a href="{{ url('rm-sourcings') }}" class="quick-action">
                    <span class="qa-icon"><i class="fa fa-truck"></i></span>
                    Raw Material Sourcing
                </a>
                <a href="{{ url('washing-plants') }}" class="quick-action">
                    <span class="qa-icon"><i class="fa fa-industry"></i></span>
                    Manage Washing Plants
                </a>
                <a href="{{ url('storage-locations') }}" class="quick-action">
                    <span class="qa-icon"><i class="fa fa-map-marker"></i></span>
                    Storage Locations
                </a>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#recentProductionsTable').DataTable({
                    paging: true,
                    searching: false,
                    lengthChange: false,
                    info: false,
                    order: []
                });
            }

            var trendLabels = @json($productionTrend['labels'] ?? []);
            var trendValues = @json($productionTrend['values'] ?? []);

            var trendCtx = document.getElementById('productionTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Washed Sand (Tons)',
                            data: trendValues,
                            borderColor: '#d98c3b',
                            backgroundColor: 'rgba(217,140,59,0.12)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                            pointBackgroundColor: '#d98c3b'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f2f2f2'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            var qualityCtx = document.getElementById('qualityBreakdownChart');
            if (qualityCtx) {
                var pass = {{ $qualityBreakdown['pass'] ?? 0 }};
                var fail = {{ $qualityBreakdown['fail'] ?? 0 }};
                var pending = {{ $qualityBreakdown['pending'] ?? 0 }};

                new Chart(qualityCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pass', 'Fail', 'Pending'],
                        datasets: [{
                            data: [pass, fail, pending],
                            backgroundColor: ['#2c7a4b', '#e0483e', '#e0a35c'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
