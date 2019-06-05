<?php

/**
 * @file
 * Load demo Campaign blocks.
 */

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_block_content_campaign() {
  $campaigns = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('block_content');
    $campaigns = $storage->loadByProperties(['type' => 'campaign']);
  }
  catch (Exception $exception) {
    \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
      '%function' => __FUNCTION__,
      '%exception' => $exception->getMessage(),
    ]);
    watchdog_exception('vicgovau_demo', $exception);
  }

  return $campaigns;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function vicgovau_demo_create_block_content_campaign_tracking() {
  return FALSE;
}
