<?php
// Todo: Change Procedural process to OOP
require_once("../globals/globals.php");
ob_start();

$file = 'GeoIP2-City-CSV.zip'; // Local stored file name, temporary
$path = BASE_PATH . "scripts/";
$target_path = BASE_PATH;


//CONNECT TO DATABASE
$db = mysqli_connect(
	DATABASE_ROLES['default']['host'],
	DATABASE_ROLES['default']['user'],
	DATABASE_ROLES['default']['password'],
	DATABASE_ROLES['default']['database']
);

//FUNCTION TO CLEAN UP FILES AFTER UNPACKING
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

echo "Building City IPv4 and IPv6 data.  Version " . SYSTEM_VERSION . "<br>";

//Get Maxmind Data
exec("wget -O $path$file 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City-CSV&license_key=" . MAXMIND_KEY . "&suffix=zip'");


//THIS WILL EXTACT FILES TO A NEW DIRECTORY scriptsGeoLite2-City-CSV_YYYYMMDD
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

//SCAN FOR DATA FOLDER
$files = scandir ($path);
unlink ("$path" . "/$file");

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