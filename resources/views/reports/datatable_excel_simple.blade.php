    <table>
        <tr>
            <td colspan="{{ count($cols) }}">Таблица: {{ $table->table_code }}. {{ $table->table_name  }}.</td>
        </tr>
        <tr>
            <td colspan="{{ count($cols) }}" style="color: #f00000;">Не для предоставления в МИАЦ в качестве отчетной формы!</td>
        </tr>
    </table>
    <table class="data">
        <tr>
            @foreach($cols as $col)
                <th width="{{ $col->size/7 }}">{{ $col->column_name }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($cols as $col)
                <th align="center">{{ "'". $col->column_code }}</th>
            @endforeach
        </tr>
        @foreach($data as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
