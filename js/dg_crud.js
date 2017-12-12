var g_debug
var g_keydown
var g_cfg_name
var g_project_name
var g_param_show = false;
var g_cfg = {ck_sql_alias: 0};
function init_app() {
    function get_url_cfg() {
        var app_url = parent.window.location.pathname.substr(0, parent.window.location.pathname.lastIndexOf('/'));
        var param = '?project=' + g_project_name + '&cfg=' + g_cfg_name + '&app=crud'
        var url = window.location.protocol + '//' + window.location.host + app_url + '/' + 'index.html' + param
        if (g_project_name === undefined) {
            return  null;
        } else {
            return url;
        }

    }

    $('#bt_lnk_cfg_ext').html(T('apri configurazione su link esterno'));
    $('#bt_lnk_cfg_info').html(T('mostra link configurazione'));
    $('#sb_lnk').menubutton({
        menu: '#mm_lnk',
        iconCls: 'fa fa-link fa-lg',
        plain: false,
    });
    $('#bt_lnk_cfg_ext').on('click', function () {
        var url = get_url_cfg();
        if (url != null) {
            var win = window.open(url, '_blank');
            win.focus();
        } else {
            $.messager.alert(T('configurazione'), T('non è stata salvata/caricata la configurazione'), 'warning');
        }
    });
    $('#bt_lnk_cfg_info').on('click', function () {
        var url = get_url_cfg();
        if (url != null) {
            $.messager.alert({
                title: T('configurazione'),
                msg: T('url configurazione:<br><br>' + url),
                icon: 'info',
                height: 250,
                width: 350,
            });
        } else {
            $.messager.alert(T('configurazione'), T('non è stata salvata/caricata la configurazione'), 'warning');
        }
    });

    $('#opt_import_field_model').html(T('Importa un campo del modello dal db'));
    $('#opt_set_width_form').html(T('Imposta larghezza campo sul form predefinita'));
    $('#tb_app_name').textbox({
        required: true,
        label: T("Nome app:"), //path app
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
        onChange: function (newValue, oldValue) {
            //var val = $('#tb_out').textbox('getValue') + newValue;
            //$('#tb_out').textbox('setValue', val);
        }
    });
    $('#tb_app_name').textbox('textbox').bind('keydown', function (e) {
        var $this = $(this);
        window.setTimeout(function () {
            //console.log($this.val() + '--' + $this.val().length);
            var add = $this.val();
            if (add.length == 1) {
                var val = $('#tb_app_folder').textbox('getValue');
                g_keydown = val
                add = val + add;
            } else {
                add = g_keydown + add;
            }
            $('#tb_app_folder').textbox('setValue', add);
        }, 0);
    });

    $('#tb_app_folder').textbox({
        value: g_param["percorso del codice generato"],
        required: true,
        label: T("Percorso app:"), //path app
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
    });
    $('#tb_table_name').combobox({
        url: 'api/list/table/db',
        valueField: 'TEXT',
        textField: 'TEXT',
        label: T("Nome tabella:"), //path app
        required: true,
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
    });
    $('#p_crud').panel({
        title: T("Altri Parametri"),
    });
    $('#p_crud').panel('header').click(function () {
        var hide = $($('#p_crud').panel('body')[0]).css('display') == 'none';
        (hide) ? $('#p_crud').panel('expand', true) : $('#p_crud').panel('collapse', true);
    });

    $('#p_base').panel({
        title: T("Parametri Principali"),
    });
    $('#p_base').panel('header').click(function () {
        var hide = $($('#p_base').panel('body')[0]).css('display') == 'none';
        (hide) ? $('#p_base').panel('expand', true) : $('#p_base').panel('collapse', true);
    });

    $('#bt_gencode').linkbutton({text: T('Genera il codice')});
    // expand and collapse on click

    $('#bt_gencode').on('click', function () {
        $.messager.confirm(T('attenzione'), T('Verrà generato il codice, confermi?'), function (r) {
            if (r) {
                if ($('#ff_crud').form('validate')) {
                    $.messager.progress({title: T('elaborazione'), msg: T('Generazione del codice in corso, attendere...')});
                    var param = read_cfg_from_input();

                    $.post('api/dg/crud/generate', param)
                            .done(function (data) {
                                $.messager.progress('close');
                                if (data.success) {
                                    $.messager.confirm(T('conferma'), T('E\' stata creata applicazione, vuoi eseguirla?'), function (r) {
                                        if (r) {
                                            var url = window.location.protocol + '//' + window.location.host + '/' + param.app_folder + '/index.html';
                                            parent.addTab(param.app_name, url, null);
                                        }
                                    });
                                } else {
                                    $.messager.alert(T('errore'), data.msg, 'error');
                                }
                            })
                            .fail(function () {
                                $.messager.progress('close');
                                $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                            });
                } else {
                    $.messager.alert(T('attenzione'), T('Valorizzare tutti i campi'), 'warning');
                }
            }
        });
    });


    $("#sb_model").switchbutton({
        checked: false,
        onText: T('si'), offText: T('no'),
        onChange: function (checked) {
            if (checked) {
                $('#div_model').show();
                load_dg_model();
            } else {
                $('#div_model').hide();
            }
            load_menu_opt();
        }
    });
    $("#sb_model_label").html(T("impostare un modello personalizzato per la tabella"));
    var dg_model_tb = ['-', {
            text: T('Aggiungi'),
            iconCls: 'icon-add',
            id: 'bt_add',
            handler: function (e) {
                $('#dg_model').edatagrid('addRow');
            }}, '-', {
            text: T('Conferma'),
            iconCls: 'icon-ok',
            handler: function () {
                $('#dg_model').edatagrid('saveRow');
            }}, '-', {
            text: T('Annulla'),
            iconCls: 'icon-undo',
            handler: function () {
                $('#dg_model').edatagrid('cancelRow');
            }}, '-', {
            text: T('Elimina'),
            iconCls: 'icon-remove',
            handler: function () {
                $('#dg_model').edatagrid('destroyRow');
            }}, '-', {
            text: T('Parametri nascosti'),
            iconCls: 'fa fa-eye-slash fa-lg fa-blue',
            toggle: true,
            handler: function () {
                show_par();
            }}, '-', {
            text: T('Importa dal db'),
            iconCls: 'icon-add',
            handler: function () {
                var table = $('#tb_table_name').combobox('getValue');
                if (table != "") {
                    $('#dg_model').datagrid('options').url = 'api/dg/model/read/db/' + table;
                    $('#dg_model').datagrid('reload');
                } else {
                    $.messager.alert(T('attenzione'), T('Impostare il nome della tabella'), 'warning');
                }
            }}, '-', {
            id: 'bt_model_opt',
        }];


    var data_pk_fk = [{
            text: T('Chiave Primaria'),
            id: 'PRIMARY_KEY'
        }, {
            text: T('Tabella Collegata'),
            id: 'FOREIGN_KEY',
        }, {
            text: T('Lista Valori'),
            id: 'LIST',
        }, {
            text: T('Nessuna'),
            id: null
        }];
    var data_type = [{text: 'textbox'}, {text: 'textarea', iconCls: 'icon-edit', }, {text: 'datebox', }, {text: 'numberbox'}, {text: 'combobox', iconCls: 'icon-edit'}, {text: 'combogrid', iconCls: 'icon-edit'}];
    function load_dg_model() {
        $('#dg_model').datagrid('removeFilterRule');
        $('#dg_model').datagrid('disableFilter');
        $('#dg_model').edatagrid({
            //url: url: 'api/dg/model/read/json',
            //updateUrl: 'api/xx',
            toolbar: dg_model_tb,
            fit: true,
            rownumbers: true,
            striped: true,
            singleSelect: true,
            checkOnSelect: false,
            selectOnCheck: false,
            fitColumns: true,
            dragSelection: true,
            destroyMsg: {
                norecord: {// when no record is selected
                    title: T('attenzione'),
                    msg: T('Nessun record selezionato'),
                },
                confirm: {// when select a row
                    title: T('conferma'),
                    msg: T('Sei sicuro che vuoi cancellare?')
                }
            },
            onLoadSuccess: function () {
                $(this).datagrid('enableDnd');
                load_menu_opt();
            },
            onEdit: function (index, row) {
            },
            frozenColumns: [[
                    {field: 'ck', checkbox: true},
                    {field: "COL", width: 150, title: BR(T('Nome Campo')), editor: "text"},
                    {field: "TITLE", width: 150, title: BR(T('Titolo Campo')), editor: "text"},
                    {field: "TYPE", width: 120, title: BR(T('Tipo Campo')), editor: {type: 'combobox', options: {
                                valueField: 'text',
                                textField: 'text',
                                editable: false,
                                panelWidth: 100,
                                data: data_type,
                                showItemIcon: true,
                                buttonText: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                                onClickButton: function () {
                                    var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TYPE'});
                                    var type = $(ed.target).combobox('getValue');
                                    open_opt_type(type, index);
                                },
                                onChange: function (newValue, oldValue) {
                                    if ((newValue != "numberbox") && (newValue != "combobox")) {
                                        var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                        var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CONSTRAINT_TYPE'});
                                        var type = $(ed.target).combobox('setValue', '');
                                        open_opt_fk(type, index);
                                    }
                                }
                            }}},
                    {field: "CONSTRAINT_TYPE", title: T('Vincoli Campo') + '<br>' + T('Origine Dati'), width: '160px', editor: {type: 'combobox'
                            , options: {
                                valueField: 'id',
                                textField: 'text',
                                editable: false,
                                panelWidth: 150,
                                data: data_pk_fk,
                                //showItemIcon: true,
                            }}
                        , formatter: function (value, row, index) {
                            var data = combo_get_text(value, data_pk_fk);
                            return data;
                        }
                    },
                ]],
            columns: [[
                    {field: "WIDTH", title: BR(T('Larghezza Campo')), editor: "text"},
                    {field: "WIDTH_FORM", title: T('Larghezza') + '<br>' + T('Campo Form'), editor: "text"},
                    {field: "WIDTH_LABEL", title: T('Larghezza') + '<br>' + T('Campo Etichetta'), editor: "text"},
                    {field: "SKIP", title: BR(T('Escludi Campo')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "HIDE_FORM", title: T('Campo Form') + '<br>' + T('Nascosto'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "HIDE", title: BR(T('Campo Nascosto')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "HIDE_INS", title: T('Nascondi') + '<br>' + T('su inserimento'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "CK", title: BR(T('Campo Si,No')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "READONLY", title: T('Campo') + '<br>' + T('sola lettura'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "EDIT", title: T('scrivi su') + '<br>' + T('database'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "REQUIRED", title: BR(T('Campo Richiesto')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "SORTABLE", title: BR(T('Campo Ordinabile')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "CK_FILTER", title: BR(T('Filtri<br>Avanzati')), editor: {type: 'combobox', options: {
                        valueField:'value',
                                textField:'text',
                                data: [{value: 0, text: '-'}, {value: 1, text: '√'}],
                                hasDownArrow: false,
                                panelHeight: 50,
                                buttonText: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                                buttonAlign: 'right',
                                editable: false,
                                onClickButton: function () {
                                    var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TYPE'});
                                    var type = $(ed.target).textbox('getValue');
                                    edit_filter(type, index);
                                }
                            }
                        }, formatter: mycheck, required: true},
                    {field: "EMPTY", title: ' ', },
                    {field: "NAME_TABLE_EXT", title: T('Nome') + '<br>' + T('Tabella Collegata'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "VALUE_FIELD", title: T('Campo ID ') + '<br>' + T('associato'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "TEXT_FIELD", title: T('Campo TEXT') + '<br>' + T('associato'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "CK_LIMIT2LIST", title: T('Limita Lista') + '<br> ' + 'combobox', editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "FIELDS", title: 'combogrid' + '<br> ' + T('lista campi'), width: 150, editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "N_ROW_TEXTAREA", title: T('N° righe') + '<br>' + T('textarea'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "LIST", title: 'combobox' + '<br> ' + T('Dati Locali'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "LIST_CAT", title: T('lista valori') + '<br> ' + T('campo categoria'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "LIST_ICON", title: T('lista valori') + '<br> ' + T('campo conCls'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "CK_SQL_COMBO", title: T('sql personalizzato') + '<br> ' + T('abilita'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "SQL_COMBO", title: T('sql personalizzato') + '<br> ' + T('stringa sql'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "CK_FILTER_LIKE", title: T('Filtro') + '<br> ' + T('Contiene'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "CK_FILTER_REQUIRED", title: T('Filtro') + '<br> ' + T('Richiesto'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "CK_FILTER_MULTIPLE", title: T('Filtro') + '<br> ' + T('Sel. Multipla'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "FILTER_DT_FIELD", title: T('Filtro') + '<br> ' + T('Associa Data'), editor: {type: 'textbox', options: {}}, hidden: true},
                ]],
        });
        $('#dg_model').datagrid('enableFilter');
        $('#dg_model').datagrid('enableFilter', [{
                field: 'SKIP',
                type: 'combobox',
                options: {
                    valueField: 'id',
                    textField: 'text',
                    editable: false,
                    data: [{id: '√', text: T('si')}, {id: '-', text: T('no')}],
                    panelHeight: 60,
                },
                op: ['equal']
            }]);
        $('#dg_model').datagrid('enableFilter', [{
                field: 'HIDE',
                type: 'combobox',
                options: {
                    valueField: 'id',
                    textField: 'text',
                    editable: false,
                    data: [{id: '√', text: T('si')}, {id: '-', text: T('no')}],
                    panelHeight: 60,
                },
                op: ['equal']
            }]);
        $('#dg_model').datagrid('enableFilter', [{
                field: 'CK',
                type: 'combobox',
                options: {
                    valueField: 'id',
                    textField: 'text',
                    editable: false,
                    data: [{id: '√', text: T('si')}, {id: '-', text: T('no')}],
                    panelHeight: 60,
                },
                op: ['equal']
            }]);
        $('#dg_model').datagrid('enableFilter', [{
                field: 'EDIT',
                type: 'combobox',
                options: {
                    valueField: 'id',
                    textField: 'text',
                    editable: false,
                    data: [{id: '√', text: T('si')}, {id: '-', text: T('no')}],
                    panelHeight: 60,
                },
                op: ['equal']
            }]);
        $('#dg_model').datagrid('enableFilter', [{
                field: 'REQUIRED',
                type: 'combobox',
                options: {
                    valueField: 'id',
                    textField: 'text',
                    editable: false,
                    data: [{id: '√', text: T('si')}, {id: '-', text: T('no')}],
                    panelHeight: 60,
                },
                op: ['equal']
            }]);
        $('#dg_model').datagrid('enableFilter', [{
                field: 'SORTABLE',
                type: 'combobox',
                options: {
                    valueField: 'id',
                    textField: 'text',
                    editable: false,
                    data: [{id: '√', text: T('si')}, {id: '-', text: T('no')}],
                    panelHeight: 60,
                },
                op: ['equal']
            }]);


    }
    function show_par() {
        g_param_show = !g_param_show;
        var field = ['N_ROW_TEXTAREA', 'TEXT_FIELD', 'FIELDS', 'VALUE_FIELD', 'NAME_TABLE_EXT', 'LIST', 'LIST_CAT', 'LIST_ICON', 'CK_SQL_COMBO', 'SQL_COMBO', 'CK_FILTER_REQUIRED', 'CK_FILTER_MULTIPLE'];
        for (var i = 0; i < field.length; i++) {
            (g_param_show) ? $('#dg_model').datagrid('showColumn', field[i]) : $('#dg_model').datagrid('hideColumn', field[i]);
        }
    }

    function combo_get_text(id, data) {
        for (var i = 0; i < data.length; i++) {
            if (data[i].id == id)
                return data[i].text;
        }

    }

    $('#nn_prefix').numberspinner({
        min: 1,
        precision: 0,
        spinAlign: 'horizontal',
        value: 1,
        required: true,
    });
    $('#nn_prefix_label').html(T('Prefisso numerico elemento es. #dg1, #dg2...'));
    $('#opt_test').on('click', function () {
        $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
    });

    $('#cc_crud').combobox({
        valueField: 'value',
        textField: 'text',
        label: T('abilita comandi su dati (C.R.U.D.)'),
        labelWidth: 250,
        width: 500,
        required: true,
        value: ['C', 'R', 'U', 'D'],
        //panelWidth: 300,
        multiple: true,
        editable: false,
        prompt: T('seleziona'),
        data: [{text: T('inserisci'), value: 'C'}, {text: T('leggi'), value: 'R'}, {text: T('aggiorna'), value: 'U'}, {text: T('elimina'), value: 'D'}],
    });

    function load_menu_opt() {
        $('#bt_model_opt').menubutton({
            menu: '#mm_opt',
            text: T('Altre opzioni'),
            iconCls: 'fa fa fa-bars fa-red fa-lg',
        });
    }

    $('#opt_import_field_model').on('click', function () {
        var table = $('#tb_table_name').combobox('getValue');
        if (table != "") {
            var dlg_msg = $.messager.prompt(T('modello'), T('Seleziona un campo del modello da importare'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var model = $('#cc_model_field').combobox('getData');
                    var field = $('#cc_model_field').combobox('getValue');

                    for (var i = 0; i < model.length; i++) {
                        if (model[i].COL == field) {
                            var row = model[i];
                            $('#dg_model').datagrid('appendRow', row);//add row select to model
                            $('#dg_model').datagrid('enableDnd');
                        }

                    }
                }

            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/dg/model/read/db/' + table,
                valueField: 'COL',
                textField: 'COL',
                required: true,
                panelWidth: 300,
                editable: false,
                prompt: T('seleziona'),
                onLoadSuccess: function () {
                    //$(this).combobox({prompt: T('seleziona')});
                },
                label: T('Campo:'),
                labelPosition: 'left',
                width: 240,
            }).attr('id', 'cc_model_field');
        } else {
            $.messager.alert(T('attenzione'), T('Impostare il nome della tabella'), 'warning');
        }
    });




    $('#opt_copy_multi').on('click', function () {

        var dlg_msg = $.messager.prompt(T('copia multipla'), T('Verranno copiati i valori della cella in colonna sulle righe selezionate:'), function (r) {
            if (r === undefined) {
                //console.log('press cancel');
            } else {

                var field = $('#cc_title').combobox('getValue');//number row
                var n_row = $('#nn_n_row').numberspinner('getValue') - 1;// field name
                var value = $('#cc_value').textbox('getValue');

                copy_value_model(field, value);
            }

        });


        dlg_msg.find('.messager-input').numberspinner({
            precision: 0,
            min: 1,
            spinAlign: 'horizontal',
            required: false,
            label: T('Riga:'),
            labelPosition: 'left',
            width: 180,
        }).attr('id', 'nn_n_row');
        var input_cel = '<div style="margin-top:5px"><input id="cc_title"><div style="margin-top:5px"><input id="cc_value">';
        dlg_msg.find('div').end().append(input_cel);
        $('#cc_title').combobox({
            data: get_titles_model(),
            mode: 'local',
            valueField: 'FIELD',
            textField: 'TITLE',
            required: true,
            panelWidth: 300,
            editable: false,
            prompt: T('seleziona'),
            label: T('Colonna:'),
            labelPosition: 'left',
            width: 240,
        });
        $('#cc_value').textbox({
            data: get_titles_model(),
            editable: true,
            required: true,
            prompt: T('valore da copiare'),
            label: T('Valore:'),
            labelPosition: 'left',
            width: 240,
            buttonText: T('leggi'),
            //iconCls: 'icon-reload',
            onClickButton: function (index) {
                var field = $('#cc_title').combobox('getValue');//number row
                var n_row = $('#nn_n_row').numberspinner('getValue') - 1;// field name
                var rows = $('#dg_model').datagrid('getRows');
                var value = rows[n_row][field];
                $('#cc_value').textbox('setValue', value);
            }
        });

    });
    $('#opt_copy_multi').html(T('Copia multipla valori di una cella'));

    function copy_value_model(field, value) {
        var rows = $('#dg_model').datagrid('getChecked');
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var index = $('#dg_model').datagrid('getRowIndex', row);
            $('#dg_model').datagrid('updateRow', {
                index: index,
                row: {
                    [field]: value
                }
            });
        }
    }
    function get_titles_model() {
        var titles = [];
        var cols = $('#dg_model').datagrid('getColumnFields');
        for (var i = 0; i < cols.length - 1; i++) {
            var col = cols[i];
            var title = $('#dg_model').datagrid('getColumnOption', col).title
            var title = title.replace("<br>", " ");
            titles.push({FIELD: col, TITLE: title});
        }
        return titles;
    }
    $("#sb_dg_inline_label").html(T("(si) modifica tabella sulle celle, (no) modifica su form"));
    $("#sb_dg_inline").switchbutton({
        checked: true,
        onText: T('si'), offText: T('no'),
        onChange: function (checked) {
            if (checked) {
                $('#div_dg_inline').hide();
                if ($('#dg_model').datagrid('getRows').length > 0) {
                    $('#dg_model').datagrid('hideColumn', 'WIDTH_FORM');
                    $('#dg_model').datagrid('hideColumn', 'WIDTH_LABEL');
                    $('#dg_model').datagrid('hideColumn', 'HIDE_FORM');
                }
            } else {
                $('#div_dg_inline').show();
                if ($('#dg_model').datagrid('getRows').length > 0) {
                    $('#dg_model').datagrid('showColumn', 'WIDTH_FORM');
                    $('#dg_model').datagrid('showColumn', 'WIDTH_LABEL');
                    $('#dg_model').datagrid('showColumn', 'HIDE_FORM');
                }
            }
        }

    });

    $("#sb_pagination_label").html(T("Paginazione Tabella"));
    $("#sb_pagination").switchbutton({
        checked: false,
        onText: T('si'), offText: T('no'),
        onChange: function (checked) {
            if (checked) {
                $('#div_pagination').show();
            } else {
                $('#div_pagination').hide();
            }
            load_menu_opt();
        }
    });
    $('#cc_pagination_list').combobox({
        label: T('lista righe per pagina impostabili'),
        labelPosition: 'top',
        multiple: true,
        valueField: 'val',
        textField: 'val',
        value: '25,50,100',
        editable: false,
        data: [{val: 25}, {val: 50}, {val: 100}, {val: 150}, {val: 250}, {val: 500}, {val: 1000}]
    });
    $('#cc_pagination_size').combobox({
        label: T('righe per pagina predefinito'),
        labelPosition: 'top',
        value: 50,
        valueField: 'val',
        textField: 'val',
        editable: false,
        data: [{val: 25}, {val: 50}, {val: 100}, {val: 150}, {val: 250}, {val: 500}, {val: 1000}]
    });
    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    $("#sb_filter_base").switchbutton({
        checked: true,
        onText: T('si'), offText: T('no'),
    });
    $("#sb_filter_base_label").html(T("Filtro semplice per colonna"));

    $("#sb_custom_sql_label").html(T("personalizza SQL per la SELECT"));
    $("#sb_custom_sql").switchbutton({
        checked: false,
        onText: T('si'), offText: T('no'),
        onChange: function (checked) {
            if (checked) {
                $('#div_custom_sql').show();
                $('#tb_custom_sql').textbox({
                    multiline: true,
                    buttonText: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                    buttonAlign: 'left',
                    onClickButton: function () {
                        var dlg_msg = $.messager.prompt({
                            id: 'dlg_sql',
                            title: T('stringa sql'),
                            msg: T('impostare la stringa sql'),
                            incon: 'info',
                            width: '60%',
                            height: '520px',
                            maximizable: true,
                            resizable: true,
                            fn: function () {
                                var sql = $('#tb_sql').textbox('getValue');
                                $('#tb_custom_sql').textbox('setValue', sql);
                            }
                        });



                        dlg_msg.find('.messager-input').remove();
                        var txt_label2 = T('inserisci alias A sui campi');
                        var input_cel = '<div style="margin-top:5px"><a id="bt_imp_sql"></a></div>\n\
                                         <div style="margin-top:5px"><input id="sb_sql_alias"><label style="margin-left:5px">' + txt_label2 + '</label></div>\n\
                                         <div style="margin-top:5px"><input id="tb_sql"></div>';
                        dlg_msg.find('div').end().append(input_cel);

                        $("#sb_sql_alias").switchbutton({
                            checked: true,
                            onText: T('si'), offText: T('no'),
                            checked: (g_cfg.ck_sql_alias == 1) ? true : false,
                            onChange: function (checked) {
                                g_cfg.ck_sql_alias = (checked) ? 1 : 0;
                            },
                        });

                        $('#tb_sql').textbox({
                            label: T('sql'),
                            value: $('#tb_custom_sql').textbox('getValue'),
                            prompt: T('inserisci qui'),
                            labelPosition: 'top',
                            width: '98%',
                            height: '350px',
                            multiline: true,
                            required: true,
                        });
                        $('#bt_imp_sql').linkbutton({
                            iconCls: 'fa fa-database fa-lg',
                            text: T('genera stringa sql'),
                            //plain: true,
                            onClick: function () {
                                $.messager.progress({title: T('recupero sql'), msg: T('lettura dati, in corso...')});
                                var param = read_cfg_from_input();
                                $.post('api/get/sql/crud', param)
                                        .done(function (data) {
                                            $.messager.progress('close');
                                            if (data.success) {
                                                $('#tb_sql').textbox('setValue', data.sql);
                                            } else {
                                                $.messager.alert(T('errore'), data.msg, 'error');
                                            }
                                        })
                                        .fail(function () {
                                            $.messager.progress('close');
                                            $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                                        });
                            }
                        });
                    },
                });
            } else {
                $('#div_custom_sql').hide();
            }
        }
    });

    $("#sb_row_styler_label").html(T("colora riga con condizioni"));
    $("#sb_row_styler").switchbutton({
        checked: false,
        onText: T('si'), offText: T('no'),
        onChange: function (checked) {
            if (checked) {
                $('#div_row_styler').show();
                $('#tb_row_styler').textbox({
                    multiline: true,
                    buttonText: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                    buttonAlign: 'left',
                    onClickButton: function () {
                        var dlg_msg = $.messager.prompt({
                            id: 'dlg_row_styler',
                            title: T('stile riga'),
                            msg: T('impostare la condizione'),
                            incon: 'info',
                            width: '60%',
                            height: '520px',
                            maximizable: true,
                            resizable: true,
                            fn: function () {
                                var code = $('#tb_row_styler2').textbox('getValue');
                                $('#tb_row_styler').textbox('setValue', code);
                            }
                        });
                        dlg_msg.find('.messager-input').remove();
                        var input_cel = '\n\
                                        <div style="margin-top:5px">\n\
                                            <input id="cc_col_color">\n\
                                            <div style="margin-top:5px">\n\
                                                <input id="tb_condition_color">\n\
                                                <input id="tb_condition_color_val">\n\
                                            </div>\n\
                                            <div style="margin-top:5px">\n\
                                                <input id="tb_color_bg">\n\
                                                <input id="tb_color">\n\
                                            </div>\n\
                                        </div>\n\
                                        <div style="margin-top:5px"><a id="bt_row_styler_add"></a></div>\n\
                                        <div style="margin-top:5px"><input id="tb_row_styler2"></div>\n\
                                        ';
                        dlg_msg.find('div').end().append(input_cel);

                        $('#tb_color_bg').color({
                            label: T('colore sfondo'),
                            width: '220px',
                            required: true,
                        });
                        $('#tb_color').color({
                            label: T('colore testo'),
                            width: '220px',
                            required: true,
                        });
                        $('#tb_condition_color').combobox({
                            label: T('condizione'),
                            width: '220px',
                            textField: 'text',
                            valueField: 'value',
                            data: [
                                {text: T('uguale'), value: '=='},
                                {text: T('maggiore'), value: '>'},
                                {text: T('maggiore uguale'), value: '>='},
                                {text: T('minore'), value: '<'},
                                {text: T('minore uguale'), value: '<='},
                                {text: T('diverso'), value: '!='},
                            ],
                            required: true,
                        });
                        $('#tb_condition_color_val').textbox({
                            label: T('valore'),
                            width: '220px',
                            required: true,
                        });

                        $('#cc_col_color').combobox({
                            url: 'api/dg/model/read/db/' + $('#tb_table_name').textbox('getValue'),
                            textField: 'COL',
                            valueField: 'COL',
                            label: T('colonna'),
                            width: '220px',
                            required: true,
                        });

                        $('#tb_row_styler2').textbox({
                            label: T('codice') + ' javascript: rowStyler: function(index,row){ ..... }',
                            value: $('#tb_row_styler').textbox('getValue'),
                            prompt: T('inserisci qui'),
                            labelPosition: 'top',
                            width: '98%',
                            height: '250px',
                            multiline: true,
                            required: true,
                        });

                        $('#bt_row_styler_add').linkbutton({
                            iconCls: 'fa fa-plus-circle fa-lg',
                            text: T('aggiungi'),
                            //plain: true,
                            onClick: function () {
                                var col = $('#cc_col_color').combobox('getValue');
                                var condition = $('#tb_condition_color').combobox('getValue');
                                var condition_val = $('#tb_condition_color_val').textbox('getValue');
                                var color_bg = $('#tb_color_bg').combobox('getValue');
                                var color = $('#tb_color').combobox('getValue');

                                var code_old = $('#tb_row_styler2').textbox('getValue');
                                var code = '\n\
if (row.' + col + condition + condition_val + '){\n\
return \'background-color:' + color_bg + '; color:' + color + '\';\n\
}\n\
                                            '
                                        ;
                                code = code_old + code;
                                $('#tb_row_styler2').textbox('setValue', code);
                            }
                        });


                    },
                });
            } else {
                $('#div_row_styler').hide();
            }
        }
    });

    $("#sb_global_var_label").html(T("imposta variabili gobali per API PHP"));
    $("#sb_global_var").switchbutton({
        checked: false,
        onText: T('si'), offText: T('no'),
        onChange: function (checked) {
            if (checked) {
                $('#div_global_var').show();
                $('#tb_global_var').textbox({
                    multiline: true,
                    buttonText: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                    buttonAlign: 'left',
                    onClickButton: function () {
                        var dlg_msg = $.messager.prompt({
                            id: 'dlg_sql',
                            title: T('variabili globali'),
                            msg: T('impostare le variabili globali'),
                            incon: 'info',
                            width: '60%',
                            height: '520px',
                            maximizable: true,
                            resizable: true,
                            fn: function () {
                                var global_var = $('#tb_global_var_lg').textbox('getValue');
                                $('#tb_global_var').textbox('setValue', global_var);
                            }
                        });
                        dlg_msg.find('.messager-input').remove();
                        var input_cel = '<div style="margin-top:5px"><input id="tb_global_var_lg"></div>';
                        dlg_msg.find('div').end().append(input_cel);

                        $('#tb_global_var_lg').textbox({
                            //label: T(''),
                            value: $('#tb_global_var').textbox('getValue'),
                            prompt: T('inserisci qui'),
                            labelPosition: 'top',
                            width: '98%',
                            height: '350px',
                            multiline: true,
                            required: true,
                        });
                    },
                });
            } else {
                $('#div_global_var').hide();
            }
        }
    });

    $('#cc_group_col').combobox({
        //url: 'api/dg/model/read/db/' + $('#tb_table_name').textbox('getValue'),
        width: 300,
        label: T('raggruppa per colonna'),
        labelPosition: 'right',
        labelWidth: 150,
        valueField: 'COL',
        textField: 'COL',
        buttonIcon: 'icon-reload',
        buttonAlign: 'left',
        onClickButton: function () {
            $(this).combobox({url: 'api/dg/model/read/db/' + $('#tb_table_name').textbox('getValue')});
        },
    });


    $('#bt_save_cfg').linkbutton({
        text: T('salva configurazione'),
        onClick: function () {
            var dlg_msg = $.messager.prompt(T('salva configurazione'), T('Verranno salvati tutti i parametri impostati'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var cfg_name = $('#cfg_name').combobox('getValue');
                    var project_name = $('#project_name').combobox('getValue');
                    if ((cfg_name != "") && (project_name != "")) {
                        var cfg = read_cfg_from_input();
                        $.post('api/crud/save/cfg2json', {cfg: cfg, cfg_name: cfg_name, project_name: project_name})
                                .done(function (data) {
                                    $.messager.progress('close');
                                    if (data.success) {
                                        g_cfg_name = cfg_name;
                                        g_project_name = project_name;
                                        set_name_cfg(g_cfg_name, g_project_name);
                                        $.messager.show({
                                            title: T('salvataggio'),
                                            msg: data.msg,
                                            showType: 'slide'
                                        });
                                    } else {
                                        $.messager.alert(T('errore'), data.msg, 'error');
                                    }
                                })
                                .fail(function () {
                                    $.messager.progress('close');
                                    $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                                });
                    } else {
                        $.messager.alert(T('attenzione'), T('valorizzare tutti i campi'), 'warning');
                    }
                }

            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/list/project',
                valueField: 'folder',
                textField: 'folder',
                label: T('Nome Progetto'),
                prompt: T('digita qui'),
                value: g_project_name,
                labelPosition: 'top',
                width: '95%',
                required: true,
                onSelect: function (rec) {
                    $('#cfg_name').combobox({url: 'api/list/all/cfg/' + rec.folder});
                }
            }).attr('id', 'project_name');
            var input_cel = '<div style="margin-top:5px"><input id="cfg_name">';
            dlg_msg.find('div').end().append(input_cel);
            $('#cfg_name').combobox({
                //url: 'api/list/all/cfg',
                valueField: 'file',
                textField: 'file',
                //hasDownArrow: false,
                label: T('Nome Configurazione'),
                prompt: T('digita qui'),
                value: g_cfg_name,
                labelPosition: 'top',
                width: '95%',
                required: true,
            });
        }
    });

    function open_cfg(project_name, cfg_name) {
        $.messager.progress({title: T('configurazione'), msg: T('lettura configurazione, attendere...')});
        $.post('api/crud/open/cfg/json', {cfg_name: cfg_name, project_name: project_name})
                .done(function (data) {
                    $.messager.progress('close');
                    if (data.success) {
                        g_cfg_name = cfg_name;
                        g_project_name = project_name;
                        set_name_cfg(g_cfg_name, g_project_name);
                        read_cfg_from_json(data.cfg);
                        $.messager.show({
                            title: T('configurazione'),
                            msg: data.msg,
                            showType: 'slide'
                        });
                    } else {
                        $.messager.alert(T('errore'), data.msg, 'error');
                    }
                })
                .fail(function () {
                    $.messager.progress('close');
                    $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                });
    }

    $('#bt_open_cfg').linkbutton({
        text: T('apri configurazione'),
        onClick: function () {
            var dlg_msg = $.messager.prompt(T('apri configurazione'), T('Verranno letti tutti i parametri dalla configurazione selezionata'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var cfg_name = $('#cfg_name').combobox('getValue');
                    var project_name = $('#project_name').combobox('getValue');
                    open_cfg(project_name, cfg_name);

                }

            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/list/project',
                valueField: 'folder',
                textField: 'folder',
                label: T('Nome Progetto'),
                prompt: T('seleziona'),
                value: g_project_name,
                labelPosition: 'top',
                width: '95%',
                required: true,
                onSelect: function (rec) {
                    $('#cfg_name').combobox({url: 'api/list/all/cfg/' + rec.folder})
                }
            }).attr('id', 'project_name');
            var input_cel = '<div style="margin-top:5px"><input id="cfg_name">';
            dlg_msg.find('div').end().append(input_cel);
            $('#cfg_name').combobox({
                //url: 'api/list/all/cfg',
                valueField: 'file',
                textField: 'file',
                label: T('Nome Configurazione'),
                prompt: T('seleziona'),
                value: g_cfg_name,
                labelPosition: 'top',
                width: '95%',
                required: true,
            });
        }
    })

    /** read configuration from input
     *
     * @returns {init_app.read_cfg.cfg}
     */
    function read_cfg_from_input() {
        $('#dg_model').datagrid('removeFilterRule');
        $('#dg_model').datagrid('disableFilter');
        var model = $('#dg_model').datagrid('getRows');
        $('#dg_model').datagrid('enableFilter');

        var app_name = $('#tb_app_name').textbox('getValue');
        var app_folder = $('#tb_app_folder').textbox('getValue');
        var table_name = $('#tb_table_name').combobox('getValue');
        var model_from_json = ($("#sb_model").switchbutton('options').checked) ? 1 : 0;
        var html_prefix = $('#nn_prefix').numberspinner('getValue');
        var crud = $('#cc_crud').combobox('getValues');


        var dg_inline = ($("#sb_dg_inline").switchbutton('options').checked) ? 1 : 0;
        var pagination = ($("#sb_pagination").switchbutton('options').checked) ? 1 : 0;
        var pagination_list = $('#cc_pagination_list').combobox('getValues');
        pagination_list = JSON.stringify(pagination_list).replaceAll('\"', '')
        var pagination_size = $('#cc_pagination_size').combobox('getValue');
        var width_form = $('#tb_width_form').textbox('getValue');
        var height_form = $('#tb_height_form').textbox('getValue');
        var form_full = ($("#sb_form_full").switchbutton('options').checked) ? 1 : 0;
        var row_num = ($("#sb_row_num").switchbutton('options').checked) ? 1 : 0;
        var filter_base = ($("#sb_filter_base").switchbutton('options').checked) ? 1 : 0;
        var ck_custom_sql = ($("#sb_custom_sql").switchbutton('options').checked) ? 1 : 0;
        var custom_sql = $('#tb_custom_sql').textbox('getValue');
        var ck_global_var = ($("#sb_global_var").switchbutton('options').checked) ? 1 : 0;
        var global_var = $('#tb_global_var').textbox('getValue');
        var ck_row_styler = ($("#sb_row_styler").switchbutton('options').checked) ? 1 : 0;
        var row_styler = $('#tb_row_styler').textbox('getValue');
        var group_col = $('#cc_group_col').combobox('getText');
        var ck_sql_alias = g_cfg.ck_sql_alias;


        var cfg = {
            type_cfg: 'crud',
            app_name: app_name,
            app_folder: app_folder,
            table_name: table_name,
            model_from_json: model_from_json,
            html_prefix: html_prefix,
            crud: crud,
            dg_inline: dg_inline,
            pagination: pagination,
            pagination_list: pagination_list,
            pagination_size: pagination_size,
            model: model,
            width_form: width_form,
            height_form: height_form,
            form_full: form_full,
            row_num: row_num,
            filter_base: filter_base,
            ck_custom_sql: ck_custom_sql,
            custom_sql: custom_sql,
            ck_global_var: ck_global_var,
            global_var: global_var,
            ck_row_styler: ck_row_styler,
            row_styler: row_styler,
            group_col: group_col,
            ck_sql_alias: ck_sql_alias,
        };
        return cfg;
    }

    /** read configuration from json
     * @param {type} cfg json configuration
     * @returns {undefined}
     */
    function read_cfg_from_json(cfg) {
        if (cfg.model_from_json == 1) {
            $("#sb_model").switchbutton('check');
        } else {
            $("#sb_model").switchbutton('uncheck');
        }

        if (cfg.model === undefined) {
            cfg.model = [];
        }
        $('#dg_model').datagrid('loadData', cfg.model);

        $('#tb_app_name').textbox('setValue', cfg.app_name);
        $('#tb_app_folder').textbox('setValue', cfg.app_folder);
        $('#tb_table_name').combobox('setValue', cfg.table_name);

        $('#nn_prefix').numberspinner('setValue', cfg.html_prefix);
        (cfg.crud) ? $('#cc_crud').combobox('setValue', cfg.crud) : $('#cc_crud').combobox('setValue', ['C', 'R', 'U', 'D']);

        if (cfg.dg_inline == 1) {
            $("#sb_dg_inline").switchbutton('check');
        } else {
            $("#sb_dg_inline").switchbutton('uncheck');
        }

        if (cfg.pagination == 1) {
            $("#sb_pagination").switchbutton('check');
        } else {
            $("#sb_pagination").switchbutton('uncheck');
        }

        $('#cc_pagination_list').combobox('setValue', JSON.parse(cfg.pagination_list));
        $('#cc_pagination_size').combobox('setValue', cfg.pagination_size);
        $('#tb_width_form').textbox('setValue', cfg.width_form);
        $('#tb_height_form').textbox('setValue', cfg.height_form);
        if (cfg.form_full == 1) {
            $("#sb_form_full").switchbutton('check');
        } else {
            $("#sb_form_full").switchbutton('uncheck');
        }

        (cfg.row_num == 1) ? $("#sb_row_num").switchbutton('check') : $("#sb_row_num").switchbutton('uncheck');

        (cfg.filter_base == 1) ? $("#sb_filter_base").switchbutton('check') : $("#sb_filter_base").switchbutton('uncheck');

        (cfg.ck_custom_sql == 1) ? $("#sb_custom_sql").switchbutton('check') : $("#sb_custom_sql").switchbutton('uncheck');
        $('#tb_custom_sql').textbox('setValue', cfg.custom_sql);

        (cfg.ck_global_var == 1) ? $("#sb_global_var").switchbutton('check') : $("#sb_global_var").switchbutton('uncheck');
        $('#tb_global_var').textbox('setValue', cfg.global_var);

        (cfg.ck_row_styler == 1) ? $("#sb_row_styler").switchbutton('check') : $("#sb_row_styler").switchbutton('uncheck');
        $('#tb_row_styler').textbox('setValue', cfg.row_styler);
        $('#cc_group_col').combobox('setValue', cfg.group_col);
        g_cfg.ck_sql_alias = cfg.ck_sql_alias;

    }
    function set_name_cfg(cfg_name, project_name) {
        $('#p_base').panel({
            title: T("Parametri Principali") + " - " + "[" + T('progetto:') + project_name + ' - ' + T("configurazione:") + cfg_name + "]",
        });
    }
    $('#tb_width_form').textbox({
        label: T('Larghezza Form'),
        labelWidth: 120,
        width: 200,
        value: '60%',
        required: true,
        icons: [{
                iconCls: 'icon-reload',
                handler: function (e) {
                    $(e.data.target).textbox('reset');
                }
            }]
    });
    $('#tb_height_form').textbox({
        label: T('Altezza Form'),
        labelWidth: 120,
        width: 200,
        value: '80%',
        required: true,
        icons: [{
                iconCls: 'icon-reload',
                handler: function (e) {
                    $(e.data.target).textbox('reset');
                }
            }]
    });
    $("#sb_form_full_label").html(T("apri il form a schermo pieno"));
    $("#sb_form_full").switchbutton({
        checked: false,
        onText: T('si'), offText: T('no'),
    });
    $("#sb_row_num_label").html(T("mostra numerazione righe"));
    $("#sb_row_num").switchbutton({
        checked: true,
        onText: T('si'), offText: T('no'),
    });

    function open_opt_type(type, index) {
        if (type == "combobox") {
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CONSTRAINT_TYPE'});
            var fk = $(ed.target).combobox('getValue');
            if (fk != "") {
                open_opt_fk(fk, index);
            } else {
                var data = [{text: T('Tabella Collegata'), value: 'FOREIGN_KEY'}, {text: T('Lista Valori'), value: 'LIST'}];
                select_fk_type(index, data); // if not set origin data (external table, lista value) show chose
            }
        }
        if (type == "combogrid") {
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CONSTRAINT_TYPE'});
            var fk = $(ed.target).combobox('getValue');
            if (fk != "") {
                open_opt_fk(fk, index);
            } else {
                var data = [{text: T('Tabella Collegata'), value: 'FOREIGN_KEY'}, ];
                select_fk_type(index, data); // if not set origin data (external table, lista value) show chose
            }
        }
        if (type == "textarea") {
            var dlg_msg = $.messager.prompt(T('textarea'), T('Impostare il numero di righe della textarea'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var new_val = $('#nn_n_row').numberspinner('getValue');
                    $(ed.target).textbox('setValue', new_val);
                    //$('#dg_model').edatagrid('saveRow');
                }
            });
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'N_ROW_TEXTAREA'});
            var current_val = $(ed.target).textbox('getValue');
            dlg_msg.find('.messager-input').numberspinner({
                precision: 0,
                min: 2,
                value: current_val,
                spinAlign: 'horizontal',
                required: false,
                label: T('righe:'),
                labelPosition: 'left',
                width: 180,
            }).attr('id', 'nn_n_row');
        }
    }
    function open_opt_fk(type_fk, index) {
        var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TYPE'});
        var type = $(ed.target).textbox('getValue');

        if ((type_fk == "FOREIGN_KEY") && (type == "combobox")) {

            var dlg_msg = $.messager.prompt({
                id: 'dlg_combobox',
                title: T('tabella collegata'),
                msg: T('Impostare la tabella esterna da collegare, e i campi da associare'),
                incon: 'info',
                width: '60%',
                height: '520px',
                maximizable: true,
                resizable: true,
                fn: function () {
                    var new_val = $('#cc_table').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
                    $(ed.target).textbox('setValue', new_val);

                    var new_val_id = $('#cc_id').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
                    $(ed.target).textbox('setValue', new_val_id);

                    var new_val_text = $('#cc_text').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
                    $(ed.target).textbox('setValue', new_val_text);

                    var ck_limit2list = $("#sb_limit2list").switchbutton('options').checked
                    ck_limit2list = (ck_limit2list) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_LIMIT2LIST'});
                    $(ed.target).textbox('setValue', ck_limit2list);

                    var ck_sql_combo = $("#sb_custom_sql_combo").switchbutton('options').checked
                    ck_sql_combo = (ck_sql_combo) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_SQL_COMBO'});
                    $(ed.target).textbox('setValue', ck_sql_combo);

                    var sql_combo = $('#tb_sql_combo').textbox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'SQL_COMBO'});
                    $(ed.target).textbox('setValue', sql_combo);

                }
            });

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
            var current_val = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
            var current_val_id = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
            var current_val_text = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_LIMIT2LIST'});
            var current_limit2list = ($(ed.target).textbox('getValue') == "") ? 1 : $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_SQL_COMBO'});
            var current_val_ck_sql_combo = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'SQL_COMBO'});
            var current_val_sql_combo = $(ed.target).textbox('getValue');



            var txt_label = T('imposta un sql personalizzato');
            var txt_label2 = T('limita inserimento dei valori su "combo" a solo quelli presenti');
            var input_cel = '\
                <div style="margin-top:5px"><input id="cc_id"></div>\n\
                <div style="margin-top:5px"><input id="cc_text"></div>\n\
                <div style="margin-top:5px"><input id="sb_limit2list"><label style="margin-left:5px">' + txt_label2 + '</label></div>\n\
                <div style="margin-top:5px"><input id="sb_custom_sql_combo"><label style="margin-left:5px">' + txt_label + '</label></div>\n\
                <div id="div_sql_combo" display:none;width:100%>\n\
                    <div style="margin-top:5px"><a id="bt_imp_sql_combo"></a></div>\n\
                    <div style="margin-top:5px;width:97%"><input id="tb_sql_combo"></div>\n\
                </div>\n\
                \n\
                ';
            dlg_msg.find('div').end().append(input_cel);
            $('#cc_id').combobox({
                width: '390px',
                label: T('Campo valore (ID)'),
                value: current_val_id,
                labelWidth: '180px',
                valueField: 'COL',
                textField: 'COL',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: true,
            });
            $('#cc_text').combobox({
                width: '390px',
                label: T('Campo descrizione'),
                value: current_val_text,
                labelWidth: '180px',
                valueField: 'COL',
                textField: 'COL',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: true,
            });
            $("#sb_limit2list").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_limit2list == 1) ? true : false,
            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/list/table/db',
                width: '390px',
                label: T('Nome tabella collegata'),
                value: current_val,
                labelWidth: '180px',
                valueField: 'TEXT',
                textField: 'TEXT',
                method: 'post',
                required: true,
                panelWidth: 250,
                //editable: false,
                onSelect(record) {
                    $('#cc_id').combobox({url: 'api/dg/model/read/db/' + record.TEXT});
                    $('#cc_text').combobox({url: 'api/dg/model/read/db/' + record.TEXT});
                },
            }).attr('id', 'cc_table');

            $("#sb_custom_sql_combo").switchbutton({
                checked: false,
                onText: T('si'), offText: T('no'),
                checked: (current_val_ck_sql_combo == 1) ? true : false,
                onChange: function (checked) {
                    if (checked) {
                        $('#div_sql_combo').show();
                    } else {
                        $('#div_sql_combo').hide();
                    }
                    $('#tb_sql_combo').textbox({width: '97%', });
                }
            });
            (current_val_ck_sql_combo == 1) ? $('#div_sql_combo').show() : $('#div_sql_combo').hide();


            $('#tb_sql_combo').textbox({
                //label: T('sql'),
                value: current_val_sql_combo,
                prompt: T('inserisci qui'),
                labelPosition: 'top',
                width: '97%',
                height: '210px',
                multiline: true,
                required: true,
            });

            $('#bt_imp_sql_combo').linkbutton({
                iconCls: 'fa fa-database fa-lg',
                text: T('genera stringa sql'),
                //plain: true,
                onClick: function () {
                    $.messager.progress({title: T('recupero sql'), msg: T('lettura dati, in corso...')});
                    var table_name = $('#cc_table').textbox('getValue');
                    $.post('api/get/sql/combo', {table_name: table_name})
                            .done(function (data) {
                                $.messager.progress('close');
                                if (data.success) {
                                    $('#tb_sql_combo').textbox('setValue', data.sql);
                                } else {
                                    $.messager.alert(T('errore'), data.msg, 'error');
                                }
                            })
                            .fail(function () {
                                $.messager.progress('close');
                                $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                            });
                }
            });

        }

        if ((type_fk == "FOREIGN_KEY") && (type == "combogrid")) {

            var dlg_msg = $.messager.prompt({
                id: 'dlg_combo-sql',
                title: T('tabella collegata - combogrid'),
                msg: T('Impostare la tabella esterna da collegare, e i campi da associare e da visualizzare'),
                incon: 'info',
                width: '60%',
                height: '520px',
                maximizable: true,
                resizable: true,
                fn: function () {
                    var new_val = $('#cc_table').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
                    $(ed.target).textbox('setValue', new_val);

                    var new_val_id = $('#cc_id').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
                    $(ed.target).textbox('setValue', new_val_id);

                    var new_val_text = $('#cc_text').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
                    $(ed.target).textbox('setValue', new_val_text);

                    var ck_limit2list = $("#sb_limit2list").switchbutton('options').checked
                    ck_limit2list = (ck_limit2list) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_LIMIT2LIST'});
                    $(ed.target).textbox('setValue', ck_limit2list);

                    var new_val_fields_ar = $('#dg_fields').datagrid('getRows');
                    var new_val_fields = "";
                    if (new_val_fields_ar.length > 0) {
                        new_val_fields = JSON.stringify(new_val_fields_ar);
                    }
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'FIELDS'});
                    $(ed.target).textbox('setValue', new_val_fields);

                    var ck_sql_combo = $("#sb_custom_sql_combo").switchbutton('options').checked
                    ck_sql_combo = (ck_sql_combo) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_SQL_COMBO'});
                    $(ed.target).textbox('setValue', ck_sql_combo);

                    var sql_combo = $('#tb_sql_combo').textbox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'SQL_COMBO'});
                    $(ed.target).textbox('setValue', sql_combo);

                }
            });


            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
            var current_val = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
            var current_val_id = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
            var current_val_text = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_LIMIT2LIST'});
            var current_limit2list = ($(ed.target).textbox('getValue') == "") ? 1 : $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'FIELDS'});
            var current_val_fields_ar = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_SQL_COMBO'});
            var current_val_ck_sql_combo = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'SQL_COMBO'});
            var current_val_sql_combo = $(ed.target).textbox('getValue');

            var txt_label = T('imposta un sql personalizzato');
            var txt_label2 = T('limita inserimento dei valori su "combo" a solo quelli presenti');

            var input_cel = '<div style="margin-top:5px"><input id="cc_id"></div>\n\
                            <div style="margin-top:5px"><input id="cc_text"></div>\n\
                            <div style="margin-top:5px"><input id="sb_limit2list"><label style="margin-left:5px">' + txt_label2 + '</label></div>\n\
                            <div style="margin-top:5px"><table id="dg_fields"></table></div>\n\
                            <div style="margin-top:5px"><input id="sb_custom_sql_combo"><label style="margin-left:5px">' + txt_label + '</label></div>\n\
                            <div id="div_sql_combo" display:none;width:100%>\n\
                                <div style="margin-top:5px"><a id="bt_imp_sql_combo"></a></div>\n\
                                <div style="margin-top:5px;width:97%"><input id="tb_sql_combo"></div>\n\
                            </div>\n\
                            \n\
                            ';

            dlg_msg.find('div').end().append(input_cel);


            $('#cc_id').combobox({
                width: '390px',
                label: T('Campo valore (ID)'),
                value: current_val_id,
                labelWidth: '180px',
                valueField: 'COL',
                textField: 'COL',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: true,
            });
            $('#cc_text').combobox({
                width: '390px',
                label: T('Campo descrizione'),
                value: current_val_text,
                labelWidth: '180px',
                valueField: 'COL',
                textField: 'COL',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: true,
            });
            $("#sb_limit2list").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_limit2list == 1) ? true : false,
            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/list/table/db',
                width: '390px',
                label: T('Nome tabella collegata'),
                value: current_val,
                labelWidth: '180px',
                valueField: 'TEXT',
                textField: 'TEXT',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: true,
                onChange: function (newValue, oldValue) {
                    $('#dg_fields').datagrid({url: 'api/dg/model/read/db/' + newValue});
                },
                onSelect(record) {
                    $('#cc_id').combobox({url: 'api/dg/model/read/db/' + record.TEXT});
                    $('#cc_text').combobox({url: 'api/dg/model/read/db/' + record.TEXT});
                },
            }).attr('id', 'cc_table');
            var dg_fields_tb = ['-', {
                    text: T('Aggiungi'),
                    iconCls: 'icon-add',
                    id: 'bt_add',
                    handler: function (e) {
                        $('#dg_fields').edatagrid('addRow');
                    }}, '-', {
                    text: T('Conferma'),
                    iconCls: 'icon-ok',
                    handler: function () {
                        $('#dg_fields').edatagrid('saveRow');
                    }}, '-', {
                    text: T('Annulla'),
                    iconCls: 'icon-undo',
                    handler: function () {
                        $('#dg_fields').edatagrid('cancelRow');
                    }}, '-', {
                    text: T('Elimina'),
                    iconCls: 'icon-remove',
                    handler: function () {
                        $('#dg_fields').edatagrid('destroyRow');
                    }}, '-', {
                    text: T('Importa dal db'),
                    iconCls: 'icon-add',
                    handler: function () {
                        var table = $('#cc_table').combobox('getValue');
                        $('#dg_fields').datagrid('options').url = 'api/dg/model/read/db/' + table;
                        $('#dg_fields').datagrid('reload');

                    }}];

            var table_name = $('#cc_table').textbox('getValue');
            $('#dg_fields').edatagrid({
                toolbar: dg_fields_tb,
                height: '200px',
                width: '99%',
                rownumbers: true,
                striped: true,
                fitColumns: true,
                dragSelection: true,
                onLoadSuccess: function () {
                    //$(this).datagrid('enableDnd');
                },
                columns: [[
                        {field: 'ck', checkbox: true},
                        {field: "COL", width: 150, title: BR(T('Nome Campo')), editor: "text"},
                        {field: "TITLE", width: 150, title: BR(T('Titolo Campo')), editor: "text"},
                        {field: "WIDTH", title: BR(T('Larghezza Campo')), editor: "text"},
                        {field: "SKIP", title: BR(T('Escludi Campo')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                        {field: "HIDE", title: BR(T('Nascondi Campo')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                        {field: "BINDFIELD", title: T('Associa Campo') + '<br>' + T('su Form'), width: '160px', editor: {type: 'combobox'
                                , options: {
                                    url: 'api/dg/model/read/db/' + table_name,
                                    valueField: 'COL',
                                    textField: 'COL',
                                    panelWidth: 150,
                                }}
                        },
                    ]]
            });
            var current_val_fields = "";
            if (current_val_fields_ar != "") {
                current_val_fields = JSON.parse(current_val_fields_ar);
                $('#dg_fields').datagrid('loadData', current_val_fields);

            }


            $("#sb_custom_sql_combo").switchbutton({
                checked: false,
                onText: T('si'), offText: T('no'),
                checked: (current_val_ck_sql_combo == 1) ? true : false,
                onChange: function (checked) {
                    if (checked) {
                        $('#div_sql_combo').show();
                    } else {
                        $('#div_sql_combo').hide();
                    }
                    $('#tb_sql_combo').textbox({width: '97%', });
                }
            });
            (current_val_ck_sql_combo == 1) ? $('#div_sql_combo').show() : $('#div_sql_combo').hide();


            $('#tb_sql_combo').textbox({
                //label: T('sql'),
                value: current_val_sql_combo,
                prompt: T('inserisci qui'),
                labelPosition: 'top',
                width: '97%',
                height: '210px',
                multiline: true,
                required: true,
            });

            $('#bt_imp_sql_combo').linkbutton({
                iconCls: 'fa fa-database fa-lg',
                text: T('genera stringa sql'),
                //plain: true,
                onClick: function () {
                    $.messager.progress({title: T('recupero sql'), msg: T('lettura dati, in corso...')});
                    var table_name = $('#cc_table').textbox('getValue');
                    $.post('api/get/sql/combo', {table_name: table_name})
                            .done(function (data) {
                                $.messager.progress('close');
                                if (data.success) {
                                    $('#tb_sql_combo').textbox('setValue', data.sql);
                                } else {
                                    $.messager.alert(T('errore'), data.msg, 'error');
                                }
                            })
                            .fail(function () {
                                $.messager.progress('close');
                                $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                            });
                }
            });

        }

        if (type_fk == "LIST") {
            var dlg_msg = $.messager.prompt(T('lista valori'), T('Aggiungere i valori alla lista'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var list = $('#dg_list').datagrid('getRows')
                    var cat = 0;
                    var icon = 0;

                    var list2 = [];
                    for (var i = 0; i < list.length; i++) {
                        var row = {iconCls: list[i].iconCls, cat: list[i].cat, value: list[i].value, text: list[i].text}
                        list2.push(row);
                        (list[i].cat != "") ? cat = 1 : false;
                        (list[i].icon != "") ? icon = 1 : false;
                    }
                    var list_string = JSON.stringify(list2);
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'LIST'});
                    $(ed.target).textbox('setValue', list_string);
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
                    $(ed.target).textbox('setValue', 'value');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
                    $(ed.target).textbox('setValue', 'text');
                    var ck_limit2list = $("#sb_limit2list").switchbutton('options').checked
                    ck_limit2list = (ck_limit2list) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_LIMIT2LIST'});
                    $(ed.target).textbox('setValue', ck_limit2list);
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'LIST_ICON'});
                    $(ed.target).textbox('setValue', icon);
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'LIST_CAT'});
                    $(ed.target).textbox('setValue', cat);
                }
            });
            dlg_msg.window({width: '60%', height: '480px', resizable: true});
            dlg_msg.window('center');


            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'LIST'});
            var current_list = $(ed.target).textbox('getValue');
            if (current_list != "") {
                var current_list_dg = JSON.parse(current_list);
            }

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_LIMIT2LIST'});
            var current_limit2list = ($(ed.target).textbox('getValue') == "") ? 1 : $(ed.target).textbox('getValue');

            var txt_label2 = T('limita inserimento dei valori su "combo" a solo quelli presenti');
            var input_cel = '<div style="margin-top:5px"><input id="sb_limit2list"><label style="margin-left:5px">' + txt_label2 + '</label></div>\n\
                            <div style="margin-top:5px"></div>\n\
                            <table id="dg_list"><table/>';

            dlg_msg.find('div').end().append(input_cel);
            dlg_msg.find('.messager-input').hide();

            $("#sb_limit2list").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_limit2list == 1) ? true : false,
            });

            var dg_list_tb = ['-', {
                    text: T('Aggiungi'),
                    iconCls: 'icon-add',
                    id: 'bt_add',
                    handler: function (e) {
                        $('#dg_list').edatagrid('addRow');
                    }}, '-', {
                    text: T('Conferma'),
                    iconCls: 'icon-ok',
                    handler: function () {
                        $('#dg_list').edatagrid('saveRow');
                    }}, '-', {
                    text: T('Annulla'),
                    iconCls: 'icon-undo',
                    handler: function () {
                        $('#dg_list').edatagrid('cancelRow');
                    }}, '-', {
                    text: T('Elimina'),
                    iconCls: 'icon-remove',
                    handler: function () {
                        $('#dg_list').edatagrid('destroyRow');
                    }},
            ];

            $('#dg_list').edatagrid({
                toolbar: dg_list_tb,
                width: '98%',
                height: '280px',
                //fit: true,
                rownumbers: true,
                striped: true,
                singleSelect: true,
                checkOnSelect: false,
                selectOnCheck: false,
                fitColumns: true,
                dragSelection: true,
                destroyMsg: {
                    norecord: {// when no record is selected
                        title: T('attenzione'),
                        msg: T('Nessun record selezionato'),
                    },
                    confirm: {// when select a row
                        title: T('conferma'),
                        msg: T('Sei sicuro che vuoi cancellare?')
                    }
                },
                onLoadSuccess: function () {
                    //$(this).datagrid('enableDnd');
                },
                columns: [[
                        {field: 'ck', checkbox: true},
                        {field: "iconCls", width: '19%', title: T('icona') + ' (classe)<br>' + T('opzionale'), editor: "textbox"},
                        {field: "cat", width: '25%', title: BR(T('categoria opzionale')), editor: "textbox"},
                        {field: "value", width: '15%', title: T('valore ') + '(ID)<br>' + T('opzionale'), editor: "textbox"},
                        {field: "text", width: '39%', title: T('descrizione'), editor: "textbox"},
                    ]],

            });
            if (current_list != "") {
                $('#dg_list').datagrid('loadData', current_list_dg);
            }
        }
    }

    $('#opt_set_width_form').on('click', function () {
        $.messager.progress({title: T('elaborazione'), msg: T('Impostazione larghezza campo, attendere...')});
        $('#dg_model').datagrid('removeFilterRule');
        $('#dg_model').datagrid('disableFilter');
        var model = $('#dg_model').datagrid('getRows');
        $('#dg_model').datagrid('enableFilter');
        $.post('api/set/width/field/form', {model: model})
                .done(function (data) {
                    $.messager.progress('close');
                    if (data.success) {
                        var model = data.model;
                        $('#dg_model').datagrid('loadData', model);
                    } else {
                        $.messager.alert(T('errore'), data.msg, 'error');
                    }
                })
                .fail(function () {
                    $.messager.progress('close');
                    $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                });
    });




    function select_fk_type(index, data) {
        var dlg_msg = $.messager.prompt(T('dati'), T('Seleziona orgine dati'), function (r) {
            if (r === undefined) {
                //console.log('press cancel');
            } else {
                var fk = $('#fk_type').combobox('getValue');
                open_opt_fk(fk, index);
            }
        });
        dlg_msg.find('.messager-input').combobox({
            data: data,
            valueField: 'value',
            textField: 'text',
            required: true,
            panelWidth: 300,
            editable: false,
            prompt: T('seleziona'),
            onLoadSuccess: function () {
                //$(this).combobox({prompt: T('seleziona')});
            },
            onSelect: function (rec) {
                var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CONSTRAINT_TYPE'});
                var fk = $(ed.target).combobox('setValue', rec.value);
            },
            label: T('Origine dati:'),
            labelPosition: 'left',
            width: 240,
        }).attr('id', 'fk_type');
    }

    function edit_filter(type, index) {
        if ((type == "textbox") || (type == "texarea")) {
            var dlg_msg = $.messager.prompt({
                id: 'dlg_filter',
                title: T('filtri avanzati'),
                msg: T('imposta i parametri sotto'),
                incon: 'info',
                width: '60%',
                height: '520px',
                maximizable: true,
                resizable: true,
                fn: function () {
                    var ck_filter = $("#sb_ck_filter").switchbutton('options').checked
                    ck_filter = (ck_filter) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
                    $(ed.target).combobox('setValue', ck_filter);

                    var ck_filter_required = $("#sb_ck_filter_required").switchbutton('options').checked
                    ck_filter_required = (ck_filter_required) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
                    $(ed.target).textbox('setValue', ck_filter_required);

                    var ck_filter_like = $("#sb_ck_filter_like").switchbutton('options').checked
                    ck_filter_like = (ck_filter_like) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_LIKE'});
                    $(ed.target).textbox('setValue', ck_filter_like);

                }
            });

            dlg_msg.find('.messager-input').remove();
            var input_cel = '\
                    <input id="sb_ck_filter"><label style="margin-left:5px">' + T('abilita filtro') + '</label>\n\
                    <div id="div_filter" style="margin-top:5px;display:none">\n\
                        <div style="margin-top:5px"><input id="sb_ck_filter_required"><label style="margin-left:5px">' + T('campo obbligatorio') + '</label></div>\n\
                        <div style="margin-top:5px"><input id="sb_ck_filter_like"><label style="margin-left:5px">' + T('cerca testo contenuto') + '</label></div>\n\
                    </div>\n\
                    ';
            dlg_msg.find('div').end().append(input_cel);

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
            var current_ck_filter = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
            var current_ck_filter_required = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_LIKE'});
            var current_ck_filter_like = $(ed.target).textbox('getValue');

            $("#sb_ck_filter").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter == 1) ? true : false,
                onChange: function (checked) {
                    (checked) ? $('#div_filter').show() : $('#div_filter').hide();
                }
            });
            (current_ck_filter == 1) ? $('#div_filter').show() : $('#div_filter').hide();
            $("#sb_ck_filter_required").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter_required == 1) ? true : false,
            });
            $("#sb_ck_filter_like").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter_like == 1) ? true : false,
            });
        }

        if ((type == "numberbox")) {
            var dlg_msg = $.messager.prompt({
                id: 'dlg_filter',
                title: T('filtri avanzati'),
                msg: T('imposta i parametri sotto'),
                incon: 'info',
                width: '60%',
                height: '520px',
                maximizable: true,
                resizable: true,
                fn: function () {
                    var ck_filter = $("#sb_ck_filter").switchbutton('options').checked
                    ck_filter = (ck_filter) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
                    $(ed.target).combobox('setValue', ck_filter);

                    var ck_filter_required = $("#sb_ck_filter_required").switchbutton('options').checked
                    ck_filter_required = (ck_filter_required) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
                    $(ed.target).textbox('setValue', ck_filter_required);

                }
            });

            dlg_msg.find('.messager-input').remove();
            var input_cel = '\
                    <input id="sb_ck_filter"><label style="margin-left:5px">' + T('abilita filtro') + '</label>\n\
                    <div id="div_filter" style="margin-top:5px;display:none">\n\
                        <div style="margin-top:5px"><input id="sb_ck_filter_required"><label style="margin-left:5px">' + T('campo obbligatorio') + '</label></div>\n\
                    </div>\n\
                    ';
            dlg_msg.find('div').end().append(input_cel);

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
            var current_ck_filter = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
            var current_ck_filter_required = $(ed.target).textbox('getValue');

            $("#sb_ck_filter").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter == 1) ? true : false,
                onChange: function (checked) {
                    (checked) ? $('#div_filter').show() : $('#div_filter').hide();
                }
            });
            (current_ck_filter == 1) ? $('#div_filter').show() : $('#div_filter').hide();
            $("#sb_ck_filter_required").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter_required == 1) ? true : false,
            });
        }

        if ((type == "combobox") || (type == "combogrid")) {
            var dlg_msg = $.messager.prompt({
                id: 'dlg_filter',
                title: T('filtri avanzati'),
                msg: T('imposta i parametri sotto'),
                incon: 'info',
                width: '60%',
                height: '520px',
                maximizable: true,
                resizable: true,
                fn: function () {
                    var ck_filter = $("#sb_ck_filter").switchbutton('options').checked
                    ck_filter = (ck_filter) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
                    $(ed.target).combobox('setValue', ck_filter);

                    var ck_filter_required = $("#sb_ck_filter_required").switchbutton('options').checked
                    ck_filter_required = (ck_filter_required) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
                    $(ed.target).textbox('setValue', ck_filter_required);

                    var ck_filter_multiple = $("#sb_ck_filter_multiple").switchbutton('options').checked
                    ck_filter_multiple = (ck_filter_multiple) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_MULTIPLE'});
                    $(ed.target).textbox('setValue', ck_filter_multiple);

                }
            });

            dlg_msg.find('.messager-input').remove();
            var input_cel = '\
                    <input id="sb_ck_filter"><label style="margin-left:5px">' + T('abilita filtro') + '</label>\n\
                    <div id="div_filter" style="margin-top:5px;display:none">\n\
                        <div style="margin-top:5px"><input id="sb_ck_filter_required"><label style="margin-left:5px">' + T('campo obbligatorio') + '</label></div>\n\
                        <div style="margin-top:5px"><input id="sb_ck_filter_multiple"><label style="margin-left:5px">' + T('selezione multipla') + '</label></div>\n\
                    </div>\n\
                    ';
            dlg_msg.find('div').end().append(input_cel);

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
            var current_ck_filter = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
            var current_ck_filter_required = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_MULTIPLE'});
            var current_ck_filter_multiple = $(ed.target).textbox('getValue');

            $("#sb_ck_filter").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter == 1) ? true : false,
                onChange: function (checked) {
                    (checked) ? $('#div_filter').show() : $('#div_filter').hide();
                }
            });
            (current_ck_filter == 1) ? $('#div_filter').show() : $('#div_filter').hide();
            $("#sb_ck_filter_required").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter_required == 1) ? true : false,
            });
            $("#sb_ck_filter_multiple").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter_multiple == 1) ? true : false,
            });
        }

        if ((type == "datebox")) {
            var dlg_msg = $.messager.prompt({
                id: 'dlg_filter',
                title: T('filtri avanzati'),
                msg: T('imposta i parametri sotto'),
                incon: 'info',
                width: '60%',
                height: '520px',
                maximizable: true,
                resizable: true,
                fn: function () {
                    var ck_filter = $("#sb_ck_filter").switchbutton('options').checked
                    ck_filter = (ck_filter) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
                    $(ed.target).combobox('setValue', ck_filter);

                    var ck_filter_required = $("#sb_ck_filter_required").switchbutton('options').checked
                    ck_filter_required = (ck_filter_required) ? 1 : 0;
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
                    $(ed.target).textbox('setValue', ck_filter_required);

                    var filter_dt_field = $("#cc_filter_dt_field").combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'FILTER_DT_FIELD'});
                    $(ed.target).textbox('setValue', filter_dt_field);

                }
            });

            dlg_msg.find('.messager-input').remove();
            var input_cel = '\
                    <input id="sb_ck_filter"><label style="margin-left:5px">' + T('abilita filtro') + '</label>\n\
                    <div id="div_filter" style="margin-top:5px;display:none">\n\
                        <div style="margin-top:5px"><input id="sb_ck_filter_required"><label style="margin-left:5px">' + T('campo obbligatorio') + '</label></div>\n\
                        <div style="margin-top:5px"><input id="cc_filter_dt_field"></div>\n\
                    </div>\n\
                    ';
            dlg_msg.find('div').end().append(input_cel);

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER'});
            var current_ck_filter = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CK_FILTER_REQUIRED'});
            var current_ck_filter_required = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'FILTER_DT_FIELD'});
            var current_filter_dt_field = $(ed.target).textbox('getValue');

            $("#sb_ck_filter").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter == 1) ? true : false,
                onChange: function (checked) {
                    (checked) ? $('#div_filter').show() : $('#div_filter').hide();
                }
            });
            (current_ck_filter == 1) ? $('#div_filter').show() : $('#div_filter').hide();
            $("#sb_ck_filter_required").switchbutton({
                checked: true,
                onText: T('si'), offText: T('no'),
                checked: (current_ck_filter_required == 1) ? true : false,
            });

            $('#cc_filter_dt_field').combobox({
                //url: 'api/dg/model/read/db/' + $('#tb_table_name').textbox('getValue'),
                width: 300,
                label: T('campo data asociato'),
                labelPosition: 'right',
                labelWidth: 150,
                valueField: 'COL',
                textField: 'COL',
                buttonIcon: 'icon-reload',
                buttonAlign: 'left',
                value: current_filter_dt_field,
                onClickButton: function () {
                    $(this).combobox({url: 'api/dg/model/read/db/' + $('#tb_table_name').textbox('getValue')});
                },
            });
        }

    }

    //auto open project from link
    var project = getURLParameter('project');
    var cfg = getURLParameter('cfg');
    if (project != null && cfg != null) {
        open_cfg(project, cfg);
    }
}
