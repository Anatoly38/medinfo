@extends('jqxdatainput.dashboardlayout')

@section('title', 'Статистические отчетные документы')
@section('headertitle', 'Статистические отчетные документы')

@section('content')
    <div id="mainSplitter">
        <div>
            <div id="filterPanelSplitter" style="padding-top: 10px; margin-bottom: 20px">
                <div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="text-center">Выбор мониторингов/отчетных документов:</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="well well-sm">
                                <div id="monitoringSelector">
                                    <div id="monTree"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="well well-sm">
                                <div id="moSelectorByTerritories"><div id="moTree"></div></div>
                                <div id="moSelectorByGroups"><div class="jqx-hideborder" id="groupTree"></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="well well-sm">
                                @include('widgets.periodfilter')
                                <div id="statusSelector">
                                    <button class="btn btn-default btn-sm" id="checkAllStates">Выбрать все</button>
                                    <button class="btn btn-default btn-sm" id="clearAllStates">Очистить</button>
                                    <button class="btn btn-primary btn-sm" id="applyStatuses">Применить</button>
                                    <div id="statesListbox"></div>
                                    <div class="row">
                                        <div class="col-md-offset-1 col-md-11">
                                            <p class="text-info">Только для первичных документов</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="dataPresenceSelector" style="display: none">
                                    <div id="presence" style="width: 300px">
                                        <button class="btn btn-primary btn-sm" id="applyDataPresence">Применить</button>
                                        <div class="row">
                                            <div class="col-md-12" style="margin-left: 15px">
                                                <div class="radio">
                                                    <label><input type="radio" name="optfilled" id="alldoc">Все документы</label>
                                                </div>
                                                <div class="radio">
                                                    <label><input type="radio" name="optfilled" id="filleddoc">Данные имеются</label>
                                                </div>
                                                <div class="radio">
                                                    <label><input type="radio" name="optfilled" id="emptydoc">Данные отсутствуют</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-offset-1 col-md-11">
                                                <p class="text-info">Только для первичных документов</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-1 col-sm-12">
                            <button class="btn btn-primary" id="clearAllFilters">Очистить фильтры</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="ContentPanel">
            <div class="jqx-hideborder jqx-hidescrollbars" id="documenttabs">
                <ul>
                    <li style="margin-left: 30px;">Отчеты субъектов</li>
                    <li>Сводные отчеты</li>
                    <li>Консолидированные отчеты</li>
                    <li>Последние документы</li>
                </ul>
                <div>
                    <div class="jqx-hideborder jqx-hidescrollbars" style="width: 100%; height: 100%">
                        <div id="DocumentPanelSplitter" style="border-top-style: none">
                            <div class="row">
                                <div class="col-md-12" style="display: flex; flex-flow: column; height: 100%">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3>Первичные отчеты</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="text-info" id="mo_parents_breadcrumb">...</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form class="navbar-form navbar-left">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" style="width: 220px" id="searchUnit" spellcheck="false" placeholder="Медицинская организация">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-default" id="clearFilter" type="button" title="Очистить фильтр">
                                                            <i class="far fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="btn-group">
                                                <button class="btn btn-default navbar-btn" id="editPrimaryDocument" title="Редактировать форму">
                                                    <i class='fa fa-edit'></i>
                                                </button>
                                                <button class="btn btn-default navbar-btn" id="changeDocumentState" title="Изменить статус документа">
                                                    <i class='far fa-tasks fa-lg'></i>
                                                </button>
                                                <button class="btn btn-default navbar-btn" id="commentingDocument" title="Сообщение/комментарий к документу">
                                                    <i class='far fa-comment'></i>
                                                </button>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-default navbar-btn" id="documentWordExport" title="Экспорт в формат MS Word">
                                                    <i class='fal fa-file-word fa-lg'></i>
                                                </button>
                                                <button class="btn btn-default navbar-btn" id="documentExcelExport" title="Экспорт в формат MS Excel">
                                                    <i class='fal fa-file-excel fa-lg'></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-default navbar-btn" id="documentInfo" title="Информация о документе">
                                                <i class='fas fa-info'></i>
                                            </button>
                                            <button class="btn btn-default navbar-btn" id="refreshPrimaryDocumentList" title="Обновить список документов">
                                                <i class="far fa-sync-alt"></i>
                                            </button>
                                            <button type="button" class="btn btn-link pull-right" style="margin-top: 10px">Документов: <span id="totalrecords">0</span></button>
                                        </div>
                                    </div>
                                    <div class="row" style="flex-grow: 1; flex-shrink: 1; flex-basis: auto">
                                        <div class="col-md-12" style="height: 100%; padding-left: 0; padding-right: 1px">
                                            <div id="Documents"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="jqx-hideborder">
                                {{--<div id="DocumentPropertiesSplitter">--}}
                                <div class="row" style="height: 100%">
                                    <div class="col-md-12" style="height: 100%">
                                        <div id="messagesExpander" class="panel panel-default panel" style="display: flex; flex-flow: column; height: 100%">
                                            <div id="messagesTitle" class="panel-heading">Сообщения и комментарии <a href="#" id="openMessagesListWindow"><...></a></div>
                                            <div id="DocumentMessages" class="panel-body" style="flex-grow: 1; flex-shrink: 1; flex-basis: auto; padding: 0; overflow-y: auto" ></div>
                                        </div>
                                    </div>
                                </div>
    {{--                                <div class="jqx-hideborder" >
                                        <div id="auditExpander">
                                            <div>Статус проверки документа <a href="#" id="openAuditionListWindow"><...></a></div>
                                            <div id="DocumentAuditions"></div>
                                        </div>
                                    </div>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="jqx-hideborder jqx-hidescrollbars" style="width: 100%; height: 100%">
                        <h3 style="margin-left: 30px">Сводные отчеты</h3>
                        <div class="col-md-12">
                            <form class="navbar-form navbar-left">
                                <div class="input-group">
                                    <input type="text" class="form-control" style="width: 220px" id="searchAggregateUnit"  placeholder="Территория/МО">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" id="clearAggregateFilter" type="button" title="Очистить фильтр">
                                            <i class="far fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="btn-group">
                                <button class="btn btn-default navbar-btn" id="viewDocument" title="Просмотр документа">
                                    <i class="far fa-eye"></i>
                                </button>
                                <button class="btn btn-default navbar-btn" id="aggregateDocument" title="Выполнить свод">
                                    <i class="far fa-layer-plus fa-lg"></i>
                                </button>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-default navbar-btn" id="aggregateWordExport" title="Экспорт в формат MS Word">
                                    <i class='fal fa-file-word fa-lg'></i>
                                </button>
                                <button class="btn btn-default navbar-btn" id="aggregateExcelExport" title="Экспорт в формат MS Excel">
                                    <i class='fal fa-file-excel fa-lg'></i>
                                </button>
                            </div>
                            <button class="btn btn-default navbar-btn" id="refreshAggregateDocumentList" title="Обновить список документов">
                                <i class="far fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-link pull-right" style="margin-top: 10px">Документов: <span id="totalaggregates">0</span></button>
                        </div>
                        <div id="Aggregates"></div>
                    </div>
                </div>
                <div>
                    <div class="jqx-hideborder jqx-hidescrollbars" style="width: 100%; height: 100%">
                        <h3 style="margin-left: 30px">Консолидированные отчеты</h3>
                        <div id="Consolidates"></div>
                    </div>
                </div>
                <div>
                    <div class="jqx-hideborder jqx-hidescrollbars" style="width: 100%; height: 100%">
                        <h3 style="margin-left: 30px">Последние документы</h3>
                        <div id="Recent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('jqxdatainput.windows')
@endsection

@push('loadcss')
    <link href="{{ secure_asset('/css/medinfodocuments.css?v=001') }}" rel="stylesheet" type="text/css" />
{{--    @if(config('medinfo.ssl_connection'))
        <link href="{{ secure_asset('/css/medinfodocuments.css?v=001') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ asset('/css/medinfodocuments.css?v=001') }}" rel="stylesheet" type="text/css" />
    @endif--}}
@endpush

@push('loadjsscripts')
    <script src="{{ secure_asset('/medinfo/widgets/periods.js?v=004') }}"></script>
    <script src="{{ secure_asset('/medinfo/documentdashboard_v2.js?v=008') }}"></script>
{{--    @if(config('medinfo.ssl_connection'))
        <script src="{{ secure_asset('/medinfo/documentdashboard.js?v=181') }}"></script>
    @else
        <script src="{{ asset('/medinfo/documentdashboard.js?v=181') }}"></script>
    @endif--}}
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let current_user_scope = '{{ $worker_scope }}';
        let audit_permission = {{ $audit_permission ? 'true' : 'false' }};
        let periods = {!! $periods !!};
        let states = {!! $states !!};
        let checkedmf = [{!! $mf->value or '' !!}]; // Выбранные в последнем сеансе мониторинги и формы
        let lasstscope = '{{ is_null($last_scope) ? $worker_scope : $last_scope }}';
        let checkedmonitorings = [{!! $mon_ids->value or '' !!}];
        let checkedforms = [{!! $form_ids->value or '' !!}];
        let checkedstates = [{!! $state_ids->value or '' !!}];
        let checkedperiods = [{!! $period_ids->value or '' !!}];
        let checkedfilled = '{{ $filleddocs->value or '-1' }}';
        let disabled_states = [{!! $disabled_states or '' !!}];
        let filter_mode = {!! $filter_mode->value or 1 !!}; // 1 - по территориям; 2 - по группам
        //let current_top_level_node = '{{ is_null($worker_scope) ? 'null' : $worker_scope }}';
        //let current_top_level_node = {{ is_null($worker_scope) ? 0 : $worker_scope }};
        let current_top_level_node = {{ is_null($last_scope) ? $worker_scope : $last_scope }};
        let current_filter = '&filter_mode=' + filter_mode + '&ou=' + lasstscope + '&states='
            + checkedstates.join() + '&mf=' + checkedmf.join() + '&monitorings=' + checkedmonitorings.join()
            + '&forms=' + checkedforms.join() + '&periods=' + checkedperiods.join() + '&filled=' + checkedfilled;
        var dgridDataAdapter;
        datasources();
        initSplitters();
        initMonitoringTree();
        initGroupTree();
        initMoTree();
        initPeriodTree();
        initStateList();
        initDataPresens();
        initDropdowns();
        initFilterIcons();
        initDocumentSource();
        initdocumentstabs();
        initdocumentproperties();
        //initauditionproperties
        initConsolidates();
        initRecentDocuments();
        initpopupwindows();
        primaryDocToolbar();
        aggregateDocToolbar();
    </script>
@endsection