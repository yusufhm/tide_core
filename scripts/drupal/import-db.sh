#!/bin/sh
##
# Import prod db.
#

# Clear cache if db found.
[[ "$(drush core-status bootstrap --pipe)" != "" ]] && drush cr

drush sql-drop -y
drush sqlc < /tmp/.data/db.sql
#drush php-eval "module_load_install('vicgovau_core'); vicgovau_core_create_demo_users();"
drush sqlsan --sanitize-password=DpcFakePass --sanitize-email=user+%uid@localhost -y
# Ensure cache cleared
drush cr
