
function init_app() {
    $('#tb_out').textbox({
        value: g_param["path output generated app"],
        label: T("Percorso app:"), //path app
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
    });

    $('#tb_table').textbox({
        label: T("Nome tabella:"), //path app
        prompt: T("digita qui..."), //type here
        labelPosition: 'top',
    });
    $('#p_crud').panel({
        title: T("Parametri"),
    });
}