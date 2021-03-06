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
                    { size: '50%', min: '10%'},
                    { size: '50%', min: '10%'}
                ]
        }
    );
};
initdatasources = function() {
    let albumsource =
    {
        datatype: "json",
        datafields: [
            { name: 'id', type: 'int' },
            { name: 'album_name', type: 'string' },
            { name: 'default', type: 'bool' }
        ],
        id: 'id',
        url: album_url,
        root: 'album'
    };
    membersource =
    {
        datatype: "json",
        datafields: [
            { name: 'id', type: 'int' },
            { name: 'album_id', type: 'int' },
            { name: 'form_id', type: 'int' },
            { name: 'formcode', map: 'form>form_code', type: 'string' },
            { name: 'formname', map: 'form>form_name', type: 'string' }
        ],
        id: 'id',
        url: member_url + currentalbum,
        root: 'member'
    };
    AlbumDataAdapter = new $.jqx.dataAdapter(albumsource);
    MemberDataAdapter = new $.jqx.dataAdapter(membersource);
};
inittablelist = function() {
    agrid.jqxGrid(
        {
            width: '98%',
            height: '30%',
            theme: theme,
            localization: localize(),
            source: AlbumDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            columns: [
                { text: 'Id Альбома', datafield: 'id', width: '70px' },
                { text: 'Наименование', datafield: 'album_name' , width: '400px'},
                { text: 'По умолчанию', datafield: 'default', columntype: 'checkbox', width: '70px' }
            ]
        });
    agrid.on("bindingcomplete", function (event) {
        let index = agrid.jqxGrid('getrowboundindexbyid', currentalbum);
        agrid.jqxGrid('selectrow', index);
        agrid.jqxGrid('ensurerowvisible', index);
    });
    agrid.on('rowselect', function (event) {
        let row = event.args.row;
        currentalbum = row.id;
        membersource.url = member_url + row.id;
        mlist.jqxGrid('updatebounddata');
        $("#album_name").val(row.album_name);
        $("#default").val(row.default != null);
    });
    mlist.jqxGrid(
        {
            width: '98%',
            height: 430,
            theme: theme,
            localization: localize(),
            source: MemberDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            columns: [
                {
                text: '№ п/п', sortable: false, filterable: false, editable: false,
                groupable: false, draggable: false, resizable: false,
                datafield: '', columntype: 'number', width: 50,
                cellsrenderer: function (row, column, value) {
                        return "<div style='margin:4px;'>" + (value + 1) + "</div>";
                    }
                },
                { text: 'Код', datafield: 'formcode' , width: '100px'},
                { text: 'Наименование', datafield: 'formname' , width: '580px'}
            ]
        });
};
setquerystring = function() {
    return "&album_name=" + $("#album_name").val() +
        "&default=" + ($("#default").val() ? 1 : 0) +
        "&migrate=" + ($("#migrate").prop('checked') ? 1 : 0);
};
initalbumactions = function() {
    $("#insert").click(function () {
        $.ajax({
            dataType: 'json',
            url: albumcreate_url,
            method: "POST",
            data: setquerystring(),
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                agrid.jqxGrid('updatebounddata', 'data');
                agrid.on("bindingcomplete", function (event) {
                    let newindex = fgrid.jqxGrid('getrowboundindexbyid', data.id);
                    agrid.jqxGrid('selectrow', newindex);
                    agrid.jqxGrid('ensurerowvisible', newindex);
                });
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#update").click(function () {
        let row = agrid.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для изменения/сохранения данных");
            return false;
        }
        let rowid = agrid.jqxGrid('getrowid', row);
        $.ajax({
            dataType: 'json',
            url: albumupdate_url + rowid,
            method: "PATCH",
            data: setquerystring(),
            success: function (data, status, xhr) {
                if (typeof data.error !== 'undefined') {
                    raiseError(data.message);
                } else {
                    raiseInfo(data.message);
                }
                agrid.jqxGrid('updatebounddata', 'data');
                agrid.on("bindingcomplete", function (event) {
                    let newindex = agrid.jqxGrid('getrowboundindexbyid', rowid);
                    agrid.jqxGrid('selectrow', newindex);
                    agrid.jqxGrid('ensurerowvisible', newindex);
                });
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#delete").click(function () {
        let row = agrid.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для удаления");
            return false;
        }
        //let rowid = agrid.jqxGrid('getrowid', row);
        raiseConfirm("<strong>Внимание!</strong> Выбранный альбом будет удален вместе со всеми входящими в состав элементами.");
    });
};

getcheckedforms = function() {
    let ids = [];
    let f = $('#Forms');
    var selected = f.jqxGrid('getselectedrowindexes');
    for (i = 0; i < selected.length; i++) {
        ids[i] =   f.jqxGrid('getrowid', selected[i]);
    }
    return ids;
};

initmemberactions = function() {
    $("#insertmembers").click(function() {
        if (agrid.jqxGrid('getselectedrowindex') === -1) {
            raiseError("Выберите альбом для добавления форм");
            return false;
        }
        let selectedforms = getcheckedforms();
        let data = "&forms=" + selectedforms;
        $.ajax({
            dataType: 'json',
            url: addmembers_url + currentalbum,
            method: "POST",
            data: data,
            success: function (data, status, xhr) {
                if (data.count_of_inserted > 0) {
                    raiseInfo("Добавлено форм в альбом " + data.count_of_inserted);
                    mlist.jqxGrid('clearselection');
                    mlist.jqxGrid('updatebounddata');
                }
                else {
                    raiseError("Формы не добавлены");
                }
            },
            error: xhrErrorNotificationHandler
        });
    });
    $("#removemember").click(function() {
        let row = mlist.jqxGrid('getselectedrowindex');
        if (row === -1) {
            raiseError("Выберите запись для удаления из списка форм, входящих в текущий альбом");
            return false;
        }
        let rowid = mlist.jqxGrid('getrowid', row);
        $.ajax({
            dataType: 'json',
            url: removemember_url + rowid,
            method: "DELETE",
            success: function (data, status, xhr) {
                if (data.member_deleted) {
                    raiseInfo(data.message);
                    mlist.jqxGrid('clearselection');
                    mlist.jqxGrid('updatebounddata');
                }
                else {
                    raiseError("Форма из альбома не удалена");
                }
            },
            error: xhrErrorNotificationHandler
        });
    });
};

initformlist = function() {
    var form_source =
    {
        dataType: "json",
        dataFields: [
            { name: 'id', type: 'int' },
            { name: 'form_code', type: 'string' },
            { name: 'form_name', type: 'string' }
        ],
        id: 'id',
        root: '',
        url: form_url
    };
    FormDataAdapter = new $.jqx.dataAdapter(form_source);
    $("#FormContainer").jqxPanel({width: '100%', height: '350px'});
    $("#Forms").jqxGrid(
        {
            width: '98%',
            height: '99%',
            theme: theme,
            localization: localize(),
            source: FormDataAdapter,
            columnsresize: true,
            showfilterrow: true,
            filterable: true,
            sortable: true,
            selectionmode: 'checkbox',
            columns: [
                {text: 'Код', dataField: 'form_code', width: 150},
                {text: 'Наименование', dataField: 'form_name'}
            ]
        });

};

initButtons = function() {
    $('#default').jqxSwitchButton({
        height: 31,
        width: 81,
        onLabel: 'Да',
        offLabel: 'Нет',
        checked: false
    });
};

performAction = function() {
    $.ajax({
        dataType: 'json',
        url: albumdelete_url + currentalbum,
        method: "DELETE",
        success: function (data, status, xhr) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.message);
            } else {
                raiseInfo(data.message);
                $("#form")[0].reset();
            }
            agrid.jqxGrid('updatebounddata', 'data');
            agrid.jqxGrid('clearselection');
        },
        error: xhrErrorNotificationHandler
    });
};