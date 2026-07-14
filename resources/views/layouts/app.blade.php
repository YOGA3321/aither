<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>IoT Dashboard - @yield('page_heading', 'Dashboard')</title>

    {{-- Font dan Ikon --}}
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    {{-- Template Styles --}}
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    {{-- Custom Styles (AgroSmartApp) --}}
    <link href="{{ asset('admin/css/custom-arcalis.css') }}" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js, Chart.js, MQTT -->
    @vite(['resources/js/app.js'])
    
    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        {{-- Sidebar --}}
        <ul class="navbar-nav bg-arcalis-gradient sidebar sidebar-dark accordion" id="accordionSidebar" style="background: linear-gradient(180deg, #3eb2ed 10%, #224abe 100%);">
            {{-- Brand --}}
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-cloud fa-2x"></i>
                </div>
                <div class="sidebar-brand-text mx-3">IoT Aither</div>
            </a>

            <hr class="sidebar-divider my-0">

            {{-- Dashboard --}}
            <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Manajemen</div>

            <li class="nav-item {{ Request::routeIs('devices.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('devices.index') }}">
                    <i class="fas fa-fw fa-microchip"></i>
                    <span>Perangkat IoT</span></a>
            </li>

            <li class="nav-item {{ Request::routeIs('profile.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('profile.index') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Profil Pengguna</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            {{-- Sidebar Toggler --}}
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        {{-- Akhir dari Sidebar --}}

        {{-- Content Wrapper --}}
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                {{-- Topbar --}}
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        {{-- Nav Item - User Information --}}
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->name }}
                                </span>
                                <i class="fas fa-user-circle fa-2x text-gray-400"></i>
                            </a>
                            {{-- Dropdown - User Information --}}
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                
                {{-- Konten Halaman --}}
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            
            {{-- Footer --}}
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>IoT Dashboard &copy; 2026</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Logout Modal --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Siap untuk Keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika Anda siap untuk mengakhiri sesi Anda saat ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Core JavaScript--}}
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
