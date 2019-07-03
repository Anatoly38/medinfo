<script type="text/javascript">
let ou_name = "{{ preg_replace('/[\r\n\t]/', '', $current_unit->unit_name) }}";
let ou_code = "{{ $current_unit->unit_code }}";
let doc_id = '{{ $document->id }}';
let doc_type = '{{ $document->dtype }}';
let docstate_id = '{{ $document->state }}';
let doc_statelabel = '{{ $statelabel }}';
let form_name = '{{ $form->form_name }}';
let form_code = '{{ $form->form_code }}';
let default_album = '{{ $album->id }}';
let current_table = parseInt('{{ $laststate['currenttable']->id }}');
let current_table_code = '{{ $laststate['currenttable']->table_code }}';
let current_table_index = parseInt('{{ $laststate['currenttable']->table_index }}');
let autocalculateTotals = {{ $autocalculate_totals ? 'true' : 'false' }}; // автоматический расчет итоговых строк и граф
let max_table_index = '{!!  $maxtableindex !!}';
let formsections = {!! $formsections !!};
let splitter = $("#formEditLayout");
let fgrid = $("#FormTables"); // селектор для сетки с перечнем таблиц
let dgrid = $("#DataGrid"); // селектор для сетки с данными таблиц
let controltabs = $("#ControlTabs");
let tdropdown = $('#TableList');
let prevtable = $('#Previous');
let nexttable = $('#Following');
let filterinput = $("#SearchField");
let clearfilter = $("#ClearFilter");
let calculate = $("#Сalculate");
let fullscreen = $("#ToggleFullscreen");
let tcheck = $("#TableCheck");
let idtcheck = $("#IDTableCheck");
let iptcheck = $("#IPTableCheck");
let formcheck = $("#FormCheck");
let excelexport = $("#tableExcelExport");
let excelimport = $("#tableExcelImport");
let fsdropdown = $('#SectionsManager');
let excelUploadWindow = $('#uploadExcelFile');
let flUpload = $('#ExcelFileUpload');
let onlyOneTable = $('#onlyOneTable');
let tl = $("#TableDataLoader");
let localizednumber = new Intl.NumberFormat('ru-RU');
let edited_tables = [{!! implode(',', $editedtables) !!}];
//let not_editable_cells = {!! json_encode($noteditablecells) !!};
let not_editable_cells = {!! $noteditablecells !!};
let edit_permission = {{ $editpermission ? 'true' : 'false' }};
let control_disabled = {{ config('app.control_disabled') ? 'true' : 'false' }};
let datafields = {!!  $datafields !!};
let calculatedfields = {!!  $calcfields !!};
let rowprops = {!!  $rowprops !!};
let colprops = {!!  $colprops !!};
let validationrules = {!!  $validationrules !!};
//let data_for_table = $.parseJSON('{!!  $tableproperties !!}');
//let data_for_table = JSON.parse('{!!  $tableproperties !!}');
let data_for_table = {!!  $tableproperties !!};
let columns = {!!  $columns !!};
let columngroups = {!!  $columngroups !!};
let firstdatacolumn = '{{ $firstdatacolumn }}';
let there_is_calculated = calculatedfields.length > 0;
let current_row_name_datafield = columns[1].dataField;
let current_row_number_datafield = columns[2].dataField;
let editedcell_row = 0;
let editedcell_column = 0;
let editedcell_value = null;
let form_tables_data = {!! $tablelist !!};
let protocol_control_created = false;
let forcereload = 0; // При наличии загружается кэшированный протокол контроля
let invalidTables = [];
let editedCells = [];
let alertedCells = [];
let invalidCells = [];
let show_table_errors_only = true;
let marking_mode = 'control';
let current_edited_cell = {};
let current_protocol_source = [];
let source_url = "/datainput/fetchvalues/" + doc_id + "/" + default_album + "/";
let tableprops_url = "/datainput/fetchtableprops/" + default_album + "/";
let savevalue_url = "/datainput/savevalue/" + doc_id + "/";
//let validate_table_url = "/datainput/tablecontrol/" + doc_id + "/";
let validate_form_url = "/datainput/formcontrol/" + doc_id;
let informTableDataCheck = "/datainput/ifdcheck/table/" + doc_id + "/";
let interFormTableDataCheck = "/datainput/interformdcheck/table/" + doc_id + "/";
let interPeriodTableDataCheck = "/datainput/interperioddcheck/table/" + doc_id + "/";
let formdatacheck_url = "/datainput/dcheck/form/" + doc_id;
let medstat_control_url = "medstat_control_protocol.php?document=" + doc_id;
let valuechangelog_url = "/datainput/valuechangelog/" + doc_id;
let tableexport_url = "/datainput/tableexport/" + doc_id + "/";
let cell_layer_url = "/datainput/fetchcelllayers/" + doc_id + "/";
let calculatedcells_url = "/datainput/calculate/" + doc_id + "/";
let cons_protocol_url = "/datainput/fetchconsprotocol/" + doc_id + "/";
let blocksection_url = "/datainput/blocksection/" + doc_id + "/";
let excelupload_url = '/datainput/excelupload/' + doc_id + '/';
let msexport_url = '/medstat_table_export/' + doc_id + '/';

let formlabels =
    {
        compare: 1,
        dependency: 2,
        interannual: 3,
        iadiapazon: 4,
        multiplicity: 5,
        ipdiapazon: 19,
        section: 20
    };
let disabled_states = [{!! $disabled_states or '' !!}];
let initialViewport = $(window).height();
let topOffset1 = 155;
let topOffset2 = 105;
//let topOffset3 = 125;
//let topOffset4 = 105;
//initDgridSize();
//initSplitterSize();
//initProtSize();
//initCellProtSize();
onResizeEventLitener();
initdatasources();
inittoolbarbuttons();
inittablelist();
initSplitter();
initfilters();
initdatagrid();
init_fc_extarbuttons();
initextarbuttons();
initExcelUpload();
renderColumnFunctions();
@yield('initTableAggregateAction')
@yield('initTableConsolidateAction')
//firefullscreenevent();
</script>