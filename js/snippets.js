var g_debug
var g_edit_code = false;
function init_app() {
    //translate
    $('#cc_layout').layout('panel', 'west').panel({title: T('elenco esempi di codice')}); //panel west
    //$('#cc_code').layout('panel', 'center').panel({title: T('Pagina codice')}); //panel west
    $('#cc_code').layout('panel', 'north').panel({title: T('Pagina codice')}); //panel west

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
            id: 'bt_edit_old',
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
        onLoadSuccess: set_star,
        onSuccess: function (index, row) {
            view_file();
        },
        onError: function (index, row) {
            $.messager.alert(T('Attenzione'), row.msg, 'warning');
        },
        onDestroy: function (index, row) {
            $('#cc_code').layout('panel', 'center').panel({content: '<div></div>'})
            $('#image_snippets').panel({content: '<div></div>'});
        },
        onEdit: function (index, row) {
            var ed = $('#dg_snippets').datagrid('getEditor', {index: index, field: 'name'});
            $(ed.target).textbox('setValue', row.file);
            $(ed.target).textbox('textbox').bind('keydown', function (e) {
                if (e.keyCode == 13) {	// when press ENTER key, accept the inputed value.
                    $('#dg_snippets').edatagrid('saveRow');
                }
            });


        },
        columns: [[
                {field: 'ck', checkbox: true},
                {field: 'name', title: T('Nome file'), width: '82%', editor: "textbox", formatter: function (value, row, index) {

                        return T(value);//transalte
                    }},
                {field: 'start', title: T('Importante'), width: '15%', formatter: function (value, row, index) {
                        return '<span id="#rateYo_' + index + '" style="float:center"></span>';
                    }},
            ]]
    });
    $('#dg_snippets').datagrid('enableFilter');


    function set_star() {
        var tot = $('span').length
        for (var i = 0; i < tot; i++) {
            var id = $('span')[i].id;
            var n = id.indexOf('#rateYo_');
            console.log(id)
            if (n > -1) {
                $($('span')[i]).rateYo({
                    numStars: 3,
                    starWidth: '15px',
                    readOnly: true,
                    rating: 2,
                })
            }
        }
    }
    function click_row() {
        view_file();
    }
    $('#bt_add').tooltip({
        content: T('inserire anche estensione es .php .html')
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

        (g_edit_code) ? $('#div_upload').show() : $('#div_upload').hide();
        // check if exists imege

        $.get(url)
                .done(function () {
                    $('#cc_code').layout('panel', 'north').panel({title: T('Pagina codice con immagine ')}); //panel west
                    var content = '<img id="image_code" style="max-width:100%;height:auto;max-height:95%;width:auto;margin:0px auto;display:block" align="middle" src="' + url + '"></img>';
                    $('#image_snippets').panel({content: content});
                }).fail(function () {
            $('#cc_code').layout('panel', 'north').panel({title: T('Pagina codice')}); //panel west
            $('#image_snippets').panel({content: '<div></div>'});
        });



    }

    $('#label_fb_upload').html(T('File immagine:'));
    $('#fb_upload').filebox({
        buttonText: T('scegli'),
        accept: 'image/*',
        required: true
    });

    $('#bt_upload').on('click', function () {
        var file = $('#dg_snippets').datagrid('getSelected').file
        $('#ff_upload').form('options').queryParams = {name_file: file}
        $('#ff_upload').form('submit');
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
            $('#fb_upload').filebox('clear');
            viw_image_code();
        },
        onLoadError: function () {
            $.messager.alert(T('attenzione'), T('Si è verificvato un errore nel trasferimento'), 'error');
        }
    });
}

