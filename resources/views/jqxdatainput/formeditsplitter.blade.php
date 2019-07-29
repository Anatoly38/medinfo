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
                        <button class="dropdown btn btn-default navbar-btn " type="button">
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
                        <div class="btn-group" @if (count($formsections) === 0) style="display: none" @endif>
                            <div id="SectionsManager" class="btn btn-default">
                                <div id="FormSections" style="display: none">
                                    <table class="table table-hover">
                                        @foreach($formsections as $formsection)
                                            <tr @if(isset($formsection->section_blocks[0]))
                                                title="Раздел {{ $formsection->section_blocks[0]->blocked ? 'принят' : 'отклонен' }} {{ $formsection->section_blocks[0]->updated_at }} пользователем {{ $formsection->section_blocks[0]->worker->description }}"
                                                class=" {{ $formsection->section_blocks[0]->blocked === true ? 'success' : 'danger' }} "
                                                @else
                                                title="Статус раздела не менялся"
                                                @endif
                                                id="{{ $formsection->id }}"
                                            >
                                                <td>{{ $formsection->section_name }}</td>
                                                @if(isset($formsection->section_blocks[0]))
                                                    <td>
                                                        <button title="Принять" class="btn btn-default blocksection" id="{{ $formsection->id }}" {{ $formsection->section_blocks[0]->blocked ? 'disabled' : '' }}>
                                                            <span class='glyphicon glyphicon-check'></span>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <button title="Отклонить" class="btn btn-default unblocksection" id="{{ $formsection->id }}" {{ $formsection->section_blocks[0]->blocked ? '' : 'disabled' }} >
                                                            <span class='glyphicon glyphicon-remove'></span>
                                                        </button>
                                                    </td>
                                                @else
                                                    <td>
                                                        <button title="Принять" class="btn btn-default blocksection" id="{{ $formsection->id }}">
                                                            <span class='glyphicon glyphicon-check'></span>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <button title="Отклонить" class="btn btn-default unblocksection" id="{{ $formsection->id }}" disabled >
                                                            <span class='glyphicon glyphicon-remove'></span>
                                                        </button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        @yield('tableAggregateButton')
                        @yield('tableConsolidateButton')
                        <form class="navbar-form navbar-right">
                            <div class="input-group" style="margin-right: 5px; z-index: 0">
                                <input type="text" class="form-control" style="width: 130px" id="SearchField" placeholder="Поиск строки">
                                <div class="input-group-btn">
                                    <button class="btn btn-default" id="ClearFilter" type="button">
                                        <i class="glyphicon glyphicon-remove"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        @if ($worker->id === 5)
                        <button class="dropdown btn btn-default navbar-btn " type="button">
                            <span class="dropdown-toggle" data-toggle="dropdown" href="#" title="Настройки редактирования таблицы">
                                <i class="fal fa-cog fa-lg"></i>  <span class="caret"></span>
                            </span>
                            <div id="tableSettings" class="dropdown-menu" style="width: 400px; height:200px; padding: 10px 10px 10px 10px">
                                <div class="row">
                                    <div class="col-md-12"><h4>Настройки редактирования таблицы (wid: {{ $worker->id }})</h4></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            <label><input type="checkbox" id="pageMode" value="pageMode">Включить страничный режим</label>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" id="disableColumnPopovers" value="disableColumnPopovers">Отключить подсказки для граф</label>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" id="disableAutosumm" value="disableAutosumm">Отключить автоматический расчет итогов</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                        @endif
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
                    @yield('additionalTabDiv')
                </div>
            </div>
    </div>
</div>
<div id="TableDataLoader"></div>