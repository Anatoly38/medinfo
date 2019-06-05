<div id="periodSelector">
    <div class="row">
        <div class="col-md-12">
            <form class="navbar-form navbar-left">
                <label class="radio-inline"><input type="radio" name="optradio" checked>Все периоды</label>
                <label class="radio-inline"><input type="radio" name="optradio">Годовые</label>
                <label class="radio-inline"><input type="radio" name="optradio">Квартальные</label>
                <label class="radio-inline"><input type="radio" name="optradio">Месячные</label>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="navbar-form navbar-left">
                <div class="form-group">
                    <label for="filterYear">Отображать период за год:</label>
                    <select class="form-control input-sm" id="filterYear">
                        <option value="allperiods">Все периоды</option>
                        @foreach($period_years as $year)
                            <option value="{{$year}}">{{$year }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="btn-group pull-right" style="margin-right: 10px">
                <button class="btn btn-default navbar-btn btn-sm" id="clearAllPeriods">Очистить</button>
                <button class="btn btn-primary navbar-btn btn-sm" id="applyPeriods">Применить</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="periodTree"></div>
        </div>
    </div>
</div>