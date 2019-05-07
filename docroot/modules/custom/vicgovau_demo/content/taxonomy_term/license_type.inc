<?php

/**
 * @file
 * Load demo License Type terms.
 */

use Drupal\taxonomy\Entity\Term;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_taxonomy_term_license_type() {
  $licenses = [];
  try {
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $licenses = $storage->loadByProperties(['vid' => 'license_type']);

    $demo_license = Term::create([
      'name' => 'Demo',
      'vid' => 'license_type',
      'weight' => 1000,
    ]);
    $demo_license->save();
    $repository = VicgovauDemoRepository::getInstance();
    $repository->trackEntity($demo_license);
    $licenses[$demo_license->id()] = $demo_license;

  }
  catch (Exception $exception) {
    \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
      '%function' => __FUNCTION__,
      '%exception' => $exception->getMessage(),
    ]);
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
