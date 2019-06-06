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
                { name: 'periodicity', map: 'periodpattern>periodicity', type: 'int' }
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
    console.log(periodsDataAdapter);
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

    $("#filterYear").change(function() {
        let year = $("#filterYear").val();
        if (year === 'allperiods') {
            periodTree.jqxTreeGrid('clearFilters');
            periodTree.jqxTreeGrid('collapseAll');
            periodTree.jqxTreeGrid('expandRow', 1000000);
        } else {
            applyFilter('year', year);
        }
    });

    $(".periodtype").change(function() {
        //console.log($("input[name='periodtype'][value='1']").prop('checked'));
        //console.log($("input[name='periodtype'][value='1']").is(":checked"));
        switch (true) {
            case $("input[name='periodtype'][value='1']").prop('checked') :
                applyFilter('periodicity', '1');
                break;
            case $("input[name='periodtype'][value='3']").prop('checked') :
                applyFilter('periodicity', '3');
                break;
            default:
                periodTree.jqxTreeGrid('clearFilters');
                periodTree.jqxTreeGrid('collapseAll');
                periodTree.jqxTreeGrid('expandRow', 1000000);
        }
    });
};

applyFilter = function (dataField, value) {
    periodTree.jqxTreeGrid('clearFilters');
    let filtertype = 'stringfilter';
    let filtergroup = new $.jqx.filter();
    let filter_or_operator = 1;
    let filtervalue = value;
    let filtercondition = 'equal';
    let filter = filtergroup.createfilter(filtertype, filtervalue, filtercondition);
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