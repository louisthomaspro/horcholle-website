<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">
      HORCHOLLE
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item {{ Request::is('*accueil') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('accueil') }}">ACCUEIL</a>
        </li>
        <li class="nav-item {{ Request::is('*activites') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('activites') }}">ACTIVITÉS</a>
        </li>
        <li class="nav-item {{ Request::is('*presentation') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('presentation') }}">PRÉSENTATION</a>
        </li>
        <li class="nav-item dropdown autodropdown {{ Request::is('*realisations*') ? 'active' : '' }}">
          <a class="nav-link dropdown-toggle no-mobile-dropdown" href="{{ route('realisations') }}" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false">
            RÉALISATIONS
          </a>
          <div class="dropdown-menu no-mobile-dropdown" aria-labelledby="navbarDropdown">
            @foreach ($categories_dd as $category_dd)
             <a class="dropdown-item {{ Request::is('*realisations/' . $category_dd->id) ? 'active' : '' }}" href="{{ route('realisations') }}/{{ $category_dd->id }}">{{ $category_dd->filename }}</a>
            @endforeach
          </div>
        </li>
        {{-- <li class="nav-item {{ Request::is('*presse') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('presse') }}">PRESSE</a>
        </li> --}}
        <li class="nav-item {{ Request::is('*contact') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('contact') }}">CONTACT</a>
        </li>
        @auth
          <li class="nav-item dropdown autodropdown">
            <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="{{ url('/admin') }}">
                Admin Panel
              </a>
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
        @endauth
      </ul>
    </div>
  </div>
</nav>