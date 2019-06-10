<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        html,
        body {
            height: 100%;
        }
        #content {
            height: calc(100vh - 80px);
        }
    </style>
    <title id="Description">@yield('headertitle')</title>
    <!-- Bootstrap core CSS -->
    <link href="{{ secure_asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- jQWidgets CSS -->
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.base.css?v=005') }}" rel="stylesheet">
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.bootstrap.css?v=001') }}" rel="stylesheet">
    <link href="{{ secure_asset('/fa582/css/all.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container-fluid" >
    @include('jqxadmin.navbar')
    <div id="content" style="display: none">
        @yield('content')
    </div>
    <div id="popups" style="display: none">
        @include('jqxdatainput.notifications')
        @include('jqxdatainput.confirmpopup')
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
{{--<script src="{{ asset('/plugins/jQuery/jquery-1.12.4.min.js') }}" type="text/javascript" ></script>--}}
<script src="{{ secure_asset('/jqwidgets/jqx-all.js?v=003') }}"></script>
<script src="{{ secure_asset('/jqwidgets/localization.js') }}"></script>
<script src="{{ secure_asset('/medinfo/admin/admin.js?v=004') }}"></script>
<script src="{{ secure_asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
@stack('loadjsscripts')
<script type="text/javascript">
    let theme = 'bootstrap';
    let confirm_action = false;
    let confirmpopup = $('#confirmPopup');
    initnotifications();
    initConfirmWindow();
    $(document).ready(function () {
/*        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content')
            }
        });*/
        $("#content").show();
    });
</script>
@yield('inlinejs')
</body>
</html>
