<?php
/**
 * @file
 * lagoon Drupal 8 development environment configuration file.
 *
 * This file will only be included on development environments.
 *
 * It contains some defaults that the lagoon team suggests, please edit them as required.
 */

// Show all error messages on the site
$config['system.logging']['error_level'] = 'all';

 // Disable Google Analytics from sending dev GA data.
$config['google_analytics.settings']['account'] = 'UA-XXXXXXXX-YY';

// Expiration of cached pages to 0
$config['system.performance']['cache']['page']['max_age'] = 0;

// Aggregate CSS files off
$config['system.performance']['css']['preprocess'] = 0;

// Aggregate JavaScript files off
$config['system.performance']['js']['preprocess'] = 0;

// Disable Shield.
$config['shield.settings']['user'] = '';
$config['shield.settings']['pass'] = '';

// Enable local split.
$config['config_split.config_split.local']['status'] = TRUE;

// Environment indicator color override.
$config['environment_indicator.indicator']['name'] = 'LOCAL';
