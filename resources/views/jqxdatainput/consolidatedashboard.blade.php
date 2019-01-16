@extends('jqxdatainput.dashboardlayout')

@section('title')
    <p class="text-info small">
        Форма №<span class="text-info">{{ $form->form_code  }} </span>
        <i class="fa fa-hospital-o fa-lg"></i>
        <span class="text-info" title="{{ $current_unit->unit_name ? $current_unit->unit_name : $current_unit->group_name }}">
            {{ str_limit($current_unit->unit_name ? $current_unit->unit_name : $current_unit->group_name, 60) }}
        </span>
        <i class="fa fa-map-o fa-lg"></i> <span class="text-info">{{ $monitoring->name }} </span>
        <i class="fa fa-calendar-o fa-lg"></i> <span class="text-info">{{ $period->name }} </span>
        <i class="fa fa-star fa-lg"></i> <span class="text-info">{{ $statelabel }} </span>
        <i class="fa fa-edit fa-lg"></i> <span class="text-info">{{ $editmode }} </span>
    </p>
@endsection

@section('rp-open')
    <li class="pull-right">
        <a href="#">
            <span class="text-right text-info pull-right" id="rp-open" title="Открыть боковую панель"><i style="font-size: 1.5em" class="fa fa-align-justify"></i></span>
        </a>
    </li>
@endsection

@section('headertitle', 'Просмотр/редактирование консолидированного отчетного документа')

@section('additionalTabLi')
    <li>Протокол консолидации</li>
    <li>Свод по периодам</li>
@endsection

@section('tableConsolidateButton')
    {{-- Кнопка для запуска консолидации текущей таблицы --}}
    <button class="btn btn-default navbar-btn" id="СonsolidateDocument" title="Расчет таблицы">Расчет формы</button>
    <button class="btn btn-default navbar-btn" id="СonsolidateTable" title="Расчет таблицы">Расчет таблицы</button>
    {{-- Плейсхолдер отображения процесса рассчета --}}
    <div id="CalculationProgress" class="btn-group" style="display: none">Производится рассчет <img src='/jqwidgets/styles/images/loader-small.gif' /></div>
@endsection

@section('initTableConsolidateAction')
{{-- Кнопкb для запуска консолидации текущей документа и таблицы --}}
initConsolidateFormButton();
initConsolidateButton();
@endsection

@section('additionalTabDiv')
    <div>
        <div style="width: 100%; overflow-y: auto" id="CellAnalysisTable"></div>
    </div>
    <div>
        <div id="CellPeriodsTable"></div>
    </div>
@endsection

@section('content')
    @include('jqxdatainput.formeditsplitter')
    @include('jqxdatainput.excelimport')
@endsection

@push('loadcss')
    @if(config('medinfo.ssl_connection'))
        <link href="{{ secure_asset('/css/medinfoeditform.css?v=014') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ asset('/css/medinfoeditform.css?v=014') }}" rel="stylesheet" type="text/css" />
    @endif
@endpush('loadcss')

@push('loadjsscripts')
@include('jqxdatainput.jsstack')
    @if(config('medinfo.ssl_connection'))
        <script src="{{ secure_asset('/medinfo/editdashboard.js?v=193') }}"></script>
    @else
        <script src="{{ asset('/medinfo/editdashboard.js?v=193') }}"></script>
    @endif
@endpush('loadjsscripts')

@section('inlinejs')
    @parent
    @include('jqxdatainput.dashboardjs')
@endsection