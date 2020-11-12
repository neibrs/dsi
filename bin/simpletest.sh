#!/bin/bash

#apt update && apt install sudo -y
vendor/bin/drush en -y simpletest

sudo rm sites/simpletest/browser_output -rf
OUTPUT="simpletest-`date +%Y%m%d`.txt"
rm ${OUTPUT}

runByIgnores() {
  ignores=( \
    # cbos modules
    'account' \
    'activity'
  )

  PROJECT="modules/dsi"
  for file in `find ${PROJECT} -name "*.info.yml"`; do
    module=$(basename $(dirname ${file}))
    FOUND=0
    for ignore in ${ignores[@]}; do
      if [[ ${module} == ${ignore} ]]; then
        FOUND=1
        break;
      fi
    done
    if [[ ${FOUND} == 1 ]]; then
      echo "Ignore $file"
    else
      echo "Testing $file"
      sudo -u $USER php \
        ./core/scripts/run-tests.sh --url http://localhost --verbose \
        $module >> $OUTPUT
    fi
  done
}

runByGroups() {
  groups=( \
    'organization' \
    'job' \
    'person' \
    'employee_assignment' \
    'element_entry' \
    'qualification' \
    'transaction' \
  )

  for group in ${groups[@]}; do
    echo "Testing $group"
    sudo -u $USER php \
      ./core/scripts/run-tests.sh --url http://localhost --verbose \
      $group >> $OUTPUT
  done
}

runByIgnores;
#runByGroups;
