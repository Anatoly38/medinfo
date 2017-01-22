@extends('reports.report_layout')

@section('title')
    <div class="col-sm-7"><h2>Справка</h2></div>
@endsection

@section('content')
    <div class="col-sm-7">
        <h4>{{ $title }}</h4>
    </div>
    <div>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Учреждение</th>
                <th>ИНН</th>
                @foreach( $structure['content'] as $index => $description)
                    <th>{{ $description['title'] }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($indexes as $index)
            <tr>
                <td>{{ $index['unit_name'] }}</td>
                <td>{{ $index['inn'] }}</td>
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
