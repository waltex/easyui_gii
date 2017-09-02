
function init_app() {
    $('#tb_out').textbox({
        value: g_param.path_out,
        label: T("path app"),
        prompt: T("type the path...")
    });
}