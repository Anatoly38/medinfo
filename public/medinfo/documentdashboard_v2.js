let mon_tree_url = 'datainput/fetch_mon_tree/';
let mo_tree_url = 'datainput/fetch_mo_tree/';
let group_tree_url = 'datainput/fetch_ugroups';
let docsource_url = 'datainput/fetchdocuments?';
let recentdocs_url = 'datainput/fetchrecent?';
let docmessages_url = 'datainput/fetchmessages?';
let changestate_url = 'datainput/changestate';
let changeaudition_url = 'datainput/changeaudition';
//let docmessagesend_url = 'datainput/sendmessage';
let docauditions_url = 'datainput/fetchauditions?';
let aggrsource_url = 'datainput/fetchaggregates?';
//let edit_form_url = 'datainput/formdashboard/';
let edit_form_url = 'datainput/formdashboard_v2/'; // Новая версия рабочего стола для редактирования отчетного документа
let edit_aggregate_url = 'datainput/aggregatedashboard';
let edit_consolidate_url = 'datainput/consolidatedashboard';
let aggregatedata_url = "/datainput/aggregatedata/";
let export_word_url = "/datainput/wordexport/";
let export_form_url = "/datainput/formexport/";
let consolsource_url = 'datainput/fetchconsolidates?';
let montree = $("#monTree");
let motree = $("#moTree");
let ouarray = [];
let oucount = 0;
let grouptree = $("#groupTree");
let tl = $("#TableDataLoader");

let stateList = $("#statesListbox");
let dgrid = $("#Documents"); // сетка для первичных документов
let primary_mo_bc = $("#mo_parents_breadcrumb");
let agrid = $("#Aggregates"); // сетка для сводных документов
let cgrid = $("#Consolidates"); // сетка для консолидированных документов
let rgrid = $("#Recent"); // сетка для последних документов
let mondropdown = $("#monitoringSelector");
let terr = $("#moSelectorByTerritories");
let groups = $('#moSelectorByGroups');

let statusDropDown = $('#statusSelector');
let dataPresenseDDown = $('#dataPresenceSelector');
let doc_id = null;
let docstate_id;
let current_document_form_code;
let current_document_form_name;
let current_document_ou_name;
let doc_state;
//let currentlet_document_audits = [];
let statelabels =
    {
        performed: 'Выполняется',
        inadvance: 'Подготовлен к предварительной проверке',
        prepared: 'Подготовлен к проверке',
        accepted: 'Принят',
        declined: 'Возвращен на доработку',
        approved: 'Утвержден'
    };
// Установка разбивки окна на области
initSplitters = function () {
    $("#mainSplitter").jqxSplitter(
        {
            width: '100%',
            height: '100%',
            theme: theme,
            panels:
                [
                    { size: "370px", min: "100px"},
                    { size: '82%', min: "30%"}
                ]
        }
    );
    $('#DocumentPanelSplitter').jqxSplitter({
        width: '100%',
        height: '100%',
        theme: theme,
        orientation: 'horizontal',
        panels: [{ size: '70%', min: 100, collapsible: false }, { min: '100px', collapsible: true}]
    });
};
// Инициализация источников данных для таблиц
datasources = function() {
    let mon_source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'parent_id', type: 'int' },
                { name: 'name', type: 'string' }
            ],
            hierarchy:
                {
                    keyDataField: { name: 'id' },
                    parentDataField: { name: 'parent_id' }
                },
            id: 'id',
            root: '',
            url: mon_tree_url
        };
    let mo_source =
    {
        dataType: "json",
        dataFields: [
            { name: 'id', type: 'int' },
            { name: 'parent_id', type: 'int' },
            { name: 'unit_code', type: 'string' },
            { name: 'unit_name', type: 'string' }
        ],
        hierarchy:
        {
            keyDataField: { name: 'id' },
            parentDataField: { name: 'parent_id' }
        },
        id: 'id',
        root: '',
        url: mo_tree_url + current_user_scope
    };

    let ugroup_source =
    {
        dataType: "json",
        dataFields: [
            { name: 'id', type: 'int' },
            { name: 'slug', type: 'string' },
            { name: 'name', type: 'string' }
        ],
        hierarchy:
        {
            keyDataField: { name: 'id' },
            parentDataField: { name: 'parent_id' }
        },
        id: 'id',
        root: '',
        url: group_tree_url
    };
    mon_dataAdapter = new $.jqx.dataAdapter(mon_source);
    mo_dataAdapter = new $.jqx.dataAdapter(mo_source);
    ugroup_dataAdapter = new $.jqx.dataAdapter(ugroup_source);

};
// Возвращает выбранные мониторинги и формы для отображения соответствующих отчетов
getCheckedMonsForms = function() {
    let monitorings = [];
    let forms = [];
    let mf = [];
    let checkedRows;
    let uniquemonitorings;
    let uniqueforms;
    checkedRows = montree.jqxTreeGrid('getCheckedRows');
    //console.log(checkedRows);
    if (typeof checkedRows !== 'undefined') {
        for (let i = 0; i < checkedRows.length; i++) {
            mf.push(checkedRows[i].id);
            let r = checkedRows[i].id.toString();
                monitorings.push(r.substr(0,6));
                let form_id = r.substr(6);
                if (form_id) {
                    forms.push(form_id);
                }
        }
    }
    //console.log(forms);
    uniquemonitorings = Array.from(new Set(monitorings));
    uniqueforms = Array.from(new Set(forms));
    if (uniquemonitorings.length > 0 || uniqueforms.length > 0 ) {
        updateDropDown(mondropdown, 'Мониторинги', 'Мониторинги выбраны', true );
    } else {
        updateDropDown(mondropdown, 'Мониторинги', 'Фильтр по отображению мониторингов отключен', false );
    }
    return {f: uniqueforms, m: uniquemonitorings, mf: mf};
};

checkstatefilter = function() {
    let checkedstates = [];
    let checkedItems = stateList.jqxListBox('getCheckedItems');
    if (typeof checkedItems !== 'undefined') {
        let statecount = checkedItems.length;
        for (i = 0; i < statecount; i++) {
            checkedstates.push(checkedItems[i].value);
        }
    }
    if (checkedstates.length > 0) {
        updateDropDown(statusDropDown, 'Статусы отчетов', 'Статусы документов выбраны', true );
    } else {
        updateDropDown(statusDropDown, 'Статусы отчетов', 'Фильтр по статусам документов отключен', false );
    }
    return checkedstates.join();
};

checkDataPresenceFilter = function() {
    switch (true) {
        case $("#alldoc").prop("checked") :
            updateDropDown(dataPresenseDDown, 'Наличие данных', 'Фильтр на наличие данных отключен', false );
            return '-1';
        case $("#filleddoc").prop("checked") :
            updateDropDown(dataPresenseDDown, 'Наличие данных', 'Выбраны документы с заполненными данными', true );
            return '1';
        case $("#emptydoc").prop("checked") :
            updateDropDown(dataPresenseDDown, 'Наличие данных', 'Выбраны пустые документы', true );
            return '0';
    }
};

function updateDropDown(dd, caption, title, on) {
    if (on) {
        dd.css('background-color', '#ceeaff');
        dd.prop('title', title);
        dd.jqxDropDownButton('setContent', '<div style="margin: 9px"><i class="fal fa-check fa-lg pull-right" style="color: #337ab7;"></i>' + caption +'</div>');
    } else if (!on) {
        dd.css('background-color', '#ffffff');
        dd.prop('title', title);
        dd.jqxDropDownButton('setContent', '<div style="margin: 9px">' + caption +'</div>');
    }
}
// обновление таблиц первичных и сводных документов в зависимости от выделенных форм, периодов, статусов документов
updatedocumenttable = function() {
    let old_doc_url = docsource.url;
    let old_aggr_url = aggregate_source.url;
    let old_cons_url = consolsource.url;
    //let states = checkedstates.join();
    //let forms = checkedforms.join();
    //let periods = checkedperiods.join();
    let new_filter =  filtersource();
    let new_doc_url = docsource_url + new_filter;
    let new_aggr_url = aggrsource_url + new_filter;
    let new_cons_url = consolsource_url + new_filter;
    if (new_doc_url !== old_doc_url) {
        docsource.url = new_doc_url;
        tl.jqxLoader('open');
        dgrid.jqxGrid('updatebounddata');
        $("#DocumentMessages").html('');
        $("#DocumentAuditions").html('');
    }
    if (new_aggr_url !== old_aggr_url) {
        aggregate_source.url = new_aggr_url;
        agrid.jqxGrid('updatebounddata');
    }
    if (new_cons_url !== old_cons_url) {
        consolsource.url = new_cons_url;
        cgrid.jqxGrid('updatebounddata');
    }

};
// выполнение сведения данных
aggregatedata = function() {
    let rowindex = agrid.jqxGrid('getselectedrowindex');
    let row_id = agrid.jqxGrid('getrowid', rowindex);
    let rowdata = agrid.jqxGrid('getrowdata', rowindex);
    if (rowindex === -1) {
        return false;
    }
    //var data = "aggregate=" + row_id;
    $.ajax({
        dataType: 'json',
        url: aggregatedata_url + row_id + '/' + filter_mode,
        method: "GET",
        //data: data,
        success: function (data, status, xhr) {
            if (typeof data.affected_cells !== 'undefined') {
                if (data.affected_cells > 0) {
                    raiseInfo("Сведение данных завершено");
                    rowdata.aggregated_at = data.aggregated_at;
                    agrid.jqxGrid('updaterow', row_id, rowdata);
                }
                else {
                    raiseError("Отсутствуют данные в первичных документах");
                }
            }
            else {
                if (data.aggregate_status === 500) {
                    raiseError("Сведение данных не выполнено! " + data.error_message);
                }
            }
        },
        error: function (xhr, status, errorThrown) {
            raiseError("Ошибка сведения данных на сервере.  Обратитесь к администратору", xhr);
        }
    });
};
// Установка класса для обозначения заполненных/пустых документов
filledFormclass = function (row, columnfield, value, rowdata) {
    if (rowdata.filled) {
        return 'filledForm';
    }
};
// Установка класса для раскрашивания строк в зависимости от статуса документа
formStatusclass = function (row, columnfield, value, rowdata) {
    switch (value) {
        case statelabels.performed :
        case statelabels.inadvance :
            return 'editedStatus';
        case statelabels.prepared :
            return 'preparedStatus';
        case statelabels.accepted :
            return 'acceptedStatus';
        case statelabels.approved :
            return 'approvedStatus';
        case statelabels.declined :
            return 'declinedStatus';
        default:
            return '';
    }
};
// фильтр для быстрого поиска по наименованию учреждения - первичные документы
mo_name_filter = function (needle) {
    let rowFilterGroup = new $.jqx.filter();
    let filter_or_operator = 1;
    let filtervalue = needle;
    let filtercondition = 'contains';
    let nameRecordFilter = rowFilterGroup.createfilter('stringfilter', filtervalue, filtercondition);
    rowFilterGroup.addfilter(filter_or_operator, nameRecordFilter);
    dgrid.jqxGrid('addfilter', 'unit_name', rowFilterGroup);
    dgrid.jqxGrid('applyfilters');
};
// фильтр для быстрого поиска по наименованию учреждения/территории - сводные документы
mo_name_aggrfilter = function (needle) {
    let rowFilterGroup = new $.jqx.filter();
    let filter_or_operator = 1;
    let filtervalue = needle;
    let filtercondition = 'contains';
    let nameRecordFilter = rowFilterGroup.createfilter('stringfilter', filtervalue, filtercondition);
    rowFilterGroup.addfilter(filter_or_operator, nameRecordFilter);
    agrid.jqxGrid('addfilter', 'unit_name', rowFilterGroup);
    agrid.jqxGrid('applyfilters');
};
// Новый рендеринг панели инструментов для первичных документов
primaryDocToolbar = function() {
    let searchField = $("#searchUnit");
    let di = $("#documentInfo");
    let oldVal = '';
    let me = {};
    searchField.on('keydown', function (event) {
        if (searchField.val().length >= 2) {
            if (me.timer) {
                clearTimeout(me.timer);
            }
            if (oldVal !== searchField.val()) {
                me.timer = setTimeout(function () {
                    mo_name_filter(searchField.val());
                }, 500);
                oldVal = searchField.val();
            }
        }
        else {
            dgrid.jqxGrid('removefilter', '1');
        }
    });
    $("#clearFilter").click(function () {
        dgrid.jqxGrid('clearfilters');
        searchField.val('');
        oldVal = '';
    });

    $("#editPrimaryDocument").click(function () {
        let rowindex = dgrid.jqxGrid('getselectedrowindex');
        if (rowindex !== -1 && typeof rowindex !== 'undefined') {
            let document_id = dgrid.jqxGrid('getrowid', rowindex);
            if (document_id === null) {
                raiseError('Не выбран документ для редактирования');
                return false;
            }
            let editWindow = window.open(edit_form_url + document_id);
        }
    });

    $("#changeDocumentState").click(function () {
        let stateWindow = $('#changeStateWindow');
        let rowindex = dgrid.jqxGrid('getselectedrowindex');
        let this_document_state = '';
        if (rowindex === -1 || typeof rowindex === 'undefined') {
            raiseError('Не выбран документ для для смены статуса');
            return false;
        }
        let data = dgrid.jqxGrid('getrowdata', rowindex);
        if (typeof data === 'undefined') {
            raiseError('Не выбран документ для смены статуса');
            return false;
        }
        if (!data.filled && current_user_role === '1') {
            raiseError('Внимание! Документ не содержит данные. Необходимо, В ОБЯЗАТЕЛЬНОМ ПОРЯДКЕ, пояснить в сообщении по какой причине!');
            $("#statusChangeMessage").val('Документ не заполнен по причине: ');
        } else {
            $("#statusChangeMessage").val('');
        }
        let radiostates = $('.stateradio');
        radiostates.each(function() {
            let state = $(this).attr('id');
            if ($.inArray(state, disabled_states) !== -1) {
                $(this).jqxRadioButton('disable');
            }
            if (statelabels[state] === data.state) {
                $(this).jqxRadioButton('check');
                this_document_state = state;
            }
        });
        $('#changeStateFormCode').html(data.form_code);
        $('#changeStateMOCode').html(data.unit_code);
        if (current_user_role === '1' && this_document_state !== 'performed' && this_document_state !== 'inadvance' && this_document_state !== 'declined') {
            //$('#inadvance').jqxRadioButton('disable');
            $('#prepared').jqxRadioButton('disable');
        } else if (current_user_role === '1' && this_document_state === 'performed') {
            //$('#inadvance').jqxRadioButton('enable');
            $('#prepared').jqxRadioButton('enable');
        } else if (current_user_role === '1' && this_document_state === 'declined') {
            //$('#inadvance').jqxRadioButton('enable');
            $('#prepared').jqxRadioButton('enable');
        } else if (current_user_role === '1' && this_document_state === 'inadvance') {
            //console.log("Переход с предварительной проверки");
            //$('#inadvance').jqxRadioButton('disable');
            $('#prepared').jqxRadioButton('enable');
            $('#performed').jqxRadioButton('enable');
        }
        if ((current_user_role === '3' || current_user_role === '4') && this_document_state === 'performed') {
            $('#declined').jqxRadioButton('disable');
        } else if ((current_user_role === '3' || current_user_role === '4') && this_document_state !== 'performed') {
            $('#declined').jqxRadioButton('enable');
        }
        stateWindow.jqxWindow('open');
    });

    $("#commentingDocument").click(function () {
        let sm = $("#sendMessageWindow");
        let rowindex = dgrid.jqxGrid('getselectedrowindex');
        if (rowindex === -1 || typeof rowindex === 'undefined') {
            raiseError('Не выбран документ для отправки сообщения/комментирования');
            return false;
        }
        if (doc_id === null) {
            raiseError('Не выбран документ для отправки сообщения/комментирования');
            return false;
        }
        $("#message").val("");
        sm.jqxWindow('open');
    });
    $("#documentWordExport").click(function () {
        let rowindex = dgrid.jqxGrid('getselectedrowindex');
        let document_id = dgrid.jqxGrid('getrowid', rowindex);
        if (document_id !== null) {
            location.replace(export_word_url + document_id);
        } else {
            raiseError('Не выбран документ для экспорта в MS Word');
        }
    });
    $("#documentExcelExport").click(function () {
        let rowindex = dgrid.jqxGrid('getselectedrowindex');
        let document_id = dgrid.jqxGrid('getrowid', rowindex);
        if (document_id !== null) {
            location.replace(export_form_url + document_id);
        } else {
            raiseError('Не выбран документ для экспорта в MS Excel');
        }
    });
    di.click(function () {
        $("#DocumentInfoWindow").jqxWindow('open');
    });
    $("#refreshPrimaryDocumentList").click(function () {
        docsource.url = docsource_url + filtersource();
        tl.jqxLoader('open');
        dgrid.jqxGrid('updatebounddata');
        $("#DocumentMessages").html('');
        $("#DocumentAuditions").html('');
    });

    if (current_user_role === '1' || current_user_role === '2') {
        di.hide();
    }
};
// Новый рендеринг панели инструментов для сводных документов
aggregateDocToolbar = function() {
    let searchField = $("#searchAggregateUnit");
    let oldVal = '';
    let me = {};
    searchField.on('keydown', function (event) {
        if (searchField.val().length >= 2) {
            if (me.timer) {
                clearTimeout(me.timer);
            }
            if (oldVal !== searchField.val()) {
                me.timer = setTimeout(function () {
                    mo_name_aggrfilter(searchField.val());
                }, 500);
                oldVal = searchField.val();
            }
        }
        else {
            dgrid.jqxGrid('removefilter', '1');
        }
    });
    $("#clearAggregateFilter").click(function () {
        agrid.jqxGrid('clearfilters');
        searchField.val('');
        oldVal = '';
    });
    $("#viewDocument").click(function () {
        let rowindex = agrid.jqxGrid('getselectedrowindex');
        let document_id = agrid.jqxGrid('getrowid', rowindex);
        if (rowindex !== -1) {
            let editWindow = window.open(edit_aggregate_url+'/'+document_id);
        }
    });
    $("#aggregateDocument").click( function() {
        aggregatedata();
    });

    $("#aggregateWordExport").click(function () {
        let rowindex = agrid.jqxGrid('getselectedrowindex');
        let document_id = agrid.jqxGrid('getrowid', rowindex);
        if (rowindex !== -1) {
            location.replace(export_word_url + document_id);
        }
    });

    $("#aggregateExcelExport").click(function () {
        let rowindex = agrid.jqxGrid('getselectedrowindex');
        let document_id = agrid.jqxGrid('getrowid', rowindex);
        if (rowindex !== -1) {
            location.replace(export_form_url + document_id);
        }
    });
    $("#refreshAggregateDocumentList").click(function () {
        aggregate_source.url = aggrsource_url + filtersource();
        agrid.jqxGrid('updatebounddata');
    });

};
// Инициализация элементов управления с выпадающими списками
initDropdowns = function () {
    if (current_user_role === '1') {
        filter_mode = 1;
        groups.jqxDropDownButton({disabled:true});
    }
    $("#clearAllFilters").click( clearAllFilters );
};

clearAllFilters = function (event) {
    let checkedMonitorings = montree.jqxTreeGrid('getCheckedRows');
    if (typeof checkedMonitorings !== 'undefined') {
        for (let i = 0; i < checkedMonitorings.length; i++) {
            montree.jqxTreeGrid('uncheckRow' , checkedMonitorings[i].id);
        }
    }
    terr.jqxTreeGrid('clearSelection');
    groups.jqxTreeGrid('clearSelection');
    let checkedPeriods = periodTree.jqxTreeGrid('getCheckedRows');
    if (typeof checkedPeriods !== 'undefined') {
        for (let i = 0; i < checkedPeriods.length; i++) {
            periodTree.jqxTreeGrid('uncheckRow' , checkedPeriods[i].id);
        }
    }
    stateList.jqxListBox('uncheckAll');
    $("#alldoc").prop('checked', 'checked');
    updateDropDown(terr, 'Медицинские организации (по территориям)', 'Отображаются документы по всем медицинским организациям', false );
    updateDropDown(groups, 'Медицинские организации (по группам)', 'Фильтр отображению документов МО по группам отключен', false );
    updateDropDown(mondropdown, 'Мониторинги', 'Фильтр по отображению мониторингов отключен', false );
    updateDropDown(periodDropDown, 'Отчетные периоды', 'Фильтр по отчетным периодам отключен', false );
    updateDropDown(statusDropDown, 'Статусы отчетов', 'Фильтр по статусам документов отключен', false );
    updateDropDown(dataPresenseDDown, 'Наличие данных', 'Фильтр на наличие данных отключен', false );
    updatedocumenttable();
};
initMonitoringTree = function () {
    mondropdown.jqxDropDownButton({width: 350, height: 32, theme: theme});
    montree.jqxTreeGrid(
        {
            width: 900,
            height: 600,
            theme: theme,
            source: mon_dataAdapter,
            selectionMode: "singleRow",
            showToolbar: true,
            renderToolbar: montreeToolbar,
            filterable: true,
            filterMode: "simple",
            localization: localize(),
            checkboxes: true,
            hierarchicalCheckboxes: true,
            columnsResize: true,
            autoRowHeight: false,
            ready: function()
            {
                montree.jqxTreeGrid('expandRow', 100000);
                for (let i = 0; i < checkedmf.length; i++) {
                    montree.jqxTreeGrid('checkRow', checkedmf[i]);
                }
            },
            columns: [
                { text: 'Наименование мониторинга/отчетной формы', dataField: 'name', width: 900 }
            ]
        });
    montree.on('filter',
        function (event)
        {
            let args = event.args;
            let filters = args.filters;
            montree.jqxTreeGrid('expandAll');
        }
    );
    if (checkedmf.length > 0) {
        updateDropDown(mondropdown, 'Мониторинги', 'Мониторинги выбраны', true );
    } else {
        updateDropDown(mondropdown, 'Мониторинги', 'Фильтр по отображению мониторингов отключен', false );
    }
};
montreeToolbar = function (toolbar) {
    toolbar.append("<button type='button' id='moncollapseAll' class='btn btn-default btn-sm'>Свернуть все</button>");
    toolbar.append("<button type='button' id='monexpandAll' class='btn btn-default btn-sm'>Развернуть все</button>");
    toolbar.append("<button type='button' id='monfilterApply' class='btn btn-primary btn-sm'>Применить фильтр</button>");
    $('#monexpandAll').click(function (event) {
        montree.jqxTreeGrid('expandAll');
    });
    $('#moncollapseAll').click(function (event) {
        montree.jqxTreeGrid('collapseAll');
        montree.jqxTreeGrid('expandRow', 0);
    });
    $('#monfilterApply').click(function (event) {
        mondropdown.jqxDropDownButton('close');
        updatedocumenttable();
        return true;
    });
};
// инициализация дерева Территорий/Медицинских организаций
initMoTree = function() {
    terr.jqxDropDownButton({width: 350, height: 32, theme: theme});
    motree.on('bindingComplete', function (event) {
        let tree = motree.jqxTreeGrid('getRows');
        var traverseTree = function(tree)
        {
            for(var i = 0; i < tree.length; i++)
            {
                ouarray.push({ id: tree[i].id, parent: tree[i].parent ? tree[i].parent['id'] : null, unit: tree[i].unit_name}) ;
                if (tree[i].records) {
                    traverseTree(tree[i].records);
                }
            }
        };
        traverseTree(tree);
        oucount = ouarray.length;
    });
    motree.jqxTreeGrid(
        {
            width: 770,
            height: 600,
            theme: theme,
            source: mo_dataAdapter,
            selectionMode: "singleRow",
            showToolbar: true,
            renderToolbar: motreeToolbar,
            hierarchicalCheckboxes: false,
            checkboxes: false,
            filterable: true,
            filterMode: "simple",
            localization: localize(),
            columnsResize: true,
            ready: function()
            {
                motree.jqxTreeGrid('expandRow', 0);
            },
            columns: [
                { text: 'Код', dataField: 'unit_code', width: 170 },
                { text: 'Наименование', dataField: 'unit_name', width: 585 }
            ]
        });
    motree.on('filter',
        function (event)
        {
            let args = event.args;
            let filters = args.filters;
            motree.jqxTreeGrid('expandAll');
        }
    );
    motree.on('rowSelect',
        function (event)
        {
            let args = event.args;
            let new_top_level_node = args.key;
            if (new_top_level_node === current_top_level_node && filter_mode === 1) {
                return false;
            }
            current_top_level_node =  new_top_level_node;
            filter_mode = 1; // режим отбора документов по территориям
            updatedocumenttable();
            terr.jqxDropDownButton('close');
/*            if (current_top_level_node == 0) {
                updateDropDown(terr, 'Медицинские организации (по территориям)', 'Отображаются документы по всем медицинским организациям', false );
            } else {
                updateDropDown(terr, 'Медицинские организации (по территориям)', 'Документы МО отображаются по-территориально', true );
            }*/
            updateDropDown(terr, 'Медицинские организации (по территориям)', 'Документы МО отображаются по-территориально', true );
            updateDropDown(groups, 'Медицинские организации (по группам)', 'Фильтр отображению документов МО по группам отключен', false );
            return true;
        }
    );
    if (filter_mode === 1) {
        updateDropDown(terr, 'Медицинские организации (по территориям)', 'Документы МО отображаются по-территориально', true );
        updateDropDown(groups, 'Медицинские организации (по группам)', 'Фильтр отображению документов МО по группам отключен', false );
    } else if (filter_mode === 2) {
        updateDropDown(groups, 'Медицинские организации (по группам)', 'Документов МО отображаются по группам', true );
        updateDropDown(terr, 'Медицинские организации (по территориям)', 'Фильтр отображению документов МО по территориям отключен', false );
    }
};
motreeToolbar = function (toolbar) {
    toolbar.append("<button type='button' id='collapseAll' class='btn btn-default btn-sm'>Свернуть все</button>");
    toolbar.append("<button type='button' id='expandAll' class='btn btn-default btn-sm'>Развернуть все</button>");
    $('#expandAll').click(function (event) {
        motree.jqxTreeGrid('expandAll');
    });
    $('#collapseAll').click(function (event) {
        motree.jqxTreeGrid('collapseAll');
        motree.jqxTreeGrid('expandRow', 0);
    });
};
// инициализация выбора отчетов по группе учреждений
initGroupTree = function() {
    groups.jqxDropDownButton({width: 350, height: 32, theme: theme});
    grouptree.jqxTreeGrid(
        {
            width: '670px',
            height: '600px',
            theme: theme,
            source: ugroup_dataAdapter,
            selectionMode: "singleRow",
            filterable: true,
            filterMode: "simple",
            localization: localize(),
            columnsResize: true,
            ready: function()
            {
                grouptree.jqxTreeGrid('expandRow', 0);
            },
            columns: [
                //{ text: 'Код', dataField: 'group_code', width: 120 },
                { text: 'Сокр', dataField: 'slug', width: 120 },
                { text: 'Наименование', dataField: 'name', width: 545 }
            ]
        });
    if (current_user_role === '1') {
        grouptree.jqxTreeGrid({ disabled:true });
    }
    grouptree.on('filter',
        function (event)
        {
            let args = event.args;
            let filters = args.filters;
            grouptree.jqxTreeGrid('expandAll');
        }
    );
    grouptree.on('rowSelect',
        function (event)
        {
            let args = event.args;
            let new_top_level_node = args.key;
            if (new_top_level_node === current_top_level_node && filter_mode === 2) {
                return false;
            }
            filter_mode = 2; // режим отбора документов по группам
            current_top_level_node =  new_top_level_node;
            updatedocumenttable();
            groups.jqxDropDownButton('close');
            updateDropDown(groups, 'Медицинские организации (по группам)', 'Документов МО отображаются по группам', true );
            updateDropDown(terr, 'Медицинские организации (по территориям)', 'Фильтр отображению документов МО по территориям отключен', false );
            return true;
        }
    );
};

// инициализация списка статусов отчетного документа
initStateList = function() {
    let checkAll = $("#checkAllStates");
    let uncheckAll = $("#clearAllStates");
    statusDropDown.jqxDropDownButton({width: 350, height: 32, theme: theme});
    let states_source =
    {
        datatype: "array",
        datafields: [
            { name: 'code' },
            { name: 'name' }
        ],
        id: 'code',
        localdata: states
    };
    statesDataAdapter = new $.jqx.dataAdapter(states_source);
    stateList.jqxListBox({
        theme: theme,
        source: statesDataAdapter,
        displayMember: 'name',
        valueMember: 'code',
        checkboxes: true,
        width: 290,
        height: 200
    });
    for(let i = 0; i < checkedstates.length; i++) {
        stateList.jqxListBox('checkItem', checkedstates[i]);
    }
    checkAll.click( function (event) {
            stateList.jqxListBox('checkAll');
    });
    uncheckAll.click( function (event) {
        stateList.jqxListBox('uncheckAll');
    });
    $("#applyStatuses").click( function (event) {
        statusDropDown.jqxDropDownButton('close');
        updatedocumenttable();
    });

    if (checkedstates.length > 0) {
        updateDropDown(statusDropDown, 'Статусы отчетов', 'Статусы документов выбраны', true );
    } else {
        updateDropDown(statusDropDown, 'Статусы отчетов', 'Фильтр по статусам документов отключен', false );
    }

};
initDataPresens = function() {
    dataPresenseDDown.jqxDropDownButton({width: 350, height: 32, theme: theme});
    if (current_user_role === '3' || current_user_role === '4' ) {
        dataPresenseDDown.show();
    }
    $("#applyDataPresence").click( function (event) {
        dataPresenseDDown.jqxDropDownButton('close');
        updatedocumenttable();
    });
    switch (checkedfilled) {
        case '-1' :
            $("#alldoc").prop('checked', 'checked');
            updateDropDown(dataPresenseDDown, 'Наличие данных', 'Фильтр на наличие данных отключен', false );
            break;
        case '1' :
            $("#filleddoc").prop('checked', 'checked');
            updateDropDown(dataPresenseDDown, 'Наличие данных', 'Выбраны документы с заполненными данными', true );
            break;
        case '0' :
            $("#emptydoc").prop('checked', 'checked');
            updateDropDown(dataPresenseDDown, 'Наличие данных', 'Выбраны пустые документы', true );
            break;
    }
};
// инициализация вкладок с документами
initdocumentstabs = function() {
    $("#documenttabs").jqxTabs({  height: '100%', width: '100%', theme: theme });
    // Кастомный лоадер
    let html = "<div class='jqx-loader-icon jqx-loader-icon-bootstrap' style='background-position-y: 0; margin-top: 5px'></div>" +
        "<div class='jqx-loader-text jqx-loader-text-bootstrap jqx-loader-text-bottom jqx-loader-text-bottom-bootstrap'>" +
        "<div>Загрузка перечня</div> " +
        "<div>отчетных документов ...</div>" +
        "</div>";
    tl.jqxLoader({theme: theme, width: 170, height: 80,
        isModal:false,
        imagePosition: 'top',
        autoOpen: true,
        html: html
    });

    // Инициализация таблицы первичных документов
    dgrid.on("bindingcomplete", function (event) {
        dgrid.jqxGrid({ pageable: true});
        let reccount = dgridDataAdapter.totalrecords;
        $("#totalrecords").html(reccount);
        if (reccount === 0) {
            primary_mo_bc.html('');
        }
        dgrid.jqxGrid('selectrow', 0);
        tl.jqxLoader('close');
    });
    dgrid.jqxGrid(
        {
            width: '100%',
            height: '100%',
            source: dgridDataAdapter,
            localization: getLocalization('ru'),
            theme: theme,
            columnsresize: true,
            showtoolbar: false,
            pagermode: "simple",
            pagesizeoptions: ['10', '50', '100'],
            pagesize: 50,
            pagerbuttonscount: 12,
            autoshowloadelement: false,
            columns: [
                { text: '№', datafield: 'id', width: '5%', cellclassname: filledFormclass },
                { text: 'Код МО', datafield: 'unit_code', width: '70px' },
                { text: 'Наименование МО', datafield: 'unit_name', width: '30%' },
                { text: 'Мониторинг', datafield: 'monitoring', width: '320px' },
                { text: 'Код формы', datafield: 'form_code', width: '80px' },
                { text: 'Период', datafield: 'period', width: '240px' },
                { text: 'Статус', datafield: 'state', width: '170px', cellclassname: formStatusclass },
                { text: 'Данные', datafield: 'filled', columntype: 'checkbox', width: '90px' }
            ]
        });
    dgrid.on('rowselect', function (event)
    {
        let row = event.args.row;
        let bc = '';
        if (typeof row === 'undefined') {
            doc_id = null;
            return false;
        }
        doc_id = row.id;
        docstate_id = row.stateid;
        current_document_form_code = row.form_code;
        current_document_form_name = row.form_name;
        current_document_ou_name = row.unit_name;
        doc_state = row.state;
        bc = makeMOBreadcrumb(row.ou_id);
        primary_mo_bc.html(bc);
        getDocumentMessages(doc_id);
        if ($("#DocumentInfoWindow").jqxWindow('isOpen')) {
            setDocInfo();
        }
    });
    dgrid.on('rowdoubleclick', function (event)
    {
        let args = event.args;
        let rowindex = args.rowindex;
        let document_id = dgrid.jqxGrid('getrowid', rowindex);
        let editWindow = window.open(edit_form_url + document_id);
    });
    // Инициализация таблицы сводных документов
    agrid.on("bindingcomplete", function (event) {
        let reccount = aggregate_report_table.totalrecords;
        $("#totalaggregates").html(reccount);
        agrid.jqxGrid('selectrow', 0);
    });
    agrid.jqxGrid(
        {
            width: '100%',
            height: '88%',
            theme: theme,
            source: aggregate_report_table,
            columnsresize: true,
            showtoolbar: false,
            localization: getLocalization('ru'),
            autoshowloadelement: false,
            columns: [
                { text: '№', datafield: 'id', width: '5%' },
                { text: 'Код Территории/МО', datafield: 'unit_code', width: 100 },
                { text: 'Наименование МО', datafield: 'unit_name', width: '20%' },
                { text: 'Мониторинг', datafield: 'monitoring', width: 320 },
                { text: 'Код формы', datafield: 'form_code', width: 100 },
                //{ text: 'Наименование формы', datafield: 'form_name', width: '20%' },
                { text: 'Период', datafield: 'period', width: 150 },
                { text: 'Сведение', datafield: 'aggregated_at', width: 150 },
                { text: 'Данные', datafield: 'filled', columntype: 'checkbox', width: 120 }
            ]
        });
    agrid.on('rowdoubleclick', function (event)
    {
        let args = event.args;
        let rowindex = args.rowindex;
        let document_id = agrid.jqxGrid('getrowid', rowindex);
        let editWindow = window.open(edit_aggregate_url + '/' + document_id);
    });
};

function getDocumentMessages(document_id) {
    let murl = docmessages_url + 'document=' + document_id;
    $.ajax({
        dataType: 'json',
        url: murl,
        method: 'GET',
        beforeSend: function (xhr) {
            let loadmessage = "<div class='row' style='margin: 0 0 -15px -15px'>" +
                "   <div class='col-md-12' style='padding: 20px'>" +
                "       <h5>Загрузка сообщений <img src='/jqwidgets/styles/images/loader-small.gif' /></h5>" +
                "   </div>" +
                "</div>";
            $("#DocumentMessages").html(loadmessage);
        },
        success: function (data, status, xhr) {
            if (data.length === 0) {
                let message = "<div class='row' style='margin: 0 0 -15px -15px'>" +
                    "   <div class='col-md-12' style='padding: 20px'>" +
                    "       <p class='text text-info'>Нет сообщений для данного документа</p>" +
                    "   </div>" +
                    "</div>";
                $("#DocumentMessages").html(message);
            }
            else {
                let items = [];
                let html = '<table class="table table-bordered table-condensed table-hover table-striped">';
                html += '<colgroup><col style="width: 120px"><col style="width: 300px"><col style="width: 70%"></colgroup>';
                html += '<thead><tr><th>Дата</th><th>Автор</th><th>Сообщение</th></tr></thead>';
                $.each( data, function( key, val ) {
                    let worker = 'н/д';
                    let description = '';
                    let wtel = 'н/д';
                    let ctel = 'н/д';
                    let fn = '';
                    let pn = '';
                    let ln = '';
                    if (val.worker !== null) {
                        let pr = val.worker.profiles;
                        for (let i = 0; i < pr.length; i++) {
                            switch (true) {
                                case (pr[i].tag === 'tel' && pr[i].attribute === 'working') :
                                    wtel = pr[i].value;
                                    break;
                                case (pr[i].tag === 'tel' && pr[i].attribute === 'cell') :
                                    ctel = pr[i].value;
                                    break;
                                case (pr[i].tag === 'firstname') :
                                    fn = pr[i].value;
                                    break;
                                case (pr[i].tag === 'patronym') :
                                    pn = pr[i].value;
                                    break;
                                case (pr[i].tag === 'lastname') :
                                    ln = pr[i].value;
                                    break;
                            }
                        }
                        description = val.worker.description === '' ? ln + ' ' + fn + ' ' + pn : val.worker.description;
                    }
                    let mark_as_unread = val.is_read_count === 1 ? "" : "info";
                    //let m = "<tr class='"+ mark_as_unread + "'>";
                    let m = "<tr class='"+ "'>";
                    m += "<td style='width: 120px'><p class='text-info'>" + formatDate(val.created_at) + "</p></td>";
                    m += '<td style="width: 300px">' +
                        '<div class="dropdown">' +
                        '  <p class="dropdown-toggle text-info" style="padding: 0; text-underline: #1c4000" id="menu1" data-toggle="dropdown">' + description + '</p>' +
                        '  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">' +
                        '    <li role="presentation"><a role="menuitem" href="mailto:' + val.worker.email + '?subject=Вопрос по заполнению формы ' + current_document_form_code +'">' +
                        '       <small>e-mail: ' + val.worker.email + '</small></a></li>' +
                        '    <li role="presentation"><a role="menuitem" href="tel:'+ wtel +'"><small>Рабочий телефон: '+ wtel +'</small></a></li>' +
                        '    <li role="presentation"><a role="menuitem" href="tel:'+ ctel +'"><small>Сотовый телефон: '+ ctel +'</small></a></li>' +
                        '  </ul>' +
                        '</div>' +
                        '</td>';
                    m += "<td style='width: 70%'><p class='text-info'>" + val.message + "</p></td>";
                    m +="</tr>";
                    items.push(m);
                });
                $("#DocumentMessages").html(html + items.join( "" ) + "</table>");
            }
        },
        error: xhrErrorNotificationHandler
    });
}

// Инициализация вкладки консолидированных отчетов
initConsolidates = function () {
    consolsource =
        {
            datatype: "json",
            datafields: [
                { name: 'id', type: 'int' },
                { name: 'unit_code', type: 'string' },
                { name: 'unit_name', type: 'string' },
                { name: 'form_code', type: 'string' },
                { name: 'monitoring', type: 'string' },
                { name: 'period', type: 'string' }
            ],
            id: 'id',
            url: consolsource_url + current_filter,
            root: 'data'
        };
    consolTableDA = new $.jqx.dataAdapter(consolsource);
    cgrid.jqxGrid(
        {
            width: '100%',
            height: '93%',
            theme: theme,
            source: consolTableDA,
            columnsresize: true,
            localization: getLocalization('ru'),
            autoshowloadelement: false,
            columns: [
                { text: '№', datafield: 'id', width: '5%' },
                { text: 'Код Территории/МО', datafield: 'unit_code', width: 100 },
                { text: 'Наименование МО', datafield: 'unit_name', width: '20%' },
                { text: 'Мониторинг', datafield: 'monitoring', width: 320 },
                { text: 'Код формы', datafield: 'form_code', width: 100 },
                { text: 'Период', datafield: 'period', width: 150 }
            ]
        });
    cgrid.on('rowdoubleclick', function (event)
    {
        let args = event.args;
        let rowindex = args.rowindex;
        let document_id = cgrid.jqxGrid('getrowid', rowindex);
        let editWindow = window.open(edit_consolidate_url + '/' + document_id);
    });
};
// Инициализация вкладки последних документов
initRecentDocuments = function () {
    recentsource =
        {
            datatype: "json",
            datafields: [
                { name: 'document_id', type: 'int' },
                { name: 'unit_code', map: 'document>unitsview>code', type: 'string' },
                { name: 'unit_name', map: 'document>unitsview>name', type: 'string' },
                { name: 'form_code', map: 'document>form>form_code', type: 'string' },
                { name: 'monitoring', map: 'document>monitoring>name', type: 'string' },
                { name: 'period', map: 'document>period>name', type: 'string' },
                { name: 'state', map: 'document>state>name', type: 'string' }
            ],
            id: 'document_id',
            ///url: docsource_url + filtersource(),
            url: recentdocs_url,
            root: 'data'
        };
    recentTableDA = new $.jqx.dataAdapter(recentsource);
    rgrid.jqxGrid(
        {
            width: '100%',
            height: '93%',
            theme: theme,
            source: recentTableDA,
            columnsresize: true,
            localization: getLocalization('ru'),
            autoshowloadelement: false,
            columns: [
                { text: '№', datafield: 'document_id', width: '5%' },
                { text: 'Код Территории/МО', datafield: 'unit_code', width: 100 },
                { text: 'Наименование МО', datafield: 'unit_name', width: '20%' },
                { text: 'Мониторинг', datafield: 'monitoring', width: 320 },
                { text: 'Код формы', datafield: 'form_code', width: 100 },
                { text: 'Период', datafield: 'period', width: 150 },
                { text: 'Статус', datafield: 'state', width: 120, cellclassname: formStatusclass }
            ]
        });
    rgrid.on('rowdoubleclick', function (event)
    {
        let args = event.args;
        let rowindex = args.rowindex;
        let document_id = rgrid.jqxGrid('getrowid', rowindex);
        let editWindow = window.open(edit_aggregate_url + '/' + document_id);
    });
};
// инициализация вкладок с сообщениями и проверками к документу
initdocumentproperties = function() {
    $("#openMessagesListWindow").on('click', function(event) {
        let bootstrap_link = "<link href='/bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css'>";
        let table = $('#DocumentMessages').clone();
        let link_to_print ="<a href='#' onclick='window.print()'>Распечатать</a>";
        let header = "<h4>Комментарии к форме №" + current_document_form_code + " \"" + current_document_form_name + "\"";
        header += " по учреждению: " + current_document_ou_name +"</h4>";
        let pWindow = window.open("", "messagesWindow", "width=1000, height=600, scrollbars=yes");
        table.find('td').addClass('small');
        pWindow.document.write(bootstrap_link + link_to_print + header + table.html());
    });
};
// инициализация всплывающих окон с формами ввода сообщения и т.д.
initpopupwindows = function() {

};

// Формирование строки запроса к серверу
filtersource = function() {
    let forms;
    let monitorings;
    let states = checkstatefilter();
    let filled = checkDataPresenceFilter();
    let mon_forms = getCheckedMonsForms();
    let mf = mon_forms.mf.join();
    let periods = checkPeriodFilter();
    forms = mon_forms.f.join();
    monitorings = mon_forms.m.join();
    return '&filter_mode=' + filter_mode + '&ou=' +current_top_level_node +'&states='+states+
        '&monitorings='+monitorings+'&forms='+forms +'&periods=' + periods + '&mf=' + mf + '&filled=' + filled;
};
// Источники данных для
initDocumentSource = function () {
    //console.log(current_filter);
    docsource =
        {
            datatype: "json",
            datafields: [
                { name: 'id', type: 'int' },
                { name: 'ou_id', type: 'int' },
                { name: 'unit_code', type: 'string' },
                { name: 'unit_name', type: 'string' },
                { name: 'form_code', type: 'string' },
                { name: 'form_name', type: 'string' },
                { name: 'monitoring', type: 'string' },
                { name: 'period', type: 'string' },
                { name: 'state', type: 'string' },
                { name: 'stateid', type: 'int' },
                { name: 'filled', type: 'bool' }
            ],
            id: 'id',
            ///url: docsource_url + filtersource(),
            url: docsource_url + current_filter,
            root: 'data'
        };
    aggregate_source =
        {
            datatype: "json",
            datafields: [
                {name: 'id', type: 'int'},
                {name: 'ou_id', type: 'int'},
                {name: 'unit_code', type: 'string'},
                {name: 'unit_name', type: 'string'},
                {name: 'form_code', type: 'string'},
                {name: 'form_name', type: 'string'},
                { name: 'monitoring', type: 'string' },
                {name: 'period', type: 'string'},
                {name: 'aggregated_at', type: 'string'},
                { name: 'filled', type: 'bool' }
            ],
            id: 'id',
            //url: aggrsource_url + '&filter_mode='+ filter_mode + '&ou=' + current_top_level_node + '&forms=' + checkedforms.join()+'&periods='+checkedperiods.join(),
            url: aggrsource_url + current_filter,
            root: 'data'
        };
    dgridDataAdapter = new $.jqx.dataAdapter(docsource, {
        loadError: function(jqXHR, status, error) {
            if (jqXHR.status === 401) {
                raiseError('Пользователь не авторизован', jqXHR);
            }
        }
    });
    aggregate_report_table = new $.jqx.dataAdapter(aggregate_source);
};
// Показываем иконки фильтров при установленных ограничениях
initFilterIcons = function () {

};

function makeMOBreadcrumb(ou_id) {
    let parents = getAncestors(ou_id);
    if (parents === null) {
        return '...';
    }
    let bc_intro = '<i class="fas fa-home"></i> ';
    let reversed = parents.reverse();
    let bc_string = reversed.join(' <i class="fas fa-caret-right"></i> ');
/*    for (i = parents.length-1; i >= 0; i--) {
        bc += parents[i] + ' <i class="fas fa-caret-right"></i> ';
    }*/
    return bc_intro + bc_string;
}

function searchMOById(id) {
    if (id === null) {
        return null;
    }
    for (i = 0; i < oucount; i++ ) {
        if (ouarray[i].id === id) {
            return ouarray[i];
        } 
    }
    return null;
}

function getAncestors(id) {
    let ancestors = [];
    let current = searchMOById(id);
    if (current === null) {
        return null;
    }
    var traversAncestors = function(parent)
    {
        let found = searchMOById(parent);
        if (found) {
            ancestors.push(found.unit) ;
            traversAncestors(found.parent);
        }
    };
    traversAncestors(current.parent);
    return ancestors;
}

function postMessageSendActions(document) {
    //let rowindex = agrid.jqxGrid('getselectedrowindex');
    let rowindex = dgrid.jqxGrid('getrowboundindexbyid', document);
    dgrid.jqxGrid('selectrow', rowindex);
}

function postChangeStateActions(selected_state) {
    let rowdata = dgrid.jqxGrid('getrowdatabyid', doc_id);
    rowdata.state = selected_state;
    //console.log(rowdata);
    dgrid.jqxGrid('updaterow', rowdata.id, rowdata);
    dgrid.jqxGrid('selectrow', rowdata.boundindex);
}