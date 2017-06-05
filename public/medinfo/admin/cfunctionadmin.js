// функция для обновления связанных объектов после выбора таблицы
updateRelated = function() {
    updateFunctionList();
    $("#form")[0].reset();
};

initdatasources = function() {
    functionsource = {
        datatype: "json",
        datafields: [
            { name: 'id', type: 'int' },
            { name: 'table_id', type: 'int' },
            { name: 'table_code', map: 'table>table_code', type: 'string' },
            { name: 'level', type: 'int' },
            { name: 'levelname', map: 'level>name', type: 'string' },
            { name: 'script', type: 'string' },
            { name: 'comment', type: 'string' },
            { name: 'blocked', type: 'bool' }
        ],
        id: 'id',
        url: functionfetch_url + current_table,
        root: 'f'
    };
    functionsDataAdapter = new $.jqx.dataAdapter(functionsource);
};
// Таблица функций
initFunctionList = function() {
    fgrid.jqxGrid(
        {
            width: '98%',
            height: '300px',
            theme: theme,
            localization: localize(),
            source: functionsDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            columns: [
                { text: 'Id', datafield: 'id', width: '50px' },
                { text: 'Код таблицы', datafield: 'table_code', width: '70px'  },
                { text: 'Уровень', datafield: 'levelname', width: '120px'  },
                { text: 'Функция контроля', datafield: 'script' , width: '50%'},
                { text: 'Комментарий', datafield: 'comment', width: '30%' },
                { text: 'Отключена', datafield: 'blocked', columntype: 'checkbox', width: '70px' }
            ]
        });
    fgrid.on('rowselect', function (event) {
        var row = event.args.row;
        $("#level").val(row.level);
        $("#script").val(row.script);
        $("#comment").val(row.comment);
        $("#blocked").val(row.blocked);
    });
};
// Обновление списка строк при выборе таблицы
updateFunctionList = function() {
    functionsource.url = functionfetch_url + current_table;
    fgrid.jqxGrid('clearselection');
    fgrid.jqxGrid('updatebounddata');
};
// Операции с функциями контроля

initButtons = function() {
    $('#blocked').jqxSwitchButton({
        height: 31,
        width: 81,
        onLabel: 'Да',
        offLabel: 'Нет',
        checked: false });
    var errorlevelsource =
    {
        datatype: "json",
        datafields: [
            { name: 'code' },
            { name: 'name' }
        ],
        id: 'code',
        localdata: errorLevels
    };
    errorLevelsDataAdapter = new $.jqx.dataAdapter(errorlevelsource);
    $("#level").jqxDropDownList({
        theme: theme,
        source: errorLevelsDataAdapter,
        displayMember: "name",
        valueMember: "code",
        placeHolder: "Выберите уровень ошибки:",
        width: 300,
        height: 34
    });
};

setquerystring = function() {
    return "&level=" + $("#level").val() +
        "&script=" + encodeURIComponent($("#script").val()) +
        "&comment=" + $("#comment").val() +
        "&blocked=" + ($("#blocked").val() ? 1 :0);
};

initFunctionActions = function() {
    $("#insert").click(function () {
        if (current_table == 0 ) {
            raiseError('Выберите форму и таблицу для которых будет применяться функция');
            return;
        }
        var data = setquerystring();
        $.ajax({
            dataType: 'json',
            url: '/admin/cfunctions/create/' + current_table ,
            method: "POST",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error != 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                fgrid.jqxGrid('updatebounddata', 'data');
            },
            error: function (xhr, status, errorThrown) {
                $.each(xhr.responseJSON, function(field, errorText) {
                    raiseError(errorText);
                });
            }
        });
    });
    $("#save").click(function () {
        var row = fgrid.jqxGrid('getselectedrowindex');
        if (row == -1 && typeof row !== 'undefined') {
            raiseError("Выберите запись для изменения/сохранения данных");
            return false;
        }
        var id = fgrid.jqxGrid('getrowid', row);
        var data = setquerystring();
        $.ajax({
            dataType: 'json',
            url: '/admin/cfunctions/update/' + id,
            method: "PATCH",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error != 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                    fgrid.jqxGrid('updatebounddata', 'data');
                    fgrid.on("bindingcomplete", function (event) {
                        var newindex = fgrid.jqxGrid('getrowboundindexbyid', id);
                        fgrid.jqxGrid('selectrow', newindex);
                    });
                }
            },
            error: function (xhr, status, errorThrown) {
                $.each(xhr.responseJSON, function(field, errorText) {
                    raiseError(errorText);
                });
            }
        });
    });
    $("#delete").click(function () {
        var row = fgrid.jqxGrid('getselectedrowindex');
        if (row == -1) {
            raiseError("Выберите запись для удаления");
            return false;
        }
        current_function = fgrid.jqxGrid('getrowid', row);
        raiseConfirm("Удалить функцию контроля № " + current_function + "?" )
    });
};

performAction = function() {
    $.ajax({
        dataType: 'json',
        url: '/admin/cfunctions/delete/' + current_function,
        method: "DELETE",
        success: function (data, status, xhr) {
            if (typeof data.error != 'undefined') {
                raiseError(data.message);
            } else {
                raiseInfo(data.message);
                $("#form")[0].reset();
            }
            fgrid.jqxGrid('updatebounddata', 'data');
            fgrid.jqxGrid('clearselection');
        },
        error: function (xhr, status, errorThrown) {
            raiseError('Ошибка удаления записи', xhr);
        }
    });
};
