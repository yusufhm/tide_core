<?php

namespace Drupal\tide_core\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\ckeditor\CKEditorPluginButtonsInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "Callout" plugin, with a CKEditor.
 *
 * @CKEditorPlugin(
 *   id = "tide_callout",
 *   label = @Translation("Callout template Plugin")
 * )
 */
class TideCallout extends PluginBase implements CKEditorPluginInterface, CKEditorPluginButtonsInterface {

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'tide_core') . '/js/plugins/callout/plugin.js';
  }

  /**
   * @return array
   */
  public function getButtons() {
    $iconImage = drupal_get_path('module', 'tide_core') . '/js/plugins/callout/images/icon.png';

    return [
      'Callout' => [
        'label' => t('Callout template'),
        'image' => $iconImage,
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

}
