let initsplitter = function() {
    $("#mainSplitter").jqxSplitter(
        {
            width: '100%',
            height: '100%',
            theme: theme,
            panels:
                [
                    { size: '55%', min: '10%'},
                    { size: '45%', min: '10%'}
                ]
        }
    );
};
let initfilterdatasources = function() {
    let forms_source =
    {
        datatype: "json",
        datafields: [
            { name: 'id' },
            { name: 'form_code' }
        ],
        id: 'id',
        localdata: forms
    };
    formsDataAdapter = new $.jqx.dataAdapter(forms_source);
};
let initdatasources = function() {
    let tablesource =
    {
        datatype: "json",
        datafields: [
            { name: 'form_code', map: 'form>form_code', type: 'string' },
            { name: 'excluded', map: 'excluded>0>id', type: 'int' },
            { name: 'id', type: 'int' },
            { name: 'table_index', type: 'int' },
            { name: 'form_id', type: 'int' },
            { name: 'table_name', type: 'string' },
            { name: 'table_code', type: 'string' },
            { name: 'transposed', type: 'int' },
            { name: 'medstat_code', type: 'string' },
            { name: 'medstatnsk_id', type: 'int' }
        ],
        id: 'id',
        url: 'fetchtables',
        root: 'table'
    };
    tableDataAdapter = new $.jqx.dataAdapter(tablesource);
};
let inittablelist = function() {
    tlist.jqxGrid(
        {
            width: '98%',
            height: '90%',
            theme: theme,
            localization: localize(),
            source: tableDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            columns: [
                { text: 'Id', datafield: 'id', width: '30px' },
                { text: '№ п/п', datafield: 'table_index', width: '50px' },
                { text: 'Код формы', datafield: 'form_code', width: '70px'  },
                { text: 'Исключена из альбома', datafield: 'excluded' , columntype: 'checkbox', width: '90px' },
                { text: 'Код таблицы', datafield: 'table_code', width: '100px'  },
                { text: 'Имя', datafield: 'table_name' , width: '390px'},
                { text: 'Транспонирование', datafield: 'transposed', columntype: 'checkbox', width: '70px' },
                { text: 'Код МС (МСК)', datafield: 'medstat_code', width: '100px' },
                { text: 'Код МС (НСК)', datafield: 'medstatnsk_id', width: '100px' }
            ]
        });
    tlist.on('rowselect', function (event) {
        let row = event.args.row;
        rowid = row.id;
        nextAction = 'PATCH';
        enableOrderButtons();
        $("#placebefore").val("");
        $("#table_index").val(row.table_index);
        $("#table_name").val(row.table_name);
        $("#form_id").val(row.form_id);
        $("#table_code").val(row.table_code);
        $("#transposed").val( row.transposed === 1 );
        $("#medstat_code").val(row.medstat_code);
        $("#medstatnsk_id").val(row.medstatnsk_id);
        $("#excluded").val(typeof row.excluded !== 'undefined');
    });
};
let initformactions = function() {
    $("#form_id").jqxDropDownList({
        theme: theme,
        source: formsDataAdapter,
        displayMember: "form_code",
        valueMember: "id",
        placeHolder: "Выберите форму:",
        //selectedIndex: 2,
        width: 200,
        height: 34
    });
    $('#transposed').jqxSwitchButton({
        height: 31,
        width: 120,
        onLabel: 'Да',
        offLabel: 'Нет',
        checked: false });
    $('#excluded').jqxSwitchButton({
        height: 31,
        width: 120,
        onLabel: 'Да',
        offLabel: 'Нет',
        disabled : true,
        checked: false });
    $("#resetform").click(function () {
        tlist.jqxGrid('clearselection');
        $("#form_id").jqxDropDownList('clearSelection');
        disableOrderButtons();
        $("#table_name").focus();
        nextAction = 'POST';
        $("#form")[0].reset();
    });
    $("#create").click(function () {
        let data = setQueryString();
        $.ajax({
            dataType: 'json',
            url: store_url,
            method: "POST",
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                tlist.jqxGrid('updatebounddata', 'data');
            },
            error: function (xhr, status, errorThrown) {
                $.each(xhr.responseJSON, function(field, errorText) {
                    raiseError(errorText);
                });
            }
        });
    });
    $("#save").click(function () {
        let url;
        if (nextAction === 'PATCH') {
            if (rowid === null) {
                raiseError("Выберите запись для изменения/сохранения данных");
                return false;
            }
            url = update_url + rowid;
        } else if (nextAction === 'POST') {
            url = store_url;
        }
        let data = setQueryString();
        $.ajax({
            dataType: 'json',
            url: url,
            method: nextAction,
            data: data,
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                tlist.jqxGrid('updatebounddata', 'data');
                tlist.on("bindingcomplete", function (event) {
                    let newindex = tlist.jqxGrid('getrowboundindexbyid', rowid);
                    tlist.jqxGrid('selectrow', newindex);
                });
            },
            error: function (xhr, status, errorThrown) {
                $.each(xhr.responseJSON, function(field, errorText) {
                    raiseError(errorText);
                });
            }
        });
    });
    $("#delete").click(function () {
        let row = tlist.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для удаления");
            return false;
        }
        if (!confirm('Если таблица не включена в отчетные документы и не содержит данных, то она будет удалена вместе с входящими в нее строками, графами')) {
            return false;
        }
        let rowid = tlist.jqxGrid('getrowid', row);
        $.ajax({
            dataType: 'json',
            url: '/admin/tables/delete/' + rowid,
            method: "DELETE",
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                    $("#form")[0].reset();
                }
                tlist.jqxGrid('updatebounddata', 'data');
                tlist.jqxGrid('clearselection');
            },
            error: function (xhr, status, errorThrown) {
                raiseError('Ошибка удаления отчетного периода', xhr);
            }
        });
    });
};

let initOrderControls = function () {
    let top = $("#top");
    let up = $("#up");
    let down = $("#down");
    let bottom = $("#bottom");
    up.click(function () {
        reorderTables(up_url);
    });
    down.click(function () {
        reorderTables(down_url);
    });
    top.click(function () {
        reorderTables(top_url);
    });
    bottom.click(function () {
        reorderTables(bottom_url);
    })
};

function getCurrentRow() {
    let row = tlist.jqxGrid('getselectedrowindex');
    if (row === -1) {
        return false;
    }
    return tlist.jqxGrid('getrowid', row);
}

function reorderTables(url) {
    let selected = getCurrentRow();
    if (!selected) {
        raiseError("Таблица не выбрана");
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
            tlist.jqxGrid('updatebounddata', 'data');
            tlist.on("bindingcomplete", function (event) {
                let newindex = tlist.jqxGrid('getrowboundindexbyid', selected);
                tlist.jqxGrid('selectrow', newindex);
            });
        },
        error: function (xhr, status, errorThrown) {
            raiseError('Ошибка при изменении порядкового номера таблицы', xhr);
        }
    });
}

function setQueryString() {
    return "&form_id=" + $("#form_id").val() +
    "&table_index=" + $("#table_index").val() +
    "&table_code=" + $("#table_code").val() +
    "&table_name=" + encodeURIComponent($("#table_name").val()) +
    "&medstat_code=" + $("#medstat_code").val() +
    "&medstatnsk_id=" + $("#medstatnsk_id").val() +
    "&transposed=" + ($("#transposed").val() ? 1 :0) +
    "&excluded=" + ($("#excluded").val() ? 1 :0) +
    "&placebefore=" + $("#placebefore").val() ;
}

let disableOrderButtons = function () {
    $("#excluded").jqxSwitchButton({disabled : true});
    $("#top").prop('disabled', true);
    $("#up").prop('disabled', true);
    $("#down").prop('disabled', true);
    $("#bottom").prop('disabled', true);
    //$("#placebefore").prop('disabled', true);
};

let enableOrderButtons = function () {
    $("#excluded").jqxSwitchButton({disabled : false});
    $("#top").prop('disabled', false);
    $("#up").prop('disabled', false);
    $("#down").prop('disabled', false);
    $("#bottom").prop('disabled', false);
    //$("#placebefore").prop('disabled', false);
};