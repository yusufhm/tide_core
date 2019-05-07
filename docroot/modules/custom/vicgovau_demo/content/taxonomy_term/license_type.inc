<?php

/**
 * @file
 * Load demo License Type terms.
 */

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_taxonomy_term_license_type() {
  $licenses = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $licenses = $storage->loadByProperties(['vid' => 'license_type']);
  }
  catch (Exception $exception) {
    watchdog_exception('vicgovau_demo', $exception);
  }

  return $licenses;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function vicgovau_demo_create_taxonomy_term_license_type_tracking() {
  return FALSE;
}
