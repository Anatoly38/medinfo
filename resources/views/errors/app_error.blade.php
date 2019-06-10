<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="Description">@yield('headertitle')</title>
    <!-- Bootstrap core CSS -->
    <link href="{{ secure_asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('/fa582/css/all.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container-fluid" >
    <div id="content" style="display: none">
        @yield('content')
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="{{ secure_asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
@stack('loadjsscripts')
<script type="text/javascript">
    $(document).ready(function () {
        $("#content").show();
    });
</script>
</body>
</html>
