@extends('jqxdatainput.dashboardlayout')

@section('title')
    <p class="text-info small">
        Форма №<span class="text-info">{{ $form->form_code  }} </span>
        <i class="fa fa-hospital-o fa-lg"></i> <span class="text-info">{{ $current_unit->unit_name ? $current_unit->unit_name : $current_unit->group_name}} </span>
        <i class="fa fa-calendar-o fa-lg"></i> <span class="text-info">{{ $period->name }} </span>
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

@section('headertitle', 'Просмотр/редактирование сводного отчетного документа')

@section('additionalTabLi')
    <li>Разрез по ячейке</li>
    <li>Разрез по периодам</li>
@endsection

@section('additionalTabDiv')
    <div>
        <div style="width: 100%; overflow-y: auto" id="CellAnalysisTable"></div>
    </div>
    <div>
        <div id="CellPeriodsTable"></div>
    </div>
@endsection

@section('tableAggregateButton')
    {{-- Экспорт в Медстат ЦНИИОИЗ--}}
    <button class="btn btn-default navbar-btn" id="tableMedstatExport" title="Экспорт данных таблицы в формат Медстат ЦНИИОИЗ (dbf)">
        <span class='fa fa-download fa-lg' ></span>
        <span>МС</span>
    </button>
@endsection

@section('initTableAggregateAction')
    initTableMedstatExportButton();
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
    @if(config('medinfo.ssl_connection'))
        <script src="{{ secure_asset('/medinfo/editdashboard.js?v=209') }}"></script>
    @else
        <script src="{{ asset('/medinfo/editdashboard.js?v=209') }}"></script>
    @endif
@endpush('loadjsscripts')

@section('inlinejs')
    @parent
    @include('jqxdatainput.dashboardjs')
@endsection