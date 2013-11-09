#!/bin/sh
clear
echo "###########################################################################################################\n
IITB Notice Board Server Side Setup Script\n
Author: Kamal Galrani\n
Date: Nov, 9, 2013\n
IMPORTANT: Make sure you are connected to internet before running this script, otherwise setup may fail.
###########################################################################################################\n"
sudo echo "Thank you!"

stty -echo
echo "Enter MySQL root password:"
read pass
stty echo

sudo apt-get install mysql-server php5 libssh2-php -y 

##### configure MySQL Databases #########
echo "Creating Database..."
mysql --user=root --password=$pass < setup.sql
