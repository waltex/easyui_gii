
function init_app() {
    $('#tb_out').textbox({
        with : 250,
        value: g_param["path output generated app"],
        label: T("percorso app:"), //path app
        prompt: T("digita qui...")//type here
    });
}