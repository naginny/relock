#!/usr/bin/env bash

sudo apt-get update
sudo apt-get install -y apache2
if ! [ -L /var/www ]; then
  rm -rf /var/www/html
  ln -fs /vagrant/web /var/www/html
fi
sudo rm /etc/apache2/sites-enabled/000-default.conf
sudo mv /vagrant/000-default.conf /etc/apache2/sites-enabled/000-default.conf
sudo apt-get install -y php
sudo apt-get install -y libapache2-mod-php
sudo apt-get install -y php-mbstring
sudo apt-get install -y php-xml
sudo apt-get install -y php-xmlrpc
sudo apt-get install -y php-curl
sudo apt-get install -y php-zip
sudo apt-get install -y php-mysql
sudo apt-get install -y composer

sudo apt-get install -y software-properties-common
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
sudo add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://ftp.cc.uoc.gr/mirrors/mariadb/repo/10.2/ubuntu xenial main'

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
sudo apt-get update
sudo apt-get -y install mariadb-server
sudo service mysql restart

cd /vagrant
# running things
composer install --no-interaction --no-scripts
# create db
mysql -uroot -proot -e "CREATE DATABASE relock;"
# symfony project init
app/console doctrine:migration:migrate -n
# install crontab
crontab -u vagrant /vagrant/vagrant-setup/crontab