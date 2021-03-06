<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid" style="z-index: 1">
        <div class="navbar-header">
            <a class="navbar-brand" href="/admin">Адмнистрирование Мединфо</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Пользователи
                    <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/admin/workers">Исполнители</a></li>
                    <li><a href="/users">Администраторы</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Структура
                    <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/admin/periods">Отчетные периоды</a></li>
                    <li><a href="/admin/monitorings">Мониторинги</a></li>
                    <li><a href="/admin/albums">Альбомы форм</a></li>
                    <li><a href="/admin/forms">Формы</a></li>
                    <li><a href="/admin/formsections">Разделы форм</a></li>
                    <li><a href="/admin/tables">Таблицы</a></li>
                    <li><a href="/admin/rc">Строки и Графы</a></li>
                    <li type="separator"></li>
                    <li><a href="/admin/necells/list">Нередактируемые ячейки, перечень</a></li>
                    {{--<li><a href="/admin/necells/conditions">Нередактируемые ячейки, условия</a></li>--}}
                    <li type="separator"></li>
                    <li><a href="/admin/sctruct/ms_rows_columns_import">Импорт структуры из формата Медстат (ЦНИИОИЗ)</a></li>
                    <li><a href="/admin/sctruct/medstatimport">Импорт структуры из формата Медстат (Новосибирск)</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Аналитика
                    <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/admin/cfunctions">Функции контроля</a></li>
                    <li><a href="/admin/cfunctions/all">Функции контроля (полный перечень)</a></li>
                    <li><a href="/admin/cons">Функции расчёта</a></li>
                    <li><a href="/admin/cons/debugrule">Отладка функций расчёта</a></li>
                    <li><a href="/reports/patterns">Отчеты</a></li>
                    <li><a href="/reports/br/querycomposer">Справка</a></li>
                    <li type="separator"></li>
                    <li><a href="/admin/cfunctions/medstatnskimport">Импорт контролей из формата Медстат (Новосибирск)</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Организационные единицы
                    <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/admin/units">Территории/Медицинские организации</a></li>
                    <li><a href="/admin/units/lists">Списки медицинских организаций</a></li>
                    <li><a href="/admin/units/medstatimport">Импорт территорий/медицинских организаций из формата Медстат (Новосибирск)</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Документы
                    <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="/admin/documents">Менеджер отчетных документов</a></li>
                    <li><a href="/admin/system/clearnecells">Очистка закрещенных ячеек</a></li>
                    <li><a href="/admin/documents/medstatimport">Импорт данных из формата Медстат (ЦНИИОИЗ)</a></li>
                    <li><a href="/admin/documents/medstatnskimport">Импорт данных из формата Медстат (Новосибирск)</a></li>
                </ul>
            </li>
            <li><a href="#">@yield('local_actions')</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="/admin/logout"><span class="glyphicon glyphicon-log-in"></span> Завершить работу</a></li>
        </ul>
    </div>
</nav>

