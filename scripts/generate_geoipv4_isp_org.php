<?php
/**
 * VERSION 1.0
 */
// Todo: Change Procedural process to OOP
ob_start();
require_once("../globals/globals.php");
$filename = GENERATED_FILES_DIR . "geoip_mm_isporg_ipv4.txt";

//CONNECT TO DATABASE
$mysqli = mysqli_connect(
    DATABASE_ROLES['default']['host'],
    DATABASE_ROLES['default']['user'],
    DATABASE_ROLES['default']['password'],
    DATABASE_ROLES['default']['database']
);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}


$mysqli->real_query("SELECT 
blocks_isp.network_cidr,
blocks_isp.isp, 
blocks_isp.organization
FROM 
blocks_isp
WHERE
blocks_isp.network_cidr != 'network'
");

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line_count = 0;
$today = date("D M j G:i:s T Y");
$today_timestamp = date("Ymd-His");

$line = "# GEOIP2v4 ISP AND ORG CREATED $today \r\n\r\n";
$line .= "192.168.0.4/32 $today|$today_timestamp\r\n";
while ($row = $res->fetch_assoc()) {
    $line .= "" . $row['network_cidr'] . " " . $row['isp'] . "|" . $row['organization'] . "\r\n";
	++$line_count;
}


$fp = fopen($filename, 'w');
fwrite($fp, $line);
fclose($fp);



echo "completed $line_count records";

