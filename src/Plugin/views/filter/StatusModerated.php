<?php

namespace Drupal\tide_core\Plugin\views\filter;

use Drupal\node\Plugin\views\filter\Status;

/**
 * Filter by published status.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("node_status_moderated")
 */
class StatusModerated extends Status {

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\node\Plugin\views\filter\Status::query()
   */
  public function query() {
    $table = $this->ensureMyTable();
    // Check for the extra permission 'view any unpublished content'
    // provided by Content Moderation.
    $this->query->addWhereExpression($this->options['group'], "$table.status = 1 OR ($table.uid = ***CURRENT_USER*** AND ***CURRENT_USER*** <> 0 AND ***VIEW_OWN_UNPUBLISHED_NODES*** = 1) OR ***VIEW_ANY_UNPUBLISHED_NODES*** = 1 OR ***BYPASS_NODE_ACCESS*** = 1");
  }

}
