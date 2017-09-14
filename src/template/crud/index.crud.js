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
    function myformatter_d_it(date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        var d = date.getDate();
        //return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
        return (d < 10 ? ('0' + d) : d) + '-' + (m < 10 ? ('0' + m) : m) + '-' + y;
    }
    function myparser_d_it(s) {
        if (!s)
            return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0], 10);
        var m = parseInt(ss[1], 10);
        var d = parseInt(ss[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
//return new Date(y, m - 1, d);
            return new Date(d, m - 1, y);
        } else {
            return new Date();
        }
    }
    function mycheck(value, row, index) {
        if (value == 1) {
            return "√"
        } else {
            return '-'
        }
    }

    function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
        //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
        var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
        var CSV = '';
        //Set Report title in first row or line

        CSV += ReportTitle + '\r\n\n';
        //This condition will generate the Label/Header
        if (ShowLabel) {
            var row = "";
            //This loop will extract the label from 1st index of on array
            for (var index in arrData[0]) {

                //Now convert each value to string and comma-seprated
                row += index + ';';
            }

            row = row.slice(0, -1);
            //append Label row with line break
            CSV += row + '\r\n';
        }

        //1st loop is to extract each row
        for (var i = 0; i < arrData.length; i++) {
            var row = "";
            //2nd loop will extract each column and convert it in string comma-seprated
            for (var index in arrData[i]) {
                row += '"' + arrData[i][index] + '";';
            }

            row.slice(0, row.length - 1);
            //add a line break after each row
            CSV += row + '\r\n';
        }

        if (CSV == '') {
            alert("Invalid data");
            return;
        }

        //Generate a file name
        var fileName = "MyReport_";
        //this will remove the blank-spaces from the title and replace it with an underscore
        fileName += ReportTitle.replace(/ /g, "_");
        //Initialize file format you want csv or xls
        var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
        // Now the little tricky part.
        // you can use either>> window.open(uri);
        // but this will not work in some browsers
        // or you will not get the correct file extension

        //this trick will generate a temp <a /> tag
        var link = document.createElement("a");
        link.href = uri;
        //set the visibility hidden so it will not effect on your web-layout
        link.style = "visibility:hidden";
        link.download = fileName + ".csv";
        //this part will append the anchor tag and remove it after automatic click
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    load_dg1();
});
