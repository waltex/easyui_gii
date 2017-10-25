var g_debug; // for debug code to browser
function init_app() {
    //translate menu
    $('#dg_set').html(T('Impostazioni'));//menu settings
    $('#mm_app_gen').html(T('Apri app Generate'));//open app generated
    $('#dg_snippets').html(T('Esempi di codice'));//snippets



    addTab('welcome', 'welcome.html', {iconCls: 'fa fa-user-circle-o fa-lg'});

    var menu = '<a id="bt_menu">' + T("Menu") + '</a>'; //Menu
    $('#layout_main').layout('panel', 'center').panel({title: 'Easyui Gii - Code Generetor 1.0   ' + menu})


     $('#bt_menu').menubutton({
        iconCls: 'fa fa-bars fa-lg',
        menu: '#mm_app',
        height: '15px',
    });

    $('#dg_crud').on('click', function () {
        addTab('crud', 'dg_crud.html', {iconCls: 'fa fa-table fa-lg fa-brown'});
    });
    $('#dg_set').on('click', function () {
        addTab(T('impostazioni'), 'settings.html', {iconCls: 'fa fa fa-cog fa-lg fa-grey'});
    });
    $('#dg_snippets').on('click', function () {
        addTab('snippets', 'snippets.html', {iconCls: 'fa fa-file-code-o fa-lg fa-blue'});
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
                //addTab_sub(T(title_open), url, opt);//new tab clone
                var iconCls = opt_iconCls(opt);
                addTab_sub(add_icon_title(T(title_open), iconCls), url, opt);//new tab clone
            } else {
                $('#tab_app').tabs('select', title_open);
            }
        });
    } else {
        //addTab_sub(T(title_open), url, opt);// new tab
        var iconCls = opt_iconCls(opt);
        addTab_sub(add_icon_title(T(title_open), iconCls), url, opt);// new tab
    }
}

function add_icon_title(title, iconCls) {
    if (iconCls != "") {
        return '<span class="fa ' + iconCls + '"><span style="font: 80% sans-serif;margin-left:2px;color:black">' + title + '</span></span>';
    } else {
        return title;
    }
}

function addTab_sub(title, url, opt) {
    if ($(title)[0] !== undefined) {
        var title_frame = $(title)[0].innerText;
    } else {
        var title_frame = title;
    }
    var content = '<iframe id="iframe_' + title_frame + '" scrolling="yes" frameborder="0"  src="' + url + '" style="width:99%;height:97.3%;padding:0.5%"></iframe>';
    var tab = $('#tab_app').tabs('add', {
        title: title,
        content: content,
        closable: true,
        pill: true,
        cache: false,
        tools: [{
                iconCls: 'icon-mini-refresh',
                handler: function () {
                    var title_name = get_title(title);
                    document.getElementById('iframe_' + title_name).contentDocument.location.reload(true);
                }
            }]
    });
}
// get title and remove span icon
function get_title(title) {
    if ($(title)[0] !== undefined) {
        return  $(title)[0].innerText;
    } else {
        return title;
    }
}

function opt_iconCls(opt) {
    if (opt != null) {
        if (opt.hasOwnProperty('iconCls')) {
            return opt.iconCls;
        }
    } else {
        return '';
    }
}

