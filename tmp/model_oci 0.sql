                SELECT
                 A.COLUMN_NAME COL
                 ,A.COLUMN_NAME TITLE
                 ,case A.DATA_TYPE when 'NUMBER' then  'numberbox'
                                   when 'VARCHAR' then  'textbox'
                                   when 'VARCHAR2' then 'textbox'
                                   when 'DATE' then 'datebox'
                                   else A.DATA_TYPE
                end TYPE
                , NVL(B.CONSTRAINT_TYPE,' ') CONSTRAINT_TYPE
                , 0 SKIP
                , case when B.CONSTRAINT_TYPE='PRIMARY_KEY' then 1 else 0 end  HIDE
                , 0 CK
                , 1 EDIT
                , case A.NULLABLE when 'Y' then 1 when 'N' then 0 end REQUIRED
                , 1 SORTABLE
                ,'' WIDTH
                FROM ALL_TAB_COLUMNS A LEFT JOIN
                (
                SELECT
                cols.column_name,
                case cons.constraint_type when 'P' then 'PRIMARY_KEY' when 'R' then 'FOREIGN_KEY' else  cons.constraint_type end CONSTRAINT_TYPE
                FROM all_constraints cons,
                all_cons_columns cols
                WHERE cols.table_name='ABB_CRUD'
                AND cons.constraint_name = cols.constraint_name
                AND cons.owner = cols.owner
                ) B on (A.COLUMN_NAME=b.COLUMN_NAME)
                WHERE table_name='ABB_CRUD'
                ORDER BY A.COLUMN_ID