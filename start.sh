#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive

echo "-- Cloning the publicmedia sdk"
git clone -b v0.1.0 --depth 1 git@github.com:publicmediaplatform/phpsdk.git
echo "-- Installing Krumo"
git clone git@github.com:oodle/krumo.git
echo "-- Starting Vagrant"
vagrant up --provision
