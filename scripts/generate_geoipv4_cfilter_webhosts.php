<?php
/**
 * VERSION 1.0
 * Updated 5/31/2016
 *
 * Version 1.0.1
 * Updated 3/28/2022
 */
// Todo: Change Procedural process to OOP
ob_start();
require_once("../globals/globals.php");
$filename = GENERATED_FILES_DIR . "geoip_cfs_webhost_ipv4.txt";

$mysqli = mysqli_connect(
    DATABASE_ROLES['default']['host'],
    DATABASE_ROLES['default']['user'],
    DATABASE_ROLES['default']['password'],
    DATABASE_ROLES['default']['database']
);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

/*
$mysqli->real_query("SELECT
blocks_isp.network_cidr,
blocks_isp.isp,
blocks_isp.organization
FROM
blocks_isp
WHERE
blocks_isp.isp = 'Google' OR
blocks_isp.organization = 'ColoCrossing' OR
blocks_isp.organization = 'B2 Net Solutions' OR
blocks_isp.organization = 'Privax' OR
blocks_isp.organization = 'Softlayer'
");
*/

echo "Build list of Web Hosting / Proxy Providers<hr>\r\n";

$mysqli->real_query('SELECT provider_name FROM cfs_hosts');
$res = $mysqli->use_result();
//echo "Result set order...\n";

$sql_line = "(";
while ($row = $res->fetch_assoc()) {
    //$line .= "" . $row['provider_name'] . " " . $row['isp'] . "|" . $row['organization'] . "\r\n";
    $sql_line .= '
    "' . $row['provider_name'] . '",';
    //echo $row['provider_name'] . "<br>\r\n";
}
$sql_line = substr($sql_line,0,-1);
$sql_line .= ")";
//echo $sql_line;



$mysqli->real_query('SELECT
blocks_isp.network_cidr,
blocks_isp.isp,
blocks_isp.organization
FROM
blocks_isp
WHERE 
      blocks_isp.isp IN ' . $sql_line . ' 
    OR 
      blocks_isp.organization IN ' . $sql_line  );

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line_count = 0;
$today = date("D M j G:i:s T Y");
$today_timestamp = date("Ymd-His");
$line = "# GEOIP2 ISP AND ORG CREATED $today \r\n\r\n";
$line .= "192.168.0.3/32 CFS $today|$today_timestamp|version" . SYSTEM_VERSION . "\r\n";
/* ENTER MANUAL LINES HERE */
//$line .= "86.96.0.0/14 Emirates Hosting|Emirates Hosting\r\n";
//$line .= "51.15.0.0/17 Dedibox SAS|Dedibox SAS\r\n";
//$line .= "163.172.208.0/20 Dedibox SAS|Dedibox SAS\r\n";
//$line .= "193.36.236.0/22 DediPath|DediPath\r\n";
//$line .= "212.83.128.0/19 Dedibox SAS Scaleway|Dedibox SAS Scaleway\r\n";



while ($row = $res->fetch_assoc()) {
    $line .= "" . $row['network_cidr'] . " " . $row['isp'] . "|" . $row['organization'] . "\r\n";
	++$line_count;
}



//echo $line;
//exit;

//$line .= "23.116.226.35/32 inMotion Group $today|inMotion Group $today\r\n";
//$line .= "3.123.34.76/32 inMotion Group $today|inMotion Group $today\r\n";

$fp = fopen($filename, 'w');
fwrite($fp, $line);
fclose($fp);



echo "completed $line_count records";

