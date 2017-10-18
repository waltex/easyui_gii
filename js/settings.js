var g_dg1_expand = true;
function init_app() {
    var dg1_tb = ['-', {
            text: T('Salva'),
            iconCls: 'icon-save',
            handler: function () {
                $('#dg_set').edatagrid('saveRow');
            }
        }, '-', {
            text: T('espandi') + '/' + T('compatta'),
            iconCls: 'fa fa-expand fa-lg fa-green',
            handler: function () {
                if (g_dg1_expand) {
                    g_dg1_expand = false;
                    dg_collapse_expand('#dg_set', 1);//espande
                } else {
                    g_dg1_expand = true;
                    dg_collapse_expand('#dg_set', 0);//contrae
                }

            }
        }];
    $('#dg_set').edatagrid({
        url: 'api/dg/setting/read',
        updateUrl: 'api/dg/setting/save',
        border: false,
        toolbar: dg1_tb,
        fit: true,
        striped: true,
        singleSelect: true,
        fitColumns: true,
        view: groupview,
        groupField: 'cat',
        groupFormatter: function (value, rows) {
            return T(value) + ' - ' + rows.length + ' ' + T('parametri');
        },
        columns: [[
                {field: 'name', title: T('Parametro'), formatter: function (value, row, index) {
                        return (!g_param["language debug"]) ? T(value) : value;//transalte only dubug off
                    }},
                {field: 'val', title: T('Valore'), editor: "text"},
            ]]
    });
    $('#dg_set').datagrid('enableFilter');
    function dg_collapse_expand(dg, collapse) {
        var groups = $(dg).datagrid('groups');
        for (var i = 0; i < groups.length; i++) {
            (collapse == 1) ? $(dg).datagrid('collapseGroup', i) : $(dg).datagrid('expandGroup', i);
        }
    }
}