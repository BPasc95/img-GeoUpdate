#!/usr/bin/env bash
# This script will retrieve the latest GeoIP and CFS Webhosts files for the balancer.
# ********************THIS IS DEPLOYED ON THE REMOTE SERVER ****************************

CFS_ID="00000" # If this is a remote client deployment, insert their client ID here
CFS_KEY="745EA2BC9D4CAD1F709BE7B918067FF1EC830CC10922AF0F271228751EEA40F9" # Key for the client
GEOIP_TARGET_FOLDER="/home/geodata/"
GEOIP_SOURCE_LOCATION="https://download-02.fbrknet.com/download/geodata/"
GEOIP_FILES=(
	"geoip_mm_region_city_combined_ipv6.txt"
	"geoip_mm_country_ipv6.txt"
	"geoip_mm_isporg_ipv6.txt"
	"geoip_cfs_webhost_ipv6.txt"
	"geoip_mm_region_city_combined_ipv4.txt"
	"geoip_mm_country_ipv4.txt"
	"geoip_mm_isporg_ipv4.txt"
	"geoip_cfs_webhost_ipv4.txt"
	"51Degrees.trie"
)

#timestamping function
timestamp() {
  date +"%F_%T"
}

PROCESS_MERGED_DATA=0
#LOOP THROUGH EACH FILE TO SEE IF IT IS NEW ON THE REMOTE SERVER
for filename in "${GEOIP_FILES[@]}"
do
	#echo "wget -N -P ${GEOIP_TARGET_FOLDER} ${GEOIP_SOURCE_LOCATION}${filename} 2>&1 | grep  'not retrieving' | wc -l)"
	OUT_1="$(wget --header "X-CFS-ID: $CFS_ID" --header "X-CFS-KEY: $CFS_KEY" -N -P ${GEOIP_TARGET_FOLDER} ${GEOIP_SOURCE_LOCATION}${filename} 2>&1 | grep  'not retrieving' | wc -l)"
	#echo "Result = ${OUT_1} ---"
	if [ $OUT_1 -eq 1 ];then
	   # remote file is not changed, do nothing
	   echo "File ${filename} is Current!"
	else
		# remote file is new, file updated locally.  Set
		# flag to merge data and restart balancer
	   echo "File ${filename} Updated!"
	   PROCESS_MERGED_DATA=1
	fi
done


echo "Process Merging Status: ${PROCESS_MERGED_DATA} $(timestamp)"
echo $(timestamp) > ${GEOIP_TARGET_FOLDER}update_last_check.txt
if [ $PROCESS_MERGED_DATA -eq 0 ];then
	echo "No files to merge.  Done."

else
	echo "File changes found, recompile data and restart balancer."
	cat ${GEOIP_TARGET_FOLDER}geoip_mm_country_ipv4.txt ${GEOIP_TARGET_FOLDER}geoip_mm_country_ipv6.txt > ${GEOIP_TARGET_FOLDER}geoip_mm_country_all.txt
	cat ${GEOIP_TARGET_FOLDER}geoip_mm_region_city_combined_ipv4.txt ${GEOIP_TARGET_FOLDER}geoip_mm_region_city_combined_ipv6.txt > ${GEOIP_TARGET_FOLDER}geoip_mm_region_city_combined_all.txt
	cat ${GEOIP_TARGET_FOLDER}geoip_mm_isporg_ipv4.txt ${GEOIP_TARGET_FOLDER}geoip_mm_isporg_ipv6.txt > ${GEOIP_TARGET_FOLDER}geoip_mm_isporg_all.txt
	cat ${GEOIP_TARGET_FOLDER}geoip_cfs_webhost_ipv4.txt ${GEOIP_TARGET_FOLDER}geoip_cfs_webhost_ipv6.txt > ${GEOIP_TARGET_FOLDER}geoip_cfs_webhost_all.txt
	echo $(timestamp) > ${GEOIP_TARGET_FOLDER}update_last_changed.txt
	chmod 777 ${GEOIP_TARGET_FOLDER}*.txt
	chmod 777 ${GEOIP_TARGET_FOLDER}*.trie
	touch ${GEOIP_TARGET_FOLDER}51Degrees.trie
	service haproxy reload
	#some haproxy deployments do not support reload, use restart process below
	#service haproxy restart
fi
