$(document).ready(function () {
    $('#dg{{n}}_reload').bind('click', function () {
        load_dg1();
    });
    $('#dg1_export').bind('click', function () {
        JSONToCSVConvertor($('#dg1').datagrid('getRows'), 'dati_fasi', 'label')
    });

    function load_dg1() {
        $('#tt').tabs('select', 'Tab1');
        $('#dg1').datagrid('uncheckAll');

        $('#dg1').datagrid('disableFilter');
        $('#dg1').edatagrid({
            border: false,
            toolbar: '#tb1',
            title: 'Fasi',
            url: 'api/XXX',
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
                    title: 'Attenzione',
                    msg: 'Non è stata selezionata nessuna riga'
                },
                confirm: {// when select a row
                    title: 'Conferma',
                    msg: 'Sei sicuro di voler cancellare?'
                }
            },
            onError: function (index, row) {
                $.messager.alert('Contattare assistenza', row.msg, 'error');
            },
            columns: [[
                    {field: 'SEQ', title: 'Ord', width: '25px', editor: {type: 'numberbox', options: {required: true}}, sortable: true},
                    {field: 'ID_CAT', title: 'Categoria', width: 150,
                        formatter: function (value, row, index) {
                            return row.CATEGORIA
                        }
                        , editor: {
                            type: 'combobox',
                            options: {
                                valueField: 'ID',
                                textField: 'TEXT',
                                method: 'post',
                                url: 'api/combo/cat/fasi/0/1',
                                required: true,
                                panelWidth: 250,
                            }
                        }, sortable: true},
                    {field: 'DTIN', title: 'Data Inizio', width: 90, editor: {type: 'datebox', options: {formatter: myformatter_d_it, parser: myparser_d_it, required: true}}, sortable: true},
                    {field: 'DTFI', title: 'Data Fine', width: 90, editor: {type: 'datebox', options: {formatter: myformatter_d_it, parser: myparser_d_it, required: true}}, sortable: true},
                    {field: 'ID_CLONE', title: 'Fase<br>Duplicata', formatter: mycheck},
                ]],
        });
        $('#dg1').datagrid('enableFilter');
    }

})
