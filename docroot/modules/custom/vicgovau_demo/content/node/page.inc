<?php

/**
 * @file
 * Create demo pages.
 */

use Drupal\node\Entity\Node;
use Drupal\vicgovau_demo\VicgovauDemoHelper;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_node_page() {
  $nodes = [];

  for ($i = 1; $i <= 10; $i++) {
    $uid = VicgovauDemoHelper::randomUid();

    $site_sections = VicgovauDemoHelper::randomSiteSections();
    $primary_site_id = VicgovauDemoHelper::randomSite($site_sections);

    $status = VicgovauDemoHelper::randomBool();
    $node_data = [
      'type' => 'page',
      'title' => VicgovauDemoHelper::randomSentence(),
      'uid' => $uid,
      'status' => $status,
      'field_page_intro_text' => [
        'value' => VicgovauDemoHelper::randomPlainParagraph(),
      ],
      'body' => [
        'value' => VicgovauDemoHelper::randomRichTextWithMedia(),
        'summary' => VicgovauDemoHelper::randomSentence(10, 30),
        'format' => 'rich_text',
      ],
      'field_node_site' => VicgovauDemoHelper::getFieldValueForSiteSections($site_sections),
      'field_node_primary_site' => [
        ['target_id' => $primary_site_id],
      ],
      'field_topic' => [
        ['target_id' => VicgovauDemoHelper::randomTopic()],
      ],
      'field_featured_image' => [
        ['target_id' => VicgovauDemoHelper::randomImage()],
      ],
    ];

    $node = Node::create($node_data);

    $booleanFields = [
      'field_show_related_content',
      'field_show_social_sharing',
      'field_show_whats_next',
      'field_show_content_rating',
    ];
    foreach ($booleanFields as $booleanField) {
      if ($node->hasField($booleanField)) {
        $node->set($booleanField, VicgovauDemoHelper::randomBool());
      }
    }

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
