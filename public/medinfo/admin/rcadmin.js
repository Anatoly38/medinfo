/**
 * Created by shameev on 13.09.2016.
 */
initsplitter = function() {
    $("#mainSplitter").jqxSplitter(
        {
            width: '100%',
            height: '100%',
            theme: theme,
            panels:
                [
                    { size: '50%', min: '10%', collapsible: false },
                    { size: '50%', min: '10%', collapsible: false }
                ]
        }
    );
};

initdatasources = function() {
    let columnTypessource =
        {
            datatype: "json",
            datafields: [
                { name: 'code' },
                { name: 'name' }
            ],
            id: 'code',
            localdata: columnTypes
        };

    rowsource = {
        datatype: "json",
        datafields: [
            { name: 'id', type: 'int' },
            { name: 'table_id', type: 'int' },
            { name: 'table_code', map: 'table>table_code', type: 'string' },
            { name: 'excluded', map: 'excluded>0>album_id', type: 'int' },
            { name: 'row_index', type: 'int' },
            { name: 'row_code', type: 'string' },
            { name: 'row_name', type: 'string' },
            { name: 'medstat_code', type: 'string' },
            { name: 'medstatnsk_id', type: 'int' },
            { name: 'prop', map: 'property>properties', type: 'string' },
        ],
        id: 'id',
        url: rowfetch_url + current_table,
        root: 'row'
    };
    columnsource = {
        datatype: "json",
        datafields: [
            { name: 'id', type: 'int' },
            { name: 'table_id', type: 'int' },
            { name: 'table_code', map: 'table>table_code', type: 'string' },
            { name: 'excluded', map: 'excluded>0>album_id', type: 'int' },
            { name: 'column_index', type: 'int' },
            { name: 'column_name', type: 'string' },
            { name: 'column_code', type: 'string' },
            { name: 'content_type', type: 'int' },
            { name: 'size', type: 'int' },
            { name: 'decimal_count', type: 'int' },
            { name: 'medstat_code', type: 'string' },
            { name: 'medstatnsk_id', type: 'int' },
            { name: 'prop', map: 'property>properties', type: 'string' },
        ],
        id: 'id',
        url: columnfetch_url + current_table,
        root: 'column'
    };
    rowsDataAdapter = new $.jqx.dataAdapter(rowsource);
    columnsDataAdapter = new $.jqx.dataAdapter(columnsource);
    columnTypesDataAdapter = new $.jqx.dataAdapter(columnTypessource);

};
// Таблица строк
initRowList = function() {
    rlist.jqxGrid(
        {
            width: '98%',
            height: '300px',
            theme: theme,
            localization: localize(),
            source: rowsDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            columns: [
                { text: 'Id', datafield: 'id', width: '50px' },
                { text: '№ п/п', datafield: 'row_index', width: '50px' },
                { text: 'Искл.', datafield: 'excluded', columntype: 'checkbox', width: '70px'  },
                //{ text: 'Исключена из альбома', datafield: 'excluded', width: '90px'  },
                { text: 'Код', datafield: 'row_code', width: '70px'  },
                { text: 'Имя', datafield: 'row_name' , width: '480px'},
                { text: 'Код МС(мск)', datafield: 'medstat_code', width: '80px' },
                //{ text: 'Код МС(нск)', datafield: 'medstatnsk_id', width: '70px' }
                { text: 'Свойства', datafield: 'prop', width: '70px'  },
            ]
        });
    rlist.on('rowselect', function (event) {
        let row = event.args.row;
        let props = {};
        if (typeof row !== 'undefined') {
            $("#row_index").val(row.row_index);
            $("#row_name").val(row.row_name);
            $("#row_code").val(row.row_code);
            $("#row_medstat_code").val(row.medstat_code);
            $("#row_medstatnsk_id").val(row.medstatnsk_id);
            //row.excluded > 0 ? $("#excludedRow").prop('checked', true) : $("#excludedRow").val(false);
            row.excluded > 0 ? $("#excludedRow").prop('checked', true) : $("#excludedRow").prop('checked', false);
            showRowProperties(row);
        }
    });
};

showRowProperties = function(row) {
    let dd = $("#aggregatedRows");
    let props = $.parseJSON(row.prop);
    dd.jqxDropDownList('uncheckAll');
    let checkedItems = [];
    if (props === null) {
        $("#IsAggregatedRow").prop('checked', false);
        $("#aggregatedRowElements").hide();
        rowids = '';
        return false;
    }
    if (props.aggregated_rows instanceof Array) {
        for (let i = 0; props.aggregated_rows.length > i; i++) {
            dd.jqxDropDownList('checkItem', props.aggregated_rows[i]);
            checkedItems.push(props.aggregated_rows[i]);
        }
    }
    rowids = checkedItems.join();
    if (props.aggregate) {
        $("#IsAggregatedRow").prop('checked', true);
        $("#aggregatedRowElements").show();
    } else {
        $("#IsAggregatedRow").prop('checked', false);
        $("#aggregatedRowElements").hide();
        rowids = '';
    }
    return true;
};

//Таблица граф
initColumnList = function() {
    clist.jqxGrid(
        {
            width: '98%',
            height: '300px',
            theme: theme,
            localization: localize(),
            source: columnsDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            columns: [
                { text: 'Id', datafield: 'id', width: '50px' },
                { text: '№ п/п', datafield: 'column_index', width: '50px' },
                { text: 'Искл.', datafield: 'excluded', columntype: 'checkbox', width: '70px'  },
                //{ text: 'Исключена из альбома', datafield: 'excluded', width: '90px'  },
                { text: 'Имя', datafield: 'column_name' , width: '300px'},
                { text: 'Код', datafield: 'column_code' , width: '50px'},
                { text: 'Тип', datafield: 'content_type', width: '50px' },
                { text: 'Размер', datafield: 'size', width: '70px' },
                { text: 'Десятичные', datafield: 'decimal_count', width: '90px' },
                { text: 'Код МС(мск)', datafield: 'medstat_code', width: '70px' },
                //{ text: 'Код МС(нск)', datafield: 'medstatnsk_id', width: '70px' }
                { text: 'Свойства', datafield: 'prop', width: '70px'  },
            ]
        });
    clist.on('rowselect', function (event) {
        let row = event.args.row;
        if (typeof row !== 'undefined') {
            $("#column_index").val(row.column_index);
            $("#column_name").val(row.column_name);
            $("#column_code").val(row.column_code);
            $("#column_type").val(row.content_type);
            $("#field_size").val(row.size);
            $("#decimal_count").val(row.decimal_count);
            $("#column_medstat_code").val(row.medstat_code);
            $("#column_medstatnsk_id").val(row.medstatnsk_id);
            //row.excluded > 0 ? $("#excludedColumn").val(true) : $("#excludedColumn").val(false);
            row.excluded > 0 ? $("#excludedColumn").prop('checked', true) : $("#excludedColumn").prop('checked', false);
            showColumnProperties(row);
        }
    });
};

showColumnProperties = function(column) {
    let dd = $("#aggregatedColumns");
    let props = $.parseJSON(column.prop);
    dd.jqxDropDownList('uncheckAll');
    let checkedItems = [];
    if (props === null) {
        $("#IsAggregatedColumn").prop('checked', false);
        $("#aggregatedColumnElements").hide();
        columnids = '';
        return false;
    }
    if (props.aggregated_columns instanceof Array) {
        for (let i = 0; props.aggregated_columns.length > i; i++) {
            dd.jqxDropDownList('checkItem', props.aggregated_columns[i]);
            checkedItems.push(props.aggregated_columns[i]);
        }
    }
    columnids = checkedItems.join();
    if (props.aggregate) {
        $("#IsAggregatedColumn").prop('checked', true);
        $("#aggregatedColumnElements").show();
    } else {
        $("#IsAggregatedColumn").prop('checked', false);
        $("#aggregatedColumnElements").hide();
        columnids = '';
    }
    return true;
};

initDropDownRows = function() {
    let rowdd = $("#aggregatedRows");
    let el = $("#aggregatedRowElements");

    let checkedItems = [];
    rowdd.jqxDropDownList({
        theme: theme,
        checkboxes: true,
        filterable: true,
        filterPlaceHolder: '',
        source: rowsDataAdapter,
        displayMember: "row_code",
        valueMember: "id",
        placeHolder: "Выберите строки:",
        width: '100%',
        height: 35,
        renderer: function (index, label, value) {
            let rec = rowsDataAdapter.records[index];
            return "<span class='text-info'><strong>" +rec.row_code + "</strong></span>&nbsp;&nbsp;" + rec.row_name;
        }
    });

    rowdd.on('select', function (event) {
       getCheckedRows();
    });

    $("#checkAllRows").on('click', function () {
        rowdd.jqxDropDownList('checkAll');
        checkedItems = [];
        let items = rowdd.jqxDropDownList('getCheckedItems');
        $.each(items, function (index) {
            checkedItems.push(this.value);
        });
        rowids = checkedItems.join();
        allrows = 1;
    });

    $("#uncheckAllRows").on('click', function () {
        rowdd.jqxDropDownList('uncheckAll');
        checkedItems = [];
        rowids = "";
        allrows = 0;
    });

    $("#IsAggregatedRow").click(function () {
        getCheckedRows();
        $("#IsAggregatedRow").prop('checked') ? el.show() : el.hide() ;
    });
    
    let getCheckedRows = function () {
        let items = rowdd.jqxDropDownList('getCheckedItems');
        let allitems = rowdd.jqxDropDownList('getItems');
        checkedItems = [];
        $.each(items, function (index) {
            checkedItems.push(this.value);
        });
        rowids = checkedItems.join();
        if (items.length === allitems.length) {
            allrows = 1;
        } else {
            allrows = 0;
        }
    } 
};

initDropDownColumns = function() {
    let columndd = $("#aggregatedColumns");
    let el = $("#aggregatedColumnElements");

    let checkedItems = [];
    columndd.jqxDropDownList({
        theme: theme,
        checkboxes: true,
        filterable: true,
        filterPlaceHolder: '',
        source: columnsDataAdapter,
        displayMember: "column_code",
        valueMember: "id",
        placeHolder: "Выберите графы:",
        width: '100%',
        height: 35,
        renderer: function (index, label, value) {
            let rec = columnsDataAdapter.records[index];
            return "<span class='text-info'><strong>" +rec.column_code + "</strong></span>&nbsp;&nbsp;" + rec.column_name;
        }
    });

    columndd.on('select', function (event) {
        getCheckedColumns();
    });

    $("#checkAllColumns").on('click', function () {
        columndd.jqxDropDownList('checkAll');
        checkedItems = [];
        let items = columndd.jqxDropDownList('getCheckedItems');
        $.each(items, function (index) {
            checkedItems.push(this.value);
        });
        columnids = checkedItems.join();
        allcolumns = 1;
    });

    $("#uncheckAllColumns").on('click', function () {
        columndd.jqxDropDownList('uncheckAll');
        checkedItems = [];
        columnids = "";
        allcolumns = 0;
    });

    $("#IsAggregatedColumn").click(function () {
        getCheckedColumns();
        $("#IsAggregatedColumn").prop('checked') ? el.show() : el.hide();

    });

    let getCheckedColumns = function () {
        let items = columndd.jqxDropDownList('getCheckedItems');
        let allitems = columndd.jqxDropDownList('getItems');
        checkedItems = [];
        $.each(items, function (index) {
            checkedItems.push(this.value);
        });
        columnids = checkedItems.join();
        if (items.length === allitems.length) {
            allcolumns = 1;
        } else {
            allcolumns = 0;
        }
    }
};

// функция для обновления связанных объектов после выбора таблицы
updateRelated = function() {
    updateRowList();
    updateColumnList();
    $("#rowform")[0].reset();
    $("#columnform")[0].reset();
};

// Обновление списка строк при выборе таблицы
updateRowList = function() {
    rowsource.url = rowfetch_url + current_table;
    rlist.jqxGrid('clearselection');
    rlist.jqxGrid('updatebounddata');
};
// Обновление списка граф при выборе таблицы
updateColumnList = function() {
    columnsource.url = columnfetch_url + current_table;
    clist.jqxGrid('clearselection');
    clist.jqxGrid('updatebounddata');
};

setrowquery = function() {
    return "&table_id=" + current_table +
        "&row_index=" + $("#row_index").val() +
        "&row_code=" + $("#row_code").val() +
        "&row_name=" + $("#row_name").val() +
        "&medstat_code=" + $("#row_medstat_code").val() +
        "&medstatnsk_id=" + $("#row_medstatnsk_id").val() +
        //"&excluded=" + ($("#excludedRow").val() ? 1 : 0);
        "&excluded=" + ($("#excludedRow").prop('checked') ? 1 : 0) +
        "&aggregated=" + ($("#IsAggregatedRow").prop('checked') ? 1 : 0) +
        "&aggregatedrows=" + rowids;


};

setcolumnquery = function() {
    return "&table_id=" + current_table +
        "&column_index=" + $("#column_index").val() +
        "&column_name=" + $("#column_name").val() +
        "&column_code=" + $("#column_code").val() +
        "&content_type=" + $("#column_type").val() +
        "&field_size=" + $("#field_size").val() +
        "&decimal_count=" + $("#decimal_count").val() +
        "&medstat_code=" + $("#column_medstat_code").val() +
        "&medstatnsk_id=" + $("#column_medstatnsk_id").val() +
        //"&excluded=" + ($("#excludedColumn").val() ? 1 : 0);
        "&excluded=" + ($("#excludedColumn").prop('checked') ? 1 : 0) +
        "&aggregated=" + ($("#IsAggregatedColumn").prop('checked') ? 1 : 0) +
        "&aggregatedcolumns=" + columnids +
        "&allownegatives=" + ($("#AllowNegatives").prop('checked') ? 1 : 0) ;
};

initButtons = function() {
    let typelist = $("#column_type");
    typelist.jqxDropDownList({
        theme: theme,
        source: columnTypesDataAdapter,
        displayMember: "name",
        valueMember: "code",
        placeHolder: "Выберите тип поля:",
        //selectedIndex: 2,
        width: 200,
        height: 32
    });
    typelist.on('change', function (event) {
        let args = event.args;
        if (args) {
            let index = args.index;
            let item = args.item;
            let label = item.label;
            let value = item.value;
            let type = args.type; // keyboard, mouse or null depending on how the item was selected.
            if (label === 'Вычисляемая графа') {
                $("#editFormula").show();
            } else {
                $("#editFormula").hide();
            }
        }
    });
};

// Операции со строками
initRowActions = function() {
    $("#insertrow").click(function () {
        if (current_table === 0) {
            raiseError('Не выбрана текущая таблица');
            return false;
        }
        let data = setrowquery();
        $.ajax({
            dataType: 'json',
            url: '/admin/rc/rowcreate',
            method: "POST",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                rlist.jqxGrid('updatebounddata', 'data');
                rlist.on("bindingcomplete", function (event) {
                    let newindex = rlist.jqxGrid('getrowboundindexbyid', data.id);
                    rlist.jqxGrid('selectrow', newindex);
                    rlist.jqxGrid('ensurerowvisible', newindex);
                });
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#saverow").click(function () {
        let row = rlist.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для изменения/сохранения данных");
            return false;
        }
        let rowid = rlist.jqxGrid('getrowid', row);
        let data = setrowquery();
        $.ajax({
            dataType: 'json',
            url: '/admin/rc/rowupdate/' + rowid,
            method: "PATCH",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                rlist.jqxGrid('updatebounddata', 'data');
                rlist.on("bindingcomplete", function (event) {
                    let newindex = rlist.jqxGrid('getrowboundindexbyid', rowid);
                    rlist.jqxGrid('selectrow', newindex);
                    rlist.jqxGrid('ensurerowvisible', newindex);
                });
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#deleterow").click(function () {
        let row = rlist.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для удаления");
            return false;
        }
        let rowid = rlist.jqxGrid('getrowid', row);
        $.ajax({
            dataType: 'json',
            url: '/admin/rc/rowdelete/' + rowid,
            method: "DELETE",
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                    $("#rowform")[0].reset();
                }
                rlist.jqxGrid('updatebounddata', 'data');
                rlist.jqxGrid('clearselection');
            },
            error: xhrErrorNotificationHandler
        });
    });
};
// Действия с графами
initColumnActions = function() {
    $("#insertcolumn").click(function () {
        if (current_table === 0) {
            raiseError('Не выбрана текущая таблица');
            return false;
        }
        let data = setcolumnquery();
        $.ajax({
            dataType: 'json',
            url: '/admin/rc/columncreate',
            method: "POST",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                clist.jqxGrid('updatebounddata', 'data');
                clist.on("bindingcomplete", function (event) {
                    let newindex = clist.jqxGrid('getrowboundindexbyid', data.id);
                    clist.jqxGrid('selectrow', newindex);
                    clist.jqxGrid('ensurerowvisible', newindex);

                });
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#savecolumn").click(function () {
        let row = clist.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для изменения/сохранения данных");
            return false;
        }
        let rowid = clist.jqxGrid('getrowid', row);
        let data = setcolumnquery();
        $.ajax({
            dataType: 'json',
            url: '/admin/rc/columnupdate/' + rowid,
            method: "PATCH",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                clist.jqxGrid('updatebounddata', 'data');
                clist.on("bindingcomplete", function (event) {
                    let newindex = clist.jqxGrid('getrowboundindexbyid', rowid);
                    clist.jqxGrid('selectrow', newindex);
                    clist.jqxGrid('ensurerowvisible', newindex);
                });
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#deletecolumn").click(function () {
        let row = clist.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для удаления");
            return false;
        }
        let rowid = clist.jqxGrid('getrowid', row);
        $.ajax({
            dataType: 'json',
            url: '/admin/rc/columndelete/' + rowid,
            method: "DELETE",
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                    $("#columnform")[0].reset();
                }
                clist.jqxGrid('updatebounddata', 'data');
                clist.jqxGrid('clearselection');
            },
            error: function (xhr, status, errorThrown) {
                raiseError('Ошибка удаления записи', xhr);
            }
        });
    });
};

let initColumnFormulaWindow = function () {
    let savebutton = $("#saveFormula");
    let formulaWindow = $('#formulaWindow');
    let formula = $("#formula");
    let formulaexists = false;
    let columnid;
    let formulaid = null;
    $("#editFormula").click(function () {
        formula.attr("placeholder", "");
        let colHeader = $("#columnNameId");
        colHeader.html("");
        let row = clist.jqxGrid('getselectedrowindex');
        columnid = clist.jqxGrid('getrowid', row);
        if (row === -1) {
            raiseError("Не выбрана графа для ввода/изменения формулы расчета");
            return false;
        }
        colHeader.html($("#column_name").val() + ' (Id:' + columnid + ')');
        $.get(showcolumnformula_url + columnid, function( data ) {
            if (data.formula) {
                formula.val(data.formula);
                formulaid = data.id;
                formulaexists = true;
            } else {
                formula.val('');
                formulaexists = false;
            }
            formula.attr("placeholder", data.placeholder);
        });
        formulaWindow.jqxWindow('open');
    });

    formulaWindow.jqxWindow({
        width: 600,
        height: 290,
        resizable: false,
        autoOpen: false,
        isModal: true,
        cancelButton: $('#cancelButton'),
        position: { x: 310, y: 125 }
    });
    savebutton.click(function() {
        let data = "&formula=" + encodeURIComponent(formula.val());
        let method;
        let url;
        if (formulaexists) {
            method = 'PATCH';
            url = updatecolumnformula_url + formulaid;
        } else {
            method = 'POST';
            url = storecolumnformula_url + columnid;
        }
        $.ajax({
            dataType: 'json',
            url: url,
            method: method,
            data: data,
            success: function (data, status, xhr) {
                if (data.saved) {
                    raiseInfo("Изменения сохранены");
                }
                else {
                    raiseError("Ошибка сохранения. " + data.message)
                }
            },
            error: xhrErrorNotificationHandler
        });
    });
};

let initOrderControls = function () {
    let up = $("#row_up");
    let down = $("#row_down");
    let left = $("#column_left");
    let right = $("#column_right");

    up.click(function () {
        reorderRows(row_up_url);
    });
    down.click(function () {
        reorderRows(row_down_url);
    });
    left.click(function () {
        reorderColumns(column_left_url);
    });
    right.click(function () {
        reorderColumns(column_right_url);
    });
};

function getCurrentRow() {
    let row = rlist.jqxGrid('getselectedrowindex');
    if (row === -1) {
        return false;
    }
    return rlist.jqxGrid('getrowid', row);
}

function getCurrentColumn() {
    let row = clist.jqxGrid('getselectedrowindex');
    if (row === -1) {
        return false;
    }
    return clist.jqxGrid('getrowid', row);
}

function reorderRows(url) {
    let selected = getCurrentRow();
    if (!selected) {
        raiseError("Строка не выбрана");
        return false;
    }
    $.ajax({
        dataType: 'json',
        url: url + selected,
        method: "PATCH",
        success: function (data, status, xhr) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.message);
            }
            rlist.jqxGrid('updatebounddata', 'data');
            rlist.on("bindingcomplete", function (event) {
                let newindex = rlist.jqxGrid('getrowboundindexbyid', selected);
                rlist.jqxGrid('selectrow', newindex);
                rlist.jqxGrid('ensurerowvisible', newindex);
            });
        },
        error: xhrErrorNotificationHandler
    });
}

function reorderColumns(url) {
    let selected = getCurrentColumn();
    if (!selected) {
        raiseError("Графа не выбрана");
        return false;
    }
    $.ajax({
        dataType: 'json',
        url: url + selected,
        method: "PATCH",
        success: function (data, status, xhr) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.message);
            }
            clist.jqxGrid('updatebounddata', 'data');
            clist.on("bindingcomplete", function (event) {
                let newindex = clist.jqxGrid('getrowboundindexbyid', selected);
                clist.jqxGrid('selectrow', newindex);
                clist.jqxGrid('ensurerowvisible', newindex);
            });
        },
        error: xhrErrorNotificationHandler
    });
}