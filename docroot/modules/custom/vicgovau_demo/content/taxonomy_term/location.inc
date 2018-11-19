<?php

/**
 * @file
 * Load demo Event terms.
 */

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_taxonomy_term_location() {
  $locations = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $locations = $storage->loadByProperties(['vid' => 'location']);
  }
  catch (Exception $exception) {
    watchdog_exception('vicgovau_demo', $exception);
  }

  return $locations;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function vicgovau_demo_create_taxonomy_term_location_tracking() {
  return FALSE;
}
