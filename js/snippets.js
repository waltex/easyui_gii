var g_debug
function init_app() {
    //translate
    $('#cc_layout').layout('panel', 'west').panel({title: T('elenco esempi di codice')}); //panel west
    $('#cc_layout').layout('panel', 'center').panel({title: T('Pagina codice')}); //panel west

    var dg1_tb = ['-', {
            text: T('Aggiungi'),
            iconCls: 'icon-add',
            handler: function () {
                $('#dg_snippets').edatagrid('addRow');
            }
        }, '-', {
            text: T('Salva'),
            iconCls: 'icon-save',
            handler: function () {
                $('#dg_snippets').edatagrid('saveRow');
            }}, '-', {
            text: T('Annulla'),
            iconCls: 'icon-undo',
            handler: function () {
                $('#dg_snippets').edatagrid('cancelRow');
            }}, '-', {
            text: T('Ricarica'),
            iconCls: 'icon-reload',
            handler: function () {
                $('#dg_snippets').edatagrid('reload');
            }}, '-'];
    $('#dg_snippets').edatagrid({
        url: 'api/dg/snippets/read',
        updateUrl: 'api/dg/xxx',
        border: false,
        toolbar: dg1_tb,
        fit: true,
        striped: true,
        nowrap: false,
        //fitColumns: true,
        onClickRow: function (index, row) {
            var file = row.file;
            $('#cc_layout').layout('panel', 'center').panel({
                href: file,
                onLoadError: function () {
                    console.log('error');
                    $(this).href = null;
                    $('#cc_layout').layout('panel', 'center').panel({href: null});
                }
            })
        },
        columns: [[
                {field: 'name', title: T('digita qui per cercare'), width: '100%', editor: "text", formatter: function (value, row, index) {
                        return T(value);//transalte
                    }},
            ]]
    });
    $('#dg_snippets').datagrid('enableFilter');

}