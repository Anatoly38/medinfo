@extends('errors.app_error')
@section('headertitle', 'Система находится в режиме обслуживания')

@section('content')
    <div class="container">
        <!-- Jumbotron -->
        <div class="jumbotron">
            <h1><i class="fa fa-frown-o red"></i>Ошибка 503</h1>
            <h2 class="lead">Система находится в режиме обслуживания.</h2>
            <p class="text-info small">Повторите попытку входа позднее.</p>
        </div>
    </div>
@endsection