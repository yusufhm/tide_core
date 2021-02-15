<?php

namespace Drupal\tide_core\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class TideCoreRouteAlter.
 *
 * @package Drupal\tide_core
 */
class TideCoreRouteAlter extends RouteSubscriberBase {

  /**
   * Alter scheduled_transitions route and path.
   *
   * {@inheritDoc}.
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('entity.node.scheduled_transitions');
    if ($route) {
      $route->setDefault('_title', 'Scheduled updates');
    }
    $route = $collection->get('entity.node.scheduled_transition_add');
    if ($route) {
      $route->setDefault('_title', 'Add Scheduled update');
    }
    $route = $collection->get('view.files.page_1');
    $enhanced_files_1_route = $collection->get('view.enhanced_files.page_1');
    if ($route && $enhanced_files_1_route) {
      $collection->add('view.files.page_1', clone $enhanced_files_1_route);
    }
  }

}
