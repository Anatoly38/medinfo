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