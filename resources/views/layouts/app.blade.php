<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <style>
        #ScrollTop
        {
            cursor: pointer;
            position:fixed;
            right:25px;
            bottom:50px;
            border:3px solid #585858;
            background-color:white;
            color:#585858;
            border-radius:100%;
            height:50px;
            width:50px;
            font-size:13px;
            display:none;
            text-align: center;
            visibility: visible;
            opacity: .2;
        }
    </style>
    
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link href="/css/jquery.dataTables.css" rel="stylesheet">
    <link href="/css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="/css/selectize.css" rel="stylesheet" >
    <link href="/css/selectize.bootstrap3.css"  rel="stylesheet" >
    <!-- Scripts -->
   
</head>
<body>
<div class="container" id="element"></div>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @if (Auth::check())
                            <li><a href="{{ url('/home') }}">Dashboard</a></li>
                        @endif
                        @role('admin')
                            <li><a href=" {{ route('authors.index') }}">Penulis</a></li>                            
                            <li><a href=" {{ route('books.index') }}">Buku</a></li>                            
                            <li><a href=" {{ route('members.index') }}">Member</a></li>                            
                            <li><a href=" {{ route('statistics.index') }}">Peminjaman</a></li>                            
                        @endrole
                        @if(Auth::check())
                            <li><a href="{{ url('/settings/profile') }}">Profil</a></li>
                        @endif
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/settings/password') }}"><i class="fa fa-btn fa-lock"></i> Ubah Password</a></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        @include('layouts._flash')
        @yield('content')
    </div>
    
    <a id="ScrollTop" onclick="scrolltotop()">
        <i class="fa fa-caret-up fa-3x"></i>
    <a>

    <!-- Scripts -->
    <script src="/js/jquery-3.2.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
     <script>
        $(document).ready(function(){
            $(window).scroll(function(){
                if ($(window).scrollTop() > 100) {
                    $('#ScrollTop').fadeIn();
                } else {
                    $('#ScrollTop').fadeOut();
                }
            });
        });

        function scrolltotop()
        {
            $('html, body').animate({scrollTop : 0},500);
        }

    </script>

    {{-- datatables --}}
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/dataTables.bootstrap.min.js"></script>
    <script src="/js/custom.js"></script>
    <script src="https://code.createjs.com/createjs-2015.11.26.min.js"></script>
    @yield('scripts')
</body>
</html>
