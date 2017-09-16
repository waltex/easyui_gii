$(document).ready(function () {
    $('#dg{{n}}_reload').bind('click', function () {
        load_dg1();
    });
    $('#dg1_export').bind('click', function () {
        JSONToCSVConvertor($('#dg1').datagrid('getRows'), "{{T('dati')}}", 'label')
    });
    function load_dg1() {
        //$('#tt').tabs('select', 'Tab{{n}}');
        $('#dg{{n}}').datagrid('uncheckAll');
        $('#dg{{n}}').datagrid('disableFilter');
        $('#dg{{n}}').edatagrid({
            border: false,
            toolbar: '#tb{{n}}',
            title: '√',
            url: 'api{{apiUrl}}/SELECT',
            saveUrl: 'api/XXX',
            updateUrl: 'api/xxx',
            destroyUrl: 'api/xx',
            method: 'post',
            rownumbers: true,
            striped: true,
            fit: true,
            idField: 'ID', /** importante **/
            singleSelect: false,
            remoteSort: false,
            multiSort: false,
            autoRowHeight: false, //For Speed refresh
            editorHeight: 32,
            destroyMsg: {
                norecord: {// when no record is selected
                    title: "{{T('Attenzione')}}",
                    msg: "{{T('Non è stata selezionata nessuna riga')}}"
                },
                confirm: {// when select a row
                    title: "{{T('Conferma')}}",
                    msg: "{{T('Sei sicuro di voler cancellare?')}}"
                }
            },
            onError: function (index, row) {
                $.messager.alert("{{T('Contattare assistenza')}}", row.msg, 'error');
            },
            columns: [[
                    {field: 'SEQ', title: 'Ord', width: '25px', editor: {type: 'numberbox', options: {required: true}}, sortable: true},
                    {field: 'DTIN', title: 'Data Inizio', width: 90, editor: {type: 'datebox', options: {formatter: myformatter_d_it, parser: myparser_d_it, required: true}}, sortable: true},
                    {field: 'DTFI', title: 'Data Fine', width: 90, editor: {type: 'datebox', options: {formatter: myformatter_d_it, parser: myparser_d_it, required: true}}, sortable: true},
                    {field: 'ID_CLONE', title: 'Fase<br>Duplicata', formatter: mycheck},
                ]],
        });
        $('#dg{{n}}').datagrid('enableFilter');
    }

    load_dg1();
});
