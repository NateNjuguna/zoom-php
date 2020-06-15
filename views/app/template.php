<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A lightweight and fast PHP MVC framework">
    <meta name="author" content="Nate Njuguna">
    <title>{{ config('app.name') }} | @('auth.title.head')@</title>
    @yield('page_css')
    @yield('extra_css')
</head>
<body style="text-align:center;">

    <h3>Zoom-PHP</h3>
    <p>A lightweight and fast PHP MVC framework</p>
    
    <!-- content -->
    @yield('content')
    <!-- /content -->

    <p class="copyright text-thin text-muted"> &copy; {{ date('Y') . ' ' . config('app.name') }} <span>•</span> @('auth.message.copyright')@</p>
    
    <!-- modals -->
    @yield('modals')
    <!-- /modals -->

    <!-- scripts -->
    @yield('page_js')
    @yield('extra_js')
    <!-- /scripts -->
</body>
</html>