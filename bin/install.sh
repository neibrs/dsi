#!/usr/bin/env bash

DRUPAL="$(pwd)"
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

chmod -R a+rw sites/default
rm -rf sites/default/settings.memcache.php
rm -rf sites/default/settings.php
rm -rf sites/default/private
rm -rf sites/default/files

vendor/bin/drush site:install -y --site-name="OAms" --account-pass=admin --db-url=mysql://root:@127.0.0.1:3306/oas
chmod -R a+rw sites/default
mkdir sites/default/private;
echo "\$settings['file_private_path'] = 'sites/default/private';" >> sites/default/settings.php

echo "ini_set('memory_limit', -1);" >> sites/default/settings.php
echo "ini_set('max_execution_time', 0);" >> sites/default/settings.php

# Enable modules
vendor/bin/drush en -y \
  config_translation \
  drush_language \
  translation

# Install zh-hans language
vendor/bin/drush language:add zh-hans

vendor/bin/drush cset -y language.negotiation url.prefixes.en "en"
vendor/bin/drush cset -y language.types negotiation.language_interface.enabled.language-browser 0

## Install contrib modules
vendor/bin/drush en -y \
  components \
  ludwig \
  views_plus \
  pinyin \
  tb_megamenu

# 重新检查使用pinyin模块的依赖性，删除这里的模块单独启用

#  layout_builder \
#
vendor/bin/drush en -y \
  role_menu
vendor/bin/drush pmu -y \
  toolbar
# adminimal_admin_toolbar \

# Enable memcache modules
#vendor/bin/drush en -y \
#  memcache
#cp modules/dsi/settings.memcache.php sites/default/
#echo "include \$app_root . '/' . \$site_path . '/settings.memcache.php';" >> sites/default/settings.php

## Dsi core modules
vendor/bin/drush en -y \
  entity_plus \
  dsi_import \
  dsi_icons

# Dsi cbos modules
vendor/bin/drush en -y \
  dsi_block

vendor/bin/drush then materialize -y
vendor/bin/drush cset system.theme default materialize -y
vendor/bin/drush cset system.theme admin seven -y

#Predefined configuration
vendor/bin/drush ucrt lijd --mail="lijd@139.com" --password="123456"

# Enable lawyer industry
vendor/bin/drush en -y dsi_lawyer