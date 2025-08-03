<?php

use Illuminate\Support\Env;

return [


    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => env('NAMA_INSTANSI'),
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => "<b>" . env('APP_NAME') . "</b>",
    'logo_img' => env('LOGO_INSTANSI'),
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'logo',


    'preloader' => [
        'enabled' => false,
        'img' => [
            'path' => 'img/logo/logo.png',
            'alt' => 'RSB Preloader Image',
            'effect' => 'animation__shake',
            'width' => 150,
            'height' => 150,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => false,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => 'bg-primary',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-primary navbar-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => false,
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        //Navbar items:
        [
            'text'        => 'Skrining',
            'url'         => '/skriningbpjs',
            'icon'        => 'fas fa-fw fa-tags',
            'topnav_right' => true,
        ],
        // [
        //     'text'        => 'Award 2025',
        //     'url'         => '/kerjo-award',
        //     'icon'        => 'fas fa-fw fa-book',
        //     'topnav_right' => true,
        // ],
        [
            'type'         => 'navbar-search',
            'text'         => 'search',
            'topnav_right' => true,
        ],
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],
        [
            'text'        => 'Keluar',
            'url'         => '/logout',
            'icon'        => 'fas fa-fw fa-sign-out-alt',
            'topnav_right' => true,
        ],
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text'        => 'Home',
            'url'         => '/home',
            'icon'        => 'fas fa-fw fa-home',
            'label_color' => 'success',
        ],
        [
            'text'        => 'Pasien',
            'url'         => '/data-pasien',
            'icon'        => 'fas fa-fw fa-user',
            'label_color' => 'success',
        ],
        [
            'text'        => 'Registrasi',
            'url'         => '/register',
            'icon'        => 'fas fa-fw fa-book',
            'label_color' => 'success',
        ],
        [
            'text'        => 'Ralan',
            'icon'        => 'fas fa-fw fa-stethoscope',
            'url'         => '/ralan/pasien',
        ],
        [
            'text'       => 'Ranap',
            'icon'       => 'fas fa-fw fa-bed',
            'url'        => '/ranap/pasien',
        ],
        // [
        //     'text'        => 'Master Obat',
        //     'url'         => '/master_obat',
        //     'icon'        => 'fas fa-fw fa-pills',
        //     'label_color' => 'success',
        // ],
        [
            'text'    => 'ILP',
            'icon'    => 'fas fa-fw fa-heartbeat',
            'submenu' => [
                [
                    'text' => 'Dashboard',
                    'url'  => '/ilp/dashboard',
                    'icon' => 'fas fa-fw fa-chart-line',
                ],
                [
                    'text' => 'Faktor Resiko',
                    'url'  => '/ilp/faktor-resiko',
                    'icon' => 'fas fa-fw fa-flask',
                ],
                [
                    'text' => 'Pendaftaran',
                    'url'  => '/ilp/pendaftaran',
                    'icon' => 'fas fa-fw fa-user-plus',
                ],
                [
                    'text' => 'Pelayanan',
                    'url'  => '/ilp/pelayanan',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                ],
                [
                    'text' => 'Sasaran CKG',
                    'url'  => '/ilp/sasaran-ckg',
                    'icon' => 'fas fa-fw fa-birthday-cake',
                ],
                [
                    'text' => 'Pendaftaran CKG',
                    'url'  => '/ilp/pendaftaran-ckg',
                    'icon' => 'fas fa-fw fa-clipboard-check',
                ],
                [
                    'text' => 'Dashboard CKG',
                    'url'  => '/ilp/dashboard-ckg',
                    'icon' => 'fas fa-fw fa-chart-bar',
                ],
                [
                    'text' => 'Data Siswa Sekolah',
                    'url'  => '/ilp/data-siswa-sekolah',
                    'icon' => 'fas fa-fw fa-graduation-cap',
                ],
                [
                    'text' => 'Dashboard Sekolah',
                    'url'  => '/ilp/dashboard-sekolah',
                    'icon' => 'fas fa-fw fa-chart-pie',
                ],
            ],
        ],
        [
            'text'    => 'ePPBGM',
            'icon'    => 'fas fa-fw fa-baby',
            'submenu' => [
                [
                    'text' => 'Data Ibu Hamil',
                    'url'  => '/anc/data-ibu-hamil',
                    'icon' => 'fas fa-fw fa-female',
                ],
                [
                    'text' => 'Data Balita Sakit',
                    'url'  => '/anc/data-balita-sakit',
                    'icon' => 'fas fa-fw fa-child',
                ],
                [
                    'text' => 'Data Rematri',
                    'url'  => '/anc/data-rematri',
                    'icon' => 'fas fa-fw fa-user-friends',
                ],
                [
                    'text' => 'Data Ibu Nifas',
                    'url'  => '/anc/data-ibu-nifas',
                    'icon' => 'fas fa-fw fa-baby-carriage',
                ],
            ],
        ],
        [
            'text'        => 'KYC (Verifikasi SSM)',
            'url'         => '/kyc',
            'icon'        => 'fas fa-fw fa-id-card',
            'label_color' => 'primary',
        ],
        [
            'text'    => 'Antrol BPJS',
            'icon'    => 'fas fa-fw fa-hospital',
            'submenu' => [
                [
                    'text' => 'Pendaftaran Mobile JKN',
                    'url'  => '/antrol-bpjs/pendaftaran-mobile-jkn',
                    'icon' => 'fas fa-fw fa-user-plus',
                ],
                [
                    'text' => 'Referensi Poli HFIS BPJS',
                    'url'  => '/antrol-bpjs/referensi-poli-hfis',
                    'icon' => 'fas fa-fw fa-clinic-medical',
                ],
                [
                    'text' => 'Referensi Dokter HFIS BPJS',
                    'url'  => '/antrol-bpjs/referensi-dokter-hfis',
                    'icon' => 'fas fa-fw fa-user-md',
                ],
            ],
        ],
        [
            'text'    => 'PCare BPJS',
            'icon'    => 'fas fa-fw fa-hospital-alt',
            'submenu' => [
                // [
                //     'text' => 'Pendaftaran',
                //     'url'  => '/pcare/form-pendaftaran',
                //     'icon' => 'fas fa-fw fa-plus-circle',
                // ],
                [
                    'text' => 'Data Pendaftaran',
                    'url'  => '/pcare/data-pendaftaran',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Referensi Poli PCare',
                    'url'  => '/pcare/referensi/poli',
                    'icon' => 'fas fa-fw fa-clinic-medical',
                ],
                [
                    'text' => 'Referensi Dokter PCare',
                    'url'  => '/pcare/referensi/dokter',
                    'icon' => 'fas fa-fw fa-user-md',
                ],
                [
                    'text' => 'Cek Data Peserta By NIK',
                    'url'  => '/pcare/data-peserta-by-nik',
                    'icon' => 'fas fa-fw fa-id-card',
                ],
            ],
        ],
        [
            'text'    => 'WhatsApp Manajemen',
            'icon'    => 'fab fa-fw fa-whatsapp',
            'submenu' => [
                [
                    'text' => 'Node Dashboard',
                    'url'  => '/ilp/whatsapp/node/dashboard',
                    'icon' => 'fas fa-fw fa-server',
                ],
                [
                    'text' => 'Queue Dashboard',
                    'url'  => '/ilp/whatsapp/dashboard',
                    'icon' => 'fas fa-fw fa-list-alt',
                ],
            ],
        ],
        [
            'text'        => 'Keluar',
            'url'         => '/logout',
            'icon'        => 'fas fa-fw fa-sign-out-alt',
            'label_color' => 'success',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'EasyUI' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'js/easyui/themes/default/easyui.css',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'js/easyui/themes/icon.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/easyui/jquery.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/easyui/jquery.easyui.min.js',
                ],
            ]
        ],
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.bootstrap.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js',
                ],
            ],
        ],
        'TempusDominusBs4' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/select2/js/select2.full.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/select2/css/select2.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/sweetalert2/sweetalert2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/sweetalert2/sweetalert2.min.css',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/pace-progress/themes/blue/pace-theme-corner-indicator.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/pace-progress/pace.min.js',
                ],
            ],
        ],
        'BootstrapSelect' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
            ],
        ],
        'BsCustomFileInput' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/bs-custom-file-input/bs-custom-file-input.min.js',
                ],
            ],
        ],
        'EkkoLightBox' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/ekko-lightbox/ekko-lightbox.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/ekko-lightbox/ekko-lightbox.css',
                ],
            ],
        ],
        'JqueryUI' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jquery-ui/jquery-ui.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/jquery-ui/jquery-ui.min.css',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => true,

    /*
    |--------------------------------------------------------------------------
    | CSS Stylesheets
    |--------------------------------------------------------------------------
    |
    | Here you can specify the CSS stylesheets that should be loaded along with
    | AdminLTE. Feel free to add your own styles, or customize the default ones.
    |
    */

    'css' => [
        'css/adminlte-premium.css',
        'css/blue-theme.css',
    ],
];
