<?php
require_once("../globals/globals.php");
echo "I can see you";
exit;
ob_start();

$file = 'GeoIP2-City-CSV.zip';
$path = BASE_PATH . "scripts/";
$target_path = BASE_PATH;
/*
$db = mysqli_connect(
	DATABASE_ROLES['default']['host'],
	DATABASE_ROLES['default']['user'],
	DATABASE_ROLES['default']['password'],
	DATABASE_ROLES['default']['database']
);
*/

$db = mysqli_connect($db_location,$db_user,$db_pass,$db_database);

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

echo "Getting New Data File<br>";

@exec("wget -O $path$file 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip'");

echo "Unzipping File<br>";

$zip = new ZipArchive;
$res = $zip->open($file);

if ($res === TRUE) {
    // extract it to the path we determined above
    $zip->extractTo($path);
    $zip->close();
    echo "$file extracted to $path <br>";
} else {
    exit("Error with file extraction");
}
echo "Removing Unpacked File<br>";
unlink ("$path" . "/$file");

//SCAN FOR DATA FOLDER
$files = scandir ($path);


foreach ($files as &$test_value)
{
    if(is_dir($test_value) && strlen($test_value) > 3)
    {
        echo "Directory $test_value -- ";
        echo "<br>Dump data files into DB<br>";
        //TRUNCATE EXISTING DATA IN blocks_city
        $result = mysqli_query($db, "TRUNCATE TABLE blocks_city");

        //LOAD DATA INTO blocks_country
        $file_blocks_country = "GeoLite2-City-Blocks-IPv4.csv";
        $query = "LOAD DATA LOCAL INFILE '$path$test_value/$file_blocks_country' INTO TABLE blocks_city FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES";
        $result = mysqli_query($db, $query);


		//TRUNCATE EXISTING DATA IN blocks_city_ipv6
		$result = mysqli_query($db, "TRUNCATE TABLE blocks_city_ipv6");

		//LOAD DATA INTO blocks_country
		$file_blocks_country = "GeoLite2-City-Blocks-IPv6.csv";
		$query = "LOAD DATA LOCAL INFILE '$path$test_value/$file_blocks_country' INTO TABLE blocks_city_ipv6 FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES";
		$result = mysqli_query($db, $query);
















		//TRUNCATE EXISTING DATA IN location_country
        $result = mysqli_query($db, "TRUNCATE TABLE location_city");

        //LOAD DATA INTO location_country
        $file_locations_country = "GeoLite2-City-Locations-en.csv";
        $query = "LOAD DATA LOCAL INFILE '$path$test_value/$file_locations_country' INTO TABLE location_city FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES";
        $result = mysqli_query($db, $query);

        //ALL DONE DUMPING, CLEAN UP THE FILES
        echo "$path$test_value";
        rrmdir ($path.$test_value);

    }
    //echo $test_value;
    echo "<br>";
}

echo "complete!";
?>