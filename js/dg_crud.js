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
            //g_debug = e;
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
    $('#tb_table_name').textbox({
        label: T("Nome tabella:"), //path app
        required: true,
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
    });
    $('#p_crud').panel({
        title: T("Altri Parametri"),
    });

    $('#bt_gencode').linkbutton({text: T('Genera il codice')});
    // expand and collapse on click
    $('#p_crud').panel('header').click(function () {
        var hide = $($('#p_crud').panel('body')[0]).css('display') == 'none';
        (hide) ? $('#p_crud').panel('expand', true) : $('#p_crud').panel('collapse', true);
    });
    $('#bt_gencode').on('click', function () {
        var validate = true;
        (!$('#tb_app_name').textbox('isValid')) ? validate = false : false;
        (!$('#tb_app_folder').textbox('isValid')) ? validate = false : false;
        (!$('#tb_table_name').textbox('isValid')) ? validate = false : false;
        if (validate) {
            $.messager.confirm(T('attenzione'), T('Verrà generato il codice, confermi?'), function (r) {
                if (r) {
                    var app_name = $('#tb_app_name').textbox('getValue');
                    var app_folder = $('#tb_app_folder').textbox('getValue');
                    var table_name = $('#tb_table_name').textbox('getValue');
                    var model_from_json = $("#sb_model").switchbutton('options').checked;
                    $.messager.progress({title: T('elaborazione'), msg: T('Generazione del codice in corso, attendere...')});
                    $.post('api/dg/crud/generate', {app_name: app_name, app_folder: app_folder, table_name: table_name, model_from_json: model_from_json})
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
                $('#dg_model').datagrid({url: 'api/dg/model/read/json'});
            }}, '-', {
            text: T('Importa dal db'),
            iconCls: 'icon-add',
            handler: function () {
                var table = $('#tb_table_name').textbox('getValue');
                if (table != "") {
                    $('#dg_model').datagrid({url: 'api/dg/model/read/db/' + table});
                } else {
                    $.messager.alert(T('attenzione'), T('Impostare il nome della tabella'), 'warning');
                }
            }}];
    var data_pk_fk = [{
            text: 'Primary Key',
            id: 'PRIMARY_KEY'
        }, {
            text: 'Foreing Key',
            id: 'FOREIGN_KEY'
        }, {
            text: T('Nessuna'),
            id: null
        }];
    var data_type = [{text: 'tetxbox'}, {text: 'datebox', }, {text: 'numberbox'}];
    function load_dg_model() {
        $('#dg_model').edatagrid({
            //url: url: 'api/dg/model/read/json',
            //updateUrl: 'api/xx',
            toolbar: dg_model_tb,
            fit: true,
            striped: true,
            singleSelect: true,
            checkOnSelect: false,
            selectOnCheck: false,
            fitColumns: true,
            columns: [[
                    {field: 'ck', checkbox: true},
                    {field: "COL", title: T('Nome Colonna'), editor: "text"},
                    {field: "TYPE", title: T('Tipo Campo'), editor: {type: 'combobox', options: {
                                valueField: 'text',
                                textField: 'text',
                                editable: false,
                                panelWidth: 100,
                                data: data_type
                            }}},
                    {field: "CONSTRAINT_TYPE", title: T('Pk - Fk'), editor: {type: 'combobox', options: {
                                valueField: 'id',
                                textField: 'text',
                                editable: false,
                                panelWidth: 100,
                                data: data_pk_fk
                            }}
                        , formatter: function (value, row, index) {
                            var data = combo_get_text(value, data_pk_fk);
                            return data;
                        }
                    },
                    {field: "SKIP", title: T('Campo') + '<br>' + T('Scartato'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "HIDE", title: T('Campo') + '<br>' + T('Nascosto'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                    {field: "CK", title: T('Campo') + '<br>' + T('Si, No'), editor: {type: 'checkbox', options: {on: '1', off: '0'}}, formatter: mycheck, required: true},
                ]]
        });
    }
    function combo_get_text(id, data) {
        for (var i = 0; i < data.length; i++) {
            if (data[i].id == id)
                return data[i].text;
        }

    }
    function dg_model_save2json() {
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
    }
}



