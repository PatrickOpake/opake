FROM phusion/baseimage
MAINTAINER Rikanishu <rikanishu@gmail.com>

# install dependencies
RUN apt-get update && apt-get install -y -q \
 	php \
 	php-memcache php-curl php-zip php-gd php-imagick php-mysql php-soap php-xml php-mbstring php-mcrypt \
 	nginx \
 	memcached \
 	xvfb \
 	imagemagick \ 
 	supervisor \
 	wget \
 	xz-utils \
 	git

# create dirs
RUN mkdir -p /data/opake && mkdir -p /data/log/opake && chown www-data:www-data /data/ -R

# install composer
RUN cd /tmp && \ 
 	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
 	php composer-setup.php && \
 	php -r "unlink('composer-setup.php');" && \
 	mv ./composer.phar /usr/bin/composer && \
 	chmod a+x /usr/bin/composer

# install wkhtmltopdf
ADD "./docker/wkhtmltopdf-xvfb.sh" "/srv/docker/wkhtmltopdf-xvfb.sh"
RUN cd /tmp && \
 	wget -q https://downloads.wkhtmltopdf.org/0.12/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz && \
 	tar -xf "./wkhtmltox-0.12.4_linux-generic-amd64.tar.xz" && \
 	mv ./wkhtmltox /opt/ && \
 	chmod a+x /opt/wkhtmltox/bin/wkhtmltoimage && \
 	chmod a+x /opt/wkhtmltox/bin/wkhtmltopdf && \
 	cp /srv/docker/wkhtmltopdf-xvfb.sh /usr/bin/wkhtmltopdf-xvfb.sh && \
 	chmod a+x /usr/bin/wkhtmltopdf-xvfb.sh

# install curl cert
ADD "./docker/cacert.pem" "/srv/docker/cacert.pem"
RUN mv /srv/docker/cacert.pem /etc/php/cacert.pem

# add composer files
ADD "./composer.lock" "/srv/composer.lock"
ADD "./composer.json" "/srv/composer.json"

# run composer update
RUN cd /srv/ && composer update

# override php.ini settings
ADD "./docker/conf/php-settings.ini" "/srv/docker/conf/php-settings.ini"
RUN cat /srv/docker/conf/php-settings.ini >> /etc/php/7.0/fpm/php.ini

# override php-fpm.conf settings
ADD "./docker/conf/php-fpm-settings.conf" "/srv/docker/conf/php-fpm-settings.conf"
RUN cat /srv/docker/conf/php-fpm-settings.conf >> /etc/php/7.0/fpm/php-fpm.conf

# set nginx conf
ADD "./docker/conf/nginx.conf" "/srv/docker/conf/nginx.conf"
RUN cp /srv/docker/conf/nginx.conf /etc/nginx/nginx.conf

# adding php run directory
RUN mkdir /run/php && chmod 777 /run/php

# add record about parent host
RUN echo "172.17.0.1 parent.host" >> /etc/hosts

# adding the app directory
ADD "." "/srv"

# chage permission to files
RUN chown www-data:www-data /srv/apps -R

EXPOSE 80

CMD ["/srv/docker/run.sh"]
