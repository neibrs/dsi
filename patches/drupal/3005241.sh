#!/usr/bin/env bash

# Patch: [PHP 7.2] Incompatible declaration of Drupal\migrate\Plugin\migrate\source\SqlBase::count()
# https://www.drupal.org/project/migrate_source_xls/issues/3005241
cd $DRUPAL/modules/contrib/migrate_source_xls && \
  wget https://www.drupal.org/files/issues/2018-10-09/3005241.patch && \
  patch -p1 < 3005241.patch && \
  rm 3005241.patch

