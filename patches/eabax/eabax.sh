#!/bin/zsh

# 手动打patch

# eabax project
cd $(pwd)/modules/eabax && \
  patch -p1 < $(pwd)/../ds/patches/eabax/eabax_fail_to_create_add_page_link.patch
