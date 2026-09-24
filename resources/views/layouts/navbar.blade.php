 <div id="sidebar-menu">
     <!-- Left Menu Start -->
     <ul class="metismenu list-unstyled" id="side-menu">
         @can('admin')
             <li class="menu-title" key="t-menu">Utama</li>
             <li>
                 <a href="{{ route('dashboard') }}" class="waves-effect">
                     <i class="bx bxs-dashboard"></i>
                     <span>Dashboard</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('home') }}" class="waves-effect">
                     <i class="bx bx-map-alt"></i>
                     <span>Peta Pelanggan</span>
                 </a>
             </li>

             <li class="menu-title" key="t-menu">Manajemen Data</li>
             <li>
                 <a href="{{ route('pelanggan.index') }}" class="waves-effect">
                     <i class="bx bxs-user-detail"></i>
                     <span>Data Master Pelanggan</span>
                 </a>
             </li>

             <li class="menu-title" key="t-menu">Penugasan & Lapangan</li>
             <li>
                 <a href="{{ route('tugas_kunjungan.index') }}" class="waves-effect">
                     <i class="bx bx-plus-circle"></i>
                     <span>Penugasan Baru</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('tugas_kunjungan.show') }}" class="waves-effect">
                     <i class="bx bx-list-check"></i>
                     <span>Status Penugasan</span>
                 </a>
             </li>

             <li class="menu-title" key="t-menu">Administrasi</li>
             <li>
                 <a href="{{ route('laporan') }}" class="waves-effect">
                     <i class="fas fa-clipboard-list"></i>
                     <span>Laporan Kunjungan</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('profile') }}" class="waves-effect">
                     <i class="bx bx-user"></i>
                     <span>Profil Akun</span>
                 </a>
             </li>
         @endcan

         @can('petugas')
             <li class="menu-title" key="t-menu">Operasional Lapangan</li>
             <li>
                 <a href="{{ route('tugas_kunjungan.map') }}" class="waves-effect">
                     <i class="bx bx-map-alt"></i>
                     <span>Peta Rute & Navigasi</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('tugas_kunjungan.show') }}" class="waves-effect">
                     <i class="bx bx-list-check"></i>
                     <span>Daftar Tugas Saya</span>
                 </a>
             </li>
             
             <li class="menu-title" key="t-menu">Akun</li>
             <li>
                 <a href="{{ route('profile') }}" class="waves-effect">
                     <i class="bx bx-user"></i>
                     <span>Profil Pribadi</span>
                 </a>
             </li>
         @endcan
     </ul>
 </div>
