#!/usr/bin/env bash

DRUPAL="$(pwd)"
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"

#1.
source $SCRIPTPATH/drupal/3005241.sh
#source $SCRIPTPATH/drupal/2794481.sh
source $SCRIPTPATH/drupal/3127026.sh
source $SCRIPTPATH/drupal/2986370.sh

#2. Delete slick.settings.yml without slick module

source $SCRIPTPATH/contrib/block_style_plugins.sh
