<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Pemetaan Pelanggan PLN TELANAIPURA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="role" content="{{ auth()->user()->role }}">

    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    >
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        .page-map .page-content {
            padding: 0 !important;
        }

        .map-wrapper {
            position: relative;
        }

        .page-map #map {
            width: 100%;
            height: calc(100vh - 70px);
            min-height: calc(100vh - 70px);
            margin-top: 50px;
        }

        body.vertical-collpsed {
            min-height: unset !important;
        }

        /* ===============================
   SEARCH WRAPPER
=============================== */
        .search-wrapper {
            position: relative;

        }

        /* ===============================
   close icon
=============================== */
        .clear-icon {
            position: absolute;
            right: 36px;
            /* sebelum icon search */
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #999;
            cursor: pointer;
            display: none;
        }

        .clear-icon:hover {
            color: #556ee6;
        }

        /* ===============================
   INPUT
=============================== */
        .search-input {
            padding-right: 36px;
        }

        /* icon */
        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #74788d;
            pointer-events: none;
        }

        /* ===============================
   DROPDOWN
=============================== */
        .search-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;

            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);

            list-style: none;
            padding: 6px 0;
            margin: 0;

            max-height: 260px;
            overflow-y: auto;

            z-index: 2000;
            display: none;
        }

        /* ===============================
   ITEM
=============================== */
        .search-dropdown li {
            padding: 8px 14px;
            font-size: 13px;
            line-height: 1.4;
            cursor: pointer;
            white-space: nowrap;
        }

        .search-dropdown li:hover,
        .search-dropdown li.active {
            background-color: #f3f4f6;
        }

        /* ===============================
   HIGHLIGHT
=============================== */
        .search-highlight {
            display: inline;
            line-height: inherit;
            vertical-align: baseline;
            color: #556ee6 !important;
        }

        /* ===============================
   APP SEARCH
=============================== */
        .app-search span {
            display: block;
            z-index: 10;
            font-size: 13px;
            line-height: 38px;
            left: 13px;
            top: 0;
        }

        .app-search {
            width: 300px;
            /* atur sesuai kebutuhan */
        }


        .app-search .search-highlight {
            display: inline !important;
            position: static !important;
            line-height: inherit !important;
            z-index: auto !important;
        }




        /* Reset inherited positioning */
        #search-result span,
        #search-result .search-highlight {
            position: static !important;
            display: inline !important;
            float: none !important;
            z-index: auto !important;
            line-height: inherit !important;
        }


        /* ===============================
   MAP FILTER DESIGN
=============================== */
        .map-filter {
            position: absolute !important;
            top: 10px !important;
            left: 11px !important;
            z-index: 998 !important;


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

        /* Primary Button  */
        .dark-card-footer .btn.primary {
            background-color: #556ee6;
            border: none;
            color: #fff;
        }

        .dark-card-footer .btn.primary:hover {
            background-color: #4458c8;
        }

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
               Loader
            ================================ */
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
    @stack('style')
</head>

<body data-sidebar="dark" class="page-map ">
    <!-- Begin page -->
    <div id="layout-wrapper">


        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="#" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ asset('images/logo.svg') }}" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('images/logo-dark.png') }}" alt="" height="17">
                            </span>
                        </a>
                        <a href="#" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="{{ asset('images/logo-light.svg') }}" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('images/logo-light.png') }}" alt="" height="19">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect"
                        id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                    <!-- App Search-->
                    <div class="app-search d-none d-lg-block">
                        <div class="search-wrapper js-map-search">
                            <input class="form-control search-input js-search-input" placeholder="Cari pelanggan..."
                                autocomplete="off">

                            <i class="bx bx-search-alt search-icon"></i>
                            <i class="bx bx-x clear-icon js-clear"></i>

                            <ul class="search-dropdown js-search-result"></ul>
                        </div>
                    </div>

                </div>

                <div class="d-flex">

                    <div class="dropdown d-inline-block d-lg-none ms-2">
                        <button type="button" class="btn header-item noti-icon waves-effect"
                            id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-3">
                            <div class="search-wrapper js-map-search">
                                <input class="form-control search-input js-search-input" placeholder="Cari pelanggan..."
                                    autocomplete="off">

                                <i class="bx bx-search-alt search-icon"></i>
                                <i class="bx bx-x clear-icon js-clear"></i>

                                <ul class="search-dropdown js-search-result"></ul>
                            </div>
                        </div>

                    </div>


                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect"
                            data-bs-toggle="fullscreen">
                            <i class="bx bx-fullscreen"></i>
                        </button>
                    </div>


                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset('images/users/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1"
                                key="t-henry">{{ auth()->user()->nama }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="{{ route('profile') }}"><i
                                    class="bx bx-user font-size-16 align-middle me-1"></i> <span
                                    key="t-profile">Profile</span></a>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                                    class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                                    key="t-logout">Logout</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                @include('layouts.navbar')
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content p-0">
                <div class="map-wrapper">
                    <div id="map"></div>
                    <div class="map-filter">
                        <button class="btn waves-effect waves-light" style="background-color: #262b44; color: #f3f4f6"
                            type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                            aria-controls="offcanvasRight">
                            <i class="bx bx-filter font-size-16 align-middle me-2"></i> Filter
                        </button>

                    </div>
                </div>
            </div>

            <!-- right offcanvas -->
            <div class="offcanvas offcanvas-end " tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel" class="offcanvas-title">Filter</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <!-- FILTER KAMU PINDAHKAN KE SINI -->
                    <select id="filterTarif" class="form-control mb-2 select2">
                        <option value="">Semua Tarif</option>
                        <option value="R1">R1</option>
                        <option value="R2">R2</option>
                        <option value="B1">B1</option>
                    </select>

                    <input id="minDaya" type="number" class="form-control mb-2" placeholder="Min daya (VA)">
                    <input id="maxDaya" type="number" class="form-control" placeholder="Max daya (VA)">
                    <button class="btn btn-secondary w-full mt-2" id="resetFilter">Reset Filter</button>
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
                                    role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                        </div>

                        <p class="card-text text-muted small mb-0" id="loaderDetail">
                            Mengambil data dari server...
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
                        <button type="button" class="btn-close" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                    <div class="toast-body">

                    </div>
                </div>
            </div>
        </div>

        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->


    <!-- JAVASCRIPT -->
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>



    <script src="{{ asset('js/app.js') }}"></script>
    @yield('map-script')

</body>

</html>
