function init_app() {
    var dg1_tb = ['-', {
            text: T('Salva'),
            iconCls: 'icon-save',
            handler: function () {
                $('#dg_set').edatagrid('saveRow');
            }
        }, '-'];
    $('#dg_set').edatagrid({
        url: 'api/dg/setting/read',
        updateUrl: 'api/dg/setting/save',
        border: false,
        toolbar: dg1_tb,
        fit: true,
        striped: true,
        singleSelect: true,
        fitColumns: true,
        columns: [[
                {field: 'name', title: T('Parametro'), formatter: function (value, row, index) {
                        return (!g_param["language debug"]) ? T(value) : value;//transalte only dubug off
                    }},
                {field: 'val', title: T('Valore'), editor: "text"},
            ]]
    });
    $('#dg_set').datagrid('enableFilter');
}