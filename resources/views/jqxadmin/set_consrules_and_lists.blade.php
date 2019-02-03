@extends('jqxadmin.app')

@section('title', 'Правила рассчета консолидированных форм')
@section('headertitle', 'Правила рассчета консолидированных форм')

@section('content')
    @include('jqxadmin.table_picker')
        <div class="row" style="margin-top: 10px">
            <label class="sr-only"  for="Rule">Правило расчета:</label>
            <div class="col-md-8">
                <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="Rule" spellcheck="false" placeholder="Правило расчета">
            </div>
            <div class="col-md-4">
                <form class="form-inline">
                    <button id="applyrule" type="button" class="btn btn-primary">Применить</button>
                    <button id="clearrule" type="button" class="btn btn-danger">Очистить</button>
                    <button id="recompileRules" type="button" class="btn btn-default"
                            title="Рекомендуется провести если производились изменения в структуре форм и таблиц">
                        Рекомпиляция
                    </button>
                    <button id="refreshGrid" class="btn btn-default" type="button" title="Обновить таблицу"> <span class='fa fa-refresh'></span></button>
                    <div class="checkbox" style="margin-left: 20px" title="Включение/отключение перерисовки таблицы после каждого сохранения правила/списка">
                        <label for="autorefresh"><input type="checkbox" id="autorefresh" name="excludedRow" value="1" checked="checked">
                            Автообновление
                        </label>
                    </div>
                </form>
            </div>
        </div>
        <div class="row" style="margin-top: 10px; margin-bottom: 5px">
            <label class="sr-only"  for="List">Списки МО:</label>
            <div class="col-md-8">
                <textarea class="form-control" id="List" spellcheck="false" style="padding: 5px"></textarea>
            </div>
            <div class="col-md-3">
                <button id="applylist" type="button" class="btn btn-primary">Применить</button>
                <button id="clearlist" type="button" class="btn btn-danger">Очистить</button>
                <button id="recompileLists" type="button" class="btn btn-default"
                        title="Рекомендуется провести если производилось изменение состава списков МО или списки МО переименовывались">
                    Рекомпиляция
                </button>
            </div>
            <div class="col-md-1">
                <div id="Selection"></div>
            </div>
        </div>
    <div class="row" style="margin:0;padding:0">
        <div class="col-md-12" style="margin: 0;padding: 0;"><div id="Grid"></div></div>
    </div>
@endsection

@push('loadjsscripts')
    <script src="{{ asset('/medinfo/admin/consrulesandlists.js?v=030') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let grid = $("#Grid");
        let ruleinput = $("#Rule");
        let listinput = $("#List");
        let selectionlog = $("#Selection");
        let current_row_name_datafield;
        let current_row_number_datafield;
        let selected = [];
        let rules_url = '/admin/consolidation';
        let getscripts_url = '/admin/cons';
        let applyrule_url = '/admin/cons/applyrule';
        let applylist_url = '/admin/cons/applylist';
        let recompilelist_url = '/admin/cons/recompilelist';
        let recompilerule_url = '/admin/cons/recompilerule';
        let fetchlists_url = '/admin/units/fetchlists_w_reserved';
        let cellbeginedit = null;
        let autorefresh = true;
        let initialViewport = $(window).height();
        let topOffset = 245;
        onResizeEventLitener();
        gridEventsInit();
        initactions();
    </script>
    @include('jqxadmin.table_pickerjs')
@endsection
