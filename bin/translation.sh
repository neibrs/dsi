#!/bin/bash

vendor/bin/drush en potx
echo $(pwd)

for file in `find web/modules/dsi -name "*.info.yml"`; do
  echo $(dirname $file) -- $(basename $(dirname $file));
  path=$(dirname $file);
  echo ${path#*web/}
  $(pwd)/vendor/bin/drush potx single --include=web/modules/contrib/potx/ --folder="${path#*web/}/" --api=9
  if [ ! -d $(dirname $file)/translations ]; then
    mkdir $(dirname $file)/translations;
    echo "mkdir $(dirname $file)/translations";
  fi
  mv "$(pwd)/web/general.pot" "$(dirname $file)/translations/$(basename $(dirname $file)).pot"
  echo 'DONE'
done
