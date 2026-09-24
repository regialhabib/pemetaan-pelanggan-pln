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
    <!-- Leaflet dependencies (Loaded via Vite) -->
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
            padding-right: 60px;
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
        /* ===============================
           MODERN SEARCH DROPDOWN
        =============================== */
        .search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18);
            list-style: none;
            padding: 6px;
            margin: 0;
            max-height: 380px;
            overflow-y: auto;
            z-index: 2050;
            display: none;
        }

        .search-dropdown li.custom-search-item {
            padding: 9px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        /* PERBAIKAN FATAL: Timpa bawaan Skote yang membuat semua span menjadi hancur (absolute, font 16px, line-height 38px) */
        .app-search .search-dropdown span {
            position: static !important;
            z-index: auto !important;
            font-size: inherit !important;
            line-height: inherit !important;
            color: inherit;
        }

        .search-dropdown li.custom-search-item:last-child {
            border-bottom: none;
        }

        .search-dropdown li.custom-search-item:hover,
        .search-dropdown li.custom-search-item.active {
            background-color: #f1f5f9;
        }

        .search-dropdown .search-empty {
            padding: 10px 14px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .search-highlight {
            display: inline;
            line-height: inherit;
            vertical-align: baseline;
            color: #556ee6 !important;
        }

        .app-search {
            width: 320px;
        }

        .app-search .search-input {
            border-radius: 20px;
            background: #f3f5f8;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .app-search .search-input:focus {
            background: #ffffff;
            border-color: #556ee6;
            box-shadow: 0 0 0 3px rgba(85, 110, 230, 0.15);
        }

        /* ===============================
           MAP FLOATING BAR & FILTER
        =============================== */
        .map-floating-bar {
            position: absolute !important;
            top: 15px !important;
            left: 15px !important;
            z-index: 998 !important;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-filter-floating {
            background: rgba(30, 41, 59, 0.94) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border-radius: 30px !important;
            padding: 8px 18px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transition: background 0.2s ease, box-shadow 0.2s ease !important;
        }

        .btn-filter-floating:hover {
            background: rgba(15, 23, 42, 0.98) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25) !important;
        }

        .filter-icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: inherit;
            font-size: 16px;
        }

        /* ===============================
           MODERN OFFCANVAS DRAWER
        =============================== */
        .modern-offcanvas {
            width: 380px !important;
            max-width: 90vw;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            box-shadow: -10px 0 35px rgba(0, 0, 0, 0.15);
            border: none;
        }

        .offcanvas-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(85, 110, 230, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-tarif-pill {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            font-weight: 500;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .btn-tarif-pill:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-tarif-pill.active {
            background: #556ee6 !important;
            border-color: #556ee6 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 3px 10px rgba(85, 110, 230, 0.35) !important;
        }

        .btn-daya-preset {
            font-size: 11px;
            padding: 3px 9px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            transition: all 0.15s ease;
        }

        .btn-daya-preset:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
            color: #1e293b;
        }

        .btn-daya-preset.active {
            background: #f1b44c !important;
            border-color: #f1b44c !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        /* ================================
           MODERN DARK CARD POPUP (LEAFLET)
        ================================ */
        .dark-card {
            width: 290px;
            background: #1a1e32;
            border-radius: 16px;
            color: #e2e8f0;
            font-family: inherit;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }

        .dark-card-header {
            padding: 14px 16px 10px;
            background: linear-gradient(180deg, #242944 0%, #1a1e32 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dark-card-header .customer-name {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            margin: 4px 0 0;
            letter-spacing: -0.2px;
            line-height: 1.3;
        }

        .idpel-tag {
            font-family: monospace;
            font-size: 11px;
            background: rgba(85, 110, 230, 0.2);
            color: #9bb0fc;
            padding: 2px 7px;
            border-radius: 5px;
            border: 1px solid rgba(85, 110, 230, 0.35);
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-copy-idpel {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 13px;
            padding: 2px 5px;
            margin-right: 28px;
            border-radius: 4px;
            transition: color 0.15s;
        }

        .btn-copy-idpel:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        .dark-card-body {
            padding: 12px 16px;
        }

        .popup-pill-row {
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .popup-pill {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .popup-pill.tarif {
            background: rgba(85, 110, 230, 0.18);
            color: #8da2fb;
            border: 1px solid rgba(85, 110, 230, 0.3);
        }

        .popup-pill.daya {
            background: rgba(241, 180, 76, 0.18);
            color: #f1b44c;
            border: 1px solid rgba(241, 180, 76, 0.3);
        }

        .popup-phone {
            margin-bottom: 8px;
            font-size: 12px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }



        .dark-card .address {
            font-size: 12px;
            color: #cbd5e1;
            line-height: 1.45;
            display: flex;
            gap: 6px;
            margin-top: 4px;
        }

        .dark-card-footer {
            padding: 10px 16px 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            background: #161a2d;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .dark-card-footer .btn {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
        }

        .dark-card-footer .btn.primary {
            background-color: #556ee6;
            border: none;
            color: #fff;
        }

        .dark-card-footer .btn.primary:hover {
            background-color: #4458c8;
        }

        .leaflet-popup-content {
            margin: 0 !important;
            line-height: inherit !important;
        }

        .leaflet-popup-content-wrapper {
            padding: 0 !important;
            border-radius: 16px !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .leaflet-popup-tip {
            background: #1a1e32 !important;
        }

        .leaflet-container a.leaflet-popup-close-button {
            top: 14px !important;
            right: 14px !important;
            color: #94a3b8 !important;
            font-size: 16px !important;
            text-align: center !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            background: transparent !important;
        }

        .leaflet-container a.leaflet-popup-close-button:hover {
            color: #ffffff !important;
        }

        /* ================================
           MODERN FLOATING CONTROLS
        ================================ */
        .my-location-btn, .reset-view-btn {
            width: 38px !important;
            height: 38px !important;
            padding: 0 !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #ffffff !important;
            color: #334155 !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.16) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            transition: all 0.2s ease !important;
            margin-bottom: 8px !important;
            cursor: pointer;
        }

        .my-location-btn:hover, .reset-view-btn:hover {
            background: #f8fafc !important;
            color: #556ee6 !important;
            transform: scale(1.06) !important;
        }

        .clear-route-btn {
            background: #f46a6a !important;
            color: #ffffff !important;
            border: none !important;
            padding: 6px 14px !important;
            border-radius: 20px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            box-shadow: 0 4px 14px rgba(244, 106, 106, 0.35) !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin-bottom: 8px !important;
            cursor: pointer;
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
