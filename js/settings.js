var g_dg1_expand = true;
var g_row_height = 28;
function init_app() {
    var dg1_tb = ['-', {
            text: T('Salva'),
            iconCls: 'icon-save',
            handler: function () {
                $('#dg_set').edatagrid('saveRow');
            }
        }, '-', {
            text: T('Annulla'),
            iconCls: 'icon-undo',
            handler: function () {
                $('#dg_set').edatagrid('cancelRow');
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
        onError: function (index, row) {
            if (row) {
                $.messager.alert(T('errore'), row.msg, 'error');
            }
        },
        onEdit: function (index, row) {
            if (row.name == "lingua corrente") {
                var ed = $(this).datagrid('getEditor', {index: index, field: "val"});
                var rows = $('#dg_set').datagrid('getRows');
                var from_lng = rows[index - 2].val;//from language setting
                var to_lng = rows[index - 1].val;// to language setting
                var data = [{text: from_lng}, {text: to_lng}];
                $(ed.target).combobox({valueField: 'text', textField: 'text', data: data, editable: false, panelHeight: 40, height: g_row_height});
            }
            if ((row.val === true) || (row.val === false)) {
                var ed = $(this).datagrid('getEditor', {index: index, field: "val"});
                var data = [{text: T('si').toLowerCase(), value: "true"}, {text: T('no').toLowerCase(), value: "false"}];
                $(ed.target).combobox({valueField: 'value', textField: 'text', data: data, editable: false, panelHeight: 40, height: g_row_height});
            }

            var find = row.name;
            if (find.search("tnsnames.ora") > -1) {
                var ed = $(this).datagrid('getEditor', {index: index, field: "val"});
                $(ed.target).textbox({multiline: true, height: 150});
            }
            var find = row.name;
            if (find.search("parametri PDO") > -1) {
                var ed = $(this).datagrid('getEditor', {index: index, field: "val"});
                $(ed.target).textbox({multiline: true, height: 150});
            }

        },
        groupFormatter: function (value, rows) {
            return T(value) + ' - ' + rows.length + ' ' + T('parametri');
        },
        columns: [[
                {field: 'name', title: T('Parametro'), formatter: function (value, row, index) {
                        return (!g_param["language debug"]) ? T(value) : value;//transalte only dubug off
                    }},
                {field: 'val', title: T('Valore'), editor: "text", formatter: function (value, row, index) {
                        if ((value === true) || (value === false)) {
                            return (value === true) ? T('si').toLowerCase() : T('no').toLowerCase();
                        }
                        if ((value === "true") || (value === "false")) {
                            return (value === "true") ? T('si').toLowerCase() : T('no').toLowerCase();
                        }
                        return value;
                    }},
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