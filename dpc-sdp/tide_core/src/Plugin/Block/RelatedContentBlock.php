<?php

namespace Drupal\tide_core\Plugin\Block;

/**
 * @file
 * Related Content block.
 */

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Related Content block.
 *
 * @Block(
 *   id = "tide_core_related_content",
 *   admin_label = @Translation("Related Content"),
 * )
 *
 * @package Drupal\tide_core\Plugin\Block
 */
class RelatedContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  const FIELD_RELATED_CONTENT = 'field_related_content';
  const FIELD_SHOW_RELATED_CONTENT = 'field_show_related_content';

  /**
   * RouteMatch service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Cache tags.
   *
   * @var string[]
   */
  protected $cacheTags = [];

  /**
   * Cache contexts.
   *
   * @var string[]
   */
  protected $cacheContexts = ['url.path'];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\Core\Routing\RouteMatchInterface $route_match */
    $route_match = $container->get('current_route_match');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $route_match
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#cache' => [
        'tags' => $this->cacheTags,
        'contexts' => $this->cacheContexts,
      ],
    ];

    /** @var \Drupal\node\Entity\Node $node */
    $node = $this->routeMatch->getParameter('node');
    if ($node && node_is_page($node)) {
      // Retrieve cache info of the current node.
      $this->cacheTags = Cache::mergeTags($this->cacheTags, $node->getCacheTags());
      $this->cacheContexts = Cache::mergeContexts($this->cacheContexts, $node->getCacheContexts());

      // Only display this block when field_show_related_content is checked.
      $show_related_content = FALSE;
      if ($node->hasField(static::FIELD_SHOW_RELATED_CONTENT)
        && !$node->get(static::FIELD_SHOW_RELATED_CONTENT)->isEmpty()
      ) {
        $show_related_content = $node->get(static::FIELD_SHOW_RELATED_CONTENT)->getString() == '1';
      }

      if ($show_related_content) {
        // Render the block of links.
        if ($node->hasField(static::FIELD_RELATED_CONTENT)
          && !$node->get(static::FIELD_RELATED_CONTENT)->isEmpty()
        ) {
          $build['#theme'] = 'item_list';
          $build['#items'] = [];
          $related_content = $node->get(static::FIELD_RELATED_CONTENT)->getValue();
          foreach ($related_content as $link) {
            // Render individual link.
            $build['#items'][] = Link::fromTextAndUrl($link['title'], Url::fromUri($link['uri'], $link['options']));
          }
        }
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), $this->cacheTags);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), $this->cacheContexts);
  }

}
