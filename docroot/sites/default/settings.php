<?php

/**
 * @file
 * VICGOVAU Drupal 8 configuration file.
 */

// Environment settings. Each environment is expected to override this.
$settings['environment'] = 'local';

// Trusted Host Patterns.
// @see https://www.drupal.org/node/2410395 for more information.
$settings['trusted_host_patterns'] = [
  '^.+$',
];

// Shield config.
$config['shield.settings']['user'] = 'dpc';
$config['shield.settings']['pass'] = 'sdp';

// Stage file proxy
$config['stage_file_proxy.settings']['origin'] = 'http://dpc:sdp@nginx-php-content-vic-production.lagoon.vicsdp.amazee.io';

// Installation profile.
$settings['install_profile'] = 'tide';

// Include Bay settings.
// Please note, that a lot of configuration is provided in Bay settings by
// default. To override this configuration, use per-environment settings files.
if (file_exists('/bay')) {
  require '/bay/settings.php';
}

// Include local settings and services files.
// Those files should be located outside of `docroot` instead of `sites/default`
// because `composer app:rebuild` removes them.
if (file_exists(dirname(DRUPAL_ROOT) . '/local/settings.local.php')) {
  include dirname(DRUPAL_ROOT) . '/local/settings.local.php';
}
if (file_exists(dirname(DRUPAL_ROOT) . '/local/services.local.yml')) {
  $settings['container_yamls'][] = dirname(DRUPAL_ROOT) . '/local/services.local.yml';
}
