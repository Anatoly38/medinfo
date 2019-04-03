@extends('jqxadmin.app')

@section('title', 'Строки и графы отчетных форм')
@section('headertitle', 'Менеджер строк м граф отчетных форм')

@section('content')
    @include('jqxadmin.table_picker')
<div id="mainSplitter" >
    <div>
        <div id="rowList" style="margin: 10px"></div>
        <div id="rowPropertiesForm" class="panel panel-default" style="padding: 3px; width: 100%">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#rowmain"><strong><span style="zoom: 1.1">Редактирование/ввод строки</span></strong></a></li>
                    <li><a data-toggle="tab" href="#rowprops">Свойства</a></li>
                    {{--<li><a data-toggle="tab" href="#menu2">Дополнительные заголовки</a></li>--}}
                </ul>
                <div class="tab-content">
                    <div id="rowmain" class="tab-pane fade in active" style="height: 360px">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="rowform" style="padding-top: 10px" class="form-horizontal" >
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="row_index">Порядковый номер в таблице:</label>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control input-sm" id="row_index">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="row_name">Имя:</label>
                                        <div class="col-md-8">
                                            <textarea rows="3" class="form-control input-sm" id="row_name"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="row_code">Код:</label>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control input-sm" id="row_code">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="row_medstat_code">Код Медстат:</label>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control input-sm" id="row_medstat_code">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="row_medstatnsk_id">Медстат (НСК) Id:</label>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control input-sm" id="row_medstatnsk_id">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-1 col-sm-11">
                                            <div class="checkbox">
                                                <label for="excludedRow">
                                                    <input type="checkbox" id="excludedRow" name="excludedRow" value="1" style="zoom: 1.7">
                                                    <p style="margin-top: 8px">
                                                        <strong>
                                                            Исключена из текущего альбома
                                                            <a href="albums" target="_blank" class="text-primary album-name" title="Изменить текущий альбом">("{{ $album->album_name }}")</a>
                                                        </strong>
                                                    </p>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>
                    <div id="rowprops" class="tab-pane fade" style="height: 360px">
                        <form id="RowProps" style="padding-top: 10px" class="form-horizontal" >
                            <div class="form-group">
                                <div class="col-sm-offset-1 col-sm-11">
                                    <div class="checkbox">
                                        <label for="IsAggregatedRow">
                                            <input type="checkbox" id="IsAggregatedRow" name="IsAggregatedRow" value="1" style="zoom: 1.7">
                                            <p style="margin-top: 8px">
                                                <strong>Итоговая строка</strong>
                                            </p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="aggregatedRowElements" class="row" style="display: none">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="aggregatedRows">Суммируемые сроки:</label>
                                    <div class="col-md-8">
                                        <div id="aggregatedRows"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-2 col-md-10">
                                        <button type="button" id="checkAllRows" class="btn btn-default btn-sm">Выбрать все строки</button>
                                        <button type="button" id="uncheckAllRows" class="btn btn-default btn-sm">Очистить</button>
                                    </div>
                                </div>
                                <input id="rowids" name="rowids" type="hidden" value="">
                                <input id="selectedallrows" name="selectedallrows" type="hidden" value="">
                            </div>
                        </form>
                    </div>
{{--                    <div id="menu2" class="tab-pane fade" style="height: 340px">
                        <h3>Свойства</h3>
                        <p>Дополнительно.</p>
                    </div>--}}
                </div>
        </div>
        <div class="form-group" >
            <div class="col-md-offset-2 col-md-10">
                <button type="button" id="saverow" class="btn btn-primary">Сохранить изменения</button>
                <button type="button" id="insertrow" class="btn btn-success">Вставить новую запись</button>
                <button type="button" id="deleterow" class="btn btn-danger">Удалить запись</button>
                <button type="button" id="row_up" class="btn btn-sm btn-default">Вверх</button>
                <button type="button" id="row_down" class="btn btn-sm btn-default">Вниз</button>
            </div>
        </div>
    </div>
    <div>
        <div id="columnList" style="margin: 10px"></div>
        <div id="columnPropertiesForm" class="panel panel-default" style="padding: 3px; width: 100%">
            <div class="panel-heading"><h3>Редактирование/ввод графы</h3></div>
            <div class="panel-body">
                <form id="columnform" class="form-horizontal" >
                    <div class="form-group">
                        <label class="control-label col-md-3" for="column_index">Порядковый номер в таблице:</label>
                        <div class="col-md-2">
                            <input type="number" class="form-control input-sm" id="column_index">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="column_name">Имя:</label>
                        <div class="col-md-8">
                            <textarea rows="2" class="form-control input-sm" id="column_name"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="column_code">Код:</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control input-sm" id="column_code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="column_type">Тип поля:</label>
                        <div class="col-md-3">
                            <div id="column_type"></div>
                        </div>
                        <div class="col-md-4">
                            <button id="editFormula" type="button" class="btn btn-primary btn-sm" style="display: none">Добавить/изменить формулу расчета</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="field_size">Размер поля (px):</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control input-sm" id="field_size" name="field_size">
                        </div>
                        <label class="control-label col-md-3" for="decimal_count">Знаков после запятой (десятичных):</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control input-sm" id="decimal_count">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="column_medstat_code">Код Медстат:</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control input-sm" id="column_medstat_code">
                        </div>
                        <label class="control-label col-md-3" for="column_medstatnsk_id">Медстат (НСК) Id:</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control input-sm" id="column_medstatnsk_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-1 col-sm-11">
                            <div class="checkbox">
                                <label for="excludedColumn">
                                    <input type="checkbox" id="excludedColumn" name="excludedColumn" value="1" style="zoom: 1.7">
                                    <p style="margin-top: 8px">
                                        <strong>
                                            Исключена из текущего альбома
                                            <a href="albums" target="_blank" class="text-primary album-name" title="Изменить текущий альбом">("{{ $album->album_name }}")</a>
                                        </strong>
                                    </p>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="button" id="savecolumn" class="btn btn-primary">Сохранить изменения</button>
                            <button type="button" id="insertcolumn" class="btn btn-success">Вставить новую запись</button>
                            <button type="button" id="deletecolumn" class="btn btn-danger">Удалить запись</button>
                            <button type="button" id="column_left" class="btn btn-sm btn-default">Влево</button>
                            <button type="button" id="column_right" class="btn btn-sm btn-default">Вправо</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="formulaWindow">
    <div id="FormHeader">
        <span id="headerContainer" style="float: left">Введите/измените формулу для вычисляемой графы</span>
    </div>
    <div>
        <div style="padding: 15px" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-3" for="columnName">Графа:</label>
                <div class="col-md-8">
                    <div id="columnNameId"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3" for="formula">Формула расчета</label>
                <div class="col-md-8">
                    <textarea rows="2" class="form-control" id="formula" placeholder="Введите формулу расчета"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-3 col-md-6">
                    <button type="button" id="saveFormula" class="btn btn-primary">Сохранить</button>
                    <button type="button" id="cancelButton" class="btn btn-danger">Отменить</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('loadjsscripts')
    <script src="{{ asset('/medinfo/admin/tablepicker.js?v=016') }}"></script>
    <script src="{{ asset('/medinfo/admin/rcadmin.js?v=059') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let tableDataAdapter;
        let formsDataAdapter;
        let rowsDataAdapter;
        let columnsDataAdapter;
        let tablesource;
        let rowsource;
        let columnsource;
        let rowfetch_url = '/admin/rc/fetchrows/';
        let rowpropfetch_url = '/admin/rc/fetchrowprops/';
        let columnfetch_url = '/admin/rc/fetchcolumns/';
        let showcolumnformula_url = '/admin/rc/columnformula/show/';
        let updatecolumnformula_url = '/admin/rc/columnformula/update/';
        let storecolumnformula_url = '/admin/rc/columnformula/store/';
        let row_up_url = '/admin/rc/rowup/';
        let row_down_url = '/admin/rc/rowdown/';
        let column_left_url = '/admin/rc/columnleft/';
        let column_right_url = '/admin/rc/columnright/';
        let forms = {!! $forms  !!};
        let columnTypes = {!! $columnTypes !!};
        let rlist = $("#rowList");
        let clist = $("#columnList");
        let current_form = 0;
        let current_table = 0;

        let isRowAggregated = 0;
        let allrows = false;
        let rowids = '';

        initFilterDatasources();
        initsplitter();
        initdatasources();
        initRowList();
        initColumnList();
        initFormTableFilter();
        initButtons();
        initRowActions();
        initColumnActions();
        initOrderControls();
        initColumnFormulaWindow();
        initDropDownRows();
    </script>
@endsection
