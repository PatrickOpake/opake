## Opake Deployment for bare OS
### Initial steps

All steps have been done in Ubuntu 16.04 LTS Server (amd64).

1. Checkout/clone the repository.
	* I downloaded the repository *web* directory into  */srv/own/Opake-Web/* in this example.
2. Install php
	* The application is tested for working under both php 5.6 and 7.0
```sh
sudo apt-get install php
```

3. Install composer.
	* The offical insturuction: https://getcomposer.org/download/
```sh
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv ./composer.phar /usr/bin/composer
sudo chmod a+x /usr/bin/composer
```

4. Install PHP extensions:
```sh
sudo apt-get install php-memcache php-curl php-zip php-gd php-imagick php-mysql php-soap php-xml php-mbstring php-mcrypt
```

4. Go to the project dir and install dependecies.
```sh
cd /srv/own/Opake-Web/
composer update
```

5. Install curl cacert
```sh
wget https://curl.haxx.se/ca/cacert.pem
sudo mv ./cacert.pem /etc/php/cacert.pem
```

6. Override php.ini settings:
```sh
vim /etc/php/7.0/fpm/php.ini
```

```
curl.cainfo ="/etc/php/cacert.pem"
memory_limit = 1024M
max_input_time = 300
max_input_vars = 20000
max_execution_time = 300
date.timezone = America/New_York
post_max_size = 512M
upload_max_filesize = 512M
```

7. Add fcgi information to php-fpm.conf:
	* I used php-fpm and nginx in this example.

```sh
sudo vim /etc/php/7.0/fpm/php-fpm.conf
```

```
[opake]

listen = /var/run/php/opake.socket
listen.backlog = -1
listen.owner = www-data
listen.group = www-data
listen.mode=0660

; Unix user/group of processes
user = www-data
group = www-data

; Choose how the process manager will control the number of child processes.
; Change this settings depending on the environment loading
pm = dynamic
pm.max_children = 75
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

; Pass environment variables
env[HOSTNAME] = $HOSTNAME
env[PATH] = /usr/local/bin:/usr/bin:/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp
```

8. restart php-fpm service
```sh
sudo service php7.0-fpm restart
```

9. install nginx
```sh
sudo apt-get install nginx
```

10. setup nginx configs
	* the hosts are *opakevm.local*, *patients.opakevm.local* and *api.opakevm.local* in this example.
```sh
sudo vim /etc/nginx/sites-enabled/00-opake.conf
```

```
server {
   server_name  www.opakevm.local;
   rewrite ^(.*) http://opakevm.local$1 permanent;
}

server {
     listen 80;
     server_name opakevm.local;
     root  /srv/own/Opake-Web/apps/admin/public;
     index index.php;

    # Logging --
    access_log  /var/log/nginx/opakevm.local.access.log;
    error_log  /var/log/nginx/opakevm.local.error.log notice;

    location ~^/common/(.*)$ {
        alias /srv/own/Opake-Web/apps/common/public/;
        try_files $1 $1/;
    }

     # serve static files directly
     location ~* ^.+.(css|js|html|eot|woff|ttf|html)$ {
           access_log        off;
           expires           max;
     }

     location ~ \.php$ {
           try_files $uri =404;
           fastcgi_pass unix:/var/run/php/opake.socket;
           fastcgi_index index.php;
           include /etc/nginx/fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }

     location / {
        try_files $uri $uri/ /index.php?$args;
        }
}

server {
     listen 80;
     server_name patients.opakevm.local;
     root  /srv/own/Opake-Web/apps/patients/public;
     index index.php;

    # Logging --
    access_log  /var/log/nginx/patients.opakevm.local.access.log;
    error_log  /var/log/nginx/patients.opakevm.local.error.log notice;

    location ~^/common/(.*)$ {
        alias /srv/own/Opake-Web/apps/common/public/;
        try_files $1 $1/;
    }

     # serve static files directly
     location ~* ^.+.(css|js|html|eot|woff|ttf|html)$ {
           access_log        off;
           expires           max;
     }

     location ~ \.php$ {
           try_files $uri =404;
           fastcgi_pass unix:/var/run/php/opake.socket;
           fastcgi_index index.php;
           include /etc/nginx/fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }

     location / {
        try_files $uri $uri/ /index.php?$args;
     }
}

server {
     listen 80;
     server_name api.opakevm.local;
     root  /srv/own/Opake-Web/apps/api/public;
     index index.php;

    # Logging --
    access_log  /var/log/nginx/api.opakevm.local.access.log;
    error_log  /var/log/nginx/api.opakevm.local.error.log notice;

    location ~^/common/(.*)$ {
        alias /srv/own/Opake-Web/apps/common/public/;
        try_files $1 $1/;
    }

     # serve static files directly
     location ~* ^.+.(css|js|html|eot|woff|ttf|html)$ {
           access_log        off;
           expires           max;
     }

     location ~ \.php$ {
           try_files $uri =404;
           fastcgi_pass unix:/var/run/php/opake.socket;
           fastcgi_index index.php;
           include /etc/nginx/fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }

     location / {
        try_files $uri $uri/ /index.php?$args;
     }
}
```

11. disable default nginx conf, change global nginx settings.
```sh
sudo vim /etc/nginx/nginx.conf
```

12. restart nginx.
```sh
sudo service nginx restart
```

13. create an environment file

	* "dev" for development environment
	* "qa" for QA
	* "staging" for Staging
	* "production" for Production
```sh
echo "dev" > /srv/own/Opake-Web/env
```

14. install memcached
```sh
sudo apt-get install memcached
```

15. install mysql
```sh
sudo apt-get install mysql-client mysql-server
```

16. create a mysql user
```sh
mysql-client -u root -p
```
```
mysql> CREATE USER 'opake'@'localhost' IDENTIFIED BY 'opake';
mysql> CREATE DATABASE opake;
mysql> GRANT ALL PRIVILEGES ON opake.* TO 'opake'@'localhost';
mysql> FLUSH PRIVILEGES;
```

17. apply database dump
```sh
mysql -u root -p opake < /srv/own/opk.sql
```

18. Run the migration
```sh
cd /srv/own/Opake-Web/
php vendor/bin/phinx migrate
```

19. Install wkhtmltopdf
```sh
wget https://downloads.wkhtmltopdf.org/0.12/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz
tar -xvf ./wkhtmltox-0.12.4_linux-generic-amd64.tar.xz
sudo mv ./wkhtmltox /opt/
sudo chmod a+x /opt/wkhtmltox/bin/wkhtmltoimage
sudo chmod a+x /opt/wkhtmltox/bin/wkhtmltopdf
sudo apt-get install xvfb
```
```sh
sudo vim /usr/bin/wkhtmltopdf-xvfb.sh
```
add the text:
```
#!/usr/bin/env bash
xvfb-run -a --server-args="-screen 0, 1024x768x24" /opt/wkhtmltox/bin/wkhtmltopdf -q $*
```
execute:
```sh
sudo chmod a+x /usr/bin/wkhtmltopdf-xvfb.sh
```

20. install imagemagick
```sh
sudo apt-get install imagemagick
```

21. Move all files from old servers to new directories for public and protected uploaded files.

22. Set up scheduled tasks
	* Every minute: ```php -f ./console.php minute```
	* Every 10 minutes: ```php -f ./console.php minute10```
	* Every hour: ```php -f ./console.php hour```
	* Every day (in night time): ```php -f ./console.php day```

23. change application settings in config files (will be done by developers once they get all needed info)
 	- database connection options
 	- hosts
 	- directory for logs
 	- directories for protected / public files
 	- third party programs paths


### Build steps

Here are steps that we use in our Jenkins CI process (taken from the QA server) that should be described in the build script.

1. Settings up the maintenance placeholder:
```sh
cd /srv/own/Opake-Web/
cp ./apps/common/public/maintenance-template.html ./apps/common/public/maintenance.html
```

2. Update the project code from CVS.

3. Update dependencies:
```sh
composer update
```

4. Run migrations:
```sh
php vendor/bin/phinx migrate
```

5. Remove the maintenance warning:
```sh
rm ./apps/common/public/maintenance.html
```


### Environment-specific notes

1. In this example DB and Web-app are in the same server. In our environments it's different servers. In this case, steps that are related to DB should be performed in a separated server.
2. Production environment has two web instances, however sheduled task should be turned on only in one.
3. Production environment has HTTPS enabled, which is not covered in this example and configs.


### Notes for HCB servers

1. Due the fact that main disk is not easily extendable, all uploaded data and logs should be stored in */data/* directory, so create the following directories:

```sh
mkdir -p /data/opake/public/uploads
mkdir -p /data/opake/protected/uploads
mkdir -p /data/log

chown www-data:www-data /data/opake -R
chown www-data:www-data /data/log -R
```

2. Add the rules to nginx virtualhosts to create an alias for the public files
```
...
 location ~^/uploads/(.*)$ {
        alias /data/opake/public/uploads/;
        try_files $1 $1/;
    }
 ...
```
So according this example the final config would be:

```
server {
   server_name  www.opakevm.local;
   rewrite ^(.*) http://opakevm.local$1 permanent;
}

server {
     listen 80;
     server_name opakevm.local;
     root  /srv/own/Opake-Web/apps/admin/public;
     index index.php;

    # Logging --
    access_log  /var/log/nginx/opakevm.local.access.log;
    error_log  /var/log/nginx/opakevm.local.error.log notice;

    location ~^/common/(.*)$ {
        alias /srv/own/Opake-Web/apps/common/public/;
        try_files $1 $1/;
    }

    location ~^/uploads/(.*)$ {
        alias /data/opake/public/uploads/;
        try_files $1 $1/;
    }

     # serve static files directly
     location ~* ^.+.(css|js|html|eot|woff|ttf|html)$ {
           access_log        off;
           expires           max;
     }

     location ~ \.php$ {
           try_files $uri =404;
           fastcgi_pass unix:/var/run/php/opake.socket;
           fastcgi_index index.php;
           include /etc/nginx/fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }

     location / {
        try_files $uri $uri/ /index.php?$args;
        }
}

server {
     listen 80;
     server_name patients.opakevm.local;
     root  /srv/own/Opake-Web/apps/patients/public;
     index index.php;

    # Logging --
    access_log  /var/log/nginx/patients.opakevm.local.access.log;
    error_log  /var/log/nginx/patients.opakevm.local.error.log notice;

    location ~^/common/(.*)$ {
        alias /srv/own/Opake-Web/apps/common/public/;
        try_files $1 $1/;
    }

    location ~^/uploads/(.*)$ {
        alias /data/opake/public/uploads/;
        try_files $1 $1/;
    }

     # serve static files directly
     location ~* ^.+.(css|js|html|eot|woff|ttf|html)$ {
           access_log        off;
           expires           max;
     }

     location ~ \.php$ {
           try_files $uri =404;
           fastcgi_pass unix:/var/run/php/opake.socket;
           fastcgi_index index.php;
           include /etc/nginx/fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }

     location / {
        try_files $uri $uri/ /index.php?$args;
     }
}

server {
     listen 80;
     server_name api.opakevm.local;
     root  /srv/own/Opake-Web/apps/api/public;
     index index.php;

    # Logging --
    access_log  /var/log/nginx/api.opakevm.local.access.log;
    error_log  /var/log/nginx/api.opakevm.local.error.log notice;

    location ~^/common/(.*)$ {
        alias /srv/own/Opake-Web/apps/common/public/;
        try_files $1 $1/;
    }

    location ~^/uploads/(.*)$ {
        alias /data/opake/public/uploads/;
        try_files $1 $1/;
    }

     # serve static files directly
     location ~* ^.+.(css|js|html|eot|woff|ttf|html)$ {
           access_log        off;
           expires           max;
     }

     location ~ \.php$ {
           try_files $uri =404;
           fastcgi_pass unix:/var/run/php/opake.socket;
           fastcgi_index index.php;
           include /etc/nginx/fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
     }

     location / {
        try_files $uri $uri/ /index.php?$args;
     }
}
```