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

echo "Generating MySQL.class.php..."
echo "<?php
class MySQL
{	
	private \$dbUsername='$uid';
	private \$dbPassword='$pass';" > "$src/MySQL.class.php"
cat "$src/MySQL.class.part" >> "$src/MySQL.class.php"

read -p "Where do I store local copy files:" path
path="$(cd "$path" && pwd)"

echo "Installing backend to '$path'..."
echo "\$path='$path/root/';" >> "$src/config.inc"
echo "?>" >> "$src/config.inc"
cp -rf "$src/backend/"* "$path/"
cp -f "$src/MySQL.class.php" "$path/"
cp -f "$src/config.inc" "$path/"

echo "Creating cronjob..."

##Insert Cronjob for backend##

read -p "Where do I store index.php (and other frontend files):" wwwpath
wwwpath="$(cd "$wwwpath" && pwd)"

echo "Installing frontend..."
cp -rf "$src/frontend/"* "$wwwpath/"
cp -f "$src/MySQL.class.php" "$wwwpath/"
cp -f "$src/config.inc" "$wwwpath/"
ln -s "$path/root" "$wwwpath/root"

rm -f "$src/MySQL.class.php"

echo "Installation successful if you saw no errors :P"
