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
/*let initProtSize = function () {
    $("#tableprotocol").height(initialViewport - topOffset3);
    $("#formprotocol").height(initialViewport - topOffset3);
    $("#CellAnalysisTable").height(initialViewport - topOffset4);
};
let initCellProtSize = function () {
    $("#cellprotocol").height(initialViewport - topOffset3);
};
*/
let onResizeEventLitener = function () {
    $(window).resize(function() {
        dgrid.jqxGrid({ height: $(window).height()-topOffset1 });
        $('#formEditLayout').jqxSplitter({ height: $(window).height()-topOffset2});
        //$("#tableprotocol").height($(window).height()-topOffset3);
        //$("#formprotocol").height($(window).height()-topOffset3);
        //$("#cellprotocol").height($(window).height()-topOffset3);
        //$("#CellAnalysisTable").height(initialViewport - topOffset4);
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
/*    $("#showallfcrule").jqxCheckBox({ theme: theme, checked: true });
    $("#showallfcrule").on('checked', function (event) {
        $(".rule-valid ").parent(".jqx-expander-header").hide().next().hide();
    });
    $("#showallfcrule").on('unchecked', function (event) {
        $(".rule-valid ").parent(".jqx-expander-header").show().next().show();
    });*/
    //$("#toggle_formcontrolscreen").jqxToggleButton({ theme: theme });
    //$("#toggle_formcontrolscreen").on('click', function () {
      //  var toggled = $("#toggle_formcontrolscreen").jqxToggleButton('toggled');
//        if (toggled) {
  //          $("#formprotocol").fullscreen();
    //    }
      //  else $.fullscreen.exit();
        //return false;
    //});
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
/*    $("#togglecontrolscreen").jqxToggleButton({ theme: theme });
    $("#togglecontrolscreen").on('click', function () {
        let toggled = $("#togglecontrolscreen").jqxToggleButton('toggled');
        if (toggled) {
            splitter.jqxSplitter({panels: [{ size: 100, collapsible: false }, { size: '50%'}]})
        } else {
            splitter.jqxSplitter({panels: [ { size: '60%', min: 100, collapsible: false }, {collapsed:true} ]});
        }
    });*/
    $('#printtableprotocol').jqxButton({ theme: theme });
    //$("#expandprotocolrow").jqxToggleButton({ theme: theme });
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
            //console.log(rule);
            if (!rule.no_rules) {
                $.each(rule.iterations, function (iteration_idx, iteration) {
                    //console.log(iteration.cells, column_id, row_id);
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
    let found = false;
    $.each(cells, function(cell_idx, cell) {
        //console.log(cell.column == column_id && cell.row == row_id);
        if (cell.column === column_id && cell.row === parseInt(row_id)) {
            found = true;
        }
    });
    return found;
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
        formcheck.attr('disabled', false);
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
    //("#formTables").jqxDataTable('render');
    //console.log(invalidTables);
    //console.log(index);
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
let getreadablecelladress = function(row, column) {
    let row_code = dgrid.jqxGrid('getcellvaluebyid', row, current_row_number_datafield);
    let column_index = dgrid.jqxGrid('getcolumnproperty', column, 'text');
    return { row: row_code, column: column_index};
};
// Возвращает данные для состава свода по медицинским организациям и движения по периодам
let fetchcelllayer = function(row, column) {
    let layer_container = $("<table class='table table-condensed table-striped table-bordered'></table>");
    let period_container = $("<table class='table table-condensed table-striped table-bordered'></table>");
    let fetch_url = cell_layer_url + row + '/' + column;
    $.getJSON( fetch_url, function( data ) {
        $.each(data.layers, function (i, layer) {
            let row = $("<tr class='rowdocument' id='"+ layer.doc_id +"'><td>" + layer.unit_code
                + "</td><td><a href='/datainput/formdashboard/" + layer.doc_id +"' target='_blank' title='Открыть для редактирования'>" + layer.unit_name + "</a>"
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
            //+ "<td><a href='/datainput/formdashboard/" + layer.unit_id + "' target='_blank' title='Открыть для редактирования'>" + layer.unit_name + "</a>"
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

function fetchDataForDataGrid(tableid) {
    $.get(tableprops_url + tableid, function (data) {
        datafields = $.parseJSON(data.datafields);
        calculatedfields = $.parseJSON(data.calcfields);
        data_for_table = $.parseJSON(data.tableproperties);
        columns = $.parseJSON(data.columns);
        columngroups = $.parseJSON(data.columngroups);
        firstdatacolumn = data.firstdatacolumn;
        there_is_calculated = calculatedfields.length > 0;
        there_is_calculated ? calculate.prop('disabled', false ) : calculate.attr('disabled', true );
        current_table = tableid;
        current_table_code = data_for_table.code;
        current_table_index = data_for_table.index;
        set_navigation_buttons_status(current_table_index);
        current_row_name_datafield = columns[1].dataField;
        current_row_number_datafield = columns[2].dataField;
        renderColumnFunctions();
        tablesource.datafields = datafields;
        tablesource.url = fetchvalues_url();
        //dataAdapter.dataBind();
        renderDgrid();
    });
}

function renderColumnFunctions() {
    $.each(columns, function(column, properties) {
        if (typeof properties.cellclassname !== 'undefined' && properties.cellclassname === 'cellclass') {
            properties.cellclassname = cellclass;
        }
        //if (typeof properties.createeditor !== 'undefined') {
        //  properties.createeditor =  eval(properties.createeditor);
        //}
        if (typeof properties.initeditor !== 'undefined') {
            properties.initeditor = eval(properties.initeditor);
        }
        if (typeof properties.validation !== 'undefined') {
            properties.validation = validation;
        }
        if (typeof properties.cellbeginedit !== 'undefined') {
            properties.cellbeginedit = cellbeginedit;
        }
        if (typeof properties.cellsrenderer !== 'undefined') {
            properties.cellsrenderer = cellsrenderer;
        }
    });
    $.each(columngroups, function(group, properties) {
        if (typeof properties.rendered !== 'undefined')
            properties.rendered = tooltiprenderer;
    });
}

function renderDgrid() {
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

let initdatagrid = function() {
    tablesource =
        {
            datatype: "json",
            datafields: datafields,
            autoBind: true,
            id: 'id',
            url: fetchvalues_url(),
            updaterow: function (rowid, rowdata) {
                //console.log(rowdata);
                if (checkIsNotEditable(rowid, editedcell_column)) {
                    return false;
                }

                let value = rowdata[editedcell_column];
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
                current_edited_cell.rowid = rowid;
                let data = "row=" + rowid + "&column=" + editedcell_column + "&value=" + value+ "&oldvalue=" + oldvalue;
                $.ajax({
                    dataType: 'json',
                    url: savevalue_url + current_table ,
                    //timeout: 1000,
                    data: data,
                    method: 'POST',
                    success: function (data, status, xhr) {
                        if (data.error === 401) {
                            raiseError("Данные не сохранены. Пользователь не авторизован!");
                        }
                        else if (data.error === 1001) {
                            raiseError(data.comment);
                            // возвращаем старое значение
                            //dgrid.jqxGrid('setcellvalue', rowBoundIndex, colid, oldvalue);
                        }
                        else {
                            if (data.cell_affected) {
                                editedCells.push({ t: current_table, r: rowdata.boundindex, c: editedcell_column});
                                if (protocol_control_created) {
                                    $(".inactual-protocol").show();
                                }
                                commit(true);
                            }
                        }
                    },
                    error: xhrErrorNotificationHandler
                });

            },
            root: null
        };
    dataAdapter = new $.jqx.dataAdapter(tablesource, {
        loadError: xhrErrorNotificationHandler
    });
    dgrid.jqxGrid(
        {
            width: '100%',
            height: initDgridSize(),
            //height: '100%',
            source: dataAdapter,
            localization: localize(),
            selectionmode: 'singlecell',
            theme: theme,
            editable: edit_permission,
            editmode: 'selectedcell',
            //editmode: 'click',
            clipboard: true,
            columnsresize: true,
            //showfilterrow: false,
            //showtoolbar: true,
            //rendertoolbar: rendertoolbar,
            filterable: false,
            columns: columns,
            columngroups: columngroups
        });
    dgrid.on('cellbeginedit', function (event)
    {
        editedcell_column = event.args.datafield;
        editedcell_value = event.args.value;
    });

    //dgrid.on('cellvaluechanged', simpleSaving);

    dgrid.on("bindingcomplete", function (event) {
        dgrid.jqxGrid('focus');
        dgrid.jqxGrid({ 'keyboardnavigation': true  });
        dgrid.jqxGrid('selectcell', 0, firstdatacolumn);
    });

    dgrid.on('cellselect', cellSelecting);
};

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
// Протокол контроля выделенной ячейки
function cellProtocolRender(row_id, column_id) {
    let cell_protocol_panel = $("#cellprotocol");
    cell_protocol_panel.html('');
    if (current_protocol_source.length > 0 && typeof current_protocol_source[current_table_code] === 'object') {
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
        calculate.attr('disabled', true );
    }
    calculate.click(fillCalculatedFields);
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

// проверяем ли находится ли данная ячейка в списке запрещенных к редактированию ячеек
let cellbeginedit = function (row, datafield, columntype, value) {
    let rowid = dgrid.jqxGrid('getrowid', row);
    if (checkIsNotEditable(rowid, datafield)) {
        return false;
    }
};

function checkIsNotEditable(rowid, colid) {
    let necell_count = not_editable_cells.length;
    for (let i = 0; i < necell_count; i++) {
        if (not_editable_cells[i].t == current_table && not_editable_cells[i].r == rowid && not_editable_cells[i].c == colid ) {
            return true;
        }
    }
    return false;
}

let defaultEditor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 0, digits: 12  });
};
let initDecimal1Editor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 1, digits: 12, decimalSeparator: ',' });
};
let initDecimal2Editor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 2, digits: 12, decimalSeparator: ',' });
};
let initDecimal3Editor = function (row, cellvalue, editor) {
    editor.jqxNumberInput({ decimalDigits: 3, digits: 12, decimalSeparator: ',' });
};
let cellsrenderer = function (row, column, value, defaulthtml, columnproperties) {
    if (!value) {
        return;
    }
    let formated = $(defaulthtml).html(localizednumber.format(value));
    return formated[0].outerHTML;
};
let cellclass = function (row, columnfield, value, rowdata) {
    let invalid_cell = '';
    let alerted_cell = '';
    let class_by_edited_row = '';
    let not_editable = '';
    for (let i = 0; i < not_editable_cells.length; i++) {
        if (not_editable_cells[i].t == current_table && not_editable_cells[i].r == rowdata.id && not_editable_cells[i].c == columnfield) {
            return 'jqx-grid-cell-pinned jqx-grid-cell-pinned-bootstrap not_editable';
        }
    }
    if (marking_mode === 'control') {
        $.each(invalidCells[current_table], function(key, value) {
            if (value.r == rowdata.id && value.c == columnfield) {
                invalid_cell = 'invalid';
            }
        });
        $.each(alertedCells[current_table], function(key, value) {
            if (value.r === rowdata.id && value.c === columnfield) {
                alerted_cell = 'alerted';
            }
        });
        for (let i = 0; i < editedCells.length; i++) {
            if (editedCells[i].t == current_table && editedCells[i].r == row && editedCells[i].c == columnfield ) {
                class_by_edited_row = "editedRow";
                invalid_cell = '';
                alerted_cell = '';
            }
        }
        return  alerted_cell + ' ' + invalid_cell +' ' + class_by_edited_row;
    }
    else if (marking_mode === 'compareperiods') {
        let class_compare = '';
        for (let i = 0; i < comparedCells.length; i++) {
            if (comparedCells[i].t == current_table && comparedCells[i].r == rowdata.id && comparedCells[i].c == columnfield ) {
                class_compare = comparedCells[i].degree;
            }
        }
        return class_compare + ' ' + not_editable;
    }
};
let validation = function(cell, value) {
    if (value < 0) {
        return { result: false, message: 'Допускаются только положительные значения' };
    }
    return true;
};
// Пояснялки названий столбцов
let tooltiprenderer = function (element) {
    $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
};

let fillCalculatedFields = function () {
    $.get(calculatedcells_url + current_table, function( data ) {
        if (typeof data.errors !== 'undefined') {
            for (i = 0; data.errors.length > i; i++ ) {
                //raiseError(data.errors[i]);
                console.log(data.errors[i]);
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