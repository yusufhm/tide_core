<?php
/**
 * @file
 * amazee.io Drupal 8 production environment configuration file.
 *
 * This file will only be included on production environments.
 *
 * It contains some defaults that the amazee.io team suggests, please edit them as required.
 */

// Shield config.
$config['shield.settings']['user'] = 'dpc';
$config['shield.settings']['pass'] = 'sdp';

$config['config_split.config_split.dev']['status'] = TRUE;

// Environment indicator color override.
$config['environment_indicator.indicator']['name'] = 'DEV';
