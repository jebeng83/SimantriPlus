<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand-md') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    <div class="{{ config('adminlte.classes_topnav_container', 'container') }}">

        {{-- Navbar brand logo --}}
        @if(config('adminlte.logo_img_xl'))
            @include('adminlte::partials.common.brand-logo-xl')
        @else
            @include('adminlte::partials.common.brand-logo-xs')
        @endif

        {{-- React TopNavbar mount point --}}
        <div id="react-topnav-root" class="flex-1"></div>
        <script>
            window.TOPNAV_LEFT = @json($adminlte->menu('navbar-left'));
            window.TOPNAV_RIGHT = @json($adminlte->menu('navbar-right'));
        </script>

        {{-- Fallback: AdminLTE default menus (hidden after React mounts) --}}
        <div id="topnav-fallback" class="w-100">
            {{-- Navbar toggler button --}}
            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Navbar collapsible menu --}}
            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                {{-- Navbar left links --}}
                <ul class="nav navbar-nav">
                    {{-- Configured left links --}}
                    @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

                    {{-- Custom left links --}}
                    @yield('content_top_nav_left')
                </ul>
            </div>

            {{-- Navbar right links (configured only) --}}
            <ul class="navbar-nav ml-auto order-1 order-md-3 navbar-no-expand">
                {{-- Custom right links --}}
                @yield('content_top_nav_right')

                {{-- Configured right links --}}
                @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')
            </ul>
        </div>

        {{-- Keep AdminLTE user menu & right sidebar toggler (outside fallback so tetap tampil) --}}
        <ul class="navbar-nav ml-auto order-1 order-md-3 navbar-no-expand">
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

    </div>

</nav>
