<?php

/**
 * @file
 * Create demo grant.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\vicgovau_demo\VicgovauDemoHelper;
use Drupal\vicgovau_demo\VicgovauDemoRepository;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE_weight().
 */
function vicgovau_demo_create_node_grant_weight() {
  return 11;
}

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_node_grant() {
  $nodes = [];
  $repository = VicgovauDemoRepository::getInstance();

  for ($i = 1; $i <= 10; $i++) {
    $uid = VicgovauDemoHelper::randomUid();

    $site_sections = VicgovauDemoHelper::randomSiteSections();
    $primary_site_id = VicgovauDemoHelper::randomSite($site_sections);

    $status = Node::PUBLISHED;
    $node_data = [
      'type' => 'grant',
      'title' => VicgovauDemoHelper::randomSentence(),
      'uid' => $uid,
      'status' => $status,
      'field_landing_page_intro_text' => [
        'value' => VicgovauDemoHelper::randomPlainParagraph(),
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
      'field_node_department' => [
        ['target_id' => VicgovauDemoHelper::randomGrantDepartment()],
      ],
      'field_node_overview' => [
        'value' => VicgovauDemoHelper::randomPlainParagraph(),
        'format' => 'rich_text',
      ],
      'field_featured_image' => [
        ['target_id' => VicgovauDemoHelper::randomImage()],
      ],
      'field_landing_page_summary' => [
        'value' => VicgovauDemoHelper::randomSentence(10, 20),
      ],
      'field_node_dates' => [
        [
          'value' => VicgovauDemoHelper::randomDate(),
          'end_value' => VicgovauDemoHelper::randomDate(),
        ],
      ],
      'field_node_fundinglevel' => [
        'value' => mt_rand(10000, 16000),
      ],
    ];

    // Add tags.
    $tags = VicgovauDemoHelper::randomTags(3);
    if (!empty($tags)) {
      foreach ($tags as $tag_id => $tag) {
        $node_data['field_tags'][] = ['target_id' => $tag_id];
      }
    }

    // Add guidelines content.
    $accordion = VicgovauDemoHelper::randomAccordionContent();
    $node_data['field_node_guidelines'] = [
      'target_id' => $accordion->id(),
      'target_revision_id' => $accordion->getRevisionId(),
    ];

    // Add Contact Us.
    $contact_us_data = [
      'type' => 'contact_us',
      'field_paragraph_title' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_name' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_email' => 'no-reply@vic.gov.au',
      'field_paragraph_cta' => [VicgovauDemoHelper::randomCtaLinkFieldValue()],
      'field_paragraph_department_name' => VicgovauDemoHelper::randomSentence(3, 5),
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
      'field_paragraph_postal_address' => [
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
    ];
    $contact_us = Paragraph::create($contact_us_data);
    $contact_us->save();
    $repository->trackEntity($contact_us);
    $node_data['field_landing_page_contact'] = [
      [
        'target_id' => $contact_us->id(),
        'target_revision_id' => $contact_us->getRevisionId(),
      ],
    ];

    $node = Node::create($node_data);

    $boolean_fields = [
      'field_landing_page_show_contact',
      'field_node_on_going',
      'field_show_social_sharing',
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