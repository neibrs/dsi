#!/usr/bin/env bash

DRUPAL="$(pwd)"
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

chmod -R a+rw web/sites/default
rm -rf web/sites/default/settings.memcache.php
rm -rf web/sites/default/settings.php
rm -rf web/sites/default/private
rm -rf web/sites/default/files

# for docker
#vendor/bin/drush site:install -y --site-name="OAms" --account-pass=admin --db-url=mysql://root:@localhost:3306/oas
# for mac
vendor/bin/drush site:install -y --site-name="OAms" --account-pass=admin --db-url=mysql://root:root@127.0.0.1:3306/prod

source $SCRIPTPATH/install-common.sh
