#!/usr/bin/env bash

set -e

[ -z "$ENV" ] && echo "Environment is not defined" && exit 1;

echo "Running the application..."
echo "Environment: $ENV"

echo "$ENV" > /srv/env

echo "Settings environment configs..."
cp "/srv/docker/conf/$ENV/nginx-host.conf" "/etc/nginx/sites-enabled/00-opake.conf"

echo "Applying migrations..."
cd /srv && php vendor/bin/phinx migrate

echo "Running supervisord..."
exec /usr/bin/supervisord -n -c /srv/docker/conf/supervisord.conf
echo "Done"