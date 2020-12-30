#!/usr/bin/env bash

SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

chmod -R a+rw web/sites/default
rm -rf web/sites/default/settings.memcache.php
rm -rf web/sites/default/settings.php
rm -rf web/sites/default/private
rm -rf web/sites/default/files

vendor/bin/drush site:install -y --site-name="OAms" --account-pass=admin --db-url=mysql://root:root@127.0.0.1:3306/dev

source $SCRIPTPATH/install-common.sh

vendor/bin/drush en -y webprofiler
