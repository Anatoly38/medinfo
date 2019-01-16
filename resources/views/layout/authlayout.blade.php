<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title or  'Medinfo WebAdmin'}}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @if(config('medinfo.ssl_connection'))
        <link href="{{ secure_asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('/adminlte/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('/plugins/iCheck/flat/blue.css') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/adminlte/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/plugins/iCheck/flat/blue.css') }}" rel="stylesheet" type="text/css" />
    @endif
</head>
<body class="hold-transition register-page">
@yield('content')

@if(config('medinfo.ssl_connection'))
    <script src="{{ secure_asset('/plugins/jQuery/jQuery-2.1.3.min.js') }}"></script>
    <script src="{{ secure_asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ secure_asset('/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
@else
    <script src="{{ asset('/plugins/jQuery/jQuery-2.1.3.min.js') }}"></script>
    <script src="{{ asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
@endif
</body>
</html>