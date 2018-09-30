<?php

/**
 * @file
 * Create demo event.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_weight().
 */
function vicgovau_demo_create_node_event_weight() {
  return 10;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_node_event() {
  $nodes = [];
  $repository = VicgovauDemoRepository::getInstance();

  for ($i = 1; $i <= 10; $i++) {
    $uid = VicgovauDemoHelper::randomUid();

    $site_sections = VicgovauDemoHelper::randomSiteSections();
    $primary_site_id = VicgovauDemoHelper::randomSite($site_sections);

    $status = Node::PUBLISHED;
    $node_data = [
      'type' => 'event',
      'title' => VicgovauDemoHelper::randomSentence(),
      'uid' => $uid,
      'status' => $status,
      'field_news_intro_text' => [
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
      'field_audience' => [
        ['target_id' => VicgovauDemoHelper::randomAudience()],
      ],
      'field_event_category' => [
        ['target_id' => VicgovauDemoHelper::randomEventCategory()],
      ],
      'field_event_description' => [
        'value' => VicgovauDemoHelper::randomPlainParagraph(),
        'format' => 'rich_text',
      ],
      'field_featured_image' => [
        ['target_id' => VicgovauDemoHelper::randomImage()],
      ],
      'field_landing_page_summary' => [
        'value' => VicgovauDemoHelper::randomSentence(10, 20),
      ],
    ];

    // Add tags.
    $tags = VicgovauDemoHelper::randomTags(3);
    if (!empty($tags)) {
      foreach ($tags as $tag_id => $tag) {
        $node_data['field_tags'][] = ['target_id' => $tag_id];
      }
    }

    // Add Event Details.
    $event_details_data = [
      'type' => 'event_details',
      'field_paragraph_link' => [VicgovauDemoHelper::randomCtaLinkFieldValue()],
      'field_paragraph_date_range' => [['value' => VicgovauDemoHelper::randomDate(), 'end_value' => VicgovauDemoHelper::randomDate()]],
      'field_event_requirements' => [
        ['target_id' => VicgovauDemoHelper::randomEventRequirements()],
      ],
      'field_paragraph_location' => [
        [
          'langcode' => '',
          'country_code' => 'AU',
          'administrative_area' => 'VIC',
          'locality' => 'Melbourne',
          'postal_code' => 3001,
          'address_line1' => 'Department of Premier and Cabinet',
          'address_line2' => 'GPO Box 4509',
        ],
      ],
      'field_paragraph_event_price_from' => [
        'value' => mt_rand(80, 120),
      ],
      'field_paragraph_event_price_to' => [
        'value' => mt_rand(120, 160),
      ],

    ];

    $event_details = Paragraph::create($event_details_data);
    $event_details->save();
    $repository->trackEntity($event_details);

    $node_data['field_event_details'] = [
      [
        'target_id' => $event_details->id(),
        'target_revision_id' => $event_details->getRevisionId(),
      ],
    ];

    $node = Node::create($node_data);

    $boolean_fields = [
      'field_landing_page_show_contact',
      'field_show_social_sharing',
      'field_landing_page_show_contact',
    ];
    foreach ($boolean_fields as $boolean_field) {
      if ($node->hasField($boolean_field)) {
        $node->set($boolean_field, VicgovauDemoHelper::randomBool());
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