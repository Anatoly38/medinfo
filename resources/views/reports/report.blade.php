@extends('reports.report_layout')

@section('description')
    Консолидированный отчет Мединфо
@endsection

@section('title')
    <div class="col-sm-7"><h2>Консолидированный отчет Мединфо</h2></div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-9">
            <h4>{{ $title }}</h4>
        </div>
    </div>
    @include('reports.calc_error_alert')
    <div class="row">
        <table class="table table-bordered table-striped">
            <colgroup>
                <col style="width: 100px">
                <col style="width: 300px">
            </colgroup>
            <thead>
            <tr>
                <th>Код</th>
                {!! in_array('inn', $extrafields) ? '<th>ИНН</th>' : '' !!}
                {!! in_array('node_type', $extrafields) ? '<th>Тип ОЕ</th>' : '' !!}
                <th>Территория/Медицинская организация</th>
                @foreach( $structure['content'] as $index => $description)
                    <th title="{{ $description['value'] }}">{{ $description['title'] }} </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($indexes as $index)
            <tr>
                <td>{{ $index['unit_code'] }}</td>
                {!! in_array('inn', $extrafields) ? "<td>{$index['inn']}</td>" : '' !!}
                {!! in_array('node_type', $extrafields) ? "<td>{$index['node_type']}</td>" : '' !!}
                <td>{{ $index['unit_name'] }}</td>
                @for($i = 0; $i < $count_of_indexes; $i++)
                    <td>{{ $index[$i]['value'] }}</td>
                @endfor
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('loadjsscripts')

@endpush

@section('inlinejs')
    @parent
@endsection
