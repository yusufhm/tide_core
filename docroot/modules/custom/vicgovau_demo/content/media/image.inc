<?php

/**
 * @file
 * Create demo images.
 */

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_media_image() {
  $media = [];

  $repository = VicgovauDemoRepository::getInstance();

  $destination = 'public://vicgovau_demo/images';
  file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);

  $directory = drupal_get_path('module', 'vicgovau_demo') . '/content/media/images';
  $files = file_scan_directory($directory, '/.*\.jpg$/i');
  foreach ($files as $file) {
    try {
      $site_section = VicgovauDemoHelper::randomSiteSections();

      $source_file = File::create([
        'uri' => $file->uri,
        'filename' => $file->filename,
        'uid' => VicgovauDemoHelper::randomUid(),
      ]);
      $image_file = file_copy($source_file, 'public://vicgovau_demo/images', FILE_EXISTS_REPLACE);
      if ($image_file) {
        $image_file->setPermanent();
        $image_file->save();
        $repository->addDemoEntity($image_file, 'file', 'file');

        $image = Media::create([
          'bundle' => 'image',
          'uid' => $image_file->get('uid'),
          'status' => 1,
          'name' => 'Demo: ' . $file->name,
          'field_media_image' => [
            [
              'target_id' => $image_file->id(),
              'title' => $file->name,
              'alt' => $file->name,
            ],
          ],
          'field_license_type' => [
            ['target_id' => VicgovauDemoHelper::randomMediaLicense()],
          ],
          'field_media_restricted' => [['value' => VicgovauDemoHelper::randomBool()]],
          'field_media_site' => VicgovauDemoHelper::getFieldValueForSiteSections($site_section),
        ]);
        $image->save();
        $media[] = $image;
      }
    }
    catch (Exception $exception) {
      \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
        '%function' => __FUNCTION__,
        '%exception' => $exception->getMessage(),
      ]);
      watchdog_exception('vicgovau_demo', $exception);
    }
  }

  return $media;
}
