<?php
/**
 * VERSION 1.0
 */

// Todo: Change Procedural process to OOP
require_once("../globals/globals.php");
ob_start();

$filename = GENERATED_FILES_DIR . "geoip_mm_country_ipv4.txt";

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

$sql_command = ("SELECT 
blocks_country.network_cidr,
blocks_country.geoname_id, 
blocks_country.registered_country_geoname_id, 
blocks_country.is_anonymous_proxy, 
location_country.country_iso_code
FROM 
blocks_country, 
location_country 
WHERE 
blocks_country.geoname_id = location_country.geoname_id AND
blocks_country.network_cidr != 'network' 

");


$mysqli->real_query($sql_command);

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line_count = 0;
$today = date("D M j G:i:s T Y");
$today_timestamp = date("Ymd-His");
$line = "# GEOIP2v4 COUNTRY CREATED $today \r\n\r\n";
$line .= "192.168.0.1/32 $today|$today_timestamp|version " . SYSTEM_VERSION . "\r\n";
while ($row = $res->fetch_assoc()) {
	
    $line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['is_anonymous_proxy'] . "\r\n";
	++$line_count;
}

$fp = fopen($filename, 'w');
fwrite($fp, $line);
fclose($fp);

/**
 * GET ALL ANONYMOUS PROXIES
 */

$sql_command = ("SELECT 
blocks_country.network_cidr,
blocks_country.geoname_id, 
blocks_country.registered_country_geoname_id, 
blocks_country.is_anonymous_proxy, 
location_country.country_iso_code
FROM 
blocks_country, 
location_country 
WHERE 
blocks_country.registered_country_geoname_id = location_country.geoname_id AND
blocks_country.network_cidr <> 'network' AND
blocks_country.is_anonymous_proxy = 1
AND
blocks_country.network_cidr != 'network' 

");

$mysqli->real_query($sql_command);

$res = $mysqli->use_result();
$line = "";
while ($row = $res->fetch_assoc()) {
	
    $line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['is_anonymous_proxy'] . " #ANO\r\n";
	++$line_count;
}

$fp = fopen($filename, 'a+');
fwrite($fp, $line);
fclose($fp);

/**
 * GET ALL SATELLITE  MAPPED IPs
 */

$sql_command = ("SELECT 
blocks_country.network_cidr,
blocks_country.geoname_id, 
blocks_country.registered_country_geoname_id, 
blocks_country.is_satellite_provider,
location_country.country_iso_code
FROM 
blocks_country, 
location_country 
WHERE 
blocks_country.registered_country_geoname_id = location_country.geoname_id AND
blocks_country.network_cidr <> 'network' AND
blocks_country.is_satellite_provider = 1 and
blocks_country.network_cidr != 'network'
");

$mysqli->real_query($sql_command);

$res = $mysqli->use_result();
$line = "";
while ($row = $res->fetch_assoc()) {
	
    $line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|0 #SAT\r\n";
	++$line_count;
}

$fp = fopen($filename, 'a+');
fwrite($fp, $line);
fclose($fp);



/**
 * GET ALL UNKNOWN COUNTRY MAPPED IPs
 */

$sql_command = ("SELECT 
blocks_country.network_cidr,
blocks_country.geoname_id, 
blocks_country.registered_country_geoname_id, 
blocks_country.is_anonymous_proxy
FROM 
blocks_country
WHERE 
geoname_id = 0 AND 
registered_country_geoname_id = 0 AND 
blocks_country.network_cidr != 'network'
");

$mysqli->real_query($sql_command);

$res = $mysqli->use_result();
$line = "";
while ($row = $res->fetch_assoc()) {
	
    $line .= "" . $row['network_cidr'] . " XX|" . $row['is_anonymous_proxy'] . " #UNK\r\n";
	++$line_count;
}

$fp = fopen($filename, 'a+');
fwrite($fp, $line);
fclose($fp);



/*
$row = $result->fetch_assoc();
echo htmlentities($row['_message']);
*/

echo "completed $line_count records";


//echo nl2br($line);

