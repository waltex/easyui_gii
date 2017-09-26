var g_debug
var g_edit_code = false;
function init_app() {
    //translate
    $('#cc_layout').layout('panel', 'west').panel({title: T('elenco esempi di codice')}); //panel west
    //$('#cc_code').layout('panel', 'center').panel({title: T('Pagina codice')}); //panel west
    $('#cc_code').layout('panel', 'north').panel({title: T('Pagina codice')}); //panel west
    $('#cc_code').layout('panel', 'north').panel({
        onCollapse: function () {
            var opts = $(this).panel('options');
            var layout = $(this).closest('.layout');
            var region = opts.region;
            var p = layout.layout('panel', 'expand' + region.substr(0, 1).toUpperCase() + region.substr(1));
            var tools = p.panel('options').tools;
            p.panel({
                tools: [{
                        iconCls: 'icon-add',
                        handler: bt_add,
                    }].concat(tools.pop())
            })
        },
        tools: [{
                iconCls: 'icon-add',
                handler: bt_add,
            }]});

    function bt_add() {
        alert('new2');
    }

    var dg1_tb = ['-', {
            text: T('Aggiungi'),
            iconCls: 'icon-add',
            id: 'bt_add',
            handler: function (e) {
                $('#dg_snippets').edatagrid('addRow');
            }}, '-', {
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
            text: T('Elimina'),
            iconCls: 'icon-remove',
            handler: function () {
                $('#dg_snippets').edatagrid('destroyRow');
            }}, '-', {
            id: 'bt_edit',
            text: T('Modifica'),
            toggle: true,
            iconCls: 'icon-edit',
            handler: function () {
                g_edit_code = !g_edit_code;
                view_file();
            }}];
    $('#dg_snippets').edatagrid({
        url: 'api/dg/snippets/read',
        updateUrl: 'api/dg/snippets/rename',
        saveUrl: 'api/dg/snippets/add',
        destroyUrl: 'api/dg/snippets/delete',
        idField: 'file',
        border: false,
        toolbar: dg1_tb,
        fit: true,
        striped: true,
        editorHeight: 45,
        singleSelect: true,
        checkOnSelect: false,
        selectOnCheck: false,
        nowrap: false,
        //fitColumns: true,
        onClickRow: click_row,
        onSuccess: view_file,
        onError: function (index, row) {
            $.messager.alert(T('Attenzione'), row.msg, 'warning');
        },
        onDestroy: function (index, row) {
            $('#cc_code').layout('panel', 'center').panel({
                content: '<div></div>',
            })
        },
        columns: [[
                {field: 'ck', checkbox: true},
                {field: 'name', title: T('digita qui per cercare'), width: '100%', editor: "textarea", formatter: function (value, row, index) {
                        return T(value);//transalte
                    }},
            ]]
    });
    function click_row() {
        view_file();
    }
    $('#dg_snippets').datagrid('enableFilter');

    $('#bt_add').tooltip({
        content: T('inserire anche estensione es .php .html')
    });
    $('#bt_edit').tooltip({
        content: T('Abilita la modifica della pagina del codice')
    });

    function view_file() {
        var file = $('#dg_snippets').datagrid('getSelected').file;
        var param = $.param({protocol: 'FTP',
            ftpserver: 'localhost',
            ftpserverport: 21,
            sshfingerprint: '',
            username: 'daemon',
            password_encrypted: '7B98661E4E',
            language: g_param["lingua corrente"],
            skin: 'shinra',
            ftpmode: 'automatic',
            passivemode: 'no',
            viewmode: 'list',
            sort: '',
            sortorder: '',
            state: (g_edit_code) ? 'edit' : 'view',
            directory: '/easyui_gii/snippets',
            entry: file});
        var url = 'http://localhost/easyui_gii/lib/net2ftp/index.php?' + param;
        var content = '<iframe id="iframe_snippets" scrolling="yes" frameborder="0"  src="' + url + '" style="width:99%;height:97.3%;padding:0.5%"></iframe>';
        $('#cc_code').layout('panel', 'center').panel({
            content: content,
        });
        viw_image_code();
    }
    function viw_image_code() {
        var file = $('#dg_snippets').datagrid('getSelected').file
        var url = "snippets/image/" + file + '.jpg';
        var content = '<img id="image_code" style="max-width:100%;height:auto;max-height:95%;width:auto;margin:0px auto;display:block" align="middle" src="' + url + '"></img>';
        $('#image_snippets').panel({content: content});
        (g_edit_code) ? $('#div_upload').show() : $('#div_upload').hide();
    }

    $('#label_fb_upload').html(T('File immagine:'));
    $('#fb_upload').filebox({
        buttonText: T('scegli'),
        accept: 'image/*',
        required: true
    });

    $('#bt_upload').on('click', function () {
        $('#ff_upload').form('options').queryParams = {name_file: file}
        $('#ff_upload').form('submit');
        viw_image_code()
    });
    $('#bt_upload').linkbutton({text: T('Salva')})
    $('#ff_upload').form({
        url: 'api/uoload/image',
        onSubmit: function (param) {
            if ($('#fb_upload').filebox('isValid')) {
                $.messager.progress({title: '** upload **', msg: T('Trasferimento immagine, in corso...')});
                return true;
            } else {
                $.messager.alert('** attenzione **', T('Selezionare una immagine'), 'warning');
                return false;
            }
        },
        queryParams: {
            name_file: null
        },
        success: function (data) {
            $.messager.progress('close');
        },
        onLoadError: function () {
            $.messager.alert(T('attenzione'), T('Si è verificvato un errore nel trasferimento'), 'error');
        }
    });
}

