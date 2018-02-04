#!/bin/sh
##
# Install site.
#
PROFILE=tide
ADMIN_NAME=admin
ADMIN_PASSWORD=admin
SITE_CORE=vicgovau_core
DOCROOT=/app/docroot

drush -r $DOCROOT si $PROFILE -y --account-name=$ADMIN_NAME --account-pass=$ADMIN_PASSWORD install_configure_form.enable_update_status_module=NULL install_configure_form.enable_update_status_emails=NULL
drush -r $DOCROOT en $SITE_CORE -y
drush -r $DOCROOT cr -y
