<?php

/**
 * @file
 * Load demo Topic terms.
 */

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_taxonomy_term_topic() {
  $topics = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $topics = $storage->loadByProperties(['vid' => 'topic']);
  }
  catch (Exception $exception) {
    \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
      '%function' => __FUNCTION__,
      '%exception' => $exception->getMessage(),
    ]);
    watchdog_exception('vicgovau_demo', $exception);
  }

  return $topics;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function vicgovau_demo_create_taxonomy_term_topic_tracking() {
  return FALSE;
}
