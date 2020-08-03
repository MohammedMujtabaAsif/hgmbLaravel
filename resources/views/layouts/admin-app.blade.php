@include('layouts.head')

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                        <a class="nav-item nav-link {{ Route::is('admin.home') ? 'nav-item nav-link active' : '' }}" href="{{ route('admin.home') }}">Home <span class="sr-only">(current)</span></a>

                        <a class="nav-item nav-link {{ Route::is('admin.approvedUsers') ? 'nav-item nav-link active' : '' }}" href="{{ route('admin.approvedUsers') }}">Approved</a>
                        <a class="nav-item nav-link {{ Route::is('admin.unapprovedUsers') ? 'nav-item nav-link active' : '' }}" href="{{ route('admin.unapprovedUsers') }}">Unapproved</a>
                        <a class="nav-item nav-link {{ Route::is('admin.bannedUsers') ? 'nav-item nav-link active' : '' }}" href="{{ route('admin.bannedUsers') }}">Banned</a>

                        <a class="nav-item nav-link {{ Route::is('admin.schedule') ? 'nav-item nav-link active' : '' }}" href="{{ route('admin.schedule') }}">Schedule</a>
                        </div>
                    </div>



                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.login') }}">{{ __('Admin Login') }}</a>
                            </li>

                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Admin: {{Auth::user()->name}} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>                            
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</body>
</html>
