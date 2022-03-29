#!/usr/bin/env bash

LOCAL_HOSTNAME='pvt.geoip-v3.inmotiongroup.net'

touch process-begin.txt

echo "$LOCAL_HOSTNAME"
#wget --timeout=300 -O /dev/null -o /dev/null "http://pvt.geoip.inmotiongroup.net/scripts/get_country_data.php" "http://pvt.geoip.inmotiongroup.net/scripts/get_city_data.php" "http://pvt.geoip.inmotiongroup.net/scripts/get_isp_data.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv4_country.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv4_city.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv4_isp_org.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv4_cfilter_webhosts.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv6_country.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv6_city.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv6_isp_org.php" "http://pvt.geoip.inmotiongroup.net/scripts/generate_geoipv6_cfilter_webhosts.php"
#wget --timeout=300 -O /dev/null -o /dev/null "http://10.39.103.126/scripts/get_country_data.php" "http://10.39.103.126/scripts/get_city_data.php" "http://10.39.103.126/scripts/get_isp_data.php" "http://10.39.103.126/scripts/generate_geoipv4_country.php" "http://10.39.103.126/scripts/generate_geoipv4_city.php" "http://10.39.103.126/scripts/generate_geoipv4_isp_org.php" "http://10.39.103.126/scripts/generate_geoipv4_cfilter_webhosts.php" "http://10.39.103.126/scripts/generate_geoipv6_country.php" "http://10.39.103.126/scripts/generate_geoipv6_city.php" "http://10.39.103.126/scripts/generate_geoipv6_isp_org.php" "http://10.39.103.126/scripts/generate_geoipv6_cfilter_webhosts.php" "http://10.39.103.126/scripts/get_device_detection_data.php" "http://10.39.103.126/scripts/push_files_to_server.php" --header "Host: pvt.geoip.inmotiongroup.net"

echo "Getting Geo data"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/get_country_data.php"  --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/get_city_data.php"   --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/get_isp_data.php"   --header "Host: $LOCAL_HOSTNAME"
echo "Getting Device data"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/get_device_detection_data.php"  --header "Host: $LOCAL_HOSTNAME"
echo "Generating Geo data files, IPv4"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv4_country.php"   --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv4_city.php"   --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv4_isp_org.php"  --header "Host: $LOCAL_HOSTNAME"
echo "Generating Geo data files, IPv6"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv6_country.php"   --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv6_city.php"   --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv6_isp_org.php"   --header "Host: $LOCAL_HOSTNAME"
echo "Generating TVS Webhosts data"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv4_cfilter_webhosts.php"   --header "Host: $LOCAL_HOSTNAME"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null  "https://$LOCAL_HOSTNAME/scripts/generate_geoipv6_cfilter_webhosts.php"  --header "Host: $LOCAL_HOSTNAME"

echo "done"


#echo "Building data files"
#wget --timeout=300 -O /dev/null -o /dev/null "http://10.39.103.126/scripts/get_country_data.php" "http://10.39.103.126/scripts/get_city_data.php" "http://10.39.103.126/scripts/get_isp_data.php" "http://10.39.103.126/scripts/generate_geoipv4_country.php" "http://10.39.103.126/scripts/generate_geoipv4_city.php" "http://10.39.103.126/scripts/generate_geoipv4_isp_org.php" "http://10.39.103.126/scripts/generate_geoipv4_cfilter_webhosts.php" "http://10.39.103.126/scripts/generate_geoipv6_country.php" "http://10.39.103.126/scripts/generate_geoipv6_city.php" "http://10.39.103.126/scripts/generate_geoipv6_isp_org.php" "http://10.39.103.126/scripts/generate_geoipv6_cfilter_webhosts.php" "http://10.39.103.126/scripts/get_device_detection_data.php" --header "Host: pvt.geoip.inmotiongroup.net"

#echo "Sending files to server(s)"
echo "Sending Data to data.trafficvalidate.com ( and later inClick )"
wget --no-check-certificate --timeout=300 -O /dev/null -o /dev/null "https://$LOCAL_HOSTNAME/scripts/push_files_to_server.php" --header "Host: $LOCAL_HOSTNAME"

touch process-end.txt

