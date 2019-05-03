<?php

/**
 * @file
 * Create demo documents.
 */

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_media_document() {
  $media = [];

  $repository = VicgovauDemoRepository::getInstance();

  $destination = 'public://vicgovau_demo/documents';
  file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);

  $directory = drupal_get_path('module', 'vicgovau_demo') . '/content/media/documents';
  $source_file = File::create([
    'uri' => $directory . '/sample.docx',
    'filename' => 'sample.docx',
    'uid' => VicgovauDemoHelper::randomUid(),
  ]);

  for ($i = 1; $i <= VicgovauDemoRepository::MAX_DEMO_PER_TYPE; $i++) {
    try {
      $site_section = VicgovauDemoHelper::randomSiteSections();

      $destination_file = 'public://vicgovau_demo/documents/demo_' . $i . '_sample.docx';
      $document_file = file_copy($source_file, $destination_file, FILE_EXISTS_REPLACE);
      if ($document_file) {
        $document_file->setPermanent();
        $document_file->save();
        $repository->addDemoEntity($document_file, 'file', 'file');

        $document = Media::create([
          'bundle' => 'document',
          'uid' => $document_file->get('uid'),
          'status' => 1,
          'name' => 'Demo: ' . VicgovauDemoHelper::randomSentence(2, 5),
          'field_media_file' => [
            [
              'target_id' => $document_file->id(),
            ],
          ],
          'field_license_type' => [
            ['target_id' => VicgovauDemoHelper::randomMediaLicense()],
          ],
          'field_media_restricted' => [['value' => VicgovauDemoHelper::randomBool()]],
          'field_media_site' => VicgovauDemoHelper::getFieldValueForSiteSections($site_section),
        ]);
        $document->save();
        $media[] = $document;
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
