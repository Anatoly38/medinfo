@extends('jqxdatainput.dashboardlayout')

@section('title')
    <p class="text-info small">
        Форма №<span class="text-info">{{ $form->form_code  }} </span>
        <i class="fal fa-hospital fa-lg"></i>
        <span class="text-info" title="{{ $current_unit->unit_name ? $current_unit->unit_name : $current_unit->group_name }}">
            {{ str_limit($current_unit->unit_name ? $current_unit->unit_name : $current_unit->group_name, 60) }}
        </span>
        <i class="fal fa-map fa-lg"></i> <span class="text-info">{{ $monitoring->name }} </span>
        <i class="fal fa-calendar fa-lg"></i> <span class="text-info">{{ $period->name }} </span>
        <i class="fal fa-star fa-lg"></i> <span class="text-info" id="StateInfo">{{ $statelabel }} </span>
        <i class="fal fa-edit fa-lg"></i> <span class="text-info">{{ $editmode }} </span>
    </p>
@endsection

@section('rp-open')
    <li class="pull-right">
        <a href="#">
            <span class="text-right text-info pull-right" id="rp-open" title="Открыть боковую панель"><i style="font-size: 1.5em" class="fa fa-align-justify"></i></span>
        </a>
    </li>
    <li class="dropdown pull-right">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Дополнительные действия">
            <i class="fa fa-ellipsis-v fa-lg text-info"></i> <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="#" id="openSendMessageWindow"><span class="far fa-comment"></span> Сообщение</a></li>
           <li><a href="#" id="openChangeStateWindow"><span class="far fa-check"></span> Смена статуса</a></li>
           @if (Auth::guard('datainput')->user()->role === 3 or Auth::guard('datainput')->user()->role === 4)
            <li><a href="#" id="openDocumentInfoWindow"><span class="far fa-info-circle"></span> Информация о документе</a></li>
           @endif
        </ul>
    </li>
@endsection

@section('tableAggregateButton')
    {{-- Экспорт в Медстат ЦНИИОИЗ--}}
    <button style="display: none" class="btn btn-default navbar-btn" id="tableMedstatExport" title="Экспорт данных таблицы в формат Медстат ЦНИИОИЗ (dbf)">
        <span class='fas fa-download fa-lg' ></span>
        <span>МС</span>
    </button>
@endsection

@section('initTableAggregateAction')
    initTableMedstatExportButton();
@endsection

@section('headertitle', 'Просмотр/редактирование первичного отчетного документа')

@section('content')
    @include('jqxdatainput.formeditsplitter')
    @include('jqxdatainput.excelimport')
    @include('jqxdatainput.windows')
@endsection

@push('loadcss')
    @if(config('medinfo.ssl_connection'))
        <link href="{{ secure_asset('/css/medinfoeditform.css?v=019') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ asset('/css/medinfoeditform.css?v=019') }}" rel="stylesheet" type="text/css" />
    @endif
@endpush('loadcss')

@push('loadjsscripts')
    <script src="{{ secure_asset('/medinfo/editdashboard_v2.js?v=033') }}"></script>
{{--
    @if(config('medinfo.ssl_connection'))
        <script src="{{ secure_asset('/medinfo/editdashboard_v2.js?v=033') }}"></script>
    @else
        <script src="{{ asset('/medinfo/editdashboard_v2.js?v=033') }}"></script>
    @endif
--}}
@endpush('loadjsscripts')

@section('inlinejs')
    @parent
    @include('jqxdatainput.dashboardjs_v2')
@endsection