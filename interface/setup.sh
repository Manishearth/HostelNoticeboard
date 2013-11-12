#!/bin/sh
clear
echo "########################################################################################
IITB Notice Board Server Side Setup Script
Author: Kamal Galrani
Date: Nov, 9, 2013

IMPORTANT: Make sure you have LAMP installed along with ssh2 php library (libssh2-php).
           Notice board server will not work without them.
########################################################################################"

read -p "Enter MySQL username:" uid
stty -echo
echo "Enter MySQL password:"
read pass
stty echo
echo "Creating database..."
mysql --user=$uid --password=$pass < setup.sql
echo "Writing default values..."
mysql --user=$uid --password=$pass < default.sql

read -p "Where do I store local copy files:" path
path="$(cd "$path" && pwd)"
src="$(dirname ${BASH_SOURCE[0]})"
src="$(cd "$src" && pwd)"

echo "Installing backend to '$path'..."

echo "<?php
\$dbHost = 'localhost';
\$dbUsername = '$uid';
\$dbPassword = '$pass';
\$dbName = 'NoticeBoard';
\$host = '/home';
?>" > "$src/config.inc"
if [ $? != 0 ]
then echo "Error occured while accessing '$path'"
     exit -1;
fi

cp -rf "$src/backend/"* "$path/"
cp -f "$src/config.inc" "$path/"
mkdir "$path/root"
mkdir "$path/root/Academics"
mkdir "$path/root/Hostel"
mkdir "$path/root/Cultural"
mkdir "$path/root/Technical"
mkdir "$path/root/Sports"

echo "Creating cronjob..."

##Insert Cronjob for backend##

read -p "Where do I store index.php (and other frontend files):" wwwpath
wwwpath="$(cd "$wwwpath" && pwd)"

echo "Installing frontend..."
cp -rf "$src/frontend/"** "$wwwpath/"
cp -f "$src/config.inc" "$wwwpath/"
ln -s "$path/root" "$wwwpath/root"

echo "Installation successful if you saw no errors :P"
