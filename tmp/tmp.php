<?php

$combo1 = combo_ABB_CRUD_COMBO();
$key = array_search_multi($COMBO, "ID", $combo1);
$COMBO_DESC = $combo1[$key]["DESCRI"];
?>
<script>
                onAfterEdit: function (index, row) {
                $(this).edatagrid('updateRow', {
                    index: index,
            row: {COMBO_DESC: row.COMBO_DESC}
    });
    },
                    {field: 'COMBO', title: 'COMBO',
                        formatter: function (value, row, index) {
                            return row.COMBO_DESC;
                                },
                                editor: {type: 'combobox',
                                        options: {
                                        valueField: 'ID',
                                                textField: 'DESCRI',
                                                method: 'get',
                                                url: 'api/data/combo_ABB_CRUD_COMBO.json',
                                                required: true,
                                                panelWidth: 250,
                                                onSelect: function (record) {
                                                var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                                var row = $('#dg1').datagrid('getRows')[index];
                                                row['COMBO_DESC'] = record.DESCRI
                                                },
                                        }},
                                sortable: true, },
</script>