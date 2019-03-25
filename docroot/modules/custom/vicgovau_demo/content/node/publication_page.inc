<?php

/**
 * @file
 * Create demo Publication pages.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_weight().
 */
function vicgovau_demo_create_node_publication_page_weight() {
  return 111;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_node_publication_page() {
  $nodes = [];

  for ($i = 1; $i <= 15; $i++) {
    $uid = VicgovauDemoHelper::randomUid();

    $repository = VicgovauDemoRepository::getInstance();
    $publications = $repository->getDemoEntities('node', 'publication');
    /** @var \Drupal\node\Entity\Node $publication */
    $publication = $publications[array_rand($publications)];

    /** @var \Drupal\tide_site\TideSiteHelper $site_helper */
    $site_helper = \Drupal::service('tide_site.helper');
    $primary_site_id = $site_helper->getEntityPrimarySite($publication)->id();

    $status = Node::PUBLISHED;
    $node_data = [
      'type' => 'publication_page',
      'title' => VicgovauDemoHelper::randomSentence(),
      'uid' => $uid,
      'status' => $status,
      'body' => [
        'value' => VicgovauDemoHelper::randomRichTextWithMedia(),
        'summary' => VicgovauDemoHelper::randomSentence(10, 30),
        'format' => 'rich_text',
      ],
      'field_node_site' => [
        ['target_id' => $primary_site_id],
      ],
      'field_node_primary_site' => [
        ['target_id' => $primary_site_id],
      ],
      'field_publication' => [
        ['target_id' => $publication->id()],
      ],
    ];

    $node = Node::create($node_data);

    if ($node->hasField('moderation_state')) {
      if ($status) {
        $node->set('moderation_state', 'published');
      }
    }

    try {
      $node->save();
      $nodes[] = $node;
    }
    catch (Exception $exception) {
      watchdog_exception('vicgovau_demo', $exception);
    }
  }

  return $nodes;
}
