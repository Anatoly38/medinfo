@extends('jqxadmin.app')

@section('title', 'Отладка функций расчета')
@section('headertitle', 'Отладка функций расчета')

@section('content')
<style>
    pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
    .string { color: green; }
    .number { color: darkorange; }
    .boolean { color: blue; }
    .null { color: magenta; }
    .key { color: red; }
</style>
<div id="formContainer" class="row">
    <div class="col-md-12">
        <div id="propertiesForm" class="panel panel-default" style="padding: 3px; width: 100%">
            <div class="panel-heading"><h3>Сведения для отладки</h3></div>
            <div class="panel-body">
            <form id="form" class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="script">Функция:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="script" value="показатель(Ф30Т3100С1Г15П0/Ф30Т3100С1Г5П0)">
                        <span class="help-block">Для примера расчет работы круглосуточной койки</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="units">Списки юнитов:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="units" value="*">
                        <span class="help-block">По умолчанию - *, т.е. юнит текущего документа + все входящие в состав</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="table">Id таблицы:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="table" value="1031">
                        <span class="help-block">форма 110-фп таблица 1000</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="document">Id документа:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="document" value="23753">
                        <span class="help-block">ф. 32 за 2018 год Уч. б-ца с. Голуметь</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-1 col-md-10">
                        <button type="button" id="run" class="btn btn-primary">Запуск</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="debugInfo" class="panel panel-default">
            <div class="panel-heading"><h3>Результат отладки</h3></div>
            <div class="panel-body">
                <div>Расчитанное значение:</div>
                <pre id="value">
                </pre>
                <div>Таблица значений по юнитам:</div>
                <pre id="valuesByUnits">

                </pre>
                <div>Журнал:</div>
                <pre id="log">
                </pre>
            </div>
        </div>
    </div>
</div>
@endsection

@push('loadjsscripts')
    <script src="{{ asset('/medinfo/admin/debugconsrule.js?v=000') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        initrunbutton();
    </script>
@endsection
