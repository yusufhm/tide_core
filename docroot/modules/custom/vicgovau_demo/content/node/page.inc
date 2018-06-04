<?php

/**
 * @file
 * Create demo pages.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_node_page() {
  $nodes = [];
  $repository = VicgovauDemoRepository::getInstance();

  for ($i = 1; $i <= 10; $i++) {
    $uid = VicgovauDemoHelper::randomUid();

    $site_sections = VicgovauDemoHelper::randomSiteSections();
    $primary_site_id = VicgovauDemoHelper::randomSite($site_sections);

    $status = Node::PUBLISHED;
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

    // Add What's Next.
    for ($j = 1; $j <= mt_rand(1, 3); $j++) {
      $link_field_value = VicgovauDemoHelper::randomLinkFieldValue();
      if (!count($link_field_value)) {
        break;
      }
      $whats_next_data = [
        'type' => 'links',
        'field_paragraph_summary' => [['value' => VicgovauDemoHelper::randomPlainParagraph()]],
        'field_paragraph_link' => [$link_field_value],
      ];
      $whats_next = Paragraph::create($whats_next_data);
      $whats_next->save();
      $repository->trackEntity($whats_next);
      $node_data['field_whats_next'][] = [
        'target_id' => $whats_next->id(),
        'target_revision_id' => $whats_next->getRevisionId(),
      ];
    }

    // Add Related links.
    for ($j = 1; $j <= mt_rand(1, 5); $j++) {
      $link_field_value = VicgovauDemoHelper::randomLinkFieldValue();
      if (!count($link_field_value)) {
        break;
      }
      $related_link_data = [
        'type' => 'related_links',
        'field_paragraph_summary' => ['value' => VicgovauDemoHelper::randomPlainParagraph()],
        'field_paragraph_link' => [$link_field_value],
      ];
      $related_link = Paragraph::create($related_link_data);
      $related_link->save();
      $repository->trackEntity($related_link);
      $node_data['field_related_links'][] = [
        'target_id' => $related_link->id(),
        'target_revision_id' => $related_link->getRevisionId(),
      ];
    }

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
