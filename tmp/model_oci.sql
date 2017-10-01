                SELECT cols.table_name,
                cols.column_name,
                cons.constraint_type
                FROM all_constraints cons,
                all_cons_columns cols
                WHERE cols.table_name='ABB_CRUD'
                --and cons.constraint_type = 'P'   -- P primary , R forenk
                AND cons.constraint_name = cols.constraint_name
                AND cons.owner = cols.owner
                go
                SELECT * FROM all_cons_columns WHERE table_name='ABB_CRUD'
                go
                SELECT * FROM all_tab_columns 
                WHERE table_name='ABB_CRUD'
                go
                select * from all_tab_columns
                go
                SELECT 
                cols.column_name,
                case cons.constraint_type when 'P' then 'PRIMARY' when 'R' then 'FORENK' else  cons.constraint_type end CONSTRAINT_TYPE
                        
                FROM all_constraints cons,
                all_cons_columns cols
                WHERE cols.table_name='ABB_CRUD'
                --and cons.constraint_type = 'P'   -- P primary , R forenk
                AND cons.constraint_name = cols.constraint_name
                AND cons.owner = cols.owner
                go
                SELECT
                 A.COLUMN_NAME COL
                 ,case             when A.DATA_TYPE="NUMBER" then  "numberbox" 
                                   when A.DATA_TYPE="VARCHAR" then  "textbox"  
                                   when A.DATA_TYPE="VARCHAR2" then "textbox"  
                                   when A.DATA_TYPE="DATE" then "datebox1"
                                   --else A.DATA_TYPE
                end TYPE1
                
                , B.CONSTRAINT_TYPE
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
                
                