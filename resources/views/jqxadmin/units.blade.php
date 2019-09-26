@extends('jqxadmin.app')

@section('title', 'Территории/медицинские организации')
@section('headertitle', 'Менеджер организационных единиц')

@section('content')
<div id="mainSplitter" >
    <div>
        <div class="row" style="width: 100%; height: 100%">
            <div class="col-md-12" style="display: flex; flex-flow: column; height: 100%">
                <div class="row">
                    <div class="col-md-4"><h3 style="margin: 10px">Организационные единицы</h3></div>
                    <div class="col-md-8">
                        <button class="btn btn-default navbar-btn" id="ExcelExport" title="Экспорт списка ОЕ в формат MS Excel">
                            <span class='fal fa-download'></span>
                            <i class='fal fa-file-excel'></i>
                        </button>
                    </div>
                </div>
                <div class="row" style="flex-grow: 1; flex-shrink: 1; flex-basis: auto">
                    <div class="col-md-12" style="height: 100%; padding: 0 0 0 16px">
                        <div id="unitList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="formContainer">
        <div id="propertiesForm" class="panel panel-default" style="height: 100%">
            <div class="panel-heading"><h3>Редактирование/ввод организационной единицы</h3></div>
            <div class="panel-body" >
                <form id="form" class="form-horizontal" >
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="unit_name">Наименование:</label>
                        <div class="col-sm-8">
                            <textarea rows="3" class="form-control" id="unit_name"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="parent_id">Входит в состав:</label>
                        <div class="col-sm-2">
                            <div id="parent_id" style="padding-left: 12px"></div>
                            <div id="aggregateUnitListContainer">
                                <div id="aggregateUnitList"></div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="unit_code">Код территории/организации:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="unit_code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="inn">Индивидуальный налоговый номер:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="inn">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="node_type">Тип организационной единицы:</label>
                        <div class="col-sm-2">
                            <div id="node_type" style="padding-left: 12px"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="adress">Адрес:</label>
                        <div class="col-sm-8">
                            <textarea rows="3" class="form-control" id="adress"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="report">Первичные отчеты:</label>
                        <div class="col-sm-8">
                            <div id="report"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="aggregate">Сводные отчеты:</label>
                        <div class="col-sm-8">
                            <div id="aggregate"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="blocked">Блокирована:</label>
                        <div class="col-sm-8">
                            <div id="blocked"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="countryside">Сельская местность:</label>
                        <div class="col-sm-8">
                            <div id="countryside"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-1 col-sm-8">
                            <button type="button" id="save" class="btn btn-primary">Сохранить изменения</button>
                            <button type="button" id="insert" class="btn btn-success">Вставить новую запись</button>
                            <button type="button" id="delete" class="btn btn-danger">Удалить запись</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('loadjsscripts')
    <script src="{{ secure_asset('/medinfo/admin/unitadmin.js?v=011') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let rowsDataAdapter;
        let tableDataAdapter;
        let unittypesDataAdapter;
        //let aggregatableDataAdapter;
        let aggregaUnitsDataAdapter;
        let unitTypes = {!! $unit_types !!};
        let aggregatables = {!! $aggregate_units !!};
        let unitlist = $("#unitList");
        let aggrUnitCont = $("#aggregateUnitListContainer");
        let aggrUnitList = $("#aggregateUnitList");
        let unitfetch_url ='/admin/units/fetchunits';
        let aggregateunitfetch_url ='/admin/units/fetchaggregateunits';
        let unitcreate_url ='/admin/units/create';
        let unitupdate_url ='/admin/units/update/';
        let unitdelete_url ='/admin/units/delete/';
        let excelexport_url ='/admin/units/excelexport/';
        let selected_unit = 0;
        let parent_unit = null;
        initAggregateDDList();
        initdropdowns();
        initsplitter();
        initdatasources();
        inittablelist();
        initunitactions();
        initToolbar();
    </script>
@endsection
