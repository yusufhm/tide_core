#!/bin/sh
##
# Drupal deployment.
#

drush updb -y
drush cim -y
drush cr -y
drush php-eval "module_load_install('vicgovau_core'); vicgovau_core_enable_modules();"
drush cr -y
