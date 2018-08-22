@extends('jqxadmin.app')

@section('title', 'Отчетные документы')
@section('headertitle', 'Менеджер документов')
@section('local_actions')
{{--<li><a href="#" id="newdocument">Новый документ</a></li>--}}

@endsection

@section('content')
<div id="mainSplitter" class="jqx-widget">
    <div>
        <div id="leftPanel">
            <div>
                <div class="jqx-hideborder jqx-hidescrollbars" id="motabs">
                    <ul>
                        <li style="margin-left: 30px;">Медицинские организации по территориям</li>
                        <li>По группам</li>
                    </ul>
                    <div>
                        <div class="jqx-hideborder" id="moTree"></div>
                    </div>
                    <div>
                        <div class="jqx-hideborder" id="groupTree"></div>
                    </div>
                </div>
            </div>
            <div id="filtertabs">
                <ul>
                    <li style="margin-left: 30px;" class="header-name">Мониторинги/формы</li>
                    <li>Статусы</li>
                    <li>Периоды</li>
                    <li>Типы</li>
                </ul>
                <div>
                    <h4>Мониторинги/Формы</h4>
                    <div id="monTree" style="float: left; margin-right: 30px"></div>
{{--                    <div id="selectedFormBox">
                        <div id="checkAllForms"><span>Выбрать все формы</span></div>
                    </div>--}}
                </div>
                <div>
                    <h4>Статусы</h4>
                    <div id="statesListbox" style="float: left; margin-right: 30px"></div>
                    <div id="checkAllStates"><span>Выбрать все статусы</span></div>
                </div>
                <div>
                    <h4>Периоды</h4>
                    <div id="periodsListbox" style="margin: 10px"></div>
                </div>
                <div>
                    <h4>Типы документов</h4>
                    <div id="dtypesListbox" style="margin: 10px"></div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div id="rightPanel" style="height: 100%">
            <div class="row">
                <div class="col-sm-4"><h4 style="margin-left: 10px">Документы</h4></div>
                <div style="padding-top: 7px" class="col-sm-6" id="checkedDocumentsToolbar">
                    <div style='float: left; margin-right: 4px;' id='statesDropdownList'></div>
                    {{--<input class='jqx-input jqx-widget-content jqx-rc-all' id='changeStates' type='text' value='Сменить статус' style='height: 25px; float: left; width: 150px; margin-left: 10px;' />--}}
                    <i style='height: 14px' class="fa fa-eraser fa-lg" id='eraseData' title="Очистить данные"></i>
                    <i style='height: 14px' class="fa fa-trash-o fa-lg" id='deleteDocuments' title="Удалить документы"></i>
                    <i style='height: 14px' class="fa fa-product-hunt fa-lg" id='protectAggregates' title="Защитить сводный документ"></i>
                    <i style='height: 14px' class="fa fa-calculator fa-lg" id='Сalculate' title="Расчет (консолидация) данных"></i>
                    <i style='height: 14px' class="fa fa-leaf fa-lg" id='ValueEditingLog' title="Журнал изменения данных"></i>
                    <i style='height: 14px' class="fa fa-clone fa-lg" id='CloneDocuments' title="Клонирование документов в новый отчетный период"></i>
                </div>
            </div>
            <div class="row" id="documentList"></div>
        </div>
    </div>
</div>
<div id="newForm">
    <div id="newFormHeader">
        <span id="headerContainer" style="float: left">Новые документы для отмеченных территорий/учреждений</span>
    </div>
    <div>
        <div style="padding: 15px" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectMonitoring">Мониторинг</label>
                <div class="col-sm-6">
                    <div id="selectMonitoring"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectAlbum">Альбом форм</label>
                <div class="col-sm-6">
                    <div id="selectAlbum"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectForm">Формы</label>
                <div class="col-sm-6">
                    <div id="selectForm"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="period">Период</label>
                <div class="col-sm-6">
                    <div id="selectPeriod"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="period">Исходный статус</label>
                <div class="col-sm-6">
                    <div id="selectState"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-8">
                    <div class="checkbox">
                        <label><input type="checkbox" id="selectPrimary"> Первичные</label>
                        <label><input type="checkbox" id="selectAggregate"> Сводные</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="button" id="saveButton" class="btn btn-default">Создать</button>
                    <button type="button" id="cancelButton" class="btn btn-default">Отменить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cloneDocuments">
    <div id="cloneDocumentsHeader">
        <span id="headerContainer" style="float: left">Клонирование документов в новый отчетный период</span>
    </div>
    <div>
        <div style="padding: 15px" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectClonePeriod">Выберите период:</label>
                <div class="col-sm-6">
                    <div id="selectClonePeriod"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectCloneMonitoring">Выберите мониторинг:</label>
                <div class="col-sm-6">
                    <div id="selectCloneMonitoring"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectCloneAlbum">Выберите альбом форм:</label>
                <div class="col-sm-6">
                    <div id="selectCloneAlbum"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="selectCloneState">Исходный статус:</label>
                <div class="col-sm-6">
                    <div id="selectCloneState"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="button" id="doClone" class="btn btn-primary">Клонировать</button>
                    <button type="button" id="cancelClone" class="btn btn-default">Отменить</button>
                </div>
            </div>
            <div class="row">
                <p class="text-info">В новом периоде будет созданы документы в соответствии с выбранными в текущем периоде с теми же основными параметрами:
                    учреждение, тип документа, мониторинг
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('loadjsscripts')
    <script src="{{ asset('/jqwidgets/jqxsplitter.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxtabs.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxdata.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxpanel.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxscrollbar.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxinput.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxbuttons.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxdropdownbutton.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxcheckbox.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxlistbox.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxdropdownlist.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxgrid.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxgrid.filter.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxgrid.columnsresize.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxgrid.selection.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxdatatable.js') }}"></script>
    <script src="{{ asset('/jqwidgets/jqxtreegrid.js') }}"></script>
    <script src="{{ asset('/jqwidgets/localization.js') }}"></script>
    <script src="{{ asset('/medinfo/admin/documentadmin.js?v=021') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let checkeddtypes = {!! $dtype_ids !!};
        let monitorings = {!! $monitorings  !!};
        let albums = {!! $albums  !!};
        let forms = {!! $forms  !!};
        let states = {!! $states !!};
        let periods = {!! $periods !!};
        let dtypes = {!! $dtypes !!};
        let checkedstates = {!! $state_ids !!};
        let checkedperiods = [{!! $period_ids !!}];
        datasources();
        initfilterdatasources();
        initnewdocumentwindow();
        initmotabs();
        initsplitters();
        initmotree();
        initgrouptree();
        initfiltertabs();
        initMonitoringTree();
        initdocumentslist();
        initdocumentactions();
    </script>
@endsection
