@extends('layouts.app')

@section('main-content')
    <div class="container-fluid">

        {{-- 🔢 SUMMARY CARDS --}}
        <div class="row">
            <!-- Card 1: Total Pelanggan -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Pelanggan</p>
                                <h4 class="mb-0">{{ $summary['total_pelanggan'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="bx bx-group font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Daya -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Daya (VA)</p>
                                <h4 class="mb-0">{{ number_format($summary['total_daya']) }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="bx bx-bolt-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Jumlah Tarif -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Jumlah Tarif</p>
                                <h4 class="mb-0">{{ $summary['jumlah_tarif'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="bx bx-tag-alt font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Jumlah Kategori Daya -->
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Jumlah Kategori Daya</p>
                                <h4 class="mb-0">{{ $summary['jumlah_wilayah'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="bx bx-layer font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📊 CHART SECTION --}}
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribusi Pelanggan per Tarif</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartTarif"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribusi Pelanggan per Daya</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartWilayah"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <!-- apexcharts -->
    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // 🎨 Bootstrap colors
            const colors = [
                'var(--bs-primary)',
                'var(--bs-success)',
                'var(--bs-warning)',
                'var(--bs-info)'
            ];

            // 🍩 DONUT: Tarif
            new ApexCharts(
                document.querySelector("#chartTarif"), {
                    chart: {
                        type: 'donut',
                        height: 300,
                        events: {
                            dataPointSelection: function(event, chartContext, config) {
                                const tarif = {!! json_encode($tarifChart['labels']) !!}[config.dataPointIndex];
                                window.location.href = `/map?tarif=${tarif}`;
                            }
                        }
                    },
                    series: {!! json_encode($tarifChart['data']) !!},
                    labels: {!! json_encode($tarifChart['labels']) !!},
                    colors: colors,
                    legend: {
                        position: 'bottom'
                    }
                }
            ).render();

            // 📊 BAR: Wilayah
            new ApexCharts(
                document.querySelector("#chartWilayah"), {
                    chart: {
                        type: 'bar',
                        height: 300
                    },
                    series: [{
                        name: 'Pelanggan',
                        data: {!! json_encode($wilayahChart['data']) !!}
                    }],
                    xaxis: {
                        categories: {!! json_encode($wilayahChart['labels']) !!}
                    },
                    colors: ['var(--bs-primary)']
                }
            ).render();

        });
    </script>
@endpush
