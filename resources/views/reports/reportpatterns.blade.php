@extends('jqxadmin.app')

@section('title', 'Шаблоны отчетов')
@section('headertitle', 'Менеджер шаблонов отчетов')

@section('content')
    <div id="mainSplitter" >
        <div>
            <div class="row" style="width: 100%; height: 100%">
                <div class="col-md-12" style="display: flex; flex-flow: column; height: 100%">
                    <div class="row">
                        <div class="col-md-12"><h3 style="margin: 10px">Шаблоны отчетов</h3></div>
{{--                        <div class="col-md-8">
                            <button class="btn btn-default navbar-btn" id="ExcelExport" title="Экспорт списка ОЕ в формат MS Excel">
                                <span class='fal fa-download'></span>
                                <i class='fal fa-file-excel'></i>
                            </button>
                        </div>--}}
                    </div>
                    <div class="row" style="flex-grow: 1; flex-shrink: 1; flex-basis: auto">
                        <div class="col-md-12" style="height: 100%; padding: 0 0 0 16px">
                            <div id="patternList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="formContainer">
            <div id="periodPropertiesForm" class="panel panel-default" style="padding-bottom: 3px; width: 90%">
                <div class="panel-heading"><h3>Перечень показателей в шаблоне</h3></div>
                <div class="panel-body">
                    <form id="period" class="form-horizontal" >
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="periodList">Выберите период:</label>
                            <div class="col-sm-5">
                                <div id="periodList"></div>
                            </div>
                            <div class="row" >
                                <div class="col-sm-offset-4 col-sm-6">
                                    <div id="periodSelected"><div class="text-bold text-info" style="margin-left: -80px; margin-top: 10px">Текущий период (по умолчанию): "{{ $last_year->name }}" </div></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Группировать данные по:</label>
                            <div class="col-md-9">
                                <label class="radio-inline"><input type="radio" id="by_territory" name="GroupBy" checked>города, районы</label>
                                <label class="radio-inline"><input type="radio" id="by_legals" name="GroupBy">медицинские организации</label>
                                <label class="radio-inline"><input type="radio" id="by_subjects" name="GroupBy">первичные отчеты</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Показать:</label>
                            <div class="col-md-9">
                                <label class="checkbox-inline"><input type="checkbox" id="ShowINN" name="ShowINN">ИНН</label>
                                <label class="checkbox-inline"><input type="checkbox" id="ShowNodeType" name="ShowNodeType">Тип ОЕ</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" id="edit" class="btn btn-primary">Редактировать шаблон</button>
                                <button type="button" id="perform" class="btn btn-success" data-toggle="modal" data-target="#repProgress">Выполнить отчет</button>
                                <a href="/reports/patterns/create" class="btn btn-info" role="button">Новый шаблон</a>
                                <button type="button" id="delete" class="btn btn-danger">Удалить шаблон</button>
                            </div>
                        </div>
                    </form>
                    <div class="panel panel-default">
                        <div class="panel-heading">Перечень показателей в альбоме</div>
                        <div class="panel-body" id="indexes" style="max-height:400px;overflow: auto;">Выберите шаблон отчета</div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body" id="indexes">
                            <p>Данные в отчете группируются по территориальному принципу. В последней строке приводятся итоговые данные в соответствии со сводными данными
                                по всем подведомственным учреждениям, включая федеральные.</p>
                            <p>Количество населения для расчетов берется из соответствующих строк т. 1000 формы 100</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="repProgress" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Выполнение отчета</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                     data-keyboard="false" style="width:0%" id="progress">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <p>Обрабатывается:</p>
                        </div>
                        <div class="col-sm-8">
                            <p id="ou"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('loadjsscripts')
<script src="{{ asset('/medinfo/admin/reportpatternsadmin.js?v=030') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let patternDataAdapter;
        let periodDataAdapter;
        let url = '/reports/patterns/perform';
        let indexes_url = '/reports/patterns/fetchindexes/';
        let sortorder = 2;
        let group_by = 2;
        let plist = $("#periodList");
        let ilist = $("#patternList");
        let periods = {!! $periods !!};
        let patterns = {!! $patterns  !!};
        let current_period = {{ $last_year->id }};
        initsplitter();
        initdatasources();
        initpatternlist();
        initformcontrols();
        initformactions();
    </script>
@endsection
