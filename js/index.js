
function init_app() {
    function addTab(title, url, icon, opt) {
        var new_url;
        new_url = url;
        if ($('#tab').tabs('exists', title)) {
            $('#tab').tabs('select', title);
        } else {
            var content = '<iframe id="iframe_' + title + '" scrolling="yes" frameborder="0"  src="' + new_url + '" style="width:100%;height:99.5%;"></iframe>';
            $('#tab').tabs('add', {
                title: title,
                content: content,
                closable: true,
                pill: true,
                tools: [{
                        iconCls: icon
                    }]
            });
        }
    }
    addTab('welcome', 'welcome.html', 'icon-edit', null);
    //addTab('welcome2', 'welcome.html', 'icon-edit');
    var menu = '<a id="bt_menu">' + T("crea") + '</a>';
    $('#layout_main').layout('panel', 'center').panel({title: 'Easyui Gii - Code Generetor   ' + menu})
    $('#bt_menu').menubutton({
        iconCls: 'icon-edit',
        menu: '#mm1',
        height: '15px',
    });
    $('#dg_create').on('click', function () {
        addTab('datagrid', 'dg_create.html', 'icon-edit', null);
    });

}