<!-- Sidebar -->
<ul class="navbar-nav bg-primary sidebar sidebar-dark accordion" id="accordionSidebar"
   style="height: 100vh; overflow-y: auto;">

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
            <a class="collapse-item {{ request()->is('ilp/dashboard-sekolah*') ? 'active' : '' }}"
               href="{{ route('ilp.dashboard-sekolah') }}">
               <i class="fas fa-school fa-sm fa-fw mr-2 text-gray-400"></i>Dashboard Sekolah
            </a>
            <a class="collapse-item {{ request()->is('ilp/data-siswa-sekolah*') ? 'active' : '' }}"
               href="{{ route('ilp.data-siswa-sekolah.index') }}">
               <i class="fas fa-graduation-cap fa-sm fa-fw mr-2 text-gray-400"></i>Data Siswa Sekolah
            </a>
         </div>
      </div>
   </li>

   <!-- Nav Item - ePPBGM Menu -->
   <li class="nav-item">
      <a class="nav-link {{ request()->is('anc*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
         data-target="#collapseEPPBGM" aria-expanded="{{ request()->is('anc*') ? 'true' : 'false' }}"
         aria-controls="collapseEPPBGM">
         <i class="fas fa-fw fa-baby"></i>
         <span>ePPBGM</span>
      </a>
      <div id="collapseEPPBGM" class="collapse {{ request()->is('anc*') ? 'show' : '' }}"
         aria-labelledby="headingEPPBGM" data-parent="#accordionSidebar">
         <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Menu ePPBGM:</h6>
            <a class="collapse-item {{ request()->is('anc/data-ibu-hamil*') ? 'active' : '' }}"
               href="{{ route('anc.data-ibu-hamil.index') }}">
               <i class="fas fa-female fa-sm fa-fw mr-2 text-gray-400"></i>Data Ibu Hamil
            </a>
            <a class="collapse-item {{ request()->is('anc/data-balita-sakit*') ? 'active' : '' }}"
               href="{{ route('anc.data-balita-sakit.index') }}">
               <i class="fas fa-child fa-sm fa-fw mr-2 text-gray-400"></i>Data Balita Sakit
            </a>
            <a class="collapse-item {{ request()->is('anc/data-rematri*') ? 'active' : '' }}"
               href="{{ route('anc.data-rematri.index') }}">
               <i class="fas fa-user-friends fa-sm fa-fw mr-2 text-gray-400"></i>Data Rematri
            </a>
            <a class="collapse-item {{ request()->is('anc/data-ibu-nifas*') ? 'active' : '' }}"
               href="{{ route('anc.data-ibu-nifas.index') }}">
               <i class="fas fa-baby-carriage fa-sm fa-fw mr-2 text-gray-400"></i>Data Ibu Nifas
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