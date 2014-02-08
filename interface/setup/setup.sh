#!/bin/bash
clear
echo "########################################################################################
IITB Notice Board Server Side Setup Script
Author: Kamal Galrani
Date: Nov, 9, 2013

IMPORTANT: Make sure you have LAMP installed.
           Notice board server will not work without that.
########################################################################################"

src="$(dirname ${BASH_SOURCE[0]})"
src="$(cd "$src" && pwd)"

echo $src

echo "Creating database..."
mysql < "$src/setup.sql"
echo "Writing default values..."
mysql < "$src/default.sql"

path="$(cd "$src/../" && pwd)"

echo "Installing in $path ..."

echo "Generating config.inc ..."
cat > $path/backend/config.inc << EOF
<?php
\$path         = '$path/root/';
\$remotepath   = '~/.HostelNoticeboard/';

\$dbUsername   = '';
\$dbPassword   = '';

\$asyncnumber  = 1;

\$maxExpiry    = array(
        "poster" => 7,
        "text"   => 3
);
\$defaultExpiry= array(
        "poster" => 5,
        "text"   => 1
);
?>
EOF

mkdir "$path/root"
mkdir "$path/root/Academic"
mkdir "$path/root/Cultural"
mkdir "$path/root/Sports"
mkdir "$path/root/Hostel"
mkdir "$path/root/Technical"

#echo "Creating cronjob..."
#crontab -l > currentcron
#echo "0 */4 * * *php $path/backend/daemon.async.php" >> currentcron
#crontab currentcron
#rm currentcron

echo "Installation successful if you saw no errors :P"
