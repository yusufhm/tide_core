#!/bin/sh
##
# Install site.
#
PROFILE=tide
ADMIN_NAME=admin
ADMIN_PASSWORD=admin
DOCROOT=/app/docroot

drush -r $DOCROOT si $PROFILE -y --account-name=$ADMIN_NAME --account-pass=$ADMIN_PASSWORD install_configure_form.enable_update_status_module=NULL install_configure_form.enable_update_status_emails=NULL
drush -r $DOCROOT php-eval "module_load_install('vicgovau_core'); vicgovau_core_enable_modules(TRUE);"
drush -r $DOCROOT ublk 1
drush -r $DOCROOT cr -y
