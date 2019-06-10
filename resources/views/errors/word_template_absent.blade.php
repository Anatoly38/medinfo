@extends('errors.app_error')
@section('headertitle', 'Ошибка: не найден шаблон документа в формате MS Word')

@section('content')
    <div class="container">
        <!-- Jumbotron -->
        <div class="jumbotron">
            <h1><i class="fa fa-frown-o red"></i>Ошибка 4004</h1>
            <p class="lead">Шаблон для экспорта документа по форме "{{ $form->form_code }}" в формате <em>MS WORD</em> не найден.</p>
            <p><a onclick="checkSite();" class="btn btn-default btn-lg"><span class="green">Вернуться на начальную страницу</span></a>
                <script type="text/javascript">
                    function checkSite(){
                        var currentSite = window.location.hostname;
                        window.location = "https://" + currentSite;
                    }
                </script>
            </p>
        </div>
    </div>
    <div class="container">
        <div class="body-content">
            <div class="row">
                <div class="col-md-6">
                    <h2>Что случилось?</h2>
                    <p class="lead">Статус ошибки 4004 означает что, файл шаблона необходимый для экспорта данных отчета в формат MS Word отстутствует на сервере.</p>
                </div>
            </div>
        </div>
    </div>
@endsection