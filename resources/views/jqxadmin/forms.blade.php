@extends('jqxadmin.app')

@section('title', 'Формы отчетов')
@section('headertitle', 'Менеджер отчетных форм')

@section('content')
<div id="mainSplitter" >
    <div>
        <div id="formList" style="margin: 10px"></div>
    </div>
    <div id="formContainer">
        <div id="propertiesForm" class="panel panel-default" style="padding: 3px; width: 100%">
            <div class="panel-heading"><h3>Редактирование/ввод отчетной формы</h3></div>
            <div class="panel-body">
                <form id="form" class="form-horizontal" >
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="form_name">Наименование формы:</label>
                        <div class="col-sm-8">
                            <textarea rows="3" class="form-control" id="form_name"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="form_index">Порядковый номер:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="form_index">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="form_code">Код формы:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="form_code">
                            <span class="help-block">Может содержать символы:  цифры 0-9, точка ".", дефис "-", строчные буквы "а-я".</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="relation">Наследуется от (разрез формы):</label>
                        <div class="col-sm-8">
                            <div id="relation"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="medstat_code">Код Медстат МСК:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="medstat_code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="short_ms_code">Сокр. код Медстат:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="short_ms_code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="medstatnsk_id">Код Медстат НСК:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="medstatnsk_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-1 col-md-10">
                            <button type="button" id="save" class="btn btn-primary">Сохранить изменения</button>
                            <button type="button" id="insert" class="btn btn-success">Вставить новую запись</button>
                            <button type="button" id="delete" class="btn btn-danger">Удалить запись</button>
                            <button type="button" id="export" class="btn btn-default">Экспорт</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('loadjsscripts')
    <script src="{{ asset('/medinfo/admin/formadmin.js?v=013') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let formDataAdapter;
        let realformsDataAdapter;
        let fl = $("#formList");
        let realforms = {!! $realforms  !!};
        initsplitter();
        initdatasources();
        initformlist();
        initbuttons();
        initformactions();
    </script>
@endsection
