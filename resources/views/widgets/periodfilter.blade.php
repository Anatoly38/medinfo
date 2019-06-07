<div id="periodSelector">
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group" style="margin-left: 10px">
                <button class="btn btn-default navbar-btn btn-sm" id="collapseAllPeriods">Свернуть все</button>
                <button class="btn btn-default navbar-btn btn-sm" id="expandAllPeriods">Развернуть все</button>
            </div>
            <div class="btn-group pull-right" style="margin-right: 10px">
                <button class="btn btn-default navbar-btn btn-sm" id="clearAllPeriods">Очистить</button>
                <button class="btn btn-primary navbar-btn btn-sm" id="applyPeriods">Применить</button>
            </div>
        </div>
    </div>
{{--    <div class="row">
        <div class="col-md-12">
            <form class="navbar-form navbar-left">
                <label class="radio-inline periodtype"><input type="radio" name="periodtype" value="0" checked >Все периоды</label>
                <label class="radio-inline periodtype"><input type="radio" name="periodtype" value="1" >Годовые</label>
                <label class="radio-inline periodtype"><input type="radio" name="periodtype" value="3" >Квартальные</label>
                <label class="radio-inline periodtype"><input type="radio" name="periodtype" value="5" >Месячные</label>
            </form>
        </div>
    </div>--}}
    <div class="row">
        <div class="col-md-12">
            <form class="navbar-form navbar-left form-inline" >
                <div class="form-group">
                    <label for="filterYear">Отображать периоды за год:</label>
                    <select class="form-control input-sm" id="filterYear">
                        <option value="allperiods" checked="true">Все периоды</option>
                        @foreach($period_years as $year)
                            <option value="{{$year}}">{{$year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="filterType">по типам:</label>
                    <select class="form-control input-sm" id="filterType">
                        <option value="0" checked="true">Все типы</option>
                        <option value="1">Годовые</option>
                        <option value="3">Квартальные</option>
                        <option value="5">Месячные</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="periodTree"></div>
        </div>
    </div>
</div>