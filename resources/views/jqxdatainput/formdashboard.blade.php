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
        <i class="fa fa-star fa-lg"></i> <span class="text-info" id="StateInfo">{{ $statelabel }} </span>
        <i class="fa fa-edit fa-lg"></i> <span class="text-info">{{ $editmode }} </span>
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
            <li><a href="#" id="openSendMessageWindow"><span class="fa fa-commenting-o"></span> Сообщение</a></li>
           <li><a href="#" id="openChangeStateWindow"><span class="fa fa-check-circle-o"></span> Смена статуса</a></li>
            {{-- <li><a href="#"><span class="fa fa-info-circle"></span> Информация</a></li>--}}
        </ul>
    </li>
@endsection

@section('headertitle', 'Просмотр/редактирование первичного отчетного документа')

@section('content')
    @include('jqxdatainput.formeditsplitter')
    @include('jqxdatainput.excelimport')
    @include('jqxdatainput.windows')
@endsection

@push('loadcss')
    <link href="{{ asset('/css/medinfoeditform.css?v=014') }}" rel="stylesheet" type="text/css" />
@endpush('loadcss')

@push('loadjsscripts')
    <script src="{{ asset('/medinfo/editdashboard.js?v=193') }}"></script>
@endpush('loadjsscripts')

@section('inlinejs')
    @parent
    @include('jqxdatainput.dashboardjs')
@endsection