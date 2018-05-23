#!/bin/sh
##
# Drupal deployment.
#

echo "Running database updates"
# Use this command to debug during the build process
# /app/scripts/xdebug.sh /app/vendor/bin/drush -r /app/docroot updb -y
drush updb -y
echo "Importing Drupal configuration"
drush cim -y
drush cr -y
echo "Enabling vicgovau modules"
drush php-eval "module_load_install('vicgovau_core'); vicgovau_core_enable_modules();"
drush cr -y
