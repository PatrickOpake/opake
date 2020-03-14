## Steps for deploying on HCB using Dokku


1. Create a Dokku application.
```sh
dokku apps:create opake
```
2. Set envitonment variable.
```sh
dokku config:set opake ENV=production
```
3. Set port forwarding configuration.
```sh
dokku proxy:ports-add opake http:80:80
```
4. Set configuration for storages.
```sg
dokku storage:mount opake /data/:/data/
dokku storage:mount opake /etc/ssl:/ssl
```
5. Add domains to application.
```sh
dokku domains:add opake opake.com api.opake.com patients.opake.com
```
6. Create directories for application data and set permissions.
```sh
mkdir -p /data/opake/public/uploads
mkdir -p /data/opake/protected/uploads
mkdir -p /data/log/opake/nginx

chmod 777 /data/opake -R
chmod 777 /data/log/opake -R
```
7. Increase request max body size.
```sh
mkdir /home/dokku/opake/nginx.conf.d/
echo 'client_max_body_size 100M;' > /home/dokku/opake/nginx.conf.d/upload.conf
chown dokku:dokku /home/dokku/opake/nginx.conf.d/upload.conf
service nginx reload
```

8. Install mailserver
  * Select the following options: Type: Internet Site, Host: opake.com
```sh
apt-get install postfix
```
  * Open the config file.
```sh
vim /etc/postfix/main.cf
```
  * Add a docker subnetwork to the allowed hosts.
```
...
mynetworks = 172.17.0.0/24 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128
...
```

9. Add HTTPS certificates info to Dokku application
10. Copy public and protected files into ```/data/opake/public/uploads``` and ```/data/opake/protected/uploads```
```sh
unzip ./shared.zip
mv ./uploads/* /data/opake/public/uploads/
mv ./protected/* /data/opake/protected/uploads/
chmod 777 /data/opake -R
```
11. Apply DB dump

12. Set DB environment variables for the application
```sh
dokku config:set opake OPAKE_DB_USER=...
dokku config:set opake OPAKE_DB_PASSWORD=...
dokku config:set opake OPAKE_DB_HOST=...
dokku config:set opake OPAKE_DB_NAME=...
```

13. Setup regular tasks using crontab.
```sh
crontab -u root -e
```

```
*/1 * * * *    dokku enter opake web  /usr/bin/php -f /srv/console.php minute >/dev/null 2>&1
*/10 * * * *   dokku enter opake web  /usr/bin/php -f /srv/console.php minute10 >/dev/null 2>&1
0 */1 * * *    dokku enter opake web  /usr/bin/php -f /srv/console.php hour >/dev/null 2>&1
0 5 * * *      dokku enter opake web  /usr/bin/php -f /srv/console.php day >/dev/null 2>&1
```
14. Deploy the application using Dokku.