<?php

/**
 * Crud  generate
 */
 function {{ApiFnName}} ($command) {
 try {
        $app = Slim\Slim::getInstance();
        include 'api_setup.php';

        if ($command == 'SELECT') {

            $sql = "
                {{sql|raw}}
                ";

            if ($debug) {
                error_log(LogTime() . ' Sql, test crud: ' . PHP_EOL . $sql . PHP_EOL, 3, 'debug.log');
            }

            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $db = oci_parse($conn, $sql);
            $rs = oci_execute($db);


            oci_fetch_all($db, $data, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);

            $app->response()->body(json_encode($data));

    }

        if ($command == 'DELETE') {
//ATTENZIONE SOLO IN  QUESTO CASO MI RITORNA MINUSCOLO
            $ID = $app->request->params('id'); // Param from Post user

            $sql = "DELETE FROM ABB_MAG_CATEGORIE WHERE ID IN ($ID)";

            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $db = oci_parse($conn, $sql);

            //$rs = oci_execute($db);
            $app->render(200, ['success' => true, 'rows affected' => oci_num_rows($db)]);
        }

        if ($command == 'UPDATE') {

            $ID = $app->request->params('ID'); // Param from Post user
            $NOME_CATEGORIA = $app->request->params('NOME_CATEGORIA'); // Param from Post user

            $parm_sql = [
                ':ID' => $ID,
                ':NOME_CATEGORIA' => $NOME_CATEGORIA,
            ];

            $sql = "
                     UPDATE ABB_MAG_CATEGORIE SET NOME_CATEGORIA=:NOME_CATEGORIA  WHERE ID=:ID
                 ";

            if ($debug) {
                if (isset($parm_sql) & isset($sql)) {
                    error_log(LogTime() . 'update ABB_MAG_ANA, sql: ' . PHP_EOL . print_r($parm_sql, true) . PHP_EOL . $sql . PHP_EOL, 3, 'debug.log');
                }
            }

            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $db = oci_parse($conn, $sql);
            oci_bind_by_name($db, ":ID", $ID, -1);
            oci_bind_by_name($db, ":NOME_CATEGORIA", $NOME_CATEGORIA, -1);

            //$rs = oci_execute($db);

            $data_r = [
                'ID' => $ID, 'NOME_CATEGORIA' => $NOME_CATEGORIA
            ];
            $app->response()->body(json_encode($data_r));
        }

        if ($command == 'INSERT') {

            $NOME_CATEGORIA = $app->request->params('NOME_CATEGORIA'); // Param from Post user

            $sql = "
                        DECLARE
                        ID_SEQ NUMBER(15,0) := NULL;
                        BEGIN
                            SELECT ABB_MAG_CATEGORIE_SEQ.NEXTVAL INTO ID_SEQ FROM DUAL;
                            INSERT INTO ABB_MAG_CATEGORIE (ID, NOME_CATEGORIA, DT_INS)
                            VALUES (ID_SEQ, :NOME_CATEGORIA, SYSDATE)
                            RETURNING ID_SEQ  INTO :ID;
                        END;
                    ";


            $parm_sql = [
                ':NOME_CATEGORIA' => $NOME_CATEGORIA,
            ];

            if ($debug) {
                if (isset($parm_sql) & isset($sql)) {
                    error_log(LogTime() . 'insert ABB_MAG_CATEGORIE, sql: ' . PHP_EOL . print_r($parm_sql, true) . PHP_EOL . $sql . PHP_EOL, 3, 'debug.log');
                }
            }


            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $db = oci_parse($conn, $sql);
            oci_bind_by_name($db, ":ID", $ID, OCI_B_ROWID);
            oci_bind_by_name($db, ":NOME_CATEGORIA", $NOME_CATEGORIA, -1);

            $rs = oci_execute($db);
            $data_r = [
                'ID' => $ID, 'NOME_CATEGORIA' => $NOME_CATEGORIA
            ];
            $app->response()->body(json_encode($data_r));
        }
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . ' errore - crud ABB_MAG_CATEGORIE: ' . $e->getMessage() . PHP_EOL, 3, 'error.log');
    }
}
