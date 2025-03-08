@section('adminlte_css')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/adminlte-premium.css') }}">
@yield('css')
@stop

<style>
   :root {
      --blue-primary: #2196F3;
      --blue-secondary: #1976D2;
      --blue-accent: #64B5F6;
      --blue-light: #BBDEFB;
      --blue-gradient: linear-gradient(to right, #2196F3, #1976D2);
   }

   /* Override navbar colors */
   .navbar-primary,
   .bg-primary,
   .navbar-dark,
   .bg-gradient-primary {
      background: var(--blue-gradient) !important;
      border: none;
   }

   /* Override sidebar colors */
   .sidebar-dark-primary,
   .bg-gradient-primary,
   .sidebar {
      background: var(--blue-gradient) !important;
   }

   /* Style sidebar brand area */
   .sidebar-brand {
      background-color: rgba(255, 255, 255, 0.1) !important;
   }

   /* Style sidebar active items */
   .sidebar .nav-item .nav-link.active {
      background-color: rgba(255, 255, 255, 0.2) !important;
   }

   /* Style sidebar items on hover */
   .sidebar .nav-item .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.1) !important;
   }

   /* Style buttons */
   .btn-primary {
      background: var(--blue-primary) !important;
      border-color: var(--blue-primary) !important;
   }

   /* Style dropdown menus */
   .dropdown-item.active,
   .dropdown-item:active {
      background-color: var(--blue-primary) !important;
   }

   /* AdminLTE specific overrides */
   .navbar-primary .dropdown-item.active {
      background-color: var(--blue-primary) !important;
      color: white !important;
   }

   /* Fix for text colors in dark backgrounds */
   .navbar-dark .navbar-nav .nav-link,
   .sidebar-dark-primary .nav-link {
      color: rgba(255, 255, 255, 0.8) !important;
   }

   .navbar-dark .navbar-nav .nav-link:hover,
   .sidebar-dark-primary .nav-link:hover {
      color: white !important;
   }
</style>