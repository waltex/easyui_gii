var g_debug
var g_keydown
function init_app() {
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
        title: T("Parametri principali"),
    });
    $('#p_base').panel('header').click(function () {
        var hide = $($('#p_base').panel('body')[0]).css('display') == 'none';
        (hide) ? $('#p_base').panel('expand', true) : $('#p_base').panel('collapse', true);
    });

    $('#bt_gencode').linkbutton({text: T('Genera il codice')});
    // expand and collapse on click

    $('#bt_gencode').on('click', function () {
        /*
         var validate = true;
         (!$('#tb_app_name').textbox('isValid')) ? validate = false : false;
         (!$('#tb_app_folder').textbox('isValid')) ? validate = false : false;
         (!$('#tb_table_name').textbox('isValid')) ? validate = false : false;
         */
        var validate = $('#ff_crud').form('validate');
        if (validate) {
            $.messager.confirm(T('attenzione'), T('Verrà generato il codice, confermi?'), function (r) {
                if (r) {
                    var app_name = $('#tb_app_name').textbox('getValue');
                    var app_folder = $('#tb_app_folder').textbox('getValue');
                    var table_name = $('#tb_table_name').combobox('getValue');
                    var model_from_json = ($("#sb_model").switchbutton('options').checked) ? 1 : 0;
                    var html_prefix = $('#nn_prefix').numberspinner('getValue');
                    $.messager.progress({title: T('elaborazione'), msg: T('Generazione del codice in corso, attendere...')});
                    $.post('api/dg/crud/generate', {app_name: app_name, app_folder: app_folder, table_name: table_name, model_from_json: model_from_json, html_prefix: html_prefix})
                            .done(function (data) {
                                $.messager.progress('close');
                                if (data.success) {
                                    $.messager.confirm(T('conferma'), T('E\' stata creata applicazione, vuoi eseguirla?'), function (r) {
                                        if (r) {
                                            var url = window.location.protocol + '//' + window.location.host + '/' + app_folder + '/index.html';
                                            parent.addTab(app_name, url, null);
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
                }
            });
        } else {
            $.messager.alert(T('attenzione'), T('compilare tutti i campi correttamente'), 'warning');
        }
    });


    $("#sb_model").switchbutton({
        label: 'jskjshjs',
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
            text: T('Salva'),
            iconCls: 'icon-save',
            handler: function () {
                $('#dg_model').edatagrid('saveRow');
                dg_model_save2json();

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
            text: T('Ricarica'),
            iconCls: 'icon-reload',
            handler: function () {
                $('#dg_model').datagrid('options').url = 'api/dg/model/read/json';
                $('#dg_model').datagrid('reload');
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
            id: 'FOREIGN_KEY'
        }, {
            text: T('Nessuna'),
            id: null
        }];
    var data_type = [{text: 'tetxbox'}, {text: 'textarea'}, {text: 'datebox', }, {text: 'numberbox'}, {text: 'combobox'}];
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
                var ed = $(this).datagrid('getEditor', {index: index, field: "NAME_TABLE_EXT"});
                $(ed.target).combobox('reload', 'api/list/table/db');

                var ed = $(this).datagrid('getEditor', {index: index, field: "TEXT_FIELD"});
                $(ed.target).combobox('reload', 'api/list/column/' + row.NAME_TABLE_EXT);

                var ed = $(this).datagrid('getEditor', {index: index, field: "VALUE_FIELD"});
                $(ed.target).combobox('reload', 'api/list/column/' + row.NAME_TABLE_EXT);
            },
            frozenColumns: [[
                    {field: 'ck', checkbox: true},
                    {field: "COL", title: BR(T('Nome Campo')), editor: "text"},
                    {field: "TITLE", title: BR(T('Titolo Campo')), editor: "text"},
                    {field: "TYPE", title: BR(T('Tipo Campo')), editor: {type: 'combobox', options: {
                                valueField: 'text',
                                textField: 'text',
                                editable: false,
                                panelWidth: 100,
                                data: data_type
                            }}},
                ]],
            columns: [[
                    {field: "WIDTH", title: BR(T('Larghezza Campo')), editor: "text"},
                    {field: "SKIP", title: BR(T('Campo Scartato')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "HIDE", title: BR(T('Campo Nascosto')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "CK", title: BR(T('Campo Si|No')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "EDIT", title: BR(T('Campo Modificabile')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "REQUIRED", title: BR(T('Campo Richiesto')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "SORTABLE", title: BR(T('Campo Ordinabile')), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "CONSTRAINT_TYPE", title: BR(T('Vincoli Campo')), editor: {type: 'combobox', options: {
                                valueField: 'id',
                                textField: 'text',
                                editable: false,
                                panelWidth: 150,
                                data: data_pk_fk
                            }}
                        , formatter: function (value, row, index) {
                            var data = combo_get_text(value, data_pk_fk);
                            return data;
                        }
                    },
                    {field: "NAME_TABLE_EXT", title: T('Nome') + '<br>' + T('Tabella Collegata'), editor: {type: 'combobox',
                            options: {
                                valueField: 'TEXT',
                                textField: 'TEXT',
                                method: 'post',
                                panelWidth: 250,
                            }}},
                    {field: "VALUE_FIELD", title: T('Campo ID ') + '<br>' + T('Tabella Collegata'), editor: {type: 'combobox',
                            options: {
                                valueField: 'TEXT',
                                textField: 'TEXT',
                                method: 'post',
                                panelWidth: 250,
                            }}},
                    {field: "TEXT_FIELD", title: T('Campo TEXT') + '<br>' + T('Tabella Collegata'), editor: {type: 'combobox',
                            options: {
                                valueField: 'TEXT',
                                textField: 'TEXT',
                                method: 'post',
                                panelWidth: 250,
                            }}, },
                ]]
        });
        $('#dg_model').datagrid('enableFilter');
    }
    function combo_get_text(id, data) {
        for (var i = 0; i < data.length; i++) {
            if (data[i].id == id)
                return data[i].text;
        }

    }
    function dg_model_save2json() {
        $('#dg_model').datagrid('removeFilterRule');
        $('#dg_model').datagrid('disableFilter');
        var rows = $('#dg_model').datagrid('getRows');
        $.post('api/dg/model/save/json', {data: rows})
                .done(function (data) {
                    $.messager.progress('close');
                    if (data.success) {
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
        $('#dg_model').datagrid('enableFilter');
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
    $('#opt_import_field_model').html(T('Importa un campo del modello dal db'));


    $('#opt_copy_multi').on('click', function () {

        var dlg_msg = $.messager.prompt(T('copia multipla'), T('Verranno copiati i valori della cella sulle righe selezionate:'), function (r) {
            if (r === undefined) {
                //console.log('press cancel');
            } else {
                var title = $('#cc_title').numberspinner('getValue');//number row
                var field = $('cc_dg_field').combobox('getValue');// field name
            }

        });

        var model = $('#dg_model').datagrid('getRows');
        dlg_msg.find('.messager-input').combobox({
            data: model,
            mode: 'local',
            valueField: 'COL',
            textField: 'COL',
            required: true,
            panelWidth: 300,
            editable: false,
            prompt: T('seleziona'),
            label: T('Campo:'),
            labelPosition: 'left',
            width: 240,
        }).attr('id', 'cc_dg_field');
        var input_cel = '<br><input id="cc_title">';
        dlg_msg.find('div').end().append(input_cel);
        $('#cc_title').combobox({
            data: get_titles_model(),
            mode: 'local',
            valueField: 'TITLE',
            textField: 'TITLE',
            required: true,
            panelWidth: 300,
            editable: false,
            prompt: T('seleziona'),
            label: T('Titolo:'),
            labelPosition: 'left',
            width: 240,
        });

    });
    $('#opt_copy_multi').html(T('Copia multipla valori di una cella'));

    function get_titles_model() {
        var titles = [];
        var cols = $('#dg_model').datagrid('getColumnFields');
        for (var i = 0; i < cols.length - 1; i++) {
            var col = cols[i];
            var title = $('#dg_model').datagrid('getColumnOption', col).title
            var title = title.replace("<br>", " ");
            titles.push({TITLE: title, TITLE: title});
        }
        return titles;
    }

}



