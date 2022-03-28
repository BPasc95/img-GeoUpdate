<?php
/**
 * VERSION 1.0
 * Updated 5/31/2016
 */
// Todo: Change Procedural process to OOP
ob_start();
require_once("../globals/globals.php");

$filename = GENERATED_FILES_DIR . "geoip_cfs_webhost_ipv6.txt";

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
$mysqli->real_query('SELECT provider_name FROM cfs_hosts ');
$res = $mysqli->use_result();

$sql_line = "(";
while ($row = $res->fetch_assoc()) {
    $sql_line .= '
    "' . $row['provider_name'] . '",';
}
$sql_line = substr($sql_line,0,-1);
$sql_line .= ")";


$mysqli->real_query('SELECT
blocks_isp_ipv6.network_cidr,
blocks_isp_ipv6.isp,
blocks_isp_ipv6.organization
FROM
blocks_isp_ipv6
WHERE 
      blocks_isp_ipv6.isp IN ' . $sql_line . ' 
    OR 
      blocks_isp_ipv6.organization IN ' . $sql_line  );


$res = $mysqli->use_result();
//echo "Result set order...\n";
$line_count = 0;
$today = date("D M j G:i:s T Y");

$today_timestamp = date("Ymd-His");
$line = "# GEOIP2 ISP AND ORG IPV6 CREATED $today \r\n\r\n";
/* ENTER MANUAL LINES HERE */
$line .= "2001:bc8:1800::/38 Scaleway|Scaleway\r\n";
$line .= "0000:0000:0000:0000:0000:0000:0000:0002/128 $today|$today_timestamp\r\n";
while ($row = $res->fetch_assoc()) {
	$line .= "" . $row['network_cidr'] . " " . $row['isp'] . "|" . $row['organization'] . "\r\n";
	++$line_count;
}


//echo $line;
//exit;

$fp = fopen($filename, 'w');
fwrite($fp, $line);
fclose($fp);



echo "completed $line_count records";

