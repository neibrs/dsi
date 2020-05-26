#!/usr/bin/env bash

DRUPAL="$(pwd)"
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

chmod -R a+rw sites/default
rm -rf sites/default/settings.php
rm -rf sites/default/private
rm -rf sites/default/files

vendor/bin/drush site:install -y --account-pass=admin --db-url=mysql://root:@127.0.0.1:3306/ds
chmod -R a+rw sites/default

# For mac install only
#mkdir -p sites/default/config/sync
#cp -R modules/ds/bin/mac/sync/.htaccess sites/default/config/sync/.htaccess
#echo "\$settings['config_sync_directory'] = 'sites/default/config/sync';" >> sites/default/settings.php

chmod -R a+rw sites/default

mkdir sites/default/private;
echo "\$settings['file_private_path'] = 'sites/default/private';" >> sites/default/settings.php

echo "ini_set('memory_limit', -1);" >> sites/default/settings.php
echo "ini_set('max_execution_time', 0);" >> sites/default/settings.php

# Enable memcache modules
#vendor/bin/drupal moi -y \
#  memcache \
#  memcache_admin

#cp modules/dsi/bin/settings.memcache.php sites/default/settings.memcache.php

#echo "include \$app_root . '/' . \$site_path . '/settings.memcache.php';" >> sites/default/settings.php

echo Set site to the product mode
vendor/bin/drupal site:mode prod

# Enable modules
vendor/bin/drupal moi -y \
  config_translation \
  drush_language
#  translation
#
## install multilingual
#source $SCRIPTPATH/install-zh.sh
#
## include standard bash.
#source $SCRIPTPATH/install-ds.sh
#source $SCRIPTPATH/install-ds-dev.sh

echo $(pwd)

cd $DRUPAL
vendor/bin/drupal user:create test test --roles='authenticated' --email="test@example.com" --status="1"
