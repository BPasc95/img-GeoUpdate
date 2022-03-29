<?php

/*
 * Get device detect file from 51degrees
 */
// Todo: Change Procedural process to OOP
require_once("../globals/globals.php");
ob_start();

//https://distributor.51degrees.com/api/v2/download?LicenseKeys=8EVAAAEAQAAASAJABSAGAWEUMXPW48FBKS3BP2ZJDL8QFKQJVNWNSJAMC4JJ72Q3JFF8QBD26A52W2QLQ7Z47YB&Type=HashTrieV34&Download=True
//https://distributor.51degrees.com/api/v2/download?LicenseKeys=8EVAAAEAQAAASAJABSAGAWEUMXPW48FBKS3BP2ZJDL8QFKQJVNWNSJAMC4JJ72Q3JFF8QBD26A52W2QLQ7Z47YB&Type=20&Download=True&Product=2
//https://distributor.51degrees.com/api/v2/download?LicenseKeys=8EVAAAEAQAAASAJABSAGAWEUMXPW48FBKS3BP2ZJDL8QFKQJVNWNSJAMC4JJ72Q3JFF8QBD26A52W2QLQ7Z47YB&Type=1&Download=True&Product=2
$license_key = DEVICE_51D_KEY;
$license_filetype = DEVICE_51D_FILETYPE;
$file = DEVICE_51D_FILENAME_PACKAGE;
$temp_path = TEMP_DIR;
$target_path = GENERATED_FILES_DIR;
$target_filename = DEVICE_51D_FILENAME_TARGET;
$path_backup = DEVICE_51D_BACKUP_DIR;


$file_datestamp = date('Ymd-His');

//echo(          "wget -O $temp_path$file 'https://distributor.51degrees.com/api/v2/download?LicenseKeys=$license_key&Type=$license_filetype&Download=True'");
exec("wget -O $temp_path$file 'https://distributor.51degrees.com/api/v2/download?LicenseKeys=$license_key&Type=$license_filetype&Download=True'");

//exec("wget -N 'https://www.inclick.net/download/geodata/test/51Degrees-PremiumV3.4.trie.gz'  -O $temp_path$file ");
//echo ("wget -N 'https://www.inclick.net/download/geodata/test/51Degrees-PremiumV3.4.trie.gz'  -O $temp_path$file ");

$filesize = filesize($temp_path.$file);

echo "Compressed Filesize: " . $filesize . "<br>";
if($filesize > 0) {
    exec("gunzip -c $temp_path$file > $target_path$target_filename");
}
rename ($temp_path.$file, $path_backup.$file.'-'.$file_datestamp);

$uncompressed_filesize = filesize($target_path.$target_filename);

echo "Uncompressed Filesize: " . $uncompressed_filesize . "<br>";
/*
$zip = new ZipArchive;
$res = $zip->open($temp_path.$file);
$zip->extractTo($temp_path);
$zip->close();
*/