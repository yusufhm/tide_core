<?php

namespace Drupal\tide_core\Plugin\Block;

/**
 * @file
 * Quick Search block.
 */

use Drupal\Core\Block\BlockBase;

/**
 * Quick Search block.
 *
 * @Block(
 *   id = "tide_core_quick_search",
 *   admin_label = @Translation("Quick Search"),
 * )
 *
 * @package Drupal\tide_core\Plugin\Block
 */
class QuickSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'quick_search',
    ];
  }

}
