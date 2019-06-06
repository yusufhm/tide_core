<?php

/**
 * @file
 * Load demo Event terms.
 */

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_taxonomy_term_department() {
  $departments = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $departments = $storage->loadByProperties(['vid' => 'department']);
  }
  catch (Exception $exception) {
    \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
      '%function' => __FUNCTION__,
      '%exception' => $exception->getMessage(),
    ]);
    watchdog_exception('vicgovau_demo', $exception);
  }

  return $departments;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function vicgovau_demo_create_taxonomy_term_department_tracking() {
  return FALSE;
}
