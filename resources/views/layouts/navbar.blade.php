 <div id="sidebar-menu">
     <!-- Left Menu Start -->
     <ul class="metismenu list-unstyled" id="side-menu">
         <li class="menu-title" key="t-menu">Menu</li>
         @can('admin')
             <li>
                 <a href="{{ route('dashboard') }}" class=" waves-effect ">
                     <i class="bx bxs-dashboard"></i>
                     <span>Dashboard</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('pelanggan.index') }}" class=" waves-effect ">
                     <i class="bx bxs-user-detail"></i>
                     <span>Data Pelanggan</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('home') }}" class=" waves-effect ">
                     <i class="bx bx-map-alt"></i>
                     <span>Peta</span>
                 </a>
             </li>
             <li>
                 <a href="{{ route('tugas_kunjungan.index') }}" class=" waves-effect ">
                     <i class="bx bx-plus-circle"></i>
                     <span>Tugas Kunjungan</span>
                 </a>
             </li>
         @endcan
         @can('petugas')
              <li>
             <a href="{{ route('tugas_kunjungan.map') }}" class=" waves-effect ">
                 <i class="bx bx-map-alt"></i>
                 <span>Peta</span>
             </a>
         </li>
         @endcan
        
         <li>
             <a href="{{ route('tugas_kunjungan.show') }}" class=" waves-effect ">
                 <i class="bx bx-list-check"></i>
                 <span>Daftar Kunjungan</span>
             </a>
         </li>
        
         <li>
             <a href="{{ route('profile') }}" class=" waves-effect ">
                 <i class="bx bx-user"></i>
                 <span>Profil</span>
             </a>
         </li>
         @can('admin')
              <li>
             <a href="{{ route('laporan') }}" class=" waves-effect ">
               <i class="fas fa-clipboard-list"></i>

                 <span>Laporan</span>
             </a>
         </li>
         @endcan
     </ul>
 </div>
