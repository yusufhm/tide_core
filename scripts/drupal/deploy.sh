#!/bin/sh
##
# Drupal deployment.
#

echo "Running database updates"
drush updb -y
drush cr -y
echo "Importing Drupal configuration"
drush cim -y
drush cr -y
echo "Enabling vigovau modules"
drush php-eval "module_load_install('vicgovau_core'); vicgovau_core_enable_modules();"
drush cr -y
