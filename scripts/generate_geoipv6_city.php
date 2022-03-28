<?php
/**
 * VERSION 1.0
 */
// Todo: Change Procedural process to OOP
ob_start();
require_once("../globals/globals.php");
$filename = BASE_PATH . "/generated/geoip_mm_region_city_combined_ipv6.txt";

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
blocks_city_ipv6.network_cidr,
blocks_city_ipv6.geoname_id, 
blocks_city_ipv6.registered_country_geoname_id, 
location_city.country_iso_code,
location_city.country_name,
location_city.continent_code,
location_city.continent_name,
location_city.subdivision_1_name,
location_city.subdivision_1_iso_code,
location_city.subdivision_2_name,
location_city.subdivision_2_iso_code,
location_city.metro_code,
location_city.city_name
FROM 
blocks_city_ipv6, 
location_city 
WHERE blocks_city_ipv6.geoname_id = location_city.geoname_id 
");

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line_count = 0;
$today = date("D M j G:i:s T Y");
$today_timestamp = date("Ymd-His");
$line = "# GEOIP2 IPV6 CITY CREATED $today \r\n\r\n";
$line .= "0000:0000:0000:0000:0000:0000:0000:0003/128 $today|$today_timestamp|version " . SYSTEM_VERSION . "\r\n";
while ($row = $res->fetch_assoc()) {
	$line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['country_name'] . "|" . $row['continent_code'] . "|" . $row['continent_name'] . "|" . $row['subdivision_1_name'] . "|" . $row['subdivision_1_iso_code'] . "|" . $row['subdivision_2_name'] . "|" . $row['subdivision_2_iso_code'] . "|" . $row['city_name'] . "|" . $row['metro_code'] . "\r\n";
	++$line_count;
}

$fp = fopen($filename, 'w');
fwrite($fp, $line);
fclose($fp);



/**
 * GET ALL ANONYMOUS PROXIES
 */


$mysqli->real_query("SELECT 
blocks_city_ipv6.network_cidr,
blocks_city_ipv6.geoname_id, 
blocks_city_ipv6.registered_country_geoname_id, 
location_city.country_iso_code,
location_city.subdivision_1_name,
location_city.metro_code,
location_city.city_name
FROM 
blocks_city_ipv6, 
location_city 
WHERE 
blocks_city_ipv6.registered_country_geoname_id = location_city.geoname_id AND 
blocks_city_ipv6.network_cidr != 'network' AND
blocks_city_ipv6.is_anonymous_proxy = 1

");

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line = "";
while ($row = $res->fetch_assoc()) {
//    $line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['continent_code'] . "|" . $row['subdivision_1_name'] . "|" . $row['subdivision_2_name'] . "|" . $row['city_name'] . "|" . $row['metro_code'] . " #ANO\r\n";
	$line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['country_name'] . "|" . $row['continent_code'] . "|" . $row['continent_name'] . "|" . $row['subdivision_1_name'] . "|" . $row['subdivision_1_iso_code'] . "|" . $row['subdivision_2_name'] . "|" . $row['subdivision_2_iso_code'] . "|" . $row['city_name'] . "|" . $row['metro_code'] . "\r\n";

	++$line_count;
}
$fp = fopen($filename, 'a+');
fwrite($fp, $line);
fclose($fp);


/**
 * GET ALL SATELLITE  MAPPED IPs
 */
$mysqli->real_query("SELECT 
blocks_city_ipv6.network_cidr,
blocks_city_ipv6.geoname_id, 
blocks_city_ipv6.registered_country_geoname_id, 
location_city.country_iso_code,
location_city.subdivision_1_name,
location_city.metro_code,
location_city.city_name
FROM 
blocks_city_ipv6, 
location_city 
WHERE 
blocks_city_ipv6.registered_country_geoname_id = location_city.geoname_id AND 
blocks_city_ipv6.network_cidr != 'network' AND
blocks_city_ipv6.is_satellite_provider = 1

");

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line = "";
while ($row = $res->fetch_assoc()) {
	$line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['continent_code'] .  "|" . $row['subdivision_1_name'] . "|" . $row['subdivision_2_name'] . "|" . $row['city_name'] . "|" . $row['metro_code'] . " #SAT\r\n";
	//$line .= "" . $row['network_cidr'] . " " . $row['country_iso_code'] . "|" . $row['country_name'] . "|" . $row['continent_code'] . "|" . $row['continent_name'] . "|" . $row['subdivision_1_name'] . "|" . $row['subdivision_1_iso_code'] . "|" . $row['subdivision_2_name'] . "|" . $row['subdivision_2_iso_code'] . "|" . $row['city_name'] . "|" . $row['metro_code'] . "\r\n";

	++$line_count;
}
$fp = fopen($filename, 'a+');
fwrite($fp, $line);
fclose($fp);





/**
 * GET ALL UNKNOWN COUNTRY MAPPED IPs
 */


$mysqli->real_query("SELECT 
blocks_city_ipv6.network_cidr,
blocks_city_ipv6.geoname_id, 
blocks_city_ipv6.registered_country_geoname_id
FROM 
blocks_city_ipv6
WHERE 
blocks_city_ipv6.registered_country_geoname_id = 0 AND 
blocks_city_ipv6.geoname_id = 0 AND
blocks_city_ipv6.network_cidr != 'network' 
");

$res = $mysqli->use_result();
//echo "Result set order...\n";
$line = "";
while ($row = $res->fetch_assoc()) {
	$line .= "" . $row['network_cidr'] . " ZZ|||||| #UNK\r\n";
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

