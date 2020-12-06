#!/usr/bin/env bash

DRUPAL="$(pwd)"
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

# Enable lawyer industry
vendor/bin/drush mim contract_xlsx
vendor/bin/drush mim 30_client_xlsx
vendor/bin/drush mim 30_record_xlsx
vendor/bin/drush mim 208_client_xlsx
vendor/bin/drush mim 208_record_xlsx
