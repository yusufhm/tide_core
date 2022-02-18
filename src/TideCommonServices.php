<?php

namespace Drupal\tide_core;

/**
 * Provides helper functions for file.
 *
 * @package Drupal\tide_core
 */
class TideCommonServices {

  /**
   * Helper to sanitise spaces in filename.
   *
   * Option to include replacement as part of regular expression.
   *
   * @param string $filename
   *   Entity type to create the field for.
   * @param string $replacement
   *   Entity bundle to create the field for.
   * @param bool $include_in_pattern
   *   Flag to create form display. Defaults to TRUE.
   *
   * @return string
   *   Sanitised filename.
   */
  public function sanitiseFileName($filename, $replacement, $include_in_pattern = TRUE) {
    $pattern = $include_in_pattern ? '/[\s' . $replacement . ']+/' : '/[\s]+/';
    return preg_replace($pattern, $replacement, $filename);
  }

}
