@section('adminlte_css')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@yield('css')
@stop

<style>
   :root {
      --primary-color: #000000;
      --secondary-color: #000000;
      --accent-color: #444444;
      --light-color: #BBDEFB;
      --gradient: #000000;
   }

   /* Override navbar colors */
   .navbar-primary,
   .bg-primary,
   .navbar-dark,
   .bg-gradient-primary,
   .main-header {
      background: var(--primary-color) !important;
      border: none;
      background-color: var(--primary-color) !important;
   }

   /* Override sidebar colors */
   .sidebar-dark-primary,
   .bg-gradient-primary,
   .sidebar,
   .main-sidebar {
      background: var(--primary-color) !important;
      background-color: var(--primary-color) !important;
      height: 100% !important;
      min-height: 100vh !important;
   }

   /* Force home route navbar to be black */
   body[data-route^="/home"] .main-header,
   body[data-route="home"] .main-header,
   [data-route^="/home"] .main-header,
   [data-route="home"] .main-header {
      background: #000000 !important;
      background-color: #000000 !important;
   }

   /* Force home route sidebar to be black */
   body[data-route^="/home"] .main-sidebar,
   body[data-route="home"] .main-sidebar,
   [data-route^="/home"] .main-sidebar,
   [data-route="home"] .main-sidebar {
      background: #000000 !important;
      background-color: #000000 !important;
      height: 100% !important;
      min-height: 100vh !important;
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
      background: var(--primary-color) !important;
      border-color: var(--primary-color) !important;
   }

   /* Style dropdown menus */
   .dropdown-item.active,
   .dropdown-item:active {
      background-color: var(--primary-color) !important;
   }

   /* AdminLTE specific overrides */
   .navbar-primary .dropdown-item.active {
      background-color: var(--primary-color) !important;
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

   /* Override all dynamic backgrounds */
   .main-header,
   .navbar-dark,
   .sidebar,
   .main-sidebar,
   .sidebar-dark-primary,
   [class*="sidebar-dark-"],
   [class*="navbar-"],
   body .main-header,
   body .main-sidebar {
      background: #000000 !important;
      background-color: #000000 !important;
   }

   /* Override home page menu */
   [data-controller="home"] .main-sidebar,
   [data-controller="dashboard"] .main-sidebar,
   [data-route^="/home"] .main-sidebar,
   [data-route="home"] .main-sidebar {
      background: #000000 !important;
      background-color: #000000 !important;
      height: 100% !important;
      min-height: 100vh !important;
   }

   [data-controller="home"] .main-header,
   [data-controller="dashboard"] .main-header,
   [data-route^="/home"] .main-header,
   [data-route="home"] .main-header {
      background: #000000 !important;
      background-color: #000000 !important;
   }

   /* Override any gradient backgrounds */
   [style*="background-image"],
   [style*="background: linear-gradient"],
   [style*="background-image: linear-gradient"],
   [style*="background:linear-gradient"] {
      background: #000000 !important;
      background-image: none !important;
      background-color: #000000 !important;
   }

   /* Prioritize black styles with !important */
   .main-sidebar,
   .sidebar {
      background: #000000 !important;
      background-color: #000000 !important;
   }

   /* Fix sidebar height untuk semua halaman */
   .wrapper {
      min-height: 100vh !important;
   }

   /* Fix layout fixed sidebar */
   .layout-fixed .wrapper .sidebar {
      height: auto !important;
      min-height: calc(100vh - 3.5rem) !important;
   }

   @supports not (-webkit-touch-callout: none) {
      .layout-fixed .wrapper .sidebar {
         height: auto !important;
         min-height: 100vh !important;
      }
   }

   /* Memperbaiki sidebar yang terpotong */
   .sidebar {
      height: 100vh !important;
      min-height: 100vh !important;
      overflow-y: auto !important;
      padding-bottom: 100px !important;
   }

   .nav-sidebar {
      padding-bottom: 50px !important;
   }

   /* Pastikan main-sidebar full height */
   .main-sidebar {
      position: fixed !important;
      min-height: 100vh !important;
      height: 100% !important;
   }

   /* Menerapkan style secara global untuk konsistensi */
   html,
   body,
   .wrapper,
   .content-wrapper {
      min-height: 100vh;
   }
</style>

<script>
   // Script untuk memastikan sidebar selalu full height di semua halaman
   document.addEventListener('DOMContentLoaded', function() {
      // Pastikan main-sidebar memiliki height yang benar
      const sidebar = document.querySelector('.main-sidebar');
      if (sidebar) {
         sidebar.style.height = '100%';
         sidebar.style.minHeight = '100vh';
         sidebar.style.backgroundColor = '#000000';
         sidebar.style.background = '#000000';
         sidebar.style.backgroundImage = 'none';
      }
      
      // Pastikan .sidebar (child dari main-sidebar) juga memiliki styling yang benar
      const sidebarInner = document.querySelector('.sidebar');
      if (sidebarInner) {
         sidebarInner.style.height = 'auto';
         sidebarInner.style.minHeight = '100%';
         sidebarInner.style.paddingBottom = '100px';
         sidebarInner.style.backgroundColor = '#000000';
         sidebarInner.style.background = '#000000';
      }
      
      // Pastikan header juga hitam
      const header = document.querySelector('.main-header');
      if (header) {
         header.style.backgroundColor = '#000000';
         header.style.background = '#000000';
      }
   });
</script>