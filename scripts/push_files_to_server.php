<?php

require_once("../globals/globals.php");
/**
 * This script will push the generated files to the target host
 */
/*
$target_host = "169.61.83.54";
$target_folder = "/";
$target_user = "bing";
$target_pass = "testing1";
*/

//push to data.trafficvalidate.com
$target_host = "localhost";
$target_folder = "/httpdocs/download/geodata/";
$target_user = "data.trafficvalidate";
$target_pass = "2Ht4Voj&kcGztbb3";


//$source_folder = "/var/www/vhosts/pvt.inclickmanager.inmotiongroup.net/pvt.geoip.inmotiongroup.net/generated/";
$source_folder = GENERATED_FILES_DIR;
$source_files = array(
    "geoip_cfs_webhost_ipv4.txt" => '1',
    "geoip_cfs_webhost_ipv6.txt" => '1',
    "geoip_mm_country_ipv4.txt" => '1',
    "geoip_mm_country_ipv6.txt" => '1',
    "geoip_mm_isporg_ipv4.txt" => '1',
    "geoip_mm_isporg_ipv6.txt" => '1',
    "geoip_mm_region_city_combined_ipv4.txt" => '1',
    "geoip_mm_region_city_combined_ipv6.txt" => '1',
    "51Degrees.trie" => '2',
);

//$source_test_file = "geoip_cfs_webhost_ipv6.txt";


//Connect to server
$conn_id = ftp_connect($target_host);
// login with username and password
$login_result = ftp_login($conn_id, $target_user, $target_pass);

ftp_pasv($conn_id, true);

// upload a file


foreach ($source_files as $key => $value) {
    echo $key . "<br>";
    //echo $value . "<br>";


    $r_file = $target_folder . $key;
    $l_file = $source_folder . $key;


    if (ftp_put($conn_id, $r_file, $l_file, $value)) {
        echo "successfully uploaded $l_file <br>\n";
        $r_filesize = ftp_size($conn_id, $r_file);
        $l_filesize = filesize($l_file);
        echo "size of $r_file is $r_filesize bytes on remote, $l_filesize locally<br>\n";
        if ($r_filesize == $l_filesize) {
            echo "File Size Matched<br>\n";
        }

    } else {
        echo "There was a problem while uploading $l_file <br>\n";

    }

}
// close the connection
ftp_close($conn_id);
