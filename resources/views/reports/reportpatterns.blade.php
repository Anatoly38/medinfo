@extends('jqxadmin.app')

@section('title', 'Шаблоны отчетов')
@section('headertitle', 'Менеджер шаблонов отчетов')

@section('content')
    <div id="mainSplitter" >
        <div>
            <div id="patternList" style="margin: 10px"></div>
        </div>
        <div id="formContainer">
            <div id="periodPropertiesForm" class="panel panel-default" style="padding-bottom: 3px; width: 90%">
                <div class="panel-heading"><h3>Перечень показателей в шаблоне</h3></div>
                <div class="panel-body">
                    <form id="period" class="form-horizontal" >
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="periodList">Выберите период:</label>
                            <div class="col-sm-5">
                                <div id="periodList"></div>
                            </div>
                            <div class="row" >
                                <div class="col-sm-offset-4 col-sm-6">
                                    <div id="periodSelected"><div class="text-bold text-info" style="margin-left: -80px; margin-top: 10px">Текущий период (по умолчанию): "{{ $last_year->name }}" </div></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="level">Сортировка:</label>
                            <div class="col-sm-3">
                                <div class="radio">
                                    <label><input type="radio" id="fordigest" name="sortOrder" checked>города, районы, округ</label>
                                </div>
                                <div class="radio">
                                    <label><input type="radio" id="byname" name="sortOrder">по медицинским организациям</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" id="edit" class="btn btn-primary">Редактировать шаблон</button>
                                <button type="button" id="perform" class="btn btn-success">Выполнить отчет</button>
                                <a href="/reports/patterns/create" class="btn btn-info" role="button">Новый шаблон</a>
                                <button type="button" id="delete" class="btn btn-danger">Удалить шаблон</button>
                            </div>
                        </div>
                    </form>
                    <div class="panel panel-default">
                        <div class="panel-heading">Перечень показателей в альбоме</div>
                        <div class="panel-body" id="indexes">Выберите шаблон отчета</div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body" id="indexes">
                            <p>Данные в отчете группируются по территориальному принципу. В последней строке приводятся итоговые данные в соответствии со сводными данными
                                по всем подведомственным учреждениям, включая федеральные.</p>
                            <p>Количество населения для расчетов берется из соответствующих строк т. 1000 формы 100 (имеются только за 2016 год)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('loadjsscripts')
<script src="{{ asset('/jqwidgets/jqxsplitter.js') }}"></script>
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
<script src="{{ asset('/medinfo/admin/reportpatternsadmin.js?v=015') }}"></script>
@endpush

@section('inlinejs')
    @parent
    <script type="text/javascript">
        let patternDataAdapter;
        let periodDataAdapter;
        let url = '/reports/patterns/';
        let sortorder = 2;
        let plist = $("#periodList");
        let ilist = $("#patternList");
        let periods = {!! $periods !!};
        let patterns = {!! $patterns  !!};
        let current_period = {{ $last_year->id }};
        initsplitter();
        initdatasources();
        initpatternlist();
        initformcontrols();
        initformactions();
    </script>
@endsection
