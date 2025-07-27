 <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
     <div class="container-fluid">
        

         <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
             <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                 <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                     aria-expanded="false" aria-haspopup="true">
                     <i class="fa fa-search"></i>
                 </a>
                 <ul class="dropdown-menu dropdown-search animated fadeIn">
                     <form class="navbar-left navbar-form nav-search">
                         <div class="input-group">
                             <input type="text" placeholder="Search ..." class="form-control" />
                         </div>
                     </form>
                 </ul>
             </li>
             
             <li class="nav-item topbar-icon hidden-caret">
                 <a class="nav-link" href="{{ url('/') }}" onclick="window.location.reload();" title="Về trang người dùng">
                     <i class="fas fa-home"></i>
                 </a>
             </li>
            

             <li class="nav-item topbar-user dropdown hidden-caret">
                 <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                     <div class="avatar-sm">
                         <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : '/default-avatar.png' }}"
                             class="avatar-img rounded" />
                     </div>
                     <span class="profile-username">
                         <span class="op-7">Hi,</span>
                         <span class="fw-bold">{{ Auth::user()->name }}</span>
                     </span>
                 </a>
                 <ul class="dropdown-menu dropdown-user animated fadeIn">
                     <div class="dropdown-user-scroll scrollbar-outer">
                         <li>
                             <div class="user-box">
                                 <div class="avatar-lg">
                                     <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : '/default-avatar.png' }}"
                                         class="avatar-img rounded" />
                                 </div>
                                 <div class="u-text">
                                     <h4>{{ Auth::user()->name }}</h4>
                                     <p class="text-muted">{{ Auth::user()->email }}</p>
                                     <a href="{{ route('admin.profile') }}" class="btn btn-xs btn-secondary btn-sm">Hồ sơ</a>
                                 </div>
                             </div>
                         </li>
                         <li>
                             <div class="dropdown-divider"></div>
                             {{-- <a class="dropdown-item" href="#">My Balance</a>
                             <a class="dropdown-item" href="#">Inbox</a>
                             <div class="dropdown-divider"></div>
                             <a class="dropdown-item" href="#">Account Setting</a>
                             <div class="dropdown-divider"></div> --}}
                             <form action="{{ route('logout') }}" method="POST">
                                 @csrf
                                 <button class="dropdown-item" type="submit">Đăng xuất</button>
                             </form>
                         </li>
                     </div>
                 </ul>
             </li>
         </ul>
     </div>
 </nav>
