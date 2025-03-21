<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}"
    style="background: #000000 !important; color: white;">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
        @if(config('adminlte.usermenu_enabled'))
        @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
        @else
        @include('adminlte::partials.navbar.menu-item-logout-link')
        @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if(config('adminlte.right_sidebar'))
        @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>

<style>
    /* Override warna teks navbar menjadi putih */
    .main-header .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .main-header .navbar-nav .nav-link:hover {
        color: #ffffff !important;
    }

    /* Override warna icon menjadi putih */
    .main-header .nav-item i {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .main-header .nav-item:hover i {
        color: #ffffff !important;
    }
</style>