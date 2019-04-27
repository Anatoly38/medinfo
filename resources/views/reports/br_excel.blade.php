<table>
    <tr>
        <td colspan="{{ count($column_titles)+2 }}">Справка по форме №{{ $form->form_code }}. {{ $form->form_name }} за период "{{ $period->name }}"</td>
    </tr>
    <tr>
        <td colspan="{{ count($column_titles)+2 }}">Таблица: {{ $table->table_code }}. {{ $table->table_name  }}. </td>
    </tr>
    <tr>
        <td colspan="{{ count($column_titles)+2 }}">{{ $group_title }} {{ $el_name }}</td>
    </tr>
    <tr>
        <td colspan="{{ count($column_titles)+2 }}">Ограничение по территории/группе: {{ $top->unit_name or $top->group_name }}</td>
    </tr>
</table>
<table class="data">
    <tr>
        <th>Код</th>
        <th>Субъект</th>
        @foreach($column_titles as $title)
            <th>{{ $title }}</th>
        @endforeach
    </tr>
    @foreach($units as $unit)
        <tr>
            <td align="right">{{ $unit->unit_code }}</td>
            <td width="70">{{ $unit->unit_name }}</td>
            @foreach($values[$unit->id] as $v)
                <td width="18">{{ $v }}</td>
            @endforeach
        </tr>
    @endforeach
    <tr>
        <td colspan="2"><strong>Итого</strong></td>
        @foreach($values[999999] as $aggregate)
            <td>{{ $aggregate }}</td>
        @endforeach
    </tr>
</table>
