<!-- Sidebar -->
<ul class="navbar-nav bg-primary sidebar sidebar-dark accordion" id="accordionSidebar">

   <!-- Sidebar - Brand -->
   <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
      <div class="sidebar-brand-icon">
         <i class="fas fa-hospital"></i>
      </div>
      <div class="sidebar-brand-text mx-3">E-Dokter <sup>RSUD Kerjo</sup></div>
   </a>

   <!-- Divider -->
   <hr class="sidebar-divider my-0">

   <!-- Nav Item - Dashboard -->
   <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/') }}">
         <i class="fas fa-fw fa-tachometer-alt"></i>
         <span>Dashboard</span>
      </a>
   </li>

   <!-- Divider -->
   <hr class="sidebar-divider">

   <!-- Heading -->
   <div class="sidebar-heading">
      Pelayanan
   </div>

   <!-- Nav Item - Rawat Jalan -->
   <li class="nav-item {{ request()->is('ralan*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/ralan') }}">
         <i class="fas fa-fw fa-stethoscope"></i>
         <span>Rawat Jalan</span>
      </a>
   </li>

   <!-- Nav Item - Rawat Inap -->
   <li class="nav-item {{ request()->is('ranap*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/ranap') }}">
         <i class="fas fa-fw fa-procedures"></i>
         <span>Rawat Inap</span>
      </a>
   </li>

   <!-- Nav Item - KYC SATUSEHAT -->
   <li class="nav-item {{ request()->is('kyc*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('kyc.index') }}">
         <i class="fas fa-fw fa-id-card"></i>
         <span>KYC SATUSEHAT</span>
      </a>
   </li>

   <!-- Nav Item - ILP Menu -->
   <li class="nav-item {{ request()->is('ilp*') ? 'active' : '' }}">
      <a class="nav-link {{ request()->is('ilp*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
         data-target="#collapseILP" aria-expanded="{{ request()->is('ilp*') ? 'true' : 'false' }}"
         aria-controls="collapseILP">
         <i class="fas fa-fw fa-heartbeat"></i>
         <span>ILP</span>
      </a>
      <div id="collapseILP" class="collapse {{ request()->is('ilp*') ? 'show' : '' }}" aria-labelledby="headingILP"
         data-parent="#accordionSidebar">
         <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Menu ILP:</h6>
            <a class="collapse-item {{ request()->is('ilp/dashboard*') ? 'active' : '' }}"
               href="{{ route('ilp.dashboard') }}">
               <i class="fas fa-chart-line fa-sm fa-fw mr-2 text-gray-400"></i>Dashboard
            </a>
            <a class="collapse-item {{ request()->is('ilp/pendaftaran*') ? 'active' : '' }}"
               href="{{ route('ilp.pendaftaran') }}">
               <i class="fas fa-user-plus fa-sm fa-fw mr-2 text-gray-400"></i>Pendaftaran
            </a>
            <a class="collapse-item {{ request()->is('ilp/pelayanan*') ? 'active' : '' }}"
               href="{{ route('ilp.pelayanan') }}">
               <i class="fas fa-clipboard-list fa-sm fa-fw mr-2 text-gray-400"></i>Pelayanan
            </a>
            <a class="collapse-item {{ request()->is('ilp/faktor-resiko*') ? 'active' : '' }}"
               href="{{ route('ilp.faktor-resiko') }}">
               <i class="fas fa-flask fa-sm fa-fw mr-2 text-gray-400"></i>Faktor Resiko
            </a>
            <a class="collapse-item {{ request()->is('ilp/sasaran-ckg*') ? 'active' : '' }}"
               href="{{ route('ilp.sasaran-ckg') }}">
               <i class="fas fa-birthday-cake fa-sm fa-fw mr-2 text-gray-400"></i>Sasaran CKG
            </a>
         </div>
      </div>
   </li>

   <!-- Divider -->
   <hr class="sidebar-divider">

   <!-- Heading -->
   <div class="sidebar-heading">
      Lainnya
   </div>

   <!-- Nav Item - Pasien -->
   <li class="nav-item {{ request()->is('data-pasien*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/data-pasien') }}">
         <i class="fas fa-fw fa-user-injured"></i>
         <span>Data Pasien</span>
      </a>
   </li>

   <!-- Nav Item - Logout -->
   <li class="nav-item">
      <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
         <i class="fas fa-fw fa-sign-out-alt"></i>
         <span>Logout</span>
      </a>
   </li>

   <!-- Divider -->
   <hr class="sidebar-divider d-none d-md-block">

   <!-- Sidebar Toggler (Sidebar) -->
   <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
   </div>

</ul>
<!-- End of Sidebar -->