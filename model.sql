WITH COL_CONSTRAINT AS (
                SELECT
                C.TABLE_NAME
                ,C.COLUMN_NAME
                ,CASE D.CONSTRAINT_TYPE WHEN 'P' THEN 'PRIMARY_KEY' WHEN 'R' THEN 'FOREIGN_KEY' ELSE  D.CONSTRAINT_TYPE END CONSTRAINT_TYPE
                --PK TABLE EXTERNAL
                 ,(
                     SELECT F.TABLE_NAME FROM ALL_CONS_COLUMNS F WHERE OWNER=USER
                     AND F.OWNER=USER AND F.CONSTRAINT_NAME=D.R_CONSTRAINT_NAME
                 ) NAME_TABLE_EXT
                 ,(
                                 SELECT F.COLUMN_NAME FROM ALL_CONS_COLUMNS F WHERE OWNER=USER
                                 AND F.OWNER=USER AND F.CONSTRAINT_NAME=D.R_CONSTRAINT_NAME
                  ) PK_TABLE_EXT
                FROM

                ALL_CONSTRAINTS D INNER JOIN ALL_CONS_COLUMNS C
                ON  (D.CONSTRAINT_NAME =C.CONSTRAINT_NAME AND D.OWNER =C.OWNER AND C.OWNER=USER AND  C.TABLE_NAME='ABB_CRUD')
)
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
            ,B.NAME_TABLE_EXT
            ,B.PK_TABLE_EXT
            ,

FROM ALL_TAB_COLUMNS A
LEFT JOIN COL_CONSTRAINT B ON ( A.COLUMN_NAME=B.COLUMN_NAME)
WHERE A.TABLE_NAME='ABB_CRUD' AND A.OWNER=USER
