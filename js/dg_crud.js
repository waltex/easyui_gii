var g_debug
var g_keydown
function init_app() {
    $('#tb_name_app').textbox({
        required: true,
        label: T("Nome app:"), //path app
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
        onChange: function (newValue, oldValue) {
            //var val = $('#tb_out').textbox('getValue') + newValue;
            //$('#tb_out').textbox('setValue', val);
        }
    });
    /*
    $('#tb_name_app').textbox('textbox').bind('keydown', function (e) {
        g_debug = e;
        if ((e.keyCode >= 32) & (e.keyCode <= 126)) {
            var val = $('#tb_out').textbox('getValue') + e.key;
            $('#tb_out').textbox('setValue', val);
        }
    });
    */

    $('#tb_name_app').textbox('textbox').bind('keydown', function (e) {
        var $this = $(this);
        window.setTimeout(function () {
            g_debug = e;
            console.log($this.val() + '--' + $this.val().length);
            var add = $this.val();
            if (add.length == 1) {
                var val = $('#tb_out').textbox('getValue');
                g_keydown = val
                add = val + add;
            } else {
                add = g_keydown + add;
            }
            $('#tb_out').textbox('setValue', add);
        }, 0);
    });

    $('#tb_out').textbox({
        value: g_param["percorso del codice generato"],
        required: true,
        label: T("Percorso app:"), //path app
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
    });
    $('#tb_table').textbox({
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
        $.messager.confirm(T('attenzione'), T('Verrà generato il codice, confermi?'), function (r) {
            if (r) {
                $.messager.progress({title: T('elaborazione'), msg: T('Generazione del codice in corso, attendere...')});
                $.post('api/dg/crud/generate')
                        .done(function (data) {
                            $.messager.progress('close');
                            if (data.success) {
                                $.messager.alert(T('Eseguito'), data.msg, 'info');
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
    });

}



