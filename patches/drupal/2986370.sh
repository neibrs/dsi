#!/usr/bin/env bash

# Patch: Library from basetheme should not be loaded from subtheme
# https://www.drupal.org/project/drupal/issues/2794481
wget https://www.drupal.org/files/issues/2018-10-18/bootstrap_sass-fix-missing-file-library-2986370-13-d8.patch && \
  patch -p1 < bootstrap_sass-fix-missing-file-library-2986370-13-d8.patch && \
  rm bootstrap_sass-fix-missing-file-library-2986370-13-d8.patch

