<?php
/**
 * Created by PhpStorm.
 * User: Bing2
 *
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);

date_default_timezone_set('America/Detroit');
define ("BASE_PATH","/" );
define ("TEMP_DIR", BASE_PATH . "temp/");
define ("GENERATED_FILES_DIR", BASE_PATH . "genearted/");


define ("DEVICE_51D_DOWNLOAD", '');
define ("DEVICE_51D_KEY", '');
define ("DEVICE_51D_FILETYPE", '');
define ("DEVICE_51D_FILENAME_PACKAGE", '');
define ("DEVICE_51D_FILENAME_TARGET", '');
define ("DEVICE_51D_BACKUP_DIR", '');

define ("MAXMIND_KEY",'');
define ("MAXMIND_UID",'');


define ('DATABASE_ROLES', array(
    'host' => '',
    'user' => '',
    'password' => '',
    'database' => ''
));

include BASE_PATH . "globals/version.php";