#!/bin/sh
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
read -p "Enter MySQL username:" uid
stty -echo
echo "Enter MySQL password:"
read pass
stty echo

echo "Creating database..."
mysql --user=$uid --password=$pass < "$src/setup.sql"
echo "Writing default values..."
mysql --user=$uid --password=$pass < "$src/default.sql"

path="$(cd "$src/../" && pwd)"

echo "Installing in $path ..."

echo "Generating config.inc ..."
echo "<?php" > "$path/backend/config.inc"
echo "\$remotepath="'~/'";" >> "$path/backend/config.inc"
echo "\$path='$path/root/';" >> "$path/backend/config.inc"
echo "\$dbUsername='$uid';"  >> "$path/backend/config.inc"
echo "\$dbPassword='$pass';" >> "$path/backend/config.inc"
echo "\$asyncnumber=1" >> "$path/backend/config.inc"
echo "?>" >> "$path/backend/config.inc"

mkdir "$path/root"
mkdir "$path/root/Academics"
mkdir "$path/root/Cultural"
mkdir "$path/root/Sports"
mkdir "$path/root/Hostel"


echo "Creating cronjob..."
crontab -l > currentcron
echo "0 */4 * * *php $path/backend/daemon.php" >> currentcron
crontab currentcron
rm currentcron

echo "Installation successful if you saw no errors :P"
