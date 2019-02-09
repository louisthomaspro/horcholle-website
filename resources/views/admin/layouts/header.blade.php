<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">
      HORCHOLLE
    </a>
    {{-- mobile menu button --}}
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item {{ Request::is('*admin/home') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.home') }}">Home</a>
        </li>
        <li class="nav-item dropdown autodropdown">
          <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">
            {{ Auth::user()->name }}
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
            <a href="{{ route('logout') }}" class="dropdown-item"
            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST"
            style="display: none;">
              {{ csrf_field() }}
            </form>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>