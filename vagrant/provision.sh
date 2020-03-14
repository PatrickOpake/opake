#!/usr/bin/env bash

set -e

: <<'COMMENT'

Provision script for Vagrant opake installation
Created for Ubuntu 16.04 amd64 box

COMMENT

PROVISION_HISTORY_FILE="/root/vagrant-opake-provision-steps-done"
TASKS=$(
echo "task_install_packages" \
	"task_install_composer" \
	"task_install_wkhtmltopdf" \
	"task_install_curl_cacert" \
	"task_install_composer_dependencies" \
	"task_setting_up_hosts" \
	"task_setting_up_environments_and_configs" \
	"task_creating_database" \
	"task_applying_database_dump" \
	"task_applying_migrations" \
	"task_restarting_services"
)


function is_step_finished {
	if grep -Fxq "$1" "$PROVISION_HISTORY_FILE" > /dev/null 2>&1
	then
		return 0
	fi

	return 1
}

function finish_step {
	echo "$1" >> "$PROVISION_HISTORY_FILE"
}

function reset_steps {
	rm "$PROVISION_HISTORY_FILE"
}

function run_tasks {
	for TASK in $TASKS
	do
		if ! is_step_finished $TASK
		then
			$TASK
			finish_step $TASK
		fi
	done

	echo "=> Done"
}

function task_install_packages {
	echo "=> Installing packages..."

	debconf-set-selections <<< "mysql-server mysql-server/root_password password root"
	debconf-set-selections <<< "mysql-server mysql-server/root_password_again password root"

	apt-get update && apt-get install -y -q \
	    php \
	    php-memcache php-curl php-zip php-gd php-imagick php-mysql php-soap php-xml php-mbstring php-mcrypt \
	    nginx \
	    memcached \
	    xvfb \
	    imagemagick \
	    wget \
	    xz-utils \
	    git \
	    unzip \
	    mysql-server \
	    mysql-client

	mkdir -p /data/log/opake/ && chown www-data:www-data -R /data/log/opake
}

function task_install_composer {
	echo "=> Installing composer..."

	cd /tmp && \
	    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
	    php composer-setup.php && \
	    php -r "unlink('composer-setup.php');" && \
	    mv ./composer.phar /usr/bin/composer && \
	    chmod a+x /usr/bin/composer
}

function task_install_wkhtmltopdf {
	echo "=> Installing wkhtmltopdf..."

	cd /tmp && \
	    wget -q "https://downloads.wkhtmltopdf.org/0.12/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz" && \
	    tar -xf "./wkhtmltox-0.12.4_linux-generic-amd64.tar.xz" && \
	    mv ./wkhtmltox /opt/ && \
	    chmod a+x /opt/wkhtmltox/bin/wkhtmltoimage && \
	    chmod a+x /opt/wkhtmltox/bin/wkhtmltopdf && \
	    cp /srv/opake/docker/wkhtmltopdf-xvfb.sh /usr/bin/wkhtmltopdf-xvfb.sh && \
	    chmod a+x /usr/bin/wkhtmltopdf-xvfb.sh
}

function task_install_curl_cacert {
	echo "=> Installing CURL cacert..."
	cp /srv/opake/docker/cacert.pem /etc/php/cacert.pem
}

function task_install_composer_dependencies {
	echo "=> Installing composer dependencies..."
	cd /srv/opake/ && composer update
}

function task_setting_up_hosts {
	echo "=> Setting up hosts..."

	cat /srv/opake/vagrant/conf/php-settings.ini >> /etc/php/7.0/fpm/php.ini
	cat /srv/opake/vagrant/conf/php-fpm-settings.conf >> /etc/php/7.0/fpm/php-fpm.conf

	cp /srv/opake/vagrant/conf/nginx.conf /etc/nginx/nginx.conf
	cp "/srv/opake/vagrant/conf/nginx-host.conf" "/etc/nginx/sites-enabled/00-opake.conf"
	echo "127.0.0.1 opake.local patients.opake.local api.opake.local" >> /etc/hosts
}

function task_setting_up_environments_and_configs {
	echo "=> Setting up environment and configs..."

	echo "dev" > /srv/opake/env

	cp -a "/srv/opake/apps/admin/assets/config/dev/." "/srv/opake/apps/admin/assets/config/local/"
	cp -a "/srv/opake/apps/common/assets/config/dev/." "/srv/opake/apps/common/assets/config/local/"
	cp -a "/srv/opake/apps/api/assets/config/dev/." "/srv/opake/apps/api/assets/config/local/"
	cp -a "/srv/opake/apps/patients/assets/config/dev/." "/srv/opake/apps/patients/assets/config/local/"

	mkdir -p /run/php
	chmod 777 /run/php
}

function task_creating_database {
	echo "=> Creating a database..."

	# backward compatibility
	echo 'sql_mode=""' >> /etc/mysql/mysql.conf.d/mysqld.cnf
	service mysql restart

	mysql -uroot -proot -e "CREATE DATABASE opake"
	mysql -uroot -proot -e "grant all privileges on opake.* to 'opake'@'localhost' identified by 'opake'"
}

function task_applying_database_dump {
	echo "=> Applying a database dump..."

	cd /tmp/ && unzip /srv/opake/vagrant/dump/opake.sql.zip && \
		mysql -uroot -proot opake < /tmp/opake.sql
}

function task_applying_migrations {
	echo "=> Applying migrations..."

	cd /srv/opake && php vendor/bin/phinx migrate
}

function task_restarting_services {
	echo "=> Restarting the services..."

	service nginx restart
	service php7.0-fpm restart
}

run_tasks