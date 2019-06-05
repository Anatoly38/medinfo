// Ининциализация списка отчетных периодов
let periodTree = $("#periodTree");
let periodDropDown = $('#periodSelector');


initPeriodTree = function () {
    let uncheckAll = $("#clearAllPeriods");
    periodDropDown.jqxDropDownButton(
        {
            width: 350,
            height: 32,
            dropDownWidth: 520,
            theme: theme
        });
    let periods_source =
        {
            datatype: "json",
            datafields: [
                { name: 'id', type: 'int' },
                { name: 'parent_id', type: 'int' },
                { name: 'name', type: 'string' },
                { name: 'year', type: 'string' },
                { name: 'begin_date', type: 'date' },
                { name: 'end_date', type: 'date' },
            ],
            hierarchy:
                {
                    keyDataField: { name: 'id' },
                    parentDataField: { name: 'parent_id' }
                },
            id: 'id',
            root: '',
            localdata: periods
        };
    periodsDataAdapter = new $.jqx.dataAdapter(periods_source);
    periodTree.jqxTreeGrid(
        {
            width: '100%',
            height: '500px',
            theme: theme,
            source: periodsDataAdapter,
            selectionMode: "singleRow",
            filterable: true,
            filterMode: 'simple',
            localization: localize(),
            checkboxes: true,
            hierarchicalCheckboxes: true,
            columnsResize: true,
            autoRowHeight: false,
            ready: function()
            {
                periodTree.jqxTreeGrid('expandRow', 1000000);
                for (let i = 0; i < checkedperiods.length; i++) {
                    periodTree.jqxTreeGrid('checkRow', checkedperiods[i]);
                }
            },
            columns: [
                { text: 'Наименование', dataField: 'name', width: '70%' },
                { text: 'Отчетный год', dataField: 'year', width: '30%' },
            ]
        });
    periodTree.on('filter',
        function (event)
        {
            let args = event.args;
            let filters = args.filters;
            periodTree.jqxTreeGrid('expandAll');
        }
    );
    uncheckAll.click( function (event) {
        let checkedRows = periodTree.jqxTreeGrid('getCheckedRows');
        if (typeof checkedRows !== 'undefined') {
            for (let i = 0; i < checkedRows.length; i++) {
                periodTree.jqxTreeGrid('uncheckRow', checkedRows[i].id);
            }
        }
    });
    $("#applyPeriods").click( function (event) {
        periodDropDown.jqxDropDownButton('close');
        updatedocumenttable();
    });
    if (checkedperiods.length > 0) {
        updateDropDown(periodDropDown, 'Отчетные периоды', 'Периоды выбраны', true );
    } else {
        updateDropDown(periodDropDown, 'Отчетные периоды', 'Фильтр по отчетным периодам отключен', false );
    }

    $( "#filterYear").change(function() {
        let year = $("#filterYear").val();
        if (year === 'allperiods') {
            periodTree.jqxTreeGrid('clearFilters');
            periodTree.jqxTreeGrid('collapseAll');
            periodTree.jqxTreeGrid('expandRow', 1000000);
        } else {
            applyFilter('year', year);
        }

    });
};

applyFilter = function (dataField, value) {
    periodTree.jqxTreeGrid('clearFilters');
    var filtertype = 'stringfilter';
    var filtergroup = new $.jqx.filter();
    var filter_or_operator = 1;
    var filtervalue = value;
    var filtercondition = 'equal';
    var filter = filtergroup.createfilter(filtertype, filtervalue, filtercondition);
    filtergroup.addfilter(filter_or_operator, filter);
    periodTree.jqxTreeGrid('addFilter', dataField, filtergroup);
    periodTree.jqxTreeGrid('applyFilters');
};

checkPeriodFilter = function() {
    let checkedperiods = [];
    let checkedRows = periodTree.jqxTreeGrid('getCheckedRows');
    if (typeof checkedRows !== 'undefined') {
        for (let i = 0; i < checkedRows.length; i++) {
            checkedperiods.push(checkedRows[i].id);
        }
    }
    if (checkedperiods.length > 0) {
        updateDropDown(periodDropDown, 'Отчетные периоды', 'Периоды выбраны', true );
    } else {
        updateDropDown(periodDropDown, 'Отчетные периоды', 'Фильтр по отчетным периодам отключен', false );
    }
    return checkedperiods.join();
};