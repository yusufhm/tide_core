<?php

namespace Drupal\tide_core;

/**
 * Provides helper functions for file.
 *
 * @package Drupal\tide_core
 */
class TideCommonServices {

  /**
   * Helper to sanitise spaces in filename with option to include $replacement
   * as part of the $pattern.
   */
  public function sanitiseFileName($filename, $replacement, $inclusive = true) {
    $pattern = $inclusive ? '/[\s' . $replacement . ']+/' : '/[\s]+/';
        return preg_replace($pattern, $replacement, $filename);
  }
}