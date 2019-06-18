<?php

/**
 * @file
 * Load demo Tags terms.
 */

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_taxonomy_term_tags() {
  $tags = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $tags = $storage->loadByProperties(['vid' => 'tags']);
  }
  catch (Exception $exception) {
    \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
      '%function' => __FUNCTION__,
      '%exception' => $exception->getMessage(),
    ]);
    watchdog_exception('vicgovau_demo', $exception);
  }

  return $tags;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function vicgovau_demo_create_taxonomy_term_tags_tracking() {
  return FALSE;
}
