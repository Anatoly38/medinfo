<div class="row">
    <div class="col-md-12" style="margin-left: 5px"><h4 id="TableTitle"></h4></div>
</div>
<div class="row" style="margin: 0">
    <div class="col-md-12" id="formEditLayout" >
        <div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group">
                        <div id="TableList" class="btn btn-default" style="margin-top: 8px">
                            <div id="FormTables"></div>
                        </div>
                        <button class="btn btn-default navbar-btn" id="Previous" title="Предыдущая таблица"> <span class='fa fa-arrow-left'></span></button>
                        <button class="btn btn-default navbar-btn" id="Following" title="Следующая таблица"> <span class='fa fa-arrow-right'></span></button>
                    </div>
                    <button class="dropdown btn btn-default navbar-btn" type="button">
                        <span class="dropdown-toggle" data-toggle="dropdown" title="Расчет">
                            <i class="fal fa-calculator fa-lg"></i>  <span class="caret"></span>
                        </span>
                        <ul class="dropdown-menu">
                            <li id="Сalculate"><a href="#">Рассчитать показатели</a></li>
                            @if ($worker->id === 5)
                            <li id="RecalculateAggregates"><a href="#">Пересчитать итоговые строки и графы</a></li>
                            @endif
                        </ul>
                    </button>
                    <button class="btn btn-default navbar-btn" id="ToggleFullscreen" title="Полноэкранный режим"> <span class='glyphicon glyphicon-fullscreen'></span></button>
                    <div class="btn-group">
                        <button class="btn btn-default navbar-btn" id="TableCheck" title="Контроль таблицы внутриформенный"><i>К</i><small>вф</small></button>
                        <button class="btn btn-default navbar-btn" id="IDTableCheck" title="Контроль таблицы межформенный"><i>К</i><small>мф</small></button>
                        <button class="btn btn-default navbar-btn" id="IPTableCheck" title="Контроль таблицы межпериодный"><i>К</i><small>мп</small></button>
                    </div>
                    <button class="btn btn-default navbar-btn" id="FormCheck" title="Контроль формы"><i>К</i><small>формы</small></button>
                    <button class="btn btn-default navbar-btn" id="tableExcelExport" title="Экспорт данных таблицы в MS Excel">
                        <span class='fas fa-download fa-lg' ></span>
                        <span class='fal fa-file-excel fa-lg' ></span>
                    </button>
                    <button class="btn btn-default navbar-btn" id="tableExcelImport" title="Импорт данных таблицы из MS Excel" style="display: none">
                        <span class='fas fa-upload fa-lg' ></span>
                        <span class='fal fa-file-excel fa-lg' ></span>
                    </button>
                    @include('widgets.formsections')
                    @include('widgets.medstatexportbutton')
                    @yield('tableConsolidateButton')
                    @include('widgets.rowsearchbutton')
                    @if ($worker->id === 5)
                        @include('widgets.tablesettingsbutton')
                    @endif
                    @if ($worker->id === 5) @endif
                        @include('widgets.savebutton')

                </div>
            </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="DataGrid"></div>
                    </div>
                </div>
        </div>
        <div>
            <div id="ControlTabs" >
                <ul>
                    <li style="margin-left: 30px;">Контроль таблицы</li>
                    <li>Контроль формы</li>
                    <li>Контроль ячейки</li>
                    @yield('logTabLi')
                    @yield('additionalTabLi')

                </ul>
                <div>
                    <div id="TableControlPanel">
                        <div style="padding: 4px; margin: 4px" id="ProtocolToolbar">
                            <div id="extrabuttons">
                                <div id="showallrule" class="extrabutton" style="float: left"><span>Показать только ошибки</span></div>
                                {{--<a id="togglecontrolscreen" style="margin-left: 2px;" target="_blank" title="Рассширить"><span class='glyphicon glyphicon-resize-full'></span></a>--}}
                                <a id='printtableprotocol' style="margin-left: 6px;" target="_blank" title="Распечатать протокол"><span class='glyphicon glyphicon-print'></span></a>
                            </div>
                        </div>
                        <div style="clear: both"></div>
                        <div style="display: none; margin-left: 10px" id="protocolloader"><h5>Выполнение проверки и загрузка протокола контроля <img src="/jqwidgets/styles/images/loader-small.gif" /></h5></div>
                        <div style="display: none" class="inactual-protocol"><span class='text-danger'>Протокол неактуален (в таблице произведены изменения после его формирования)</span></div>
                        <div style="width: 98%; overflow-y: auto; margin: 5px" id="tableprotocol"></div>
                    </div>
                </div>
                <div>
                    <div id="formControlToolbar" style="padding: 4px; margin: 4px">
                        <div id="fc_extrabuttons">
                            <a id='printformprotocol' style="margin-left: 2px;" target="_blank" title="Распечатать протокол"><span class='glyphicon glyphicon-print'></span></a>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                    <div style="display: none" class="inactual-protocol"><span class='text-danger'>Протокол неактуален (в форме произведены изменения после его формирования)</span></div>
                    <div style="display: none; margin-left: 10px" id="formprotocolloader"><h5>Выполнение проверки и загрузка протокола контроля <img src='/jqwidgets/styles/images/loader-small.gif' /></h5></div>
                    <div style="width: 98%; overflow-y: auto; margin: 5px" id="formprotocol"></div>
                </div>
                <div>
                    <div style="width: 98%; overflow-y: auto; margin: 5px" id="cellprotocol"></div>
                </div>
                @yield('logTabDiv')
                @yield('additionalTabDiv')
            </div>
        </div>
    </div>
</div>
<div id="TableDataLoader"></div>