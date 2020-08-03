<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HGMB</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>

    <body>
        <div class="flex-center position-ref full-height">

                <div class="top-right links">
                    @if (Auth::guard('admin')->check() && Auth::check())
                        <a href="{{ route('user.home') }}">User Home</a>                        
                        <a href="{{ route('admin.home') }}">Admin Home</a>
                    @elseif (Auth::check())
                        <a href="{{ route('user.home') }}">Home</a>
                    @elseif (Auth::guard('admin')->check())
                        <a href="{{ url('/login') }}">User Login</a>
                        <a href="{{ url('/register') }}">User Register</a>
                        <a href="{{ route('admin.home') }}">Admin Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>

            <div class="container mx-auto" style= "width: auto;">
                <div class="row">
                    <div class="card-body">
                        @if (session('status.message'))
                            <div class="alert alert-{{session('status.level')}}" role="alert">
                                {{ session('status.message') }}
                            </div>
                        @endif
                    </div>
                </div>


                <div class="row mx-auto">
                    <div class="title m-b-md">
                        HGMB
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
