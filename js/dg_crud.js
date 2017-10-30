var g_debug
var g_keydown
var g_cfg_name
var g_param_show = false;
function init_app() {
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
            text: T('Mostra Parametri'),
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
            iconCls: 'icon-edit',
        }, {
            text: T('Lista Valori'),
            id: 'LIST',
            iconCls: 'icon-edit',
        }, {
            text: T('Nessuna'),
            id: null
        }];
    var data_type = [{text: 'textbox'}, {text: 'textarea', iconCls: 'icon-edit', }, {text: 'datebox', }, {text: 'numberbox'}, {text: 'combobox'}];
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
                                buttonText: '<i class="fa fa-list-alt" aria-hidden="true"></i>',
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
                    {field: "CONSTRAINT_TYPE", title: T('Vincoli Campo') + '<br>' + T('Origine Dati'), width: '160px', editor: {type: 'combobox', options: {
                                valueField: 'id',
                                textField: 'text',
                                editable: false,
                                panelWidth: 150,
                                data: data_pk_fk,
                                buttonText: '<i class="fa fa-list-alt" aria-hidden="true"></i>',
                                showItemIcon: true,
                                onClickButton: function () {
                                    var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'CONSTRAINT_TYPE'});
                                    var type = $(ed.target).combobox('getValue');
                                    open_opt_fk(type, index);
                                },
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
                    {field: "SKIP", title: BR(T('Campo Scartato')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "HIDE", title: BR(T('Campo Nascosto')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "CK", title: BR(T('Campo Si,No')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "EDIT", title: BR(T('Campo Modificabile')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "REQUIRED", title: BR(T('Campo Richiesto')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "SORTABLE", title: BR(T('Campo Ordinabile')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "NAME_TABLE_EXT", title: T('Nome') + '<br>' + T('Tabella Collegata'), editor: {type: 'textbox',
                            options: {}}, hidden: true},
                    {field: "VALUE_FIELD", title: T('Campo ID ') + '<br>' + T('associato'), editor: {type: 'textbox',
                            options: {}}, hidden: true},
                    {field: "TEXT_FIELD", title: T('Campo TEXT') + '<br>' + T('associato'), editor: {type: 'textbox',
                            options: {}}, hidden: true},
                    {field: "N_ROW_TEXTAREA", title: T('N° righe') + '<br>' + T('textarea'), editor: {type: 'textbox', options: {}}, hidden: true},
                    {field: "LIST", title: 'combobox' + '<br> ' + T('Dati Locali'), editor: {type: 'textbox',
                            options: {}}, hidden: true},
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
        var field = ['N_ROW_TEXTAREA', 'TEXT_FIELD', 'VALUE_FIELD', 'NAME_TABLE_EXT', 'LIST'];
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
                    $('#dg_model').datagrid('hideColumn', 'WIDTH_FORM')
                    $('#dg_model').datagrid('hideColumn', 'WIDTH_LABEL')
                }
            } else {
                $('#div_dg_inline').show();
                if ($('#dg_model').datagrid('getRows').length > 0) {
                    $('#dg_model').datagrid('showColumn', 'WIDTH_FORM')
                    $('#dg_model').datagrid('showColumn', 'WIDTH_LABEL')
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

    $('#bt_save_cfg').linkbutton({
        text: T('salva configurazione'),
        onClick: function () {
            var dlg_msg = $.messager.prompt(T('salva configurazione'), T('Verranno salvati tutti i parametri impostati'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var cfg_name = $('#cfg_name').combobox('getValue');
                    if (cfg_name != "") {
                        var cfg = read_cfg_from_input();
                        $.post('api/crud/save/cfg2json', {cfg: cfg, cfg_name: cfg_name})
                                .done(function (data) {
                                    $.messager.progress('close');
                                    if (data.success) {
                                        g_cfg_name = cfg_name;
                                        set_name_cfg(g_cfg_name);
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
                        $.messager.alert(T('attenzione'), T('non è stato messo un nome alla configurazione'), 'warning');
                    }
                }

            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/list/all/cfg',
                valueField: 'file',
                textField: 'file',
                hasDownArrow: false,
                label: T('Nome Configurazione'),
                prompt: T('digita qui'),
                value: g_cfg_name,
                labelPosition: 'top',
                width: '95%',
                required: true,
            }).attr('id', 'cfg_name');
            //$('#file_cfg_app').focus().select();
        }
    });
    $('#bt_open_cfg').linkbutton({
        text: T('apri configurazione'),
        onClick: function () {
            var dlg_msg = $.messager.prompt(T('apri configurazione'), T('Verranno letti tutti i parametri dalla configurazione selezionata'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    $.messager.progress({title: T('configurazione'), msg: T('lettura configurazione, attendere...')});
                    var cfg_name = $('#cfg_name').combobox('getValue');
                    $.post('api/crud/open/cfg/json', {cfg_name: cfg_name})
                            .done(function (data) {
                                $.messager.progress('close');
                                if (data.success) {
                                    g_cfg_name = cfg_name;
                                    set_name_cfg(g_cfg_name);
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

            });
            dlg_msg.find('.messager-input').combobox({
                url: 'api/list/all/cfg',
                valueField: 'file',
                textField: 'file',
                label: T('Nome Configurazione'),
                prompt: T('seleziona'),
                value: g_cfg_name,
                labelPosition: 'top',
                width: '95%',
                required: true,
            }).attr('id', 'cfg_name');
            //$('#file_cfg_app').focus().select();
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

        var dg_inline = ($("#sb_dg_inline").switchbutton('options').checked) ? 1 : 0;
        var pagination = ($("#sb_pagination").switchbutton('options').checked) ? 1 : 0;
        var pagination_list = $('#cc_pagination_list').combobox('getValues');
        pagination_list = JSON.stringify(pagination_list).replaceAll('\"', '')
        var pagination_size = $('#cc_pagination_size').combobox('getValue');
        var width_form = $('#tb_width_form').textbox('getValue');
        var height_form = $('#tb_height_form').textbox('getValue');

        var cfg = {
            app_name: app_name,
            app_folder: app_folder,
            table_name: table_name,
            model_from_json: model_from_json,
            html_prefix: html_prefix,
            dg_inline: dg_inline,
            pagination: pagination,
            pagination_list: pagination_list,
            pagination_size: pagination_size,
            model: model,
            width_form: width_form,
            height_form: height_form,
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
    }
    function set_name_cfg(cfg_name) {
        $('#p_base').panel({
            title: T("Parametri Principali") + " - " + "[" + T("configurazione:") + cfg_name + "]",
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
    function open_opt_type(type, index) {
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
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
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
    function open_opt_fk(type, index) {
        if (type == "FOREIGN_KEY") {
            var dlg_msg = $.messager.prompt(T('tabella collegata'), T('Impostare la tabella esterna da collegare, e i campi da associare'), function (r) {
                if (r === undefined) {
                    //console.log('press cancel');
                } else {
                    var new_val = $('#cc_table').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
                    $(ed.target).textbox('setValue', new_val);

                    var new_val_id = $('#cc_id').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
                    $(ed.target).textbox('setValue', new_val_id);

                    var new_val_text = $('#cc_text').combobox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
                    $(ed.target).textbox('setValue', new_val_text);

                }
            });
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'NAME_TABLE_EXT'});
            var current_val = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
            var current_val_id = $(ed.target).textbox('getValue');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
            var current_val_text = $(ed.target).textbox('getValue');

            var input_cel = '<div style="margin-top:5px"><input id="cc_id"><div style="margin-top:5px"><input id="cc_text">';
            dlg_msg.find('div').end().append(input_cel);
            $('#cc_id').combobox({
                width: '390px',
                label: T('Campo chiave primaria'),
                value: current_val_id,
                labelWidth: '180px',
                valueField: 'TEXT',
                textField: 'TEXT',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: false,
            });
            $('#cc_text').combobox({
                width: '390px',
                label: T('Campo descrizione'),
                value: current_val_text,
                labelWidth: '180px',
                valueField: 'TEXT',
                textField: 'TEXT',
                method: 'post',
                required: true,
                panelWidth: 250,
                editable: false,
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
                editable: false,
                onSelect(record) {
                    $('#cc_id').combobox({url: 'api/list/column/' + record.TEXT});
                    $('#cc_text').combobox({url: 'api/list/column/' + record.TEXT});
                },
            }).attr('id', 'cc_table');
        }
        if (type == "LIST") {
            var dlg_msg = $.messager.prompt(T('lista valori'), T('Aggiungere i valori alla lista, e i campi da associare'), function (r) {
                if (r === undefined) {
                    console.log('cancel');
                    //console.log('press cancel');
                } else {
                    var new_val_id = $('#cc_id').textbox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
                    $(ed.target).textbox('setValue', new_val_id);

                    var new_val_text = $('#cc_text').textbox('getValue');
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
                    $(ed.target).textbox('setValue', new_val_text);

                    var list = $('#dg_list').datagrid('getRows')
                    var list2 = [];
                    for (var i = 0; i < list.length; i++) {
                        var row = {[new_val_id]: list[i].VALUE, [new_val_text]: list[i].TEXT}
                        var row2 = {[new_val_text]: list[i].TEXT}
                        if (new_val_id != new_val_text) {
                            list2.push(row)
                        } else {
                            list2.push(row2)
                        }
                    }
                    var list_string = JSON.stringify(list2);
                    var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'LIST'});
                    $(ed.target).textbox('setValue', list_string);

                }
            });
            dlg_msg.window({width: '60%', height: '550px', resizable: true});
            dlg_msg.window('center');
            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'VALUE_FIELD'});
            var current_val_id = $(ed.target).textbox('getValue');
            (current_val_id == "") ? current_val_id = "value" : false;

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'TEXT_FIELD'});
            var current_val_text = $(ed.target).textbox('getValue');
            (current_val_text == "") ? current_val_text = "text" : false;

            var ed = $('#dg_model').datagrid('getEditor', {index: index, field: 'LIST'});
            var current_list = $(ed.target).textbox('getValue');
            if (current_val_id != current_val_text) {
                current_list = current_list.replaceAll('"' + current_val_id + '"', '"VALUE"');
                current_list = current_list.replaceAll('"' + current_val_text + '"', '"TEXT"');
            } else {
                current_list = current_list.replaceAll('"' + current_val_text + '"', '"TEXT"');
            }
            if (current_list != "") {
                var current_list_dg = JSON.parse(current_list);
            }




            var input_cel = '<div style="margin-top:5px"></div><input id="cc_id"><div style="margin-top:5px"></div><input id="cc_text"><div style="margin-top:5px"></div><table id="dg_list"><table/>';
            dlg_msg.find('div').end().append(input_cel);
            $('#cc_id').textbox({
                width: '98%',
                label: T('Campo valore (VALUE)'),
                labelPosition: 'top',
                prompt: T('scrivi qui'),
                value: current_val_id,
                required: true,
            });
            $('#cc_text').textbox({
                width: '98%',
                label: T('Campo descrizione (TEXT)'),
                labelPosition: 'top',
                prompt: T('scrivi qui'),
                value: current_val_text,
                required: true,

            });
            dlg_msg.find('.messager-input').hide();

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
                height: '50%',
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
                    $(this).datagrid('enableDnd');
                },
                columns: [[
                        {field: 'ck', checkbox: true},
                        {field: "VALUE", width: '49%', title: BR(T('valore VALUE')), editor: "textbox"},
                        {field: "TEXT", width: '49%', title: BR(T('descrizione TEXT')), editor: "textbox"},
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


}
