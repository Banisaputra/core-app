<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ url('/') }}">
                <img src="{{ asset('images/logo-kokarhardo.png')}}" class="navbar-brand-img" width="80px" alt="logo-company">
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

    @if (auth()->user()->hasPermission('master'))
            
        <p class="text-muted nav-heading mt-4 mb-1">
            <span>Master Data</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item dropdown">
                @if (auth()->user()->hasPermission('masterSetting'))
                <a href="#access-asign" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-shield fe-16"></i>
                    <span class="ml-3 item-text">Akses Pengguna</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="access-asign">
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('roles.index')}}">
                            <span class="ml-1 item-text">Role</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="#">
                            <span class="ml-1 item-text">Permission</span>
                        </a>
                    </li>
                </ul>
                @endif
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('access.info')}}">
                        <i class="fe fe-shield fe-16"></i>
                        <span class="ml-3 item-text">Role Info</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('position.index')}}">
                        <i class="fe fe-pocket fe-16"></i>
                        <span class="ml-3 item-text">Jabatan</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('members.index')}}">
                        <i class="fe fe-user fe-16"></i>
                        <span class="ml-3 item-text">Anggota</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('devision.index')}}">
                        <i class="fe fe-briefcase fe-16"></i>
                        <span class="ml-3 item-text">Bagian</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('supplier.index')}}">
                        <i class="fe fe-package fe-16"></i>
                        <span class="ml-3 item-text">Supplier</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('category.index')}}">
                        <i class="fe fe-align-left fe-16"></i>
                        <span class="ml-3 item-text">Kategori</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link" href="{{ route('items.index')}}">
                        <i class="fe fe-box fe-16"></i>
                        <span class="ml-3 item-text">Barang</span>
                    </a>
                </li>
                
            </li>
        </ul>
    @endif

    @if (auth()->user()->hasPermission('usaha'))
        
        <p class="text-muted nav-heading mt-4 mb-1">
            <span>Usaha</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('pos.index') }}">
                    <i class="fe fe-shopping-bag fe-16"></i>
                    <span class="ml-3 item-text">Penjualan</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('purchases.index')}}">
                    <i class="fe fe-package fe-16"></i>
                    <span class="ml-3 item-text">Pembelian</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('inv.index') }}">
                    <i class="fe fe-box fe-16"></i>
                    <span class="ml-3 item-text">Inventory</span>
                </a>
            </li>
        </ul>
    @endif

    @if (auth()->user()->hasPermission('koperasi'))
        
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
                <a class="nav-link" href="{{ route('loans.index')}}">
                    <i class="fe fe-arrow-up-circle fe-16"></i>
                    <span class="ml-3 item-text">Pinjaman</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('withdrawals.index')}}">
                    <i class="fe fe-credit-card fe-16"></i>
                    <span class="ml-3 item-text">Penarikan</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('repayments.index')}}">
                    <i class="fe fe-credit-card fe-16"></i>
                    <span class="ml-3 item-text">Pelunasan</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link" href="{{ route('policy.index')}}">
                    <i class="fe fe-settings fe-16"></i>
                    <span class="ml-3 item-text">Pengaturan</span>
                </a>
            </li>
             
        </ul>
    @endif

    @if (auth()->user()->hasPermission('laporan'))
        
        <p class="text-muted nav-heading mt-4 mb-1">
            <span>Laporan</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
            {{-- koperasi --}}
            <li class="nav-item dropdown">
                <a href="#usahaReport" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-shopping-cart fe-16"></i>
                    <span class="ml-3 item-text">Usaha</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="usahaReport">
                    <li class="nav-item">
                        <a class="nav-link pl-3" target="_blank" href="{{ route('reports.deductionPdf')}}">
                            <span class="ml-1 item-text">Laba Rugi</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- koperasi --}}
            <li class="nav-item dropdown">
                <a href="#koperasiReport" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe fe-file-text fe-16"></i>
                    <span class="ml-3 item-text">Koperasi</span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="koperasiReport">
                    <li class="nav-item">
                        <a class="nav-link pl-3" target="_blank" href="{{ route('reports.deductionPdf')}}">
                            <span class="ml-1 item-text">Potong Gaji Anggota</span>
                        </a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link pl-3" href="{{ route('reports.index')}}">
                            <span class="ml-1 item-text">Laporan</span>
                        </a>
                    </li> 
                </ul>
            </li>
        </ul>
    @endif
 
    </nav>
  </aside>