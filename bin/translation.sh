#!/bin/bash

vendor/bin/drush en potx
echo $(pwd)

for file in `find modules/dsi -name "*.info.yml"`; do
  echo $(dirname $file) -- $(basename $(dirname $file));
  $(pwd)/vendor/bin/drush potx single --include=modules/potx --folder="$(dirname $file)/" --api=9
  if [ ! -d $(dirname $file)/translations ]; then
    mkdir $(dirname $file)/translations;
    echo "mkdir $(dirname $file)/translations";
  fi
  mv "$(pwd)/general.pot" "$(dirname $file)/translations/$(basename $(dirname $file)).pot"
  echo 'DONE'
done
