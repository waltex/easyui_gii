function init_app() {
    $('#dg_set').datagrid({
        url: 'api/dg/setting/read',
        //fit: true,
        striped: true,
        fitColumns: true,
        columns: [[
                {field: 'name', title: T('Parametro'), width: 100},
                {field: 'val', title: T('Valore'), width: 100},
            ]]
    });

}