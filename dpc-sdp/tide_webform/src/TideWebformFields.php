<?php

namespace Drupal\tide_webform;

use Drupal\tide_core\TideCoreFields;

/**
 * Class TideWebformFields.
 *
 * @package Drupal\tide_webform.
 */
class TideWebformFields extends TideCoreFields {

  /**
   * Show content rating field machine name.
   */
  const FIELD_SHOW_CONTENT_RATING = 'field_show_content_rating';

  /**
   * Helper to convert field name to machine name.
   */
  public static function normaliseFieldName($field_name, $entity_type_id = '', $bundle = '') {
    $field_name = str_replace('__', '_', $field_name);

    return $field_name;
  }

  /**
   * Config for 'Show Content Rating?' filed.
   */
  protected function getFieldShowContentRatingConfig() {
    return [
      'field_name' => self::FIELD_SHOW_CONTENT_RATING,
      'label' => 'Show Content Rating?',
      'description' => 'Check this box if you want to show content ratings on this page.',
      'default_value' => [
        ['value' => 1],
      ],
      'settings' => [
        'on_label' => 'On',
        'off_label' => 'Off',
      ],
    ];
  }

  /**
   * Config for 'Show Content Rating?' filed form display components.
   */
  protected function getFieldShowContentRatingFormDisplayComponents() {
    return [
      'type' => 'boolean_checkbox',
      'region' => 'content',
    ];
  }

}
