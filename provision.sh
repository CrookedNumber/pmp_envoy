#!/usr/bin/env bash

echo "-- Setting up box"
sudo apt-get update
sudo apt-get install -y apache2 php5 php-pear php5-mysql php5-gd

cd /pmp_envoy

echo "--- Setting document root ---"
sudo rm -rf /var/www/html
sudo ln -fs /pmp_envoy /var/www/html

echo "-- Installing webserver packages"
echo "-- Restart Server"
/etc/init.d/apache2 restart > /dev/null 2>&1
