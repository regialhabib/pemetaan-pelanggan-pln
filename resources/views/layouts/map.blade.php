<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Pemetaan Pelanggan PLN TELANAIPURA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
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
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    <style>
        /* =========================
   ICON SIDEBAR (FIX)
========================= */
        .icon-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 64px;
            height: 100vh;
            background: #2a3042;
            /* dark skote */
            z-index: 1030;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* toggle button */
        .icon-sidebar .toggle-btn {
            width: 100%;
            border-radius: 0;
        }

        /* icon menu */
        .icon-sidebar .nav-link {
            color: rgba(255, 255, 255, .45);
            padding: 16px 0;
            text-align: center;
        }

        /* =========================
   MAP
========================= */
        .map-wrapper {
            position: fixed;
            top: 0;
            left: 64px;
            width: calc(100vw - 64px);
            height: 100vh;
            z-index: 1;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        /* SEPARATOR */
        .menu-separator {
            margin: 8px 0 3px;
        }

        .menu-title {
            font-size: 10px;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.6);
        }

        /* =========================
   OFFCANVAS SIDEBAR FULL
========================= */
        .sidebar-full {
            width: 260px;
        }

        /* pastikan semua item center */
        .icon-sidebar .nav {
            align-items: center;
        }

        /* NAV ITEM FULL WIDTH */
        .icon-sidebar .nav-item {
            width: 100%;
        }

        /* NAV LINK & BUTTON CENTER */
        .icon-sidebar .nav-link,
        .icon-sidebar .toggle-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-left: 0;
            padding-right: 0;
            width: 100%;
        }

        /* SEPARATOR CENTER */
        .menu-separator {
            width: 100%;
            text-align: center;
        }

        /* TEXT MENU */
        .menu-title {
            display: block;
            text-align: center;
        }

        .icon-nav .mm-active>.nav-link,
        .icon-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        /* HOVER */
        .nav-link:hover {
            color: #fff;
            cursor: pointer;
        }

        .map-filter {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            /* WAJIB lebih besar dari leaflet */
            background: #fff;
            padding: 8px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            width: 220px;
        }
    </style>
    @stack('style')
</head>

<body data-sidebar="dark" class="sidebar-dark">

    <!-- =========================
     ICON SIDEBAR
========================= -->
    <aside class="icon-sidebar">
        <!-- icons -->
        <ul class="nav flex-column mt-3">
            <li class="nav-item mm-active">
                <button class="nav-link " data-bs-toggle="offcanvas" data-bs-target="#sidebarFull">
                    <i class="bx bx-menu fs-4"></i>
                </button>
            </li>

            <!-- SEPARATOR TEXT -->
            <li class="nav-item menu-separator">
                <span class="menu-title">MENU</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Home">

                    <i class="bx bx-home fs-4"></i>
                </a>
            </li>

            <li class="nav-item mm-active">
                <a class="nav-link active">
                    <i class="bx bx-folder fs-4"></i>
                </a>
            </li>

        </ul>
    </aside>

    <!-- =========================
     MAP
========================= -->
    @yield('main-content')

    <!-- =========================
     OFFCANVAS FULL SIDEBAR
========================= -->
    <div class="offcanvas offcanvas-start bg-primary text-white" tabindex="-1" id="sidebarFull">

        <div class="offcanvas-header">
            <h5 class="mb-0">Menu</h5>
            <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body p-0">

            <ul class="metismenu list-unstyled icon-nav">

                <li class="mm-active" data-menu="dashboard">
                    <a href="#">
                        <i class="bx bx-home-circle me-2"></i>
                        Dashboard
                    </a>
                </li>

                <li data-menu="file">
                    <a href="#">
                        <i class="bx bx-folder me-2"></i>
                        File Manager
                    </a>
                </li>

            </ul>

        </div>
    </div>



    <!-- JAVASCRIPT -->
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>



    {{-- <script src="{{ asset('pages/dashboard.init.js') }}"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('script')
</body>

</html>
