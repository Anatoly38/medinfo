@extends('reports.report_layout')

@section('title')
    <div class="col-sm-7"><h2>Протокол перекомпилирования списков МО для расчета</h2></div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-9">
            <h4>Списки МО для расчета консолидированных форм</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-11">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Список</th>
                    <th>Результат</th>
                </tr>
                </thead>
                <tbody>
                    @foreach( $protocol as $list )
                        <tr  @if (!$list['error']) class="danger" @endif>
                            <td>{{ $list['i'] }}.</td>
                            <td>{{ $list['script'] }}</td>
                            <td>{{ $list['comment'] }}</td>
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
