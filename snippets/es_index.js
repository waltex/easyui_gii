var g_debug // for debug code to browser
function init_app() {
    //translate menu
    $('#dg_set').html(T('Impostazioni'));//menu settings
    $('#mm_app_gen').html(T('Apri app Generate'));//Apri applicazioni generate


    addTab('welcome', 'welcome.html', null);

    var menu = '<a id="bt_menu">' + T("Menu") + '</a>'; //Menu
    $('#layout_main').layout('panel', 'center').panel({title: 'Easyui Gii - Code Generetor 1.0   ' + menu})


     $('#bt_menu').menubutton({
        iconCls: 'icon-edit',
        menu: '#mm_app',
        height: '15px',
    });

    $('#dg_crud').on('click', function () {
        addTab('crud', 'dg_crud.html', null);
    });
    $('#dg_set').on('click', function () {
        addTab(T('impostazioni'), 'settings.html', null);
    });
}
function addTab(title, url, opt) {
    var title_open;
    title_open = title;
    if ($('#tab_app').tabs('exists', title)) {
        //check if exists, and message confirm to create new tab
        var msg = $.messager.confirm(T('Nuova Pagina'), T('Già è presente, vuoi aprirne una nuova?'), function (r) {
            if (r) {
                var new_title = false;
                var i = 1;
                while (!new_title) {
                    i++;
                    title_open = title + "_" + i;
                    (!$('#tab_app').tabs('exists', title_open)) ? new_title = true : false;
                }
                addTab_sub(T(title_open), url, opt);//new tab clone
            } else {
                $('#tab_app').tabs('select', title_open);
            }
        });
    } else {
        addTab_sub(T(title_open), url, opt);// new tab
    }
}
function addTab_sub(title, url, opt) {
    var content = '<iframe id="iframe_' + title + '" scrolling="yes" frameborder="0"  src="' + url + '" style="width:100%;height:99.5%;"></iframe>';
    var tab = $('#tab_app').tabs('add', {
        title: title,
        content: content,
        closable: true,
        pill: true,
        cache: false,
        tools: [{
                iconCls: 'icon-mini-refresh',
                handler: function () {
                    document.getElementById('iframe_' + title).contentDocument.location.reload(true);
                }
            }]
    });
}
