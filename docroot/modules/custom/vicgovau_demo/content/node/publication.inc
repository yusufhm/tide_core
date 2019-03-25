<?php

/**
 * @file
 * Create demo Publication.
 */

use Drupal\node\Entity\Node;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_weight().
 */
function vicgovau_demo_create_node_publication_weight() {
  return 110;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_node_publication() {
  $nodes = [];
  $total = 5;

  for ($i = 1; $i <= $total; $i++) {
    // Turn off Random mode for the last publication.
    $random = ($i < $total) ? TRUE : FALSE;

    $uid = VicgovauDemoHelper::randomUid();

    $site_sections = VicgovauDemoHelper::randomSiteSections();
    $primary_site_id = VicgovauDemoHelper::randomSite($site_sections);

    $status = Node::PUBLISHED;
    $node_data = [
      'type' => 'publication',
      'title' => $random ? VicgovauDemoHelper::randomSentence() : 'Demo Publication',
      'uid' => $uid,
      'status' => $status,
      'body' => [
        'value' => VicgovauDemoHelper::randomRichTextWithMedia(),
        'summary' => VicgovauDemoHelper::randomSentence(10, 30),
        'format' => 'rich_text',
      ],
      'field_node_site' => VicgovauDemoHelper::getFieldValueForSiteSections($site_sections),
      'field_node_primary_site' => [
        ['target_id' => $primary_site_id],
      ],
    ];

    if (!$random) {
      $node_data['field_node_site'][] = ['target_id' => VicgovauDemoRepository::SITE_ID_VICGOVAU];

      // Set path alias in the absence of Pathauto.
      if (!\Drupal::moduleHandler()->moduleExists('pathauto')) {
        $node_data['path'] = ['alias' => '/demo-publication'];
      }
    }

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
