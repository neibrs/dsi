#!/usr/bin/env bash

# Patch: Allow end date to be optional
# https://www.drupal.org/project/drupal/issues/2794481
wget https://www.drupal.org/files/issues/2020-05-07/Allow_optional_start_and_end_date-2794481-99.patch && \
  patch -p1 < Allow_optional_start_and_end_date-2794481-99.patch && \
  rm Allow_optional_start_and_end_date-2794481-99.patch

