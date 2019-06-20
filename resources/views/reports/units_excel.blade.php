    <table>
        <tr>
            <td colspan="8"><h2>Список МО/территорий</h2></td>
        </tr>
    </table>
    <table class="units">
        <tr>
            <th>Id</th>
            <th>Входит в</th>
            <th>Код</th>
            <th>ИНН</th>
            <th>Наименование</th>
            <th>Тип</th>
            <th>Адрес</th>
            <th>Село</th>
        </tr>
        @foreach($units as $unit)
            <tr>
                <td>{{ $unit->id }}</td>
                <td>{{ isset($unit->parent) ? $unit->parent->unit_name  : '' }}</td>
                <td>{{ $unit->unit_code }}</td>
                <td>{{ $unit->inn }}</td>
                <td>{{ $unit->unit_name }}</td>
                <td>{{ $unit->node_type }}</td>
                <td>{{ $unit->adress }}</td>
                <td>{{ $unit->countryside }}</td>
            </tr>
        @endforeach
    </table>
