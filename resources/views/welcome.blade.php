<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-image: url("{{asset('images/main.jpg')}}");
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
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

            .m-b-md {
                margin-bottom: 30px;
            }

            .buttonsTagA {
                padding: 7px 15px;
                margin-right: 40px;
                border: 1px solid darkblue;
                font-size: 19px;
                font-weight: bold;
            }
            .colorCustomAndTdN {
                color: #0075ae;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div>
            @php
                //$p = \App\SBlog\Core\BlogApp::get_instance();
                //dump($p->getProperties());
                //dump($p->getProperties()['admin_email']);
            @endphp
        </div>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        @if(Auth::user()->isDisabled())
                            <strong><a href="{{ url('/') }}" class="colorCustomAndTdN buttonsTagA">Main</a></strong>
                        @elseif(Auth::user()->isUser())
                            <strong><a href="{{ url('/user/index') }}" class="colorCustomAndTdN buttonsTagA">Cabinet</a></strong>
                            <strong><a href="{{ url('/') }}" class="colorCustomAndTdN buttonsTagA">Main</a></strong>
                        @elseif(Auth::user()->isVisitor())
                            <strong><a href="{{ url('/') }}" class="colorCustomAndTdN buttonsTagA">Main</a></strong>
                        @elseif(Auth::user()->isAdministrator())
                            <strong><a href="{{ url('/admin/index') }}" class="colorCustomAndTdN buttonsTagA">Admin Panel</a></strong>
                            <strong><a href="{{ url('/') }}" class="colorCustomAndTdN buttonsTagA">Main</a></strong>
                        @endif

                        <strong>
                            <a href="{{ route('logout') }}" class="colorCustomAndTdN buttonsTagA"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit()"
                            >Go away</a>
                        </strong>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                    @else
                        <strong>
                            <a href="{{ route('login') }}" class="colorCustomAndTdN buttonsTagA">Login</a>
                        </strong>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="colorCustomAndTdN buttonsTagA">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

        </div>
    </body>
</html>
