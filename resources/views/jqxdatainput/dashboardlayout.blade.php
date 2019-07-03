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
            overflow: hidden;
        }
        #content {
            height: calc(100vh - 60px);
            margin-top: -20px;
            margin-right: 5px;
        }
    </style>
    <title id="Description">@yield('headertitle')</title>
    {{--<link href="{{ secure_asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />--}}
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">--}}


    <link href="{{ secure_asset('/jqwidgets/styles/jqx.base.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    {{--    <link href="{{ secure_asset('/jqwidgets/styles/jqx.material.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('/jqwidgets/styles/jqx.material-green.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ secure_asset('/jqwidgets/styles/jqx.material-purple.css') }}" rel="stylesheet" type="text/css" />--}}
    {{--<link href="{{ secure_asset('/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">--}}
    <link href="{{ secure_asset('/fa582/css/all.min.css') }}" rel="stylesheet" type="text/css">

{{--@if(config('medinfo.ssl_connection'))
    <link href="{{ secure_asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.base.css?v=005') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.bootstrap.css?v=002') }}" rel="stylesheet" type="text/css" />
--}}{{--    <link href="{{ secure_asset('/jqwidgets/styles/jqx.material.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.material-green.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ secure_asset('/jqwidgets/styles/jqx.material-purple.css') }}" rel="stylesheet" type="text/css" />--}}{{--
    <link href="{{ secure_asset('/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
@else
    <link href="{{ asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/jqwidgets/styles/jqx.base.css?v=005') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/jqwidgets/styles/jqx.bootstrap.css?v=002') }}" rel="stylesheet" type="text/css" />
--}}{{--    <link href="{{ asset('/jqwidgets/styles/jqx.material.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/jqwidgets/styles/jqx.material-green.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/jqwidgets/styles/jqx.material-purple.css') }}" rel="stylesheet" type="text/css" />--}}{{--
    <link href="{{ asset('/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
@endif--}}

@stack('loadcss')
</head>

<body>
    <div class="container-fluid">
        @include('jqxdatainput.top')
        <div id="content" style="display: none">
            @yield('content')
        </div>
        <div id="popups" style="display: none">
            @include('jqxdatainput.notifications')
            @include('jqxdatainput.userprofileedit')
        </div>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
@if(config('medinfo.websocket'))
    <script src="https://js.pusher.com/4.3/pusher.min.js"></script>
@endif

    <script src="{{ secure_asset('/jqwidgets/jqx-all.js?v=006') }}"></script>
    <script src="{{ secure_asset('/jqwidgets/globalization/globalize.js') }}"></script>
    <script src="{{ secure_asset('/jqwidgets/localization.js?v=004') }}"></script>
    <script src="{{ secure_asset('/medinfo/dashboard.js?v=098') }}"></script>
    <script src="{{ secure_asset('/plugins/fullscreen/jquery.fullscreen.js?v=004') }}"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
{{--    <script src="{{ secure_asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>--}}

{{--@if(config('medinfo.ssl_connection'))
    <script src="{{ secure_asset('/jqwidgets/jqx-all.js?v=006') }}"></script>
    <script src="{{ secure_asset('/medinfo/dashboard.js?v=094') }}"></script>
    <script src="{{ secure_asset('/jqwidgets/localization.js?v=003') }}"></script>
    <script src="{{ secure_asset('/plugins/fullscreen/jquery.fullscreen.js?v=004') }}"></script>
    <script src="{{ secure_asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
@else
    <script src="{{ asset('/jqwidgets/jqx-all.js?v=006') }}"></script>
    <script src="{{ asset('/medinfo/dashboard.js?v=094') }}"></script>
    <script src="{{ asset('/jqwidgets/localization.js?v=003') }}"></script>
    <script src="{{ asset('/plugins/fullscreen/jquery.fullscreen.js?v=004') }}"></script>
    <script src="{{ asset('/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
@endif--}}

@stack('loadjsscripts')
<script type="text/javascript">
    var theme = 'bootstrap';
    //var theme = 'material';
    //var theme = 'material-purple';
    //var theme = 'material-green';
    var current_user_id = '{{ $worker->id }}';
    var current_user_role = '{{ $worker->role }}';
    var messagefeed = $("#messageFeed");
    var messagefeedtoggle = $("#messageFeedToggle");
    var messagefeed_readts = 0;
    var pkey = '{{ config('broadcasting.connections.pusher.key') }}';
    var pusher;
    var channel;
    initnotifications();
    var show_websocket_notification = {!! config('medinfo.show_websocket_notification') ? 'true' : 'false' !!};

@if(config('medinfo.websocket'))
    initPusher();
    initMessageFeed();
    initStateChangeChannel();
    initMessageSentChannel();
@endif
    inituserprofilewindow();
    initSendMessage();
    initChangeState();
    initDocumentInfoWindow();
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#content").show();
    });
</script>
@yield('inlinejs')
</body>
</html>
