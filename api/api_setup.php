<?php
$APP_NAME = 'SCRIPT';

$db1_user = 'root';
$db1_psw = '';
$db1_mysql = 'mysql:host=localhost;dbname=test';

//vedi SID in fondo
//$db2_explorer = 'oci:dbname=DB11G;charset=UTF8';
$db2_user = 'TEMSI';
$db2_psw = 'TEMSI';


//$db3_explorer ='oci:dbname=DB11G;charset=UTF8';
$db3_user = 'EXPLORER';
$db3_psw = 'EXPLORER';

$db4_user = 'CENPROD';
$db4_psw = 'CENPROD';
//$db4_user = 'CENTEST';
//$db4_psw = 'CENTEST';
//$db4_GOLD = $GOLDPROD; Vedi in fondo

$db5_user = 'STKPROD';
$db5_psw = 'STKPROD';

//Driver={SQL Server};Server=192.168.1.9;Database=abb;

$db6_abb = 'Driver={SQL Server};Server=192.168.1.9;Database=abb;';
$db6_camidb = 'Driver={SQL Server};Server=192.168.1.9;Database=camidb;';
$db6_sqlserver = $db6_camidb;
$db6_user = 'coge';
$db6_psw = 'carlotta';


$db7_abb = 'Driver={SQL Server};Server=192.168.1.9;Database=abb;charset=UTF-8';
$db7_camidb = 'Driver={SQL Server};Server=192.168.1.9;Database=camidb;charset=UTF-8';
$db7_sqlserver = $db7_camidb;
$db7_user = 'coge';
$db7_psw = 'carlotta';


$db8_firebird = 'Driver={SQL Server};Server=192.168.1.9;Database=camidb;charset=UTF-8';
$db8_user = 'coge';
$db8_psw = 'carlotta';

$db9_inaz = 'Driver={SQL Server};Server=192.168.20.136;Database=PORTALE;charset=UTF-8;';
$db9_user = 'userHR';
$db9_psw = 'iride';

$ip_active_directory = "192.168.20.101"; //old 192.168.1.101

$debug = true;
$debug_log_mb = 10; //max megabyte logs, -1 no limite
$api_log_mb = 10; //max megabyte logs, -1 no limite
$error_log_mb = 20; //max megabyte logs, -1 no limite
//$var_k = 7; // x Sql

$mail_log = "walter.cardelli@labbondanza.it";


//api mailer, ostituisce la funzione nativa "mail" con "mail2" di php usando api mailer;
//mittente predefinito,
$mailer_from = "ced@labbondanza.it";
$mailer_Host = 'smtp.office365.com'; //smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
$mailer_SMTPAuth = true;                                     // Enable SMTP authentication
$mailer_Username = 'alertprg@labbondanza.it';                // SMTP username
$mailer_Password = 'Smtp.Acce$$1';                           // SMTP password
$mailer_SMTPSecure = null; //'tls';                          // Enable TLS encryption, `ssl` also accepted
$mailer_Port = 25;                                           // TCP port to connect to
$mailer_SMTPDebug = null; //3 debug


$explorer = '
  (DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.1.27)(PORT = 1522))
    )
    (CONNECT_DATA =
      (SID = ora12c)
      (SERVER = DEDICATED)
    )
  )';

$GOLDPROD = '
  (DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.20.46)(PORT = 1521))
    )
    (CONNECT_DATA =
      (SID = GOLDPROD)
      (SERVER = DEDICATED)
    )
  )';


$GOLDTEST = '
  (DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.20.42)(PORT = 1521))
    )
    (CONNECT_DATA =
      (SID = GOLDTEST)
      (SERVER = DEDICATED)
    )
  )';
$db4_GOLD = $GOLDPROD;
//$db4_GOLD = $GOLDTEST;
$db5_STOCK = $GOLDPROD;


$DB11G = '
  (DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.1.27)(PORT = 1521))
    )
    (CONNECT_DATA =
      (SID = DB11G)
      (SERVER = DEDICATED)
    )
  )';

