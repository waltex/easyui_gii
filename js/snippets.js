var g_debug
var g_edit_code = false;
var g_dg_edit = false;
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
        fitColumns: true,
        striped: true,
        editorHeight: 45,
        singleSelect: true,
        checkOnSelect: false,
        selectOnCheck: false,
        nowrap: false,
        queryParams: {filter: "", name: "*"},
        onClickRow: click_row,
        onLoadSuccess: function (data) {
            //if (!data.isError) {
            set_star_readonly();
            //}
        },
        onSuccess: function (index, row) {
            view_file();
            set_star_readonly();
        },
        onError: function (index, row) {
            $.messager.alert(T('Attenzione'), row.msg, 'warning');
        },
        onDestroy: function (index, row) {
            $('#cc_code').layout('panel', 'center').panel({content: '<div></div>'})
            $('#image_snippets').panel({content: '<div></div>'});
        },
        onCancelEdit: function (index) {
            var tot = $('#dg_snippets').datagrid('getRows').length;
            //esclude new row
            if (index < tot) {
                var star = $('#dg_snippets').datagrid('getRows')[index].star;
                //$("#rateYo_" + index).rateYo("option", "rating", star);
                $('#rateYo_' + index).rateYo({
                    numStars: 3,
                    maxValue: 3,
                    starWidth: '20px',
                    readOnly: false,
                    rating: star,
                    fullStar: true,
                });
            }
        },
        onBeforeEdit: function (index, row) {
            g_dg_edit = true;
            var tot = $('#dg_snippets').datagrid('getRows').length - 1;
            if (index < tot) {
                set_star_on_edit(index);
            } else {
                $('#dg_snippets').datagrid('updateRow', {
                    index: index,
                    row: {
                        star: '<span id="rateYo_' + index + '"></span>'
                    }
                });
                $('#rateYo_' + index).rateYo({
                    numStars: 3,
                    maxValue: 3,
                    starWidth: '20px',
                    readOnly: false,
                    rating: 0,
                    fullStar: true,
                });

            }
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
        onEndEdit: function (index, row, changes) {
            var star = $('#rateYo_' + index).rateYo("rating");
            $('#dg_snippets').datagrid('updateRow', {
                index: index,
                row: {star: star}
            });
            g_dg_edit = false;
        },
        columns: [[
                {field: 'ck', checkbox: true},
                {field: 'name', title: T('Nome file'), width: 240, editor: "textbox", formatter: function (value, row, index) {

                        return T(value);//transalte
                    }},
                {field: 'star', title: T('Importanza'), width: 60, formatter: function (value, row, index) {
                        return '<span id="rateYo_' + index + '" style="float:center">' + value + '</span>';
                    }},
            ]]
    });
    //$('#dg_snippets').datagrid('enableFilter');

    $('#bt_add').tooltip({
        content: T('Aggiunge una riga (inserire anche estensione es .php .html)')
    });
    $('#bt_edit').tooltip({
        content: T('Abilita la modifica del codice nella pagina a fianco')
    });


    function set_star_on_edit(index) {
        $('#rateYo_' + index).rateYo("option", "readOnly", false);
    }
    function set_star_readonly() {
        //console.log('set star');
        var rows = $('#dg_snippets').datagrid('getRows');
        for (var i = 0; i < rows.length; i++) {
            var star = rows[i].star;
            $('#rateYo_' + i).rateYo({
                numStars: 3,
                maxValue: 3,
                starWidth: '20px',
                readOnly: true,
                rating: star,
                fullStar: true,
            });

        }
    }
    function click_row() {
        (!g_dg_edit) ? view_file() : false;
    }



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
    $('#bt_upload').linkbutton({text: T('Salva')});
    $('#bt_upload_del').linkbutton({
        text: T('Elimina'),
        onClick: function () {
            var file = $('#dg_snippets').datagrid('getSelected').file;
            $.messager.progress({title: T('cancellazione'), msg: 'Attendere, cancellazione in corso...'});
            $.post('api/delete/uoload/image', {file: file})
                    .done(function (data) {
                        $.messager.progress('close');
                        if (data.success) {
                            $.messager.alert(T('cancellazione'), data.msg, data.title);
                        } else {
                            $.messager.alert('** errore **', data.msg, data.title);
                        }
                    })
                    .fail(function () {
                        $.messager.progress('close');
                        $.messager.alert(T('attenzione'), T('Si è verificato un errore'), 'error');
                    });
        }
    });
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
    /*
    $('#mm').menu('appendItem', {
        text: 'php',
        iconCls: 'icon-ok',
        onclick: function () {
            alert('New Item')
        }
    });
    */
    $('#ss_search').searchbox({
        searcher: function (value, name) {
            var filter_content = $('#bt_filter_content').linkbutton('options').selected;
            $('#dg_snippets').datagrid('options').queryParams = {filter: value, name: name, filter_content: filter_content};
            $('#dg_snippets').datagrid('reload');
        },
        menu: '#mm',
        prompt: T('cerca...')
    });
    $('#bt_filter_content').linkbutton({text: T('Contenuto'), toggle: true});
}

