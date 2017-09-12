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
                    $.messager.progress({title: T('elaborazione'), msg: T('Generazione del codice in corso, attendere...')});
                    $.post('api/dg/crud/generate', {app_name: app_name, app_folder: app_folder, table_name: table_name, opt: null})
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
        }
        else {
            $.messager.alert(T('attenzione'), T('compilare tutti i campi correttamente'), 'warning');
        }
    });

}



