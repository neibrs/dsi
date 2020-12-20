#!/usr/bin/env bash

DRUPAL="$(pwd)"
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

chmod -R a+rw sites/default
rm -rf sites/default/settings.memcache.php
rm -rf sites/default/settings.php
rm -rf sites/default/private
rm -rf sites/default/files

vendor/bin/drush site:install -y --site-name="OAms" --account-pass=admin --db-url=mysql://root:@localhost:3306/oas

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

vendor/bin/drush language:default zh-hans

vendor/bin/drush cset -y language.negotiation url.prefixes.en "en"
vendor/bin/drush cset -y language.types negotiation.language_interface.enabled.language-browser 0

## Install contrib modules
vendor/bin/drush en -y \
  alert \
  block_style_plugins \
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

# drupal console issue: https://github.com/hechoendrupal/drupal-console/issues/4005
#vendor/bin/drush cr

# Enable varnish_purge
#vendor/bin/drupal moi -y \
#  varnish_purger
vendor/bin/drush en -y \
  person

## Dsi core modules
vendor/bin/drush en -y \
  entity_plus \
  dsi_import \
  dsi_icons

# Dsi cbos modules
vendor/bin/drush en -y \
  dsi_color_alert \
  dsi_color_block \
  dsi_color_user

vendor/bin/drush then dsi_color -y
vendor/bin/drush cset system.theme default dsi_color -y
vendor/bin/drush cset system.theme admin dsi_color -y

# Set timezone
vendor/bin/drush cset system.date country.default CN -y
vendor/bin/drush cset system.date first_day 1 -y
vendor/bin/drush cset system.date timezone.default Asia/Shanghai -y
vendor/bin/drush cset system.date timezone.user.configurable false -y
vendor/bin/drush cset system.date timezone.user.warning false -y
vendor/bin/drush cset system.date timezone.user.default 0 -y

# Enable lawyer industry
vendor/bin/drush en -y dsi_lawyer
# 翻译问题
vendor/bin/drush language:import:translations  modules/dsi/contrib/translation/translations/drupal.zh-hans.po
vendor/bin/drush language:import:translations  modules/dsi/industry/dsi_client/translations/dsi_client.zh-hans.po

vendor/bin/drush mim 30_client_xlsx
vendor/bin/drush mim 30_record_xlsx
vendor/bin/drush mim 208_client_xlsx
vendor/bin/drush mim 208_record_xlsx
vendor/bin/drush mim contract_xlsx

# Fixed, 翻译文件导入时不能移动.
chmod -R a+rw sites/default/files

