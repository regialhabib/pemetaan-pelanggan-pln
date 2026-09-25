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

    <!-- Leaflet dependencies (Loaded via Vite) -->
    <link href="{{ asset('css/custom.css?v=2') }}" id="custom-map-style" rel="stylesheet" type="text/css" />
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
                                autocomplete="off" spellcheck="false">

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
                                    autocomplete="off" spellcheck="false">

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
                    <!-- Floating Map Action Bar -->
                    <div class="map-floating-bar">
                        <button class="btn btn-filter-floating" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                            <span class="filter-icon-box"><i class="bx bx-filter-alt"></i></span>
                            <span>Filter Pelanggan</span>
                            <span id="activeFilterBadge" class="badge rounded-pill bg-danger d-none">0</span>
                        </button>

                        <button id="heatmapToggleBtn" class="btn btn-filter-floating" type="button">
                            <span class="filter-icon-box"><i class="bx bx-layer"></i></span>
                            <span>Heatmap</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modern Offcanvas Drawer -->
            <div class="offcanvas offcanvas-end modern-offcanvas" tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="offcanvas-icon-wrap">
                            <i class="bx bx-filter-alt font-size-20 text-primary"></i>
                        </div>
                        <div>
                            <h5 id="offcanvasRightLabel" class="offcanvas-title fw-bold mb-0 text-dark">Filter Pelanggan</h5>
                            <small class="text-muted">Saring sebaran titik pada peta</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <!-- Section 1: Golongan Tarif -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase font-size-11 text-muted tracking-wider mb-2">
                                <i class="bx bx-tag me-1 text-primary"></i> Golongan Tarif
                            </label>
                            <div class="tarif-pill-group d-flex flex-wrap gap-2 mb-2" id="tarifPillsContainer">
                                <button type="button" class="btn btn-sm btn-tarif-pill active" data-tarif="">Semua</button>
                                <button type="button" class="btn btn-sm btn-tarif-pill" data-tarif="R1">R1</button>
                                <button type="button" class="btn btn-sm btn-tarif-pill" data-tarif="R2">R2</button>
                                <button type="button" class="btn btn-sm btn-tarif-pill" data-tarif="R3">R3</button>
                                <button type="button" class="btn btn-sm btn-tarif-pill" data-tarif="INDUSTRI">INDUSTRI</button>
                            </div>
                            <!-- Hidden select for programmatic sync -->
                            <select id="filterTarif" class="form-select form-select-sm d-none">
                                <option value="">Semua Tarif</option>
                                <option value="R1">R1</option>
                                <option value="R2">R2</option>
                                <option value="R3">R3</option>
                                <option value="INDUSTRI">INDUSTRI</option>
                            </select>
                        </div>

                        <!-- Section 2: Daya Listrik (VA) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase font-size-11 text-muted tracking-wider mb-2">
                                <i class="bx bx-bolt-circle me-1 text-warning"></i> Kapasitas Daya (VA)
                            </label>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">Min</span>
                                        <input id="minDaya" type="number" class="form-control" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">Max</span>
                                        <input id="maxDaya" type="number" class="form-control" placeholder="Maks">
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Presets -->
                            <small class="text-muted d-block mb-2 font-size-12">Preset Cepat Daya Listrik:</small>
                            <div class="d-flex flex-wrap gap-1" id="dayaPresetsContainer">
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="0" data-max="450">450 VA</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="451" data-max="900">900 VA</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="901" data-max="1300">1.300 VA</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="1301" data-max="2200">2.200 VA</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="2201" data-max="3500">3.500 VA</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="3501" data-max="5500">5.500 VA</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-daya-preset" data-min="5501" data-max="1000000">&gt; 5.500 VA</button>
                            </div>
                        </div>

                        <!-- Section 3: Live Summary Stats Card -->
                        <div class="card bg-light border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted font-size-12">Pelanggan Tampil:</span>
                                    <span id="filteredCustomerCount" class="fw-bold text-primary font-size-14">- / -</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-12">Estimasi Beban Daya:</span>
                                    <span id="filteredTotalDaya" class="fw-bold text-dark font-size-13">- kVA</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Action Buttons -->
                    <div class="pt-3 border-top d-flex gap-2">
                        <button class="btn btn-light w-50 d-flex align-items-center justify-content-center gap-1" id="resetFilter">
                            <i class="bx bx-reset"></i> Reset
                        </button>
                        <button class="btn btn-primary w-50 d-flex align-items-center justify-content-center gap-1" data-bs-dismiss="offcanvas">
                            <i class="bx bx-x"></i> Tutup
                        </button>
                    </div>
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




            <div class="position-fixed end-0 p-3" style="top: 75px; z-index: 1050;">
                <div id="liveToast" class="toast shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
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
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>



    <script src="{{ asset('js/app.js') }}"></script>
    @yield('map-script')

</body>

</html>
