#!/usr/bin/env bash

# Add drupal 9 compatible
# https://www.drupal.org/project/block_style_plugins/issues/3144849#comment-13659292
cd modules/contrib/block_style_plugins && \
  wget https://www.drupal.org/files/issues/2020-06-02/3144849-1.patch && \
  patch -p1 < ./3144849-1.patch && \
  rm -rf 3144849-1.patch

