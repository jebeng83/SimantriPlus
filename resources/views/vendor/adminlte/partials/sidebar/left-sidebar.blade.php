<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }} bg-gradient-to-b from-slate-950 via-slate-900 to-slate-800 text-white">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
    @include('adminlte::partials.common.brand-logo-xl')
    @else
    @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- React Sidebar mount point --}}
    <div class="sidebar p-2">
        <div id="react-sidebar-root" class="h-full">
            {{-- Fallback menu is now INSIDE the react root. React will replace it. --}}
            <nav id="fallback-sidebar-menu" class="pt-2">
                <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                    data-widget="treeview" role="menu" @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                    @if(!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
                    {{-- Configured sidebar links --}}
                    @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
                </ul>
            </nav>
        </div>
    </div>

    {{-- Expose AdminLTE menu to React --}}
    <script>
        window.ADMIN_MENU = @json($adminlte->menu('sidebar'));
    </script>

</aside>