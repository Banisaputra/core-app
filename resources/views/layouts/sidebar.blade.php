@php
use App\Models\Menu;
$parentMenus = Menu::whereNull('parent_id')->orderBy('order')->get();
@endphp

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

@foreach ($parentMenus as $parent)
    @if(!$parent->permission || auth()->user()->can($parent->permission))
    {{-- <p class="text-muted nav-heading mt-4 mb-1">
        <span>{{ $parent->name }}</span>
    </p> --}}
    <ul class="navbar-nav flex-fill w-100 mb-2">
        @if($parent->children->count())
            <li class="nav-item dropdown"> 
                <a href="{{ $parent->route ? route($parent->route) : '#'.$parent->permission }}"
                    data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                    <i class="fe {{ $parent->icon }} fe-16"></i>
                    <span class="ml-3 item-text"> {{ $parent->name }} </span>
                </a>
                <ul class="collapse list-unstyled pl-4 w-100" id="{{ $parent->permission }}">
                    @foreach ($parent->children as $child)
                    @if(!$child->permission || auth()->user()->can($child->permission))
                        <li class="nav-item">
                            <a class="nav-link pl-3" href="{{ route($child->route) }}"> 
                                <i class="fe {{ $child->icon ?? 'fe-grid' }}"></i>
                                <span class="ml-1 item-text">{{ $child->name }}</span>
                            </a>
                        </li>
                    @endif
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item w-100">
                <a href="{{ $parent->route ? route($parent->route) : "#" }}"
                    class="nav-link">
                    <i class="fe {{ $parent->icon }} fe-16"></i>
                    <span class="ml-3 item-text"> {{ $parent->name }} </span>
                </a>
            </li>
        @endif
    </ul>
    @endif
@endforeach
 
    </nav>
  </aside>

  