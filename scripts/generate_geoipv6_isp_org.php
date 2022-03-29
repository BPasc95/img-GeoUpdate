<?php
/**
 * VERSION 1.0
 */

// Todo: Change Procedural process to OOP
ob_start();
require_once("../globals/globals.php");
$filename = GENERATED_FILES_DIR . "geoip_mm_isporg_ipv6.txt";

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
blocks_isp_ipv6.network_cidr,
blocks_isp_ipv6.isp, 
blocks_isp_ipv6.organization
FROM 
blocks_isp_ipv6
WHERE
blocks_isp_ipv6.network_cidr != 'network'
");

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line_count = 0;
$today = date("D M j G:i:s T Y");
$line = "# GEOIP2 IPV6 ISP AND ORG CREATED $today \r\n\r\n";
while ($row = $res->fetch_assoc()) {
	$line .= "" . $row['network_cidr'] . " " . $row['isp'] . "|" . $row['organization'] . "\r\n";
	++$line_count;
}

$fp = fopen($filename, 'w');
fwrite($fp, $line);
fclose($fp);



echo "completed $line_count records";

