#!/usr/bin/env bash

# Patch:
#Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay class does not correspond to an entity type.
# https://www.drupal.org/project/drupal/issues/3127026
cd $DRUPAL && \
  wget https://www.drupal.org/files/issues/2020-04-13/3127026-layout_builder-1.patch && \
  patch -p1 < 3127026-layout_builder-1.patch && \
  rm 3127026-layout_builder-1.patch

