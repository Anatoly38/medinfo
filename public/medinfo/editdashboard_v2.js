/* Версия 2. 22.03.2019 */
let fetchvalues_url = function() {
    return source_url + current_table;
};
let initDgridSize = function () {
    return initialViewport - topOffset1;
};
let initSplitterSize = function () {
    return initialViewport - topOffset2;
};
let onResizeEventLitener = function () {
    $(window).resize(function() {
        dgrid.jqxGrid({ height: $(window).height()-topOffset1 });
        $('#formEditLayout').jqxSplitter({ height: $(window).height()-topOffset2});
    });
};
let initSplitter = function () {
    let splitter = $('#formEditLayout');
    splitter.jqxSplitter({
        width: '100%',
        height: initSplitterSize(),
        //height: '100%',
        theme: theme,
        splitBarSize: 10,
        panels: [
            { size: '65%', min: 100, collapsible: false }, {collapsed:true}
        ]
    });
    splitter.on('collapsed',
        function (event) {
        // При сворачивании панели очищаем протокол контроля ячейки
            $("#cellprotocol").html('<p class="text-info">Для просмотра результов выберите ячейку</p>');
        });
    $("#ControlTabs").jqxTabs({ theme: theme, height: '100%', width: '100%' });
    $("#TableTitle").html('Таблица ' + data_for_table.code + ', "' + data_for_table.name + '"');

};
// Контроль таблицы - вывод протокола контроля на страницу и для печати
let tabledatacheck = function(table_id, type) {
    let data = "";
    let url = "";
    switch (type) {
        case 'inform' :
            url = informTableDataCheck + table_id + "/" + forcereload;
            break;
        case 'interform' :
            url = interFormTableDataCheck + table_id + "/" + forcereload;
            break;
        case 'interperiod' :
            url = interPeriodTableDataCheck + table_id + "/" + forcereload;
            break;
    }

    $.ajax({
        dataType: "json",
        url: url,
        data: data,
        beforeSend: beforecheck,
        success: gettableprotocol
    }).fail(function(xhr, textStatus, errorThrown) {
        $("#tableprotocol").html('');
        $('#formprotocolloader').hide();
        $('#protocolloader').hide();
        if (xhr.status === 401) {
            raiseError('Пользователь не авторизован.', xhr );
            return false;
        }
        raiseError("Ошибка получения протокола контроля с сервера.");
        tcheck.attr('disabled', false);
        idtcheck.attr('disabled', false);
        iptcheck.attr('disabled', false);
    });
};
let beforecheck = function( xhr ) {
    tcheck.attr('disabled', true);
    idtcheck.attr('disabled', true);
    iptcheck.attr('disabled', true);
    $("#tableprotocol").html('');
    //$("#cellvalidationprotocol").html('');
    $('#protocolloader').show();
    //$('#showallrule').jqxCheckBox('check');
    $("#extrabuttons").hide();
    $(".inactual-protocol").hide();
    if (typeof invalidCells[current_table] !== 'undefined' ) {
        invalidCells[current_table].length = 0;
    }
    if (typeof alertedCells[current_table] !== 'undefined' ) {
        alertedCells[current_table].length = 0;
    }
    marking_mode = 'control';
    dgrid.jqxGrid('refresh');
};
let renderCellProtocol = function(cell_protocol) {
    let row = $("<tr class='control-row'></tr>");
    let column = $("<td></td>");
    cell_protocol.valid ? valid = 'верно' : valid = 'не верно';
    let rule = $("<div class='showrule'><span class='text-info'><strong>" + cell_protocol.left_part_formula + "</strong></span> <em>" + cell_protocol.boolean_readable
        + "</em> <span class='text-info'>" + cell_protocol.right_part_formula +"</span></div>");
    let t = "<table class='control-result'><tr><td>Значение</td>";
    t += "<td>Знак сравнения</td><td>Контрольная сумма</td><td>Отклонение</td>";
    t += "<td>Результат контроля</td></tr>";
    t += "<tr><td>" + cell_protocol.left_part_value + "</td><td>" + cell_protocol.boolean_readable + "</td>";
    t += "<td>" + cell_protocol.right_part_value + "</td>";
    t += "<td>"+cell_protocol.deviation + "</td><td class='check'>" + valid + "</td></tr></table>";
    let explanation = $(t);
    column.append(rule);
    column.append(explanation);
    if (!cell_protocol.valid) {
        explanation.addClass('invalid');
    } else {
        explanation.addClass('bg-success');
    }
    row.append(column);
    return row;
};
// Отображение результатов контроля по каждой итерации
let renderCompareControl = function(result, boolean_sign, mode, level) {
    //console.log(result.cells);
    let explanation_intro = setIntro(mode);
    let error_level_mark = 'invalid';
    switch (level) {
        case 1 :
            error_level_mark = 'invalid';
            break;
        case 2 :
            error_level_mark = 'alerted';
            break;
    }
    let row = $("<div class='control-row'></div>");
    result.valid ? valid = 'верно' : valid = 'не верно';
    if (typeof result.code === 'object' && result.code !== null) {
        row.append($("<div class='showrule'><span class='text-info'><strong>" + explanation_intro + "</strong></span> <em>" +
            " таблице: " + result.code.table_code + ", " +
            " строке: " + result.code.row_code + ", " +
            " графе: " + result.code.column_code + "</em>:</div>"));
    } else if (typeof result.code !== 'object' && result.code !== null) {
        row.append($("<div class='showrule'><span class='text-info'><strong>" + explanation_intro + "</strong></span> <em>" + result.code + "</em>:</div>"));
    }
    let t = "<table class='control-result'><tr><td>Значение</td>";
    t += "<td>Знак сравнения</td><td>Контрольная сумма</td><td>Отклонение</td>";
    t += "<td>Результат контроля</td></tr>";
    t += "<tr><td>" + result.left_part_value + "</td><td>" + boolean_sign + "</td>";
    t += "<td>" + result.right_part_value + "</td>";
    t += "<td>"+result.deviation + "</td><td class='check'>" + valid + "</td></tr></table>";
    let explanation = $(t);

    row.append(explanation);
    if (!result.valid) {
        explanation.addClass(error_level_mark);
    } else {
        explanation.addClass('bg-success');
    }
    return row;
};
let renderDependencyControl = function(result, mode, level) {
    //console.log(result.cells);
    let explanation_intro = mode == 1 ? 'По строке' : 'По графе';
    let error_level_mark = 'invalid';
    switch (level) {
        case 1 :
            error_level_mark = 'invalid';
            break;
        case 2 :
            error_level_mark = 'alerted';
            break;
    }
    let row = $("<div class='control-row'></div>");
    result.valid ? valid = 'верно' : valid = 'не верно';
    if (typeof result.code !== 'undefined') {
        //console.log(result.code);
        let rule = $("<div class='showrule'><span class='text-info'><strong>" + explanation_intro + "</strong></span> <em>" + result.code + "</em>:</div>");
        row.append(rule);
    }

    let t = "<table class='control-result'><tr><td>Значение</td>";
    t += "<td>Контрольная сумма</td><td>Отклонение</td>";
    t += "<td>Результат контроля</td></tr>";
    t += "<tr><td>" + result.left_part_value + "</td>";
    t += "<td>" + result.right_part_value + "</td>";
    t += "<td>"+result.deviation + "</td><td class='check'>" + valid + "</td></tr></table>";
    let explanation = $(t);

    row.append(explanation);
    if (!result.valid) {
        explanation.addClass(error_level_mark);
    } else {
        explanation.addClass('bg-success');
    }
    return row;
};
let renderInDiapazonControl = function(result, level) {
    //console.log(result.cells);
    let error_level_mark = 'invalid';
    switch (level) {
        case 1 :
            error_level_mark = 'invalid';
            break;
        case 2 :
            error_level_mark = 'alerted';
            break;
    }
    let row = $("<div class='control-row'></div>");
    result.valid ? valid = 'верно' : valid = 'не верно';
    if (typeof result.code !== 'undefined') {
        let rule = $("<div class='showrule'><span class='text-info'><strong> По ячейке </strong></span> <em>" + result.code + "</em>:</div>");
        row.append(rule);
    }
    let t = "<table class='control-result'><tr><td>Текущее</td>";
    t += "<td>Прошлогоднее</td><td>Отклонение (%)</td>";
    t += "<td>Результат контроля</td></tr>";
    t += "<tr><td>" + result.left_part_value + "</td>" ;
    t += "<td>" + result.right_part_value + "</td>";
    t += "<td>"+result.deviation + "</td> <td class='check'>" + valid + "</td></tr></table>";
    let explanation = $(t);

    row.append(explanation);
    if (!result.valid) {
        explanation.addClass(error_level_mark);
    } else {
        explanation.addClass('bg-success');
    }
    return row;
};
let renderInterannualControl = function(result, level) {
    //console.log(result.cells);
    let error_level_mark = 'invalid';
    switch (level) {
        case 1 :
            error_level_mark = 'invalid';
            break;
        case 2 :
            error_level_mark = 'alerted';
            break;
    }
    let row = $("<div class='control-row'></div>");
    result.valid ? valid = 'верно' : valid = 'не верно';
    if (typeof result.code !== 'undefined') {
        let rule = $("<div class='showrule'><span class='text-info'><strong> По ячейке </strong></span> <em>" + result.code + "</em>:</div>");
        row.append(rule);
    }
    let t = "<table class='control-result'><tr><td>Текущее</td>";
    t += "<td>Прошлогоднее</td><td>Отклонение (%)</td>";
    t += "<td>Результат контроля</td></tr>";
    t += "<tr><td>" + result.left_part_value + "</td>" ;
    t += "<td>" + result.right_part_value + "</td>";
    t += "<td>"+result.deviation + "</td> <td class='check'>" + valid + "</td></tr></table>";
    let explanation = $(t);

    row.append(explanation);
    if (!result.valid) {
        explanation.addClass(error_level_mark);
    } else {
        explanation.addClass('bg-success');
    }
    return row;
};
let renderFoldControl = function(result, level) {
    //console.log(result.cells);
    let error_level_mark = 'invalid';
    switch (level) {
        case 1 :
            error_level_mark = 'invalid';
            break;
        case 2 :
            error_level_mark = 'alerted';
            break;
    }
    let row = $("<div class='control-row'></div>");
    result.valid ? valid = 'верно' : valid = 'не верно';
    if (typeof result.code !== 'undefined') {
        let rule = $("<div class='showrule'><span class='text-info'><strong> По ячейке </strong></span> <em>" + result.code + "</em>:</div>");
        row.append(rule);
    }
    let t = "<table class='control-result'><tr><td>Текущее значение</td>";
    t += "<td>Результат контроля</td></tr>";
    t += "<tr><td>" + result.left_part_value + "</td>" ;
    t += "<td class='check'>" + valid + "</td></tr></table>";
    let explanation = $(t);

    row.append(explanation);
    if (!result.valid) {
        explanation.addClass(error_level_mark);
    } else {
        explanation.addClass('bg-success');
    }
    return row;
};
function setIntro(mode) {
    let intro = '';
    switch (mode) {
        case 1 :
            intro = 'По строке';
            break;
        case 2 :
            intro = 'По графе';
            break;
        case 3 :
            intro = 'По ';
            break;
        case null :
        default :
            intro = '';
    }
    return intro;
}
// Отображение результатов контроля (новый формат) по каждой функции контроля
let renderFunctionProtocol = function (container, table_id, rule) {
    let rule_valid = rule.valid ? 'rule-valid' : 'rule-invalid';
    let rule_wrapper = $("<div class='rule " + rule_valid + "'></div>");
    let header = $("<div class='rule-header " + rule_valid + "'></div>");
    let content = $("<div class='rule-content " + rule_valid + "'></div>");
    let error_level_mark;
    if (rule.comment !== '') {
        content.append("<div class='text-warning small'><strong>^ Пояснения: </strong>" + rule.comment + "</div>");
    }
    if (typeof rule.error !== 'undefined') {
        header.append(rule.error);
        return false;
    }
    switch (rule.level) {
        case 1 :
            error_level_mark = 'text-danger bg-danger';
            break;
        case 2 :
            error_level_mark = 'text-warning bg-warning';
            break;
    }
    header.append("<strong>Правило контроля: </strong><span class='" + error_level_mark +"'>" + rule.formula + "</span> ");
    let r = 0;
    let i = 0;

    $.each(rule.iterations, function(i_index, result) {
        if ( typeof result.valid !=='undefined' ) {
            //let valid = '';
            let row;

            switch (rule.function_id) {
                case formlabels.compare :
                    row = renderCompareControl(result, rule.boolean_sign, rule.iteration_mode, rule.level);
                    break;
                case formlabels.dependency :
                    row = renderDependencyControl(result, rule.iteration_mode, rule.level);
                    break;
                case formlabels.interannual :
                    row = renderInterannualControl(result, rule.level);
                    break;
                case formlabels.iadiapazon :
                case formlabels.ipdiapazon :
                    row = renderInDiapazonControl(result, rule.level);
                    break;
                case formlabels.multiplicity :
                    row = renderFoldControl(result, rule.level);
                    break;
                case formlabels.section :
                    row = renderCompareControl(result, rule.boolean_sign, rule.iteration_mode, rule.level);
                    break;
            }
            content.append(row);
            if (!result.valid) {
                if (rule.level === 1) {
                    $.each(result.cells, function(c_index, cell) {
                        invalidCells[table_id].push({r: cell.row, c: cell.column});
                    });
                } else {
                    $.each(result.cells, function(c_index, cell) {
                        alertedCells[table_id].push({r: cell.row, c: cell.column});
                    });
                }
                row.show();
                row.addClass('control-invalid');
                i++;
            } else {
                row.addClass('control-valid');
                //row.hide();
            }
            r++;
        }
    });
    if (r > 1) {
        header.append("<span class='badge' title='Всего выполнено / Обнаружены ошибки'> Проверено: " + r + " / Ошибок: " + i + "</span>");
    }
    //content.append(info);
    rule_wrapper.append(header);
    rule_wrapper.append(content);
    container.append(rule_wrapper);
    return content;
};
// Вывод в читаемом виде контроля таблицы
let renderTableProtocol = function (table_id, data) {
    invalidCells[table_id] = [];
    alertedCells[table_id] = [];
    let container = $("<div></div>");
    let protocol_wrapper = $("<div class='tableprotocol-content'></div>");
    if(typeof data.rules !== 'undefined') {
        $.each(data.rules, function(rule_index, rule ) {
            renderFunctionProtocol(container, table_id, rule);
        });
    }
    protocol_wrapper.append(container);
    return protocol_wrapper;
};
// Инициализация дополнительных кнопок на панели инструментов контроля формы
let init_fc_extarbuttons = function () {
    $("#fc_extrabuttons").hide();
    $('#printformprotocol').jqxButton({ theme: theme });
};
// Инициализация дополнительных кнопок на панели инструментов контроля таблицы
let initextarbuttons = function () {
    $("#extrabuttons").hide();
    $("#showallrule").jqxCheckBox({ theme: theme, checked: true });
    $("#showallrule").on('checked', function (event) {
        $(".rule-valid").hide();
        $(".control-valid").hide();
    });
    $("#showallrule").on('unchecked', function (event) {
        $(".rule-valid").show();
        $(".control-valid").show();
    });
    $('#printtableprotocol').jqxButton({ theme: theme });
};
// поиск в протоколе контроля по id строки и столбца (старый формат)
function searchprotocol(source, column_id, row_id) {
    var results;
    results = $.map(source, function(value, index) {
        if(typeof value == 'object') {
            //if (value.column_id == column_id && value.row_id == row_id) {
            if (value.column == column_id && value.row == row_id) {
                return value;
            } else {
                return searchprotocol(value, column_id, row_id);
            }
        }
    });
    return results;
}
function selectedcell_protocol(form_protocol, table_id, table_code, column_id, row_id) {
    let tableprotocol = form_protocol[table_code];
    let cell_protocol = [];
    if (tableprotocol.no_rules) {
        return false;
    } else {
        $.each(tableprotocol.rules, function (rule_idx, rule) {
            if (!rule.no_rules) {
                $.each(rule.iterations, function (iteration_idx, iteration) {
                    if (cellfound(iteration.cells, column_id, row_id)) {
                        cell_protocol.push({ rule: rule, result: iteration });
                    }
                });
            }
        });
    }
    return cell_protocol;
}
function cellfound(cells, column_id, row_id) {
    if (typeof cells === 'undefined') {
        return false;
    }
    for (let i = 0; cells.length > i; i++) {
        if (cells[i].column === column_id && cells[i].row === parseInt(row_id)) {
            return true;
        }
    }
    return false;
}
// Поиск таблицы по индексу
function searchTableByIndex(table_index) {
    for (let i = 0; i < form_tables_data.length; i++ ) {
        if (form_tables_data[i].tindex === table_index) {
            return form_tables_data[i];
        }
    }
    return false;
}
// Поиск таблицы по Id
function searchTableById(table_id) {
    for (let i = 0; i < form_tables_data.length; i++ ) {
        if (form_tables_data[i].id === table_id) {
            return form_tables_data[i];
        }
    }
    return false;
}

function set_navigation_buttons_status(index) {
    switch (true) {
        case 1 == max_table_index :
            nexttable.attr('disabled', true );
            prevtable.attr('disabled', true );
            break;
        case index == max_table_index :
            nexttable.attr('disabled', true );
            prevtable.attr('disabled', false );
            break;
        case index == 1 :
            nexttable.attr('disabled', false );
            prevtable.attr('disabled', true );
            break;
        default :
            nexttable.attr('disabled', false );
            prevtable.attr('disabled', false );
            break;
    }
}

let checkform = function () {
    let data;
    $.ajax({
        dataType: "json",
        //url: validate_form_url,
        url: formdatacheck_url +"/" + forcereload,
        data: data,
        beforeSend: function( xhr ) {
            formcheck.attr('disabled', true);
            $('#formprotocolloader').show();
            $("#formprotocol").html('');
            $(".inactual-protocol").hide();
            $("#fc_extrabuttons").hide();
            current_protocol_source = null;
        },
        success: function (data, status, xhr) {
            let formprotocol = $("#formprotocol");
            let protocol_wrapper = $("<div></div>");
            let header;
            let printable;
            let formprotocolheader;
            current_protocol_source = data;
            raiseInfo("Протокол контроля формы загружен");
            $('#formprotocolloader').hide();
            invalidCells.length = 0;
            alertedCells.length = 0;
            invalidTables.length = 0;
            let now = new Date();
            let timestamp = now.toLocaleString();
            if  (data.nodata) {
                formprotocol.html("<div class='alert alert-info'>"+ timestamp+" Проверяемая форма не содержит данных</div>");
                protocol_control_created = true;
                return true;
            }
            else if (data.no_rules) {
                formprotocol.html("<div class='alert alert-info'>"+ timestamp+" Для данной формы не заданы правила контроля</div>");
                protocol_control_created = false;
                return true;
            }
            else if (data.valid && data.no_alerts) {
                formprotocol.html("<div class='alert alert-success'>" + timestamp + " При проверке формы ошибок/замечаний не выявлено</div>");
                protocol_control_created = true;
                return true;
            }
            $("#fc_extrabuttons").show();
            $.each(data, function(tablecode, tablecontrol) {
                if (typeof tablecontrol === 'object') {
                    if (!tablecontrol.valid || !tablecontrol.no_alerts) {
                        invalidCells[tablecontrol.table_id] = [];
                        alertedCells[tablecontrol.table_id] = [];
                        markTableInvalid(tablecontrol.table_id);

                        let header_text = "(" + tablecode + ") " + searchTableById(tablecontrol.table_id).name;
                        let theader = $("<div class='tableprotocol-header text-info'><span class='glyph glyphicon-plus'></span>" + header_text + " " + "</div>");
                        let tcontent = renderTableProtocol(tablecontrol.table_id, tablecontrol);
                        //tcontent.hide();
                        //tcontent.append(r.firstChild);
                        protocol_wrapper.append(theader);
                        protocol_wrapper.append(tcontent);
                    }
                }
            });
            header = $("<div class='alert'></div>");
            header.html(timestamp + " При проверке формы выявлены ошибки/замечания в следующих таблицах: ");
            if (!data.valid) {
                header.addClass('alert-danger');
            } else {
                header.addClass('alert-warning');
            }
            formprotocol.append(header);
            formprotocol.append(protocol_wrapper);
            printable = formprotocol.clone();
            $("#formprotocol .tableprotocol-header").click(function() {
                $(this).next().toggle();
                let glyph = $(this.firstChild);
                if (glyph.hasClass('glyphicon-plus')) {
                    glyph.removeClass('glyphicon-plus');
                    glyph.addClass('glyphicon-minus');
                } else {
                    glyph.addClass('glyphicon-plus');
                    glyph.removeClass('glyphicon-minus');
                }
            });
            $("#formprotocol .tableprotocol-content").hide();
            $(".rule-valid ").parent(".jqx-expander-header").hide().next().hide();
            $(".control-valid ").hide();
            formprotocolheader ="<a href='#' onclick='window.print()'>Распечатать</a>";
            formprotocolheader +="<h2>Протокол контроля формы № "+ form_code +": \"" + form_name +"\" </h2>";
            formprotocolheader +="<h3>Учреждение: " + ou_code + " " + ou_name + "</h3>";
            let print_style = "<style>.tableprotocol-header { margin-top: 20px; font-size: 1.1em; font-weight: bold }";
            //print_style += ".badge { background-color: #cbcbcb }";
            print_style += ".badge { display:none }";
            print_style += ".rule-valid { display:none }";
            print_style += ".control-valid { display:none }";
            print_style += ".rule-comment { text-indent: 20px; font-style: italic }";
            print_style += ".rule-header { border-bottom: 1px solid; margin-top: 10px}";
            print_style += ".showrule { font-size: 0.8em; }";
            print_style += ".control-result { border: 1px solid #7f7f7f; border-collapse: collapse; margin-bottom: 10px; width: 600px; text-align: center; }";
            print_style += ".control-result td { border: 1px solid #7f7f7f; }";
            print_style += "</style>";
            $('#printformprotocol').click(function () {
                let pWindow = window.open("", "ProtocolWindow", "width=900, height=600, scrollbars=yes");
                pWindow.document.body.innerHTML = " ";
                pWindow.document.write(print_style + formprotocolheader + printable.html());
            });
            protocol_control_created = true;
            fgrid.jqxDataTable('refresh');
            dgrid.jqxGrid('refresh');
            formcheck.attr('disabled', false);
            tcheck.attr('disabled', false);
            idtcheck.attr('disabled', false);
            iptcheck.attr('disabled', false);
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        $('#formprotocol').html('');
        $('#formprotocolloader').hide();
        if (xhr.status === 401) {
            raiseError('Пользователь не авторизован.', xhr );
            return false;
        }
        raiseError("Ошибка получения протокола контроля с сервера.");
        formcheck.prop('disabled', false);
    });
};
let markTableInvalid = function (id) {
    if ($.inArray(id, invalidTables) === -1) {
        invalidTables.push(id);
    }
};
let markTableValid = function (id) {
    let index = $.inArray(id, invalidTables);
    if (index !== -1) {
        delete invalidTables[index];
    }
};
let gettableprotocol = function (data, status, xhr) {
    let protocol_wrapper;
    let header;
    let printable;
    let scripterrors;
    let tableprotocol = $("#tableprotocol");
    let now = new Date();
    let timestamp = now.toLocaleString();
    let cashed = "";
    $('#protocolloader').hide();
    if (data.cashed) {
        cashed = "(сохраненная версия)";
    }
    raiseInfo("Протокол контроля таблицы загружен");
    if (typeof data.no_rules !== 'undefined' && data.no_rules) {
        tableprotocol.html("<div class='alert alert-info'>"+ timestamp+" Для данной таблицы не заданы правила этого типа контроля</div>");
        protocol_control_created = false;
    }
    else if (data.valid && data.no_alerts) {
        markTableValid(data.table_id);
        tableprotocol.append("<div class='alert alert-success'>" + timestamp + " При проверке таблицы ошибок/замечаний не выявлено" + " " + cashed + "</div>");
        protocol_control_created = true;
    } else {
        header = $("<div class='alert'></div>");
        header.html(timestamp + " При проверке таблицы выявлены ошибки/замечания " + " " + cashed);
        if (!data.valid) {
            header.addClass('alert-danger');
        } else {
            header.addClass('alert-warning');
        }
        markTableInvalid(data.table_id);
        $("#extrabuttons").show();
        protocol_wrapper = renderTableProtocol(data.table_id, data);
        printable = protocol_wrapper.clone();
        tableprotocol.append(header);
        tableprotocol.append(protocol_wrapper);
        if ($("#showallrule").jqxCheckBox('checked'))  {
            $(".rule-valid ").hide();
            $(".control-valid ").hide();
        } else {
            $(".rule-valid").show();
            $(".control-valid ").show();
        }
        let printprotocolheader ="<a href='#' onclick='window.print()'>Распечатать</a>";
        printprotocolheader += "<h2>Протокол контроля таблицы " + current_table_code + " \""+ data_for_table.name;
        printprotocolheader += "\" формы № "+ form_code + "</h2>";
        printprotocolheader +="<h4>Учреждение: " + ou_code + " " + ou_name + "</h4>";
        let print_style = "<style>.badge { background-color: #cbcbcb }";
        print_style += ".badge { display:none }";
        print_style += ".rule-valid { display:none }";
        print_style += ".control-valid { display:none }";
        print_style += ".rule-comment { text-indent: 20px; font-style: italic }";
        print_style += ".rule-header { border-bottom: 1px solid; margin-top: 10px}";
        print_style += ".showrule { font-size: 0.8em; }";
        print_style += ".control-result { border: 1px solid #7f7f7f; border-collapse: collapse; margin-bottom: 10px; width: 600px; text-align: center; }";
        print_style += ".control-result td { border: 1px solid #7f7f7f; }";
        print_style += "</style>";
        table_protocol_comment = "<div>Дата и время проведения проверки: " + timestamp + " "+ cashed + "</div>";
        $('#printtableprotocol').click(function () {
            let pWindow = window.open("", "ProtocolWindow", "width=900, height=600, scrollbars=yes");
            // почистить окошко от предыдущего протокола
            pWindow.document.body.innerHTML = " ";
            pWindow.document.write(print_style + printprotocolheader + printable.html());
        });
        protocol_control_created = true;
    }
    if (typeof data.errors !== 'undefined' && data.errors.length > 0 && (current_user_role === 3 || current_user_role === 4 )) {
        scripterrors = $("<div class='alert alert-danger'></div>");
        scripterrors.append("<p><strong>Ошибка выполнения!</strong> При выполнения контроля по данной таблицы выявлен ряд ошибок в функциях:</p>");
        $.each(data.errors, function(error_inx, error ) {
            scripterrors.append("<p><strong>Код ошибки: " + error.code + "</strong> " + error.message + "</p>");
        });
        tableprotocol.append(scripterrors);
    }
    current_protocol_source[current_table_code] = data;
    fgrid.jqxDataTable('refresh');
    dgrid.jqxGrid('refresh');
    dgrid.jqxGrid('focus');
    dgrid.jqxGrid('selectcell', 0, firstdatacolumn);
    tcheck.attr('disabled', false);
    idtcheck.attr('disabled', false);
    iptcheck.attr('disabled', false);
};
// Экспорт данных текущей таблицы в эксель
let tabledataexport = function(table_id) {
    window.open(tableexport_url + table_id);
};
let initdatasources = function() {
    form_table_source = {
        dataType: "json",
        dataFields: [{
            name: 'id',
            type: 'int'
        }, {
            name: 'code',
            type: 'string'
        }, {
            name: 'name',
            type: 'string'
        }],
        id: 'id',
        localdata: form_tables_data
    };
    tableListDataAdapter = new $.jqx.dataAdapter(form_table_source);
};
// Получение читабельных координат ячейки - код строки, индекс графы
function getReadableCellAdress(row, column) {
    let row_code = dgrid.jqxGrid('getcellvaluebyid', row, current_row_number_datafield);
    let column_code = dgrid.jqxGrid('getcolumnproperty', column, 'text');
    return { row: row_code, column: column_code};
}
// Возвращает данные для состава свода по медицинским организациям и движения по периодам
let fetchcelllayer = function(row, column) {
    let layer_container = $("<table class='table table-condensed table-striped table-bordered'></table>");
    let period_container = $("<table class='table table-condensed table-striped table-bordered'></table>");
    let fetch_url = cell_layer_url + row + '/' + column;
    $.getJSON( fetch_url, function( data ) {
        $.each(data.layers, function (i, layer) {
            let row = $("<tr class='rowdocument' id='"+ layer.doc_id +"'><td>" + layer.unit_code
                + "</td><td><a href='/datainput/formdashboard_v2/" + layer.doc_id +"' target='_blank' title='Открыть для редактирования'>" + layer.unit_name + "</a>"
                + "</td><td style='min-width: 40px' class='text-primary text-right'>" + layer.value
                + "</td></tr>");
            layer_container.append(row);
        });
        $.each(data.periods, function (i, period) {
            let row = $("<tr><td>" + period.period
                + "</td><td style='min-width: 40px' class='text-primary text-right'>" + period.value
                + "</td></tr>");
            period_container.append(row);
        });
    });
    return { layers: layer_container, periods: period_container} ;
};
let fetchconsolidationprotocol = function(row, column) {
    let layer_container = $("<table class='table table-condensed table-striped table-bordered'></table>");
    let fetch_url = cons_protocol_url + row + '/' + column;
    $.getJSON( fetch_url, function( data ) {
        //console.log(data);
        $.each(data, function (i, layer) {
            let row = $("<tr class='rowdocument' ><td>" + layer.unit_code + "</td>"
                + "<td>" + layer.unit_name + "</td><td style='min-width: 40px' class='text-primary text-right'>" + layer.value + "</td></tr>");
            layer_container.append(row);
        });
    });
    return { layers: layer_container } ;
};
// Инициализация перечня таблиц текущей формы
let inittablelist = function() {
    fgrid.jqxDataTable({
        width: 700,
        height: 400,
        filterable: true,
        filterMode: 'simple',
        theme: theme,
        source: tableListDataAdapter,
        localization: localize(),
        ready: function () {
            fgrid.jqxDataTable('selectRow', 0);
        },
        columns: [{
            text: 'Код',
            dataField: 'code',
            width: 70,
            cellClassName: function (row, column, value, data) {
                let cell_class = '';
                if ($.inArray(data.id, edited_tables) !== -1) {
                    cell_class += " editedRow";
                }
                if ($.inArray(data.id, invalidTables) !== -1) {
                    cell_class += " invalidTable";
                }
                return cell_class;
            }
        }, {
            text: 'Наименование',
            dataField: 'name',
            cellClassName: function (row, column, value, data) {
                if ($.inArray(data.id, edited_tables) !== -1) {
                    return "editedRow";
                }
            }
        }]
    });
    fgrid.on('rowSelect', function (event) {
        if (event.args.row.id === current_table) {
            return true;
        }
        fetchDataForDataGrid(event.args.row.id);
    });
};

// Инициализация вкладки протокола контроля формы
let initcheckformtab = function() {
    //$("#checkform").jqxButton({ theme: theme, disabled: control_disabled });
    $("#checkform").click(function () { checkform() });
    var refresh_protocol = $("<i style='margin-left: 2px;height: 14px; float: left' class='fa fa-lg fa-circle-o' title='Обновить/пересоздать протокол контроля'></i>");
    refresh_protocol.jqxToggleButton({ theme: theme, toggled: false });
    refresh_protocol.on('click', function () {
        var toggled = $(this).jqxToggleButton('toggled');
        if (toggled) {
            forcereload = 1;
            $(this).removeClass('fa-circle-o');
            $(this).addClass('fa-circle');
            raiseInfo("При следующием запуске контроля формы/таблицы протоколы будут обновлены");
        } else {
            forcereload = 0;
            $(this).removeClass('fa-circle');
            $(this).addClass('fa-circle-o');
        }


    });
    $("#fc_extrabuttons").append(refresh_protocol);

};
let initfilters = function() {
    row_name_filter = function (needle) {
        let rowFilterGroup = new $.jqx.filter();
        let filter_or_operator = 1;
        // create a string filter with 'contains' condition.
        //var filtervalue = 'всего';
        let filtervalue = needle;
        let filtercondition = 'contains';
        let nameRecordFilter = rowFilterGroup.createfilter('stringfilter', filtervalue, filtercondition);
        rowFilterGroup.addfilter(filter_or_operator, nameRecordFilter);
        //$("#DataGrid").jqxGrid('addfilter', '1', rowFilterGroup);
        dgrid.jqxGrid('addfilter', current_row_name_datafield, rowFilterGroup);
        dgrid.jqxGrid('applyfilters');
    };
    not_null_filter = function () {
        let notnullFilterGroup = new $.jqx.filter();
        // create a filter.
        let filter_or_operator = 1;
        let filtervalue = 0;
        let filtercondition = 'NOT_NULL';
        let notnullFilterGroup1 = notnullFilterGroup.createfilter('numericfilter', filtervalue, filtercondition);
        notnullFilterGroup.addfilter(filter_or_operator, notnullFilterGroup1);
        // TODO: Здесь нужно получить итоговый столбец из описания таблицы
        dgrid.jqxGrid('addfilter', '3', notnullFilterGroup);
        dgrid.jqxGrid('applyfilters');
    }
};
function initdatagrid() {
    let html = "<div class='jqx-loader-icon jqx-loader-icon-bootstrap' style='background-position-y: 0; margin-top: 5px'></div>" +
        "<div class='jqx-loader-text jqx-loader-text-bootstrap jqx-loader-text-bottom jqx-loader-text-bottom-bootstrap'>" +
        "<div>Загрузка структуры</div> " +
        "<div>таблицы и статданных ...</div>" +
        "</div>";
    tl.jqxLoader({theme: theme, width: 170, height: 80,
        isModal:false,
        imagePosition: 'top',
        autoOpen: true,
        html: html
    });

/*    $("#messageNotification").jqxNotification({
        width: 250, position: "top-right", opacity: 0.9,
        autoOpen: false, animationOpenDelay: 800, autoClose: true, autoCloseDelay: 3000, template: "info"
    });*/

    tablesource =
        {
            datatype: "json",
            datafields: datafields,
            autoBind: true,
            id: 'id',
            url: fetchvalues_url(),
            //updaterow: serverDataupdate,
            updaterow: function () { },
            root: null
        };
    dgridDataAdapter = new $.jqx.dataAdapter(tablesource, {
        loadError: xhrErrorNotificationHandler
    });
    dgrid.on("bindingcomplete", function (event) {
        //console.log('Завершение обновления данных таблицы первичных статданных');
        let rowcount = dgridDataAdapter.totalrecords;
        //console.log('Количество строк в загружаемой таблице ' + rowcount);
        getAggregatingRowsList(rowprops);
        getAggregatingColumnsList(colprops);
        if (biggrid_pageble) {
            if (rowcount > 50) {
                dgrid.jqxGrid({ pageable: true });
                //dgrid.jqxGrid({ rowsheight: 31});
            } else {
                dgrid.jqxGrid({ pageable: false });
            }
        }
        dgrid.jqxGrid('focus');
        dgrid.jqxGrid('selectcell', 0, firstdatacolumn);
        tl.jqxLoader('close');
    });
    dgrid.jqxGrid(
        {
            width: '100%',
            height: initDgridSize(),
            enabletooltips: false,
            //height: '100%',
            source: dgridDataAdapter,
            //localization: localize(),
            localization: getLocalization('ru'),
            selectionmode: 'singlecell',
            theme: theme,
            editable: edit_permission,
            editmode: 'selectedcell',
            keyboardnavigation: true,
            clipboard: true,
            columnsresize: true,
            showfilterrow: false,
            filterable: false,
            columns: columns,
            columngroups: columngroups,
            pagermode: "simple",
            pagesizeoptions: ['10', '20', '100'],
            pagesize: 20,
            pagerbuttonscount: 15,
            autoshowloadelement: false
        });
    dgrid.on('cellselect', cellSelecting);

    dgrid.on('cellbeginedit', cellbeginedit);

    dgrid.on("cellclick", function (event)
    {
        console.log("Событие: click");
    });
    dgrid.on('cellvaluechanged', cellValueChanged);
    dgrid.on('cellendedit', function (event)
    {
        console.log("Событие завершение редактирования ячейки");
        cell_is_editing = false;
    });

    /*    dgrid.on('paste', function (event) {
            console.log('Событие paste');
        });*/
/*    dgrid.onpaste( function (event) {
        console.log('Событие onpaste');
    });*/
/*    $(".jqx-input")[0].onpaste = function (event) {
        console.log('Событие onpaste');
    };*/
/*    $("div[role='gridcell']")[0].onpaste = function (event) {
        console.log('Событие onpaste');
    };*/

    //console.log($(".jqx-input")[0].onpaste);
};

function fetchDataForDataGrid(tableid) {
    tl.jqxLoader('open');
    $.get(tableprops_url + tableid, function (data) {
        datafields = data.datafields;
        calculatedfields = $.parseJSON(data.calcfields);
        data_for_table = $.parseJSON(data.tableproperties);
        columns = $.parseJSON(data.columns);
        columngroups = $.parseJSON(data.columngroups);
        firstdatacolumn = data.firstdatacolumn;
        not_editable_cells = data.noteditablecells;
        there_is_calculated = calculatedfields.length > 0;
        there_is_calculated ? calculate.removeClass('disabled') : calculate.addClass('disabled');
        rowprops = data.rowprops;
        colprops = data.colprops;
        current_table = parseInt(tableid);
        current_table_code = data_for_table.code;
        current_table_index = data_for_table.index;
        set_navigation_buttons_status(current_table_index);
        current_row_name_datafield = columns[1].dataField;
        current_row_number_datafield = columns[2].dataField;
        renderColumnFunctions();
        tablesource.datafields = datafields;
        tablesource.url = fetchvalues_url();
        renderDgrid();
    }).fail(xhrErrorNotificationHandler);
}
function renderColumnFunctions() {
    $.each(columns, function(column, properties) {
        if (typeof properties.cellclassname !== 'undefined' && properties.cellclassname === 'cellclass') {
            properties.cellclassname = cellclass;
        }
        //if (typeof properties.createeditor !== 'undefined') {
        //  properties.createeditor =  eval(properties.createeditor);
        //}
        //if (typeof properties.initeditor !== 'undefined') {
        //properties.initeditor = eval(properties.initeditor);
        //}

        //if (typeof properties.initeditor !== 'undefined') {
        if (typeof properties.createeditor !== 'undefined') {
            //properties.initeditor = function () { };
            //properties.createeditor = eval(properties.initeditor);
            switch (properties.createeditor) {
                case 'defaultEditor' :
                    properties.createeditor = defaultEditor;
                    break;
                case 'decimal1Editor' :
                    properties.createeditor = decimal1Editor;
                    break;
                case 'decimal2Editor' :
                    properties.createeditor = decimal2Editor;
                    break;
                case 'decimal3Editor' :
                    properties.createeditor = decimal3Editor;
                    break;
            }
        }

        if (typeof properties.validation !== 'undefined') {
            properties.validation = validation;
        }
        if (typeof properties.cellbeginedit !== 'undefined') {
            properties.cellbeginedit = cellbegineditByColumn;
        }
        /*        if (typeof properties.cellsrenderer !== 'undefined') {
                    properties.cellsrenderer = cellsrenderer;
                }*/
    });
    $.each(columngroups, function(group, properties) {
        if (typeof properties.rendered !== 'undefined')
            properties.rendered = tooltiprenderer;
    });
}
function renderDgrid() {
    console.log('Начало рендеринга таблицы первичных статданных');
    dgrid.jqxGrid('endcelledit', current_edited_cell.r, current_edited_cell.c, false);
    dgrid.jqxGrid('clearselection');
    dgrid.jqxGrid('clearfilters');
    dgrid.jqxGrid('beginupdate');
    dgrid.jqxGrid( { columns: columns } );
    dgrid.jqxGrid( { columngroups: columngroups } );
    dgrid.jqxGrid('updatebounddata');
    dgrid.jqxGrid('endupdate');
    $("#TableTitle").html("Таблица " + data_for_table.code + ', "' + data_for_table.name + '"');
    $("#tableprotocol").html('');
    $("#extrabuttons").hide();
    tdropdown.jqxDropDownButton('close');
    splitter.jqxSplitter('collapse');
}
// Действия при выборе ячейки
function cellSelecting(event) {
    let panels = $('#formEditLayout').jqxSplitter('panels');
    // Если правая панель скрыта, дальнейшие действия при выборе ячейки не производим.
    if (panels[1].collapsed) {
        return false;
    }
    let header;
    let args = event.args;
    let column_id = args.datafield;
    let rowindex = event.args.rowindex;
    let row_id = dgrid.jqxGrid('getrowid', rowindex);
    let row_code = dgrid.jqxGrid('getcellvaluebyid', row_id, current_row_number_datafield);
    let colindex = dgrid.jqxGrid('getcolumnproperty', column_id, 'text');
    let analitic_header = "<b>Строка " + row_code + ", Графа " + colindex +  ": </b><br/>";
    cellProtocolRender(row_id, column_id);
    if (doc_type === '2') {
        let returned = fetchcelllayer(row_id, column_id);
        $("#CellAnalysisTable").html(analitic_header).append(returned.layers);
        //$("#CellAnalysisTable").append(returned.layers);
        $("#CellPeriodsTable").html(analitic_header).append(returned.periods);
        //$("#CellPeriodsTable").append(returned.periods);
    } else if (doc_type === '3') {
        let returned = fetchconsolidationprotocol(row_id, column_id);
        $("#CellAnalysisTable").html(analitic_header).append(returned.layers);
    }
}
// в том числе, проверяем ли находится ли данная ячейка в списке запрещенных к редактированию ячеек
let cellbeginedit = function (event) {
    console.log('Начало редактирования ячейки - событие из объекта dgrid');
    cell_is_editing = true;
    let args = event.args;
    let colid = parseInt(args.datafield);
    let row = args.rowindex;
    let rowid = parseInt(dgrid.jqxGrid('getrowid', row));
    let value = args.value;
    editedcell_column = colid;
    editedcell_value = value;
    editedcell_row = rowid;
};
// На всякий случай "заглушка", если нужно определить событие на редактирование отдельной графы
let cellbegineditByColumn = function(row, datafield, columntype) {
    console.log('Начало редактирования ячейки - событие из свойств column');
    //console.log(row);
    //console.log(datafield);
    //console.log(columntype);
    let rowid = parseInt(dgrid.jqxGrid('getrowid', row));
    let colid = parseInt(datafield);
    if (checkIsNotEditable(rowid, colid)) {
        return false;
    }
};
// Обработка события изменения данных ячейки при отправке данных на сервер при каждом измении ячейки
/**
 * Функция не используется в текущей версии - пока оставлена здесь
let cellValueChanged_route1 = function (event) {
    console.log("Событие смены значения ячейки - вариант 1");
    if (typeof window.event === 'undefined') {
        console.log('Была вставка из буфера?');
        if (event.args.newvalue !==  event.args.oldvalue) {
            let rowid = parseInt(dgrid.jqxGrid('getrowid', event.args.rowindex));
            let colid = parseInt(event.args.datafield);
            saveCellValue(rowid, event.args.rowindex, colid, event.args.newvalue, event.args.oldvalue);
        }
    }
};*/
// Обработка события изменения данных ячейки с записью в локальный журнал
let cellValueChanged = function (event) {
    console.log("Событие смены значения ячейки");
    // loose comparision - в данном случае для корректного сравнения 0 и null
    if (event.args.newvalue !=  event.args.oldvalue) {
        let table = current_table;
        let rowindex = event.args.rowindex;
        let row = parseInt(dgrid.jqxGrid('getrowid', rowindex));
        let column = parseInt(event.args.datafield);
        let record = logCellValueChange(table, row, column, event.args.newvalue, event.args.oldvalue, rowindex);
        if (autocalculateTotals) {
            // Авторасчет поместить сюда
            calculateAggregatedCell(record);
        }
        console.log("Изменение ячейки сохранено в журнале");
    }
};
// Запись изменения ячейки в локальный журнал
function logCellValueChange(table, row, column, newvalue, oldvalue, rowindex) {
    //console.log("Сохранение изменения значения ячейки в журнале");
    //let edit_ts = new Date;
    let rc_codes = getReadableCellAdress(row, column);
    let record = {
        table: table,
        tablecode: current_table_code,
        row: row,
        rowcode: rc_codes.row,
        rowindex: rowindex,
        column: column,
        columncode: rc_codes.column,
        newvalue: newvalue,
        oldvalue: oldvalue || null ,
        //beginstore_at: edit_ts.toISOString(),
        beginstore_at: Math.floor(Date.now()/1000),
        endstore_at: null,
        stored: null,
        commited: false,
        message: ''
     };
    cellValueChangingLog.push(record);
    return record;
}
// Сохранение данных на сервере из локального журнала изменений
function flushCellValueChangesCache(message) {
    if (cells_are_recalculating) {
        console.log("Сброс кеша измененных ячеек отложен - идет пересчет итоговых строк и граф");
        flushTimer = setTimeout(flushCellValueChangesCache, 3000);
        return;
    }
/*    if (autocalculateTotals) {
        // Перерасчет итоговых строк на основе журнала изменения данных
        recalculateOnlyActiveAggregates();
    }*/
    let unsaved = cellValueChangingLog.filter(cell => (cell.stored === null || cell.stored === false));
    if (unsaved.length > 0) {
        $.ajax({
            dataType: 'json',
            url: flushlog_url,
            data: { unsaved },
            method: 'POST',
            success: function (data, status, xhr) {
                let server_success_records = data.filter(cell => cell.stored === true);
                let server_fault_records = data.filter(cell => cell.stored === false);
                if (!server_fault_records.length) {
                    unsaved.map(function(cell) {
                        cell.stored = true;
                        cell.message = 'Запись успешно сохранена';
                    });
                    if (typeof (message) !== 'undefined') {
                        raiseInfo(message);
                    }
                    if (dataStoreErrorNotification.is(":visible")) {
                        dataStoreErrorNotification.hide();
                        raiseInfo("Изменения сохранены");
                        storeAttempts = 0;
                    }
                    flushTimer = setTimeout(flushCellValueChangesCache, 3000);
                } else {
                    //raiseError('Внимание! Изменения не сохранены! Необходимо проверить текущий статус документа и/или раздела документа (при наличии).');
                    //console.log(unsaved);
                    data.forEach(function (handled) {
                        let rec = unsaved.filter( cell => cell.table === parseInt(handled.table)
                            && cell.row === parseInt(handled.row)
                            && cell.column === parseInt(handled.column)
                            && cell.beginstore_at === parseInt(handled.beginstore_at)
                        );
                        rec[0].stored = handled.stored;
                        rec[0].message = handled.message;
                        rec[0].endstore_at = handled.endstore_at;
                        //console.log(rec);
                    });
                    let error = { status: '1001', responseText: 'Изменения не сохранены! Необходимо проверить текущий статус документа и/или раздела документа (при наличии).'};
                    storeAttempts++;
                    dataStoreErrorInformer(error);
                    flushTimer = setTimeout(flushCellValueChangesCache, 12000);
                }
                logTable.jqxGrid('updatebounddata', 'cells');
                if (!cell_is_editing) {
                    dgrid.jqxGrid('render');
                }
                console.log("Отправка данных на сервер из журнала изменений. Сохранено ячеек" , server_success_records, server_fault_records);
            },
            error: function(xhr, status, errorThrown) {
                storeAttempts++;
                dataStoreErrorInformer(xhr);
                logTable.jqxGrid('updatebounddata', 'cells');
                flushTimer = setTimeout(flushCellValueChangesCache, 12000);
            }
        });
    } else {
        flushTimer = setTimeout(flushCellValueChangesCache, 3000);
        console.log("В журнале нет несохраненных записей");
    }
}
// Управление выводом сообщений об ошибке сохранения данных
function dataStoreErrorInformer(error) {
    let message = '<strong>Ошибка! </strong><span>Не все внесенные изменения сохранены!</span>. ' +
        'Статус: ' + error.status + ' (' + error.responseText + '). ' +
        'Попытка сохранения: ' + storeAttempts;
    dataStoreErrorNotification.html(message);
    if (dataStoreErrorNotification.is(":hidden")) {
        dataStoreErrorNotification.show();
    };
    console.log(error);
}
// Функция для метода updaterow объекта tablesource
/**
 * Функция не используется в текущей версии - оставлена здесь на всякий случай

function serverDataupdate(rowid, rowdata) {
    console.log("Начало выполнения функции updaterow объекта tablesource");
    if (typeof window.event === 'undefined') {
        return false;
    }
    let value = rowdata[editedcell_column];
    /!*                let cellvalidation = validateCell(rowid, editedcell_column, value);
                    if (!cellvalidation.result) {
                        dgrid.jqxGrid('showvalidationpopup', 5, editedcell_column, "Invalid Value");
                        return false;
                    }*!/
    let oldvalue;
    if (typeof editedcell_value !== 'undefined') {
        oldvalue = editedcell_value;
    } else {
        oldvalue = null;
    }
    current_edited_cell.t = current_table;
    current_edited_cell.r = rowdata.boundindex;
    current_edited_cell.c = editedcell_column;
    current_edited_cell.valid = true;
    current_edited_cell.rowid = parseInt(rowid);
    if (oldvalue !== value) {
        saveCellValue(current_edited_cell.rowid, rowdata.boundindex, editedcell_column, value, oldvalue, autocalculateTotals);
    }
}
*/

// сохранение значения ячейки на сервере
/**
 * Неиспользуемый в данной версии метод - пока сохранен.
 *
 function saveCellValue(row, rowindex, column, newvalue, oldvalue, calculateTotals = false) {
    console.log('Запуск функции сохранения значения ячейки');
    if (checkIsNotEditable(row, column)) {
        raiseError("Изменения в нередактируемых ячейках не сохранены.");
        return false;
    }
    let data = "row=" + row + "&column=" + column + "&value=" + newvalue + "&oldvalue=" + oldvalue;
    $.ajax({
        dataType: 'json',
        url: savevalue_url + current_table ,
        data: data,
        method: 'POST',
        success: function (data, status, xhr) {
            if (data.error === 401) {
                raiseError("Данные не сохранены. Пользователь не авторизован!");
                return false;
            }
            else if (data.error === 1001) {
                raiseError(data.comment);
                return false;
            }
            else {
                if (data.cell_affected) {
                    editedCells.push({ t: current_table, r: rowindex, c: column});
                    if (protocol_control_created) {
                        $(".inactual-protocol").show();
                    }
                    if (calculateTotals) {
                        console.log('Автосуммирование включено ' + calculateTotals);
                        let aggregatingrows = checkIsAggregatedRowCell(row);
                        //console.log(aggregatingrows);
                        let aggregatingcolumns = checkIsAggregatedColumnCell(column);
                        //console.log(aggregatingcolumns);
                        if (aggregatingrows.length > 0) {
                            for (let i = 0; aggregatingrows.length > i; i++) {
                                calculateAggregatingRowCell(aggregatingrows[i], column);
                            }
                        }
                        if (aggregatingcolumns.length > 0) {
                            for (let i = 0; aggregatingcolumns.length > i; i++) {
                                calculateAggregatingColumnCell(row, aggregatingcolumns[i]);
                            }
                        }
                    }
                    return true;
                }
            }
        },
        error: xhrErrorNotificationHandler
    });
}
*/
// Протокол контроля выделенной ячейки
function cellProtocolRender(row_id, column_id) {
    let cell_protocol_panel = $("#cellprotocol");
    cell_protocol_panel.html('');
    if (typeof current_protocol_source[current_table_code] === 'object') {
        let cellprotocol = selectedcell_protocol(current_protocol_source, current_table, current_table_code, column_id, row_id);
        let count_of_rules  = cellprotocol.length > 0 ? cellprotocol.length : " не определены ";
        if ( cellprotocol.length > 0) {
            header = $("<div class='alert alert-info'><p>Количество заданых правил контроля для данной ячейки - " + count_of_rules + " </p></div>");
        } else {
            header = $("<div class='alert alert-warning'><p>Нет заданых правил контроля для данной ячейки</p></div>");
        }
        cell_protocol_panel.append(header);
        for (i = 0; i < count_of_rules ; i++) {
            cell_protocol_panel.append("<strong>Правило контроля: </strong><span>" + cellprotocol[i].rule.formula + "</span>");
            let row;
            let result = cellprotocol[i].result;
            let rule = cellprotocol[i].rule;
            switch (cellprotocol[i].rule.function_id) {
                case formlabels.compare :
                    row = renderCompareControl(result, rule.boolean_sign, rule.iteration_mode, rule.level);
                    break;
                case formlabels.dependency :
                    row = renderDependencyControl(result, rule.iteration_mode, rule.level);
                    break;
                case formlabels.interannual :
                    row = renderInterannualControl(result, rule.level);
                    break;
                case formlabels.iadiapazon :
                    row = renderInDiapazonControl(result, rule.level);
                    break;
                case formlabels.multiplicity :
                    row = renderFoldControl(result, rule.level);
                    break;
            }
            cell_protocol_panel.append(row);
            if (cellprotocol[i].rule.comment !== '') {
                cell_protocol_panel.append("<div style='margin-bottom: 5px'><strong>^ </strong><small>" + cellprotocol[i].rule.comment + "</small></div>");
            }
        }
    } else {
        cell_protocol_panel.html("<div class='alert alert-danger'><p>Протокол контроля текущей таблицы не найден. Выполните контроль текущей таблицы</p></div>");
    }
}
// На случай вставки данных из буфера - не версим данные в нередатируемые ячейки
function checkIsNotEditable(rowid, colid) {
    //console.log(current_table);
    //console.log(rowid);
    //console.log(colid);
/*    for (let i = 0; i < not_editable_cells.length; i++) {
        if (not_editable_cells[i].t === current_table && not_editable_cells[i].r === rowid && not_editable_cells[i].c === colid) {
            console.log('ячейка в списке нередактируемых');
            return true;
        }
    }*/
    if (not_editable_cells.length > 0) {
        let found = not_editable_cells.findIndex(x => x.t === current_table && x.r === rowid && x.c === colid);
        if (found !== -1) {
            console.log('ячейка в списке нередактируемых');
            return true;
        }
    }

    if (rowprops.length > 0) {
        if (checkIsAggregatingdRow(rowid)) {
            console.log('ячейка входит в итоговую строку - не редактируется');
            return true;
        }
    }
    if (colprops.length > 0) {
        if (checkIsAggregatingColumn(colid)) {
            console.log('ячейка входит в итоговую графу - не редактируется');
            return true;
        }
    }
    return false;
}




// Получаем свойства строки
function getRowProperties(rowid) {
    for (let i = 0; i < rowprops.length; i++ ) {
        if (rowprops[i].row === rowid ) {
            return rowprops[i];
        }
    }
    return null;
}
// Проверяем - итоговая ли строка?
function checkIsAggregatingdRow(rowid) {
    for (let i = 0; i < rowprops.length; i++ ) {
        if (rowprops[i].row === rowid && rowprops[i].aggregate === true ) {
            return true;
        }
    }
    return false;
}
// Получение списка "рассчитывающих" строк
function getAggregatingRowsList(props) {
    let rows = [];
    aggregatingRows = [];
    rowAggregatingRules = [];
    for (let i = 0; i < props.length; i++ ) {
        if (props[i].aggregate) {
            //rows.push(typeof props[i].aggregated_rows !== 'undefined' ? props[i].aggregated_rows : []);
            if (typeof props[i].aggregated_rows !== 'undefined') {
                for (let j = 0; j < props[i].aggregated_rows.length; j++ ) {
                    let agrow = props[i].aggregated_rows[j];
                    rows.push(agrow);
                    if (typeof (rowAggregatingRules[agrow]) === 'undefined') {
                        rowAggregatingRules[agrow] = [];
                    }
                    rowAggregatingRules[agrow].push(props[i].row);
                }
            }
        }
    }
    //rows = rows.flat();
    aggregatingRows = rows.filter(onlyUnique);
}
// Получение списка "рассчитывающих" граф
function getAggregatingColumnsList(props) {
    let columns = [];
    aggregatingColumns = [];
    columnAggregatingRules = [];
    for (let i = 0; i < props.length; i++ ) {
        if (props[i].aggregate) {
            //columns.push(typeof props[i].aggregated_columns !== 'undefined' ? props[i].aggregated_columns : []);
            if (typeof props[i].aggregated_columns !== 'undefined') {
                for (let j = 0; j < props[i].aggregated_columns.length; j++ ) {
                    let agcolumn = props[i].aggregated_columns[j];
                    columns.push(agcolumn);
                    if (typeof (rowAggregatingRules[agcolumn]) === 'undefined') {
                        columnAggregatingRules[agcolumn] = [];
                    }
                    columnAggregatingRules[agcolumn].push(props[i].column);
                }
            }
        }
    }
    //columns = columns.flat();
    aggregatingColumns = columns.filter(onlyUnique);
}
// Проверяем участвует ли ячейка при расчете итоговой строки
function checkIsAggregatedRowCell(rowid) {
    //console.log(rowprops);
    let aggregatingrows = [];
    for (let i = 0; i < rowprops.length; i++ ) {
        if (rowprops[i].aggregate) {
            let rowcollection = typeof rowprops[i].aggregated_rows !== 'undefined' ? rowprops[i].aggregated_rows : [];
            for (let j = 0; j < rowcollection.length; j++) {
                if (rowcollection[j] === rowid ) {
                    aggregatingrows.push(rowprops[i].row);
                }
            }
        }
    }
    return aggregatingrows;
}
// Получение списка Id строк для подсчета итоговой строки при условии, если свойство строки aggregate == true
function getAggregatedRows(aggregating_row) {
    for (let i = 0; i < rowprops.length; i++ ) {
        if (rowprops[i].row === aggregating_row && rowprops[i].aggregate) {
            return rowprops[i].aggregated_rows;
        }
    }
    return [];
}
// ПОлучаем свойства графы
function getColumnProperties(colid) {
    for (let i = 0; i < colprops.length; i++ ) {
        if (colprops[i].column === colid) {
            return colprops[i];
        }
    }
    return null;
}
// Проверяем - итоговая ли графа?
function checkIsAggregatingColumn(colid) {
    for (let i = 0; i < colprops.length; i++ ) {
        if (colprops[i].column === colid && colprops[i].aggregate) {
            return true;
        }
    }
    return false;
}
// Проверяем участвует ли ячейка при расчете итоговой графы
function checkIsAggregatedColumnCell(colid) {
    let aggregatingcolumns = [];
    for (let i = 0; i < colprops.length; i++ ) {
        if (colprops[i].aggregate) {
            let colcollection = typeof colprops[i].aggregated_columns !== 'undefined' ? colprops[i].aggregated_columns : [];
            for (let j = 0; j < colcollection.length; j++) {
                if (colcollection[j] === colid ) {
                    //return colprops[i].column;
                    aggregatingcolumns.push(colprops[i].column);
                }
            }
        }
    }
    return aggregatingcolumns;
}
// Получение списка Id граф для подсчета итоговой графы
function getAggregatedColumns(aggregating_column) {
    for (let i = 0; i < colprops.length; i++ ) {
        if (colprops[i].column === aggregating_column && colprops[i].aggregate ) {
            return colprops[i].aggregated_columns;
        }
    }
    return [];
}
// Расчет ячейки входящей в итоговую строку
function calculateAggregatingRowCell(rowid, colid) {
    let aggregated_rows = getAggregatedRows(parseInt(rowid));
    let value = 0;
    for (let i = 0; i < aggregated_rows.length; i++) {
        let cc = parseFloat(dgrid.jqxGrid('getcellvaluebyid', aggregated_rows[i], colid))||0;
        //console.log(cc);
        value += cc;
    }
    dgrid.jqxGrid('setcellvaluebyid', rowid, colid, value);
    let aggregatingcolumns = checkIsAggregatedColumnCell(colid);
    if (aggregatingcolumns.length > 0) {
        for (let i = 0; aggregatingcolumns.length > i; i++) {
            calculateAggregatingColumnCell(rowid, aggregatingcolumns[i]);
        }
    }
}
// Расчет ячейки входящей в итоговую графу
function calculateAggregatingColumnCell(rowid, colid) {
    let aggregated_columns = getAggregatedColumns(parseInt(colid));
    //console.log(aggregated_columns);
    let value = 0;
    for (let i = 0; i < aggregated_columns.length; i++) {
        let cc = parseFloat(dgrid.jqxGrid('getcellvaluebyid', rowid, aggregated_columns[i]))||0;
        //console.log(cc);
        value += cc;
    }
    editedcell_column = colid;
    dgrid.jqxGrid('setcellvaluebyid', rowid, colid, value);
    let aggregatingrows = checkIsAggregatedRowCell(rowid);
    if (aggregatingrows.length > 0) {
        for (let i = 0; aggregatingrows.length > i; i++) {
            calculateAggregatingRowCell(aggregatingrows[i], colid);
        }
    }
}



// Перерасчет итогов только для затронутых ячеек по журналу измененных ячеек
function recalculateOnlyActiveAggregates() {
    // Включаем в перерасчет только ячейки, сохраненные на сервере
    // let notcommited = cellValueChangingLog.filter(cell => (cell.stored === true && cell.commited === false));
    // Включаем в перерасчет все не обработанные ячейки
    let notcommited = cellValueChangingLog.filter(cell => (cell.commited === false));
    let rows_changed = 0;
    let columns_changed = 0;
    if (notcommited.length > 0) {
        notcommited.map(function(cell) {
            cell.commited = true;
            rows_changed += calculateAggregatedRow(cell);
            columns_changed += calculateAggregatedColumn(cell);
        });
        console.log("Пересчитано итоговых строк " + rows_changed);
        console.log("Пересчитано итоговых граф" + columns_changed);
    } else {
        console.log("В журнале нет записей для перерасчета итогов");
    }
}


function calculateAggregatedCell(cell) {
    calculateAggregatedRow(cell);
    calculateAggregatedColumn(cell);
}

//
function calculateAggregatedRow(cell) {
    let rows_changed = 0;
    if (aggregatingRows.includes(cell.row)) {
        let agrows = rowAggregatingRules[cell.row];
        agrows.forEach(function(agrow) {
            // вариант 1 - с полным пересчетом итога
            let filtered_rows = rowprops.filter(rowproperty => rowproperty.row === agrow);
            let rows_to_sum = filtered_rows[0].aggregated_rows;
            let value = aggregateRowsByColumn(rows_to_sum, cell.column);
            dgrid.jqxGrid('setcellvaluebyid', agrow, cell.column, value);
            // вариант 2  - вычисление итога по изменению значения ячейки
            // не удалось отладить: считает непредсказуемо
            /*let diff = cell.newvalue - cell.oldvalue;
            console.log(diff);
            let old_aggregated_value = dgrid.jqxGrid('getcellvalue', cell.rowindex, cell.column);
            let new_aggregated_value = old_aggregated_value + diff;
            dgrid.jqxGrid('setcellvaluebyid', agrow, cell.column, new_aggregated_value);*/
            rows_changed++;
        });
    }
    return rows_changed;
}
//
function calculateAggregatedColumn(cell) {
    let columns_changed = 0;
    if (aggregatingColumns.includes(cell.column)) {
        let agcolumns = columnAggregatingRules[cell.column];
        agcolumns.forEach(function(agcolumn) {
            // вариант 1 - с полным пересчетом итога
            let filtered_rows = colprops.filter(colproperty => colproperty.column === agcolumn);
            let columns_to_sum = filtered_rows[0].aggregated_columns;
            let value = aggregateColumnsByRow(columns_to_sum, cell.row);
            dgrid.jqxGrid('setcellvaluebyid', cell.row, agcolumn, value);
            // вариант 2  - вычисление итога по изменению значения ячейки
            // не удалось отладить: считает непредсказуемо
            /*let diff = cell.newvalue - cell.oldvalue;
            console.log(diff);
            let old_aggregated_value = dgrid.jqxGrid('getcellvalue', cell.rowindex, cell.column);
            let new_aggregated_value = old_aggregated_value + diff;
            dgrid.jqxGrid('setcellvaluebyid', agrow, cell.column, new_aggregated_value);*/
            columns_changed++;
        });
    }
    return columns_changed;
}

// Пересчет итоговых ячеек в таблице
function calculateAllAggregatinCells() {
    console.log("Начало перерасчета итоговых ячеек");
    cells_are_recalculating = true;
    let initialAutocalculateState = autocalculateTotals;
    autocalculateTotals = false;
    cellsRecalculatingCache = [];
    calculateAllAggregatingRows();
    calculateAllAggregatingColumns();
    flushRecalculatingCache(initialAutocalculateState);
}
// Расчет суммы всех итоговых строк в таблице
function calculateAllAggregatingRows() {
    console.log("Начало перерасчета итоговых строк");
    let rows = rowprops.filter(rowproperty => rowproperty.aggregate === true);
    let columns = datafields.filter(datafield => datafield.type === 'number');
    for (let i = 0; i < rows.length; i++ ) {
        for (let j = 0; j < columns.length; j++) {
            editedcell_column = columns[j].name;
            let value = aggregateRowsByColumn(rows[i].aggregated_rows, columns[j].name);
            if (value === 0) {
                //dgrid.jqxGrid('setcellvaluebyid', rows[i].row, columns[j].name, null);
                cellsRecalculatingCache.push({ row: rows[i].row, column: columns[j].name, value: null });
            } else {
                //dgrid.jqxGrid('setcellvaluebyid', rows[i].row, columns[j].name, value);
                cellsRecalculatingCache.push({ row: rows[i].row, column: columns[j].name, value: value });
            }
        }
    }
}
// Расчет суммы всех итоговых граф в таблице
function calculateAllAggregatingColumns() {
    console.log("Начало перерасчета итоговых граф");
    let columns = colprops.filter(columnproperty => columnproperty.aggregate === true);
    let rows = dgridDataAdapter.records.map(function (rec) {
        return rec.id;
    }) ;
    for (let i = 0; i < columns.length; i++ ) {
        for (let j = 0; j < rows.length; j++) {
            editedcell_column = columns[i].column;
            let value = aggregateColumnsByRow(columns[i].aggregated_columns, rows[j]);
            if (value === 0) {
                //dgrid.jqxGrid('setcellvaluebyid', rows[j], columns[i].column, null);
                cellsRecalculatingCache.push({ row: rows[j], column: columns[i].column, value: null });
            } else {
                //dgrid.jqxGrid('setcellvaluebyid', rows[j], columns[i].column, value);
                cellsRecalculatingCache.push({ row: rows[j], column: columns[i].column, value: value });
            }
        }
    }

}
// Внесение расчитанных итоговых значений в стаблицу из кэша
function flushRecalculatingCache(initialAutocalculateState) {
    console.log("Сброс кеща перерасчитанных ячеек");
    for (let i = 0; cellsRecalculatingCache.length > i; i++) {
        dgrid.jqxGrid('setcellvaluebyid', cellsRecalculatingCache[i].row, cellsRecalculatingCache[i].column, cellsRecalculatingCache[i].value);
    }
    autocalculateTotals = initialAutocalculateState;
    cells_are_recalculating = false;
    console.log("Перерасчет итоговых ячеек завершен");
}

// Расчет суммы строк по выбранной графе
function aggregateRowsByColumn(rows, colid) {
    let value = 0;
    for (let i = 0; i < rows.length; i++) {
        value += parseFloat(dgrid.jqxGrid('getcellvaluebyid', rows[i], colid))||0;
    }
    return value;
}
// Расчет суммы граф по выбранной строке
function aggregateColumnsByRow(columns, rowid) {
    let value = 0;
    for (let i = 0; i < columns.length; i++) {
        value += parseFloat(dgrid.jqxGrid('getcellvaluebyid', rowid, columns[i]))||0;
    }
    return value;
}



// Панель инструментов для редактируемой таблицы
let inittoolbarbuttons = function () {
    tdropdown.jqxDropDownButton({width: 120, height: 22, theme: theme});
    tdropdown.jqxDropDownButton('setContent', '<div style="margin-top: 3px">Таблицы</div>');
    set_navigation_buttons_status(current_table_index);
    nexttable.click( function() {
        let oldindex = current_table_index;
        let found = false;
        let next = ++current_table_index;
        do {
            found = searchTableByIndex(next);
            if (found) {
                fetchDataForDataGrid(found.id);
                return true;
            }
            next++;
        } while (next < max_table_index);
        if (!found) {
            current_table_index = oldindex;
            return false;
        }
    });
    prevtable.click( function() {
        let oldindex = current_table_index;
        let found = false;
        let prev = --current_table_index;
        do {
            found = searchTableByIndex(prev);
            if (found) {
                fetchDataForDataGrid(found.id);
                return true;
            }
            prev--;
        } while (prev > 0);
        if (!found) {
            current_table_index = oldindex;
            return false;
        }
    });
    if (!there_is_calculated) {
        //calculate.attr('disabled', true );
        calculate.addClass('disabled');
    }
    calculate.click(fillCalculatedFields);
    recalculateAllAggregates.click( function () {
        calculateAllAggregatinCells();
    });
    fullscreen.click(function() {
        dgrid.fullscreen();
    });
    tcheck.click( function() {
        tabledatacheck(current_table, 'inform');
        splitter.jqxSplitter('expand');
        controltabs.jqxTabs('select', 0);

    });
    idtcheck.click( function() {
        tabledatacheck(current_table, 'interform');
        splitter.jqxSplitter('expand');
        controltabs.jqxTabs('select', 0);

    });
    iptcheck.click( function() {
        tabledatacheck(current_table, 'interperiod');
        splitter.jqxSplitter('expand');
        controltabs.jqxTabs('select', 0);
    });
    formcheck.click( function () {
        splitter.jqxSplitter('expand');
        controltabs.jqxTabs('select', 1);
        checkform();
    });

    excelexport.click(function () {
        let url = tableexport_url + current_table ;
        location.replace(url);
    });
    edit_permission ? excelimport.show() : excelimport.hide();
    excelimport.click(function () {
        let url = excelupload_url + current_table ;
        flUpload.jqxFileUpload({ uploadUrl: url });
        $("#UploadResult").html('');
        $("#ExcelUploadComment").show();
        excelUploadWindow.jqxWindow('open');
    });
    fsdropdown.jqxDropDownButton({width: 170, height: 22, theme: theme});
    fsdropdown.jqxDropDownButton('setContent', '<div style="margin-top: 3px">Управление разделами</div>');
    $("#FormSections").show();
    if ((current_user_role !== '3' && current_user_role !== '4') || doc_type === '2') {
        fsdropdown.hide();
    }

    $(".blocksection").click(function () {
        let selector = $(this);
        let fs = selector.prop('id');
        $.get(blocksection_url + fs + '/' + '1', function( data ) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.error);
                return false;
            }
            let tr = selector.parent().parent();
            let nextbutton = selector.parent().next().children().first();
            tr.removeClass('danger').addClass('success');
            tr.prop('title', 'Раздел принят ' + data.section.updated_at + ' пользователем ' + data.worker.description);
            selector.prop('disabled', 'disabled');
            nextbutton.removeAttr('disabled');
            raiseInfo("Смена статуса раздела документа выполнена - 'принят'");
        });
    });

    $(".unblocksection").click(function () {
        let selector = $(this);
        let fs = selector.prop('id');
        $.get(blocksection_url + fs + '/' + '0', function( data ) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.error);
                return false;
            }
            let tr = selector.parent().parent();
            let prevbutton = selector.parent().prev().children().first();
            tr.removeClass('success').addClass('danger');
            tr.prop('title', 'Раздел отклонен ' + data.section.updated_at + ' пользователем ' + data.worker.description);
            selector.prop('disabled', 'disabled');
            prevbutton.removeAttr('disabled');
            raiseInfo("Смена статуса раздела документа выполнена - отклонен");
        });
    });

    disableAutosumm.click(function () {
        autocalculateTotals = !disableAutosumm.prop('checked');
    });
    $("#pageMode").click(function () {
        $("#pageMode").prop('checked') ? dgrid.jqxGrid({ pageable: true }) : dgrid.jqxGrid({ pageable: false });
    });

    $("#disableColumnPopovers").click(function () {
        dgrid.jqxGrid('columngroups').forEach(function (column_group) {
            $("#disableColumnPopovers").prop('checked') ? column_group.rendered = null : column_group.rendered = tooltiprenderer;
        });
        dgrid.jqxGrid('render');
    });

    disableAutosumm.click(function () {
        autocalculateTotals = !disableAutosumm.prop('checked');
    });

    let oldVal = "";
    filterinput.on('keydown', function (event) {
        if (filterinput.val().length >= 2) {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            if (oldVal !== filterinput.val()) {
                this.timer = setTimeout(function () {
                    row_name_filter(filterinput.val());
                }, 500);
                oldVal = filterinput.val();
            }
        } else {
            dgrid.jqxGrid('removefilter', '1');
        }
    });
    clearfilter.click(function () { dgrid.jqxGrid('clearfilters'); filterinput.val(''); });
    $("#rp-open").click(function () {
        if ($("#ControlTabs")[0].clientWidth === 0) {
            splitter.jqxSplitter('expand');
        } else {
            splitter.jqxSplitter('collapse');
        }
    });
};

let initConsolidateFormButton = function () {
    let csd = $("#СonsolidateDocument");
    let cst = $("#СonsolidateTable");
    csd.click(function () {
        csd.attr('disabled', true );
        cst.attr('disabled', true );
        $("#CalculationProgress").show();
        $.get('/admin/consolidate/'+ doc_id, function( data ) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.error);
                return false;
            }
            raiseInfo("Произведен расчет документа");
            dgrid.jqxGrid('updatebounddata');
            $("#CalculationProgress").hide();
            csd.attr('disabled', false );
            cst.attr('disabled', false );
        }).fail(function () {
            raiseError('При расчете документа произошла ошибка');
            csd.attr('disabled', false );
            cst.attr('disabled', false );
        });
    });
};

let initConsolidateButton = function () {
    let cst = $("#СonsolidateTable");
    let csd = $("#СonsolidateDocument");
    cst.click(function () {
        cst.attr('disabled', true );
        csd.attr('disabled', true );
        $("#CalculationProgress").show();
        $.get('/admin/cons_by_rule_list/'+ doc_id +'/' + current_table, function( data ) {
            if (typeof data.error !== 'undefined') {
                raiseError(data.error);
                return false;
            }
            raiseInfo("Произведен расчет текущей таблицы");
            dgrid.jqxGrid('updatebounddata');
            $("#CalculationProgress").hide();
            cst.attr('disabled', false );
            csd.attr('disabled', false );
        }).fail(function () {
            raiseError('При расчете таблицы произошла ошибка');
            cst.attr('disabled', false );
            csd.attr('disabled', false );
        });
    });
};

let initTableMedstatExportButton = function() {
    let me = $("#tableMedstatExport");
    if (current_user_role === '3' || current_user_role === '4') {
        me.show();
    }
    me.click(function () {
        let url = msexport_url + current_table ;
        location.replace(url);
    });
};

let initFlushValueChangesLogButton = function () {
    if ($("#flushValueChangesLog").length) {
        $("#flushValueChangesLog").click(function () {
            flushCellValueChangesCache(undefined);
        });
    }
};

let defaultEditor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 0, digits: 12  });
};
let decimal1Editor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 1, digits: 12, decimalSeparator: ',' });
    editor.jqxNumberInput({ decimalDigits: 1, digits: 12, decimalSeparator: ',' });
};
let decimal2Editor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 2, digits: 12, decimalSeparator: ',' });
};
let decimal3Editor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 3, digits: 12, decimalSeparator: ',' });
};
let cellclass = function (row, columnfield, value, rowdata) {
    let invalid_cell = '';
    let alerted_cell = '';
    let class_by_edited_row = '';
    let class_by_not_saved_row = '';
    let not_editable = '';
    let rowaggregate = '';
    let columnaggregate = '';

    for (let i = 0; i < not_editable_cells.length; i++) {
        if (not_editable_cells[i].t === parseInt(current_table) && not_editable_cells[i].r === rowdata.id && not_editable_cells[i].c === columnfield) {
            return 'jqx-grid-cell-pinned jqx-grid-cell-pinned-bootstrap not_editable';
        }
    }
    if (marking_mode === 'control') {
        if (typeof invalidCells[current_table] !== 'undefined' ) {
            for (let i = 0; i < invalidCells[current_table].length; i++ ) {
                if (invalidCells[current_table][i].r === rowdata.id && invalidCells[current_table][i].c === columnfield) {
                    invalid_cell = 'invalid';
                }
            }
        }
        if (typeof alertedCells[current_table] !== 'undefined' ) {
            for (let i = 0; i < alertedCells[current_table].length; i++ ) {
                if (alertedCells[current_table][i].r === rowdata.id && alertedCells[current_table][i].c === columnfield) {
                    alerted_cell = 'alerted';
                }
            }
        }
    }

    cellIsEdited = function (element, index, array) {
        return element.table === current_table && element.rowindex === row && element.column === columnfield && element.stored
    };
    cellIsNotSaved = function (element, index, array) {
        return element.table === current_table && element.rowindex === row && element.column === columnfield && !element.stored
    };

    if (cellValueChangingLog.find(cellIsEdited)) {
        class_by_edited_row = "editedRow";
    }
    if (cellValueChangingLog.find(cellIsNotSaved)) {
        class_by_not_saved_row = "notSaved";
    }

    if (checkIsAggregatingdRow(rowdata.id)) {
        rowaggregate = 'rowaggregate'
    }
    if (checkIsAggregatingColumn(columnfield)) {
        columnaggregate = 'columnaggregate'
    }

    return  alerted_cell + ' ' + invalid_cell + ' ' + rowaggregate + ' ' + columnaggregate + ' ' +  class_by_edited_row + ' ' + class_by_not_saved_row;
};
// Функция не нужна - оставим пока на всякий случай
let validation = function(cell, value) {
    let props = getColumnProperties(cell.column);
    let cellrules = null;
    if (props !== null && typeof props.validation !== 'undefined') {
        cellrules = props.validation;
    }
    for (let grkey in validationrules) {
        let cellrule = overideRule(grkey, cellrules);
        let vrule = cellrule ? cellrule  : validationrules[grkey];
        if (!assertvrule(value, vrule.rule)) {
            return { result: false, message: vrule.message };
        }
    }
    return { result: true, message: 'Проверка значения ячейки произведена' };
};

function validateCell(rowid, colid, value) {
    let props = getColumnProperties(colid);
    let cellrules = null;
    if (props !== null && typeof props.validation !== 'undefined') {
        cellrules = props.validation;
    }
    for (let grkey in validationrules) {
        let cellrule = overideRule(grkey, cellrules);
        let vrule = cellrule ? cellrule  : validationrules[grkey];
        if (!assertvrule(value, vrule.rule)) {
            return { result: false, message: vrule.message };
        }
    }
    return { result: true, message: 'Проверка значения ячейки произведена' };
}

function overideRule(rule, cellrules) {
    for (let key in cellrules) {
        if (key === rule) {
            return cellrules[key];
        }
    }
    return false;
}

function assertvrule(value, rule) {
    //console.log(eval(value + ' ' + rule));
    return rule === null ? true : eval(value + ' ' + rule);
}

// Пояснялки названий столбцов
let tooltiprenderer = function (element) {
    $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
};

let fillCalculatedFields = function () {
    $.get(calculatedcells_url + current_table, function( data ) {
        if (typeof data.errors !== 'undefined') {
            for (i = 0; data.errors.length > i; i++ ) {
                //raiseError(data.errors[i]);
                //console.log(data.errors[i]);
            }
        }
        dgrid.jqxGrid('updatebounddata');
        raiseInfo("Заполнение рассчитываемых ячеек выполнено");
        /*        if (typeof data.calculations !== 'undefined') {
                    for (i = 0; data.calculations.length > i; i++ ) {
                        let row_id = data.calculations[i].r;
                        let datafield = data.calculations[i].c;
                        let v = data.calculations[i].v;
                        dgrid.jqxGrid('setcellvaluebyid', row_id, datafield, v);
                    }
                }*/
    });
};
let initExcelUpload = function () {
    excelUploadWindow.jqxWindow({
        width: 500,
        height: 350,
        position: 'center',
        resizable: true,
        isModal: true,
        autoOpen: false,
        theme: theme
    });
    flUpload.on('uploadStart', function (event) {
        $("#UploadResult").html('');
        let only = onlyOneTable.prop('checked') ? '1' : '0';
        flUpload.jqxFileUpload({
            uploadUrl: excelupload_url + current_table + '/' + only,
        });
    });
    flUpload.on('uploadEnd', function (event) {
        let args = event.args;
        let fileName = args.file;
        let serverResponce = $.parseJSON(args.response);
        let m = '<div style="margin-top: 25px">';
        if (typeof serverResponce.error !== 'undefined') {
            m += "<p class='text-danger'> Ошибка загрузки данных. Проверьте формат файла.</p>";
        } else if (serverResponce.length === 0) {
            m += "<p class='text-danger'>В предоставленном файле не обнаружены данные подходящие для загрузки. Проверьте формат файла.</p>";
        } else {
            $("#ExcelUploadComment").hide();
            for (let tcode in serverResponce) {
                m += "<p class='text-info'>т. " + tcode +
                    ": Импортировано ячеек: " + serverResponce[tcode].saved + "," +
                    " Очищено ячеек: " + serverResponce[tcode].deleted + "," +
                    " Пропущено закрещенных ячеек: " + serverResponce[tcode].noteditable + "." +
                    "</p>";
            }
        }
        m += '</div>';
        $("#UploadResult").html(m);
        dgrid.jqxGrid('updatebounddata');
    });
    flUpload.jqxFileUpload({
        width: 470,
        uploadUrl: excelupload_url + current_table,
        multipleFilesUpload: false,
        fileInputName: 'fileToUpload'
    });

    flUpload.jqxFileUpload({
        localization: {
            browseButton: 'Выбрать файл',
            uploadButton: 'Загрузить',
            cancelButton: 'Отменить',
            uploadFileTooltip: 'загрузить',
            cancelFileTooltip: 'отменить'
        } });
};

let initLogTable = function() {
    let cellclassname = function (row, columnfield, value, rowdata) {
        if (!rowdata.stored) {
            return 'notSaved'
        }
        return '';
    };
    var source =
        {
            localdata: cellValueChangingLog,
            datafields:
                [
                    { name: 'table', type: 'number' },
                    { name: 'tablecode', type: 'string' },
                    { name: 'row', type: 'number' },
                    { name: 'rowcode', type: 'string' },
                    { name: 'column', type: 'number' },
                    { name: 'columncode', type: 'string' },
                    { name: 'message', type: 'string' },
                    { name: 'beginstore_at', type: 'number' },
                    { name: 'newvalue', type: 'number' },
                    { name: 'oldvalue', type: 'number' },
                    { name: 'stored', type: 'bool' }
                ],
            datatype: "array"
        };
    var dataAdapter = new $.jqx.dataAdapter(source);
    var columns = [
        { text: 'Таблица', dataField: 'tablecode', width: '15%' },
        { text: 'Строка', dataField: 'rowcode', width: '10%', cellclassname : cellclassname },
        { text: 'Графа', dataField: 'columncode', width: '10%', cellclassname : cellclassname },
        { text: 'СтЗ', dataField: 'oldvalue', width: 60, cellsalign: 'right', cellclassname : cellclassname },
        { text: 'НовЗ', dataField: 'newvalue', width: 60, cellsalign: 'right', cellclassname : cellclassname },
        { text: 'Сообщение', dataField: 'message', width: '43%', cellclassname : cellclassname },
    ];
    // create data grid.
    logTable.jqxGrid(
        {
            width: '100%',
            height: '95%',
            theme: theme,
            localization: getLocalization('ru'),
            source: dataAdapter,
            columns: columns
        });
    $("#refreshLogTable").click(function () {
        logTable.jqxGrid('updatebounddata', 'cells');
    });
    
};

let initCatchOnUnloadEvent = function () {
    $(window).bind('beforeunload', function(eventObject) {
        let unsaved = cellValueChangingLog.filter(cell => cell.stored === false);
        let returnValue = undefined;
        if (unsaved.length) {
            flushCellValueChangesCache('Изменения сохранены, можно покинуть страницу');
            returnValue = "Do you really want to close?";
        }
        eventObject.returnValue = returnValue;
        return returnValue;
    });
};

function checkDocumentOrSectionState() {

}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}