@extends('layouts.map')
@section('main-content')
    <!-- Search Container -->
    {{-- <div id="map-search">
        <input type="text" id="searchPelanggan" placeholder="Cari nama pelanggan..." autocomplete="off" />
        <button id="searchBtn">🔍</button>
    </div> --}}
    <div id="map-search" class="position-relative">
        <input type="text" id="searchPelanggan" class="form-control" placeholder="Search..." autocomplete="off" />
        <span class="bx bx-search-alt"></span>
        <ul id="search-result" class="list-group"></ul>
    </div>


    <div class="map-wrapper">
        <div id="map"></div>
        <div class="map-filter bg-primary position-absolute top-0 start-50 translate-middle-x mt-3 p-2 rounded shadow">
            <select id="filterTarif" class="form-select form-select-sm mb-1">
                <option value="">Semua Tarif</option>
                <option value="R1">R1</option>
                <option value="R2">R2</option>
                <option value="B1">B1</option>
            </select>

            <input id="minDaya" type="number" class="form-control form-control-sm mb-1" placeholder="Min daya (VA)">
            <input id="maxDaya" type="number" class="form-control form-control-sm" placeholder="Max daya (VA)">
        </div>
    </div>

    <!-- Loader Overlay -->
    <div id="loader" class="loader-overlay" style="display: none;">
        <div class="loader-content card shadow" style="width: 400px; max-width: 90vw;">
            <div class="card-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="card-title mb-3">Memuat Data Pelanggan</h5>

                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="progress">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="progressText">0%</span>
                        </div>
                    </div>
                </div>

                <p class="card-text text-muted small mb-0" id="loaderDetail">
                    Mengambil data dari server...
                </p>
                <p class="card-text text-muted small" id="counterText">
                    Memuat: <span id="currentCount">0</span> dari <span id="totalCount">0</span> pelanggan
                </p>
            </div>
        </div>
    </div>




    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1005">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img alt="" class="me-2" height="18">
                <strong class="me-auto"></strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">

            </div>
        </div>
    </div>
@endsection

@push('style')
    <!-- CSS untuk overlay -->
    <style>
        .my-location-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
        }
.clear-route-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
}

        /* ================================
           LEAFLET POPUP SAFE OVERRIDE
           (minimal & aman)
        ================================ */
        .leaflet-popup-content {
            margin: 0;
        }

        .leaflet-popup-content-wrapper {
            padding: 0;
            border-radius: 12px;
            background: transparent;
        }

        .leaflet-popup-tip {
            background: #1f2337;
        }

        /* ================================
           DARK CARD POPUP (SCOPED)
        ================================ */
        .dark-card {
            width: 260px;
            background: #1f2337;
            border-radius: 12px;
            color: #e9ecef;
            font-family: inherit;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        /* Header */
        .dark-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            background: #262b44;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dark-card-header h3 {
            font-size: 14px;
            margin: 0;
            font-weight: 600;
            color: #ffffff;
        }

        .dark-card-header .btn-close {
            background: none;
            border: none;
            color: #adb5bd;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
            padding: 0;
        }

        .dark-card-header .btn-close:hover {
            color: #ffffff;
        }

        /* Body */
        .dark-card-body {
            padding: 12px 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-row .label {
            color: #adb5bd;
        }

        .info-row .value {
            font-weight: 500;
            color: #ffffff;
        }

        /* Address */
        .address {
            margin-top: 10px;
            font-size: 12px;
            color: #ced4da;
            line-height: 1.4;
        }

        /* Footer */
        .dark-card-footer {
            padding: 12px 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dark-card-footer .btn {
            width: 100%;
            font-size: 13px;
            padding: 6px 10px;
            border-radius: 8px;
        }

        /* Primary Button (Skote tone) */
        .dark-card-footer .btn.primary {
            background-color: #556ee6;
            border: none;
            color: #fff;
        }

        .dark-card-footer .btn.primary:hover {
            background-color: #4458c8;
        }

        #search-result {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;

            z-index: 1001;
            max-height: 220px;
            overflow-y: auto;

            display: none;
        }

        #search-result li {
            cursor: pointer;
            font-size: 13px;
        }

        #search-result li:hover,
        #search-result li.active {
            background-color: #f3f4f6;
        }

        #map-search {
            position: absolute;
            top: 15px;
            left: 60px;
            z-index: 1000;
            width: 250px;
        }

        #map-search .bx-search-alt {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #74788d;
            pointer-events: none;
        }

        .search-highlight {
            font-weight: 600;
            color: #0d6efd;
            /* warna primary Skote */
        }

        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        .loader-content {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('script')
    <script type="module" src="{{ asset('js/main.js') }}"></script>
@endpush
