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
        ['target_id' => VicgovauDemoHelper::randomDepartment()],
      ],
      'field_overview_title' => [
        'value' => VicgovauDemoHelper::randomSentence(),
      ],
      'field_description' => [
        'value' => VicgovauDemoHelper::randomSentence(),
      ],
      ['field_call_to_action'] => [
        VicgovauDemoHelper::randomLinkFieldValue(),
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
    $accordion_data = [
      'type' => 'accordion',
      'field_paragraph_title' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_accordion_style' => [
        'value' => 'basic',
      ],
      'field_paragraph_accordion' => [],
    ];

    // Add 4 Guidelines accordion content data.
    $accordion_content = VicgovauDemoHelper::randomAccordionContent();
    $accordion_data['field_paragraph_accordion'][] = [
      'target_id' => $accordion_content->id(),
      'target_revision_id' => $accordion_content->getRevisionId(),
    ];

    $accordion_content = VicgovauDemoHelper::randomAccordionContent();
    $accordion_data['field_paragraph_accordion'][] = [
      'target_id' => $accordion_content->id(),
      'target_revision_id' => $accordion_content->getRevisionId(),
    ];

    $accordion_content = VicgovauDemoHelper::randomAccordionContent();
    $accordion_data['field_paragraph_accordion'][] = [
      'target_id' => $accordion_content->id(),
      'target_revision_id' => $accordion_content->getRevisionId(),
    ];

    $accordion_content = VicgovauDemoHelper::randomAccordionContent();
    $accordion_data['field_paragraph_accordion'][] = [
      'target_id' => $accordion_content->id(),
      'target_revision_id' => $accordion_content->getRevisionId(),
    ];

    // Create host paragraph.
    $accordion = Paragraph::create($accordion_data);
    $accordion->save();
    $repository->trackEntity($accordion);

    $node_data['field_node_guidelines'] = [
      'target_id' => $accordion->id(),
      'target_revision_id' => $accordion->getRevisionId(),
    ];

    // Add Grants Overview.
    $grant_overview_data = [
      'type' => 'grants_overview',
      'field_paragraph_title' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_summary' => VicgovauDemoHelper::randomSentence(10, 20),
      'field_paragraph_cta' => [VicgovauDemoHelper::randomCtaLinkFieldValue()],
      'field_paragraph_media' => [
        ['target_id' => VicgovauDemoHelper::randomImage()],
      ],
    ];

    $grant_overview = Paragraph::create($grant_overview_data);
    $grant_overview->save();
    $repository->trackEntity($grant_overview);
    $node_data['field_node_overview'] = [
      [
        'target_id' => $grant_overview->id(),
        'target_revision_id' => $grant_overview->getRevisionId(),
      ],
    ];

    // Add Timelines.
    $timelines_data = [
      'type' => 'timelines',
      'field_paragraph_title' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_timeline' => [],
    ];

    $timeline = Paragraph::create([
      'type' => 'timeline',
      'field_paragraph_title' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_summary' => VicgovauDemoHelper::randomSentence(10, 20),
      'field_paragraph_link' => [VicgovauDemoHelper::randomLinkFieldValue()],
      'field_paragraph_cta_text' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_department_name' => VicgovauDemoHelper::randomSentence(3, 5),
      'field_paragraph_date_range' => [
        [
          'value' => VicgovauDemoHelper::randomDate(),
          'end_value' => VicgovauDemoHelper::randomDate(),
        ],
      ],
    ]);
    $timeline->save();
    $repository = VicgovauDemoRepository::getInstance();
    $repository->trackEntity($timeline);

    $timelines_data['field_timeline'][] = [
      'target_id' => $timeline->id(),
      'target_revision_id' => $timeline->getRevisionId(),
    ];

    // Create host paragraph.
    $timelines = Paragraph::create($timelines_data);
    $timelines->save();
    $repository->trackEntity($timelines);

    $node_data['field_node_timeline'] = [
      'target_id' => $timelines->id(),
      'target_revision_id' => $timelines->getRevisionId(),
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
