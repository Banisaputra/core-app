<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="./index.html">
                <svg version="1.1" id="logo" class="navbar-brand-img brand-sm" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
                    <g>
                        <polygon class="st0" points="78,105 15,105 24,87 87,87"/>
                        <polygon class="st0" points="96,69 33,69 42,51 105,51"/>
                        <polygon class="st0" points="78,33 15,33 24,15 87,15"/>
                    </g>
                </svg>
            </a>
        </div>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="fe fe-home fe-16"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
        </ul>
        <p class="text-muted nav-heading mt-4 mb-1">
            <span>Master & Pengaturan</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item dropdown">
                <a href="#ui-elements" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-user fe-16"></i>
                    <span class="ml-3 item-text">Grup Pengguna</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="ui-elements">
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="#">
                            <span class="ml-1 item-text">Role</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('members.index')}}">
                            <span class="ml-1 item-text">Anggota</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-box fe-16"></i>
                    <span class="ml-3 item-text">Inventori</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="forms">
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Kategori</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Data Barang</span></a>
                    </li>
                </ul>
            </li>
        </ul>
        <p class="text-muted nav-heading mt-4 mb-1">
            <span>Penjualan</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
                <a class="nav-link" href="#">
                    <i class="fe fe-monitor fe-16"></i>
                    <span class="ml-3 item-text">POS</span>
                </a>
            </li>
        </ul>
        
        <p class="text-muted nav-heading mt-4 mb-1">
            <span>Koperasi</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('savings.index')}}">
                    <i class="fe fe-arrow-down-circle fe-16"></i>
                    <span class="ml-3 item-text">Simpanan</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link" href="#">
                    <i class="fe fe-arrow-up-circle fe-16"></i>
                    <span class="ml-3 item-text">Pinjaman</span>
                </a>
            </li>
        </ul>
 
    </nav>
  </aside>