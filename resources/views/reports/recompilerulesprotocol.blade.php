@extends('reports.report_layout')

@section('title')
    <div class="row">
        <div class="col-md-11"><h2>Протокол перекомпилирования правил расчета</h2></div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-11">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Id</th>
                    <th style="width:70%">Правило</th>
                    <th>Старый хэш</th>
                    <th>Новый хэш</th>
                    <th>Результат</th>
                </tr>
                </thead>
                <tbody>
                    @foreach( $protocol as $rule )
                        <tr  class="@if ($rule['error']) danger @endif">
                            <td>{{ $rule['i'] }}.</td>
                            <td>{{ $rule['id'] }}</td>
                            <td title="{{ $rule['script'] }}">{{ str_limit($rule['script'], 180)  }}</td>
                            <td>{{ $rule['old_hash'] }}</td>
                            <td>{{ $rule['new_hash'] }}</td>
                            <td>{{ $rule['comment'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('loadjsscripts')

@endpush

@section('inlinejs')
    @parent
@endsection
