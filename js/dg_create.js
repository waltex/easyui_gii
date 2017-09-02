
function init_app() {
    $('#tb_out').textbox({
        value: g_param.path_out,
        label: T("percorso app"),
        prompt: T("digita qui...")
    });
}