var g_var
function init_app() {
    function addTab(title, url, opt) {
        if ($('#tab').tabs('exists', title)) {
            $('#tab').tabs('select', title);
        } else {
            var content = '<iframe id="iframe_' + title + '" scrolling="yes" frameborder="0"  src="' + url + '" style="width:100%;height:99.5%;"></iframe>';
            var tab = $('#tab').tabs('add', {
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
            g_var = tab;
            /*
            var mb = p.panel('options').tab.find('a.tabs-inner');
            mb.menubutton({
                menu: '#mm_tab',
                iconCls: 'icon-help'
            }).click(function () {
                $('#tt').tabs('select', 2);
            });
            */
        }
    }
    addTab('welcome', 'welcome.html', null);
    //addTab('welcome2', 'welcome.html', 'icon-edit');
    var menu = '<a id="bt_menu">' + T("crea") + '</a>'; //create
    $('#layout_main').layout('panel', 'center').panel({title: 'Easyui Gii - Code Generetor   ' + menu})
    $('#bt_menu').menubutton({
        iconCls: 'icon-edit',
        menu: '#mm_app',
        height: '15px',
    });
    $('#dg_create').on('click', function () {
        addTab('datagrid', 'dg_create.html', null);
    });

}