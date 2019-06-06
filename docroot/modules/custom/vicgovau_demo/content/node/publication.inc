<?php

/**
 * @file
 * Create demo Publication.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
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
  $total = 1;
  $repository = VicgovauDemoRepository::getInstance();

  for ($i = 1; $i <= $total; $i++) {
    // Turn off Random mode for the last publication.
    $random = ($i < $total) ? TRUE : FALSE;

    $uid = VicgovauDemoHelper::randomUid();

    $site_sections = VicgovauDemoHelper::randomSiteSections();
    $primary_site_id = $random ? VicgovauDemoHelper::randomSite($site_sections) : VicgovauDemoRepository::SITE_ID_VICGOVAU;

    $status = Node::PUBLISHED;
    $node_data = [
      'type' => 'publication',
      'title' => $random ? VicgovauDemoHelper::randomSentence() : 'Demo Publication',
      'uid' => $uid,
      'status' => $status,
      'field_node_site' => VicgovauDemoHelper::getFieldValueForSiteSections($site_sections),
      'field_node_primary_site' => [
        ['target_id' => $primary_site_id],
      ],
      'field_landing_page_intro_text' => [
        'value' => VicgovauDemoHelper::randomPlainParagraph(),
      ],
      'field_landing_page_summary' => [
        'value' => VicgovauDemoHelper::randomSentence(10, 20),
      ],
      'field_license_type' => [
        ['target_id' => VicgovauDemoHelper::randomLicense()],
      ],
      'field_publication_date' => [
        'value' => VicgovauDemoHelper::randomDate(TRUE),
      ],
      'field_topic' => [
        ['target_id' => VicgovauDemoHelper::randomTopic()],
      ],
      'field_featured_image' => [
        ['target_id' => VicgovauDemoHelper::randomImage()],
      ],
    ];

    if (!$random) {
      $node_data['field_node_site'][] = ['target_id' => VicgovauDemoRepository::SITE_ID_VICGOVAU];

      // Set path alias in the absence of Pathauto.
      if (!\Drupal::moduleHandler()->moduleExists('pathauto')) {
        $node_data['path'] = ['alias' => '/demo-publication'];
      }
    }

    for ($k = 1; $k <= mt_rand(1, 5); $k++) {
      $node_data['field_publication_authors'][] = ['value' => VicgovauDemoHelper::randomSentence(1, 3)];
    }

    // Add Related links.
    for ($j = 1; $j <= mt_rand(1, 5); $j++) {
      $link_field_value = VicgovauDemoHelper::randomLinkFieldValue();
      if (!count($link_field_value)) {
        break;
      }
      $related_link_data = [
        'type' => 'related_links',
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

    // Add components.
    $components = VicgovauDemoHelper::randomLandingPageComponents(mt_rand(2, 5), $random, ['accordion', 'basic_text']);
    foreach ($components as $component) {
      /** @var \Drupal\paragraphs\Entity\Paragraph $component */
      $node_data['field_landing_page_component'][] = [
        'target_id' => $component->id(),
        'target_revision_id' => $component->getRevisionId(),
      ];
    }
    unset($components);

    // Add Documents.
    $documents = $repository->getDemoEntities('media', 'document');
    if (!empty($documents)) {
      $random_document = array_rand($documents);
      $node_data['field_node_documents'][] = [
        'target_id' => $documents[$random_document]->id(),
      ];
    }
    unset($documents);

    $node = Node::create($node_data);

    $boolean_fields = [
      'field_show_related_content',
      'field_show_social_sharing',
      'field_show_content_rating',
      'field_landing_page_show_contact',
      'field_show_table_of_content',
      'field_show_publication_nav',
    ];
    foreach ($boolean_fields as $boolean_field) {
      if ($node->hasField($boolean_field)) {
        $node->set($boolean_field, $random ? VicgovauDemoHelper::randomBool() : TRUE);
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
      \Drupal::logger('vicgovau_demo')->error('Exception occurs in the function %function: %exception', [
        '%function' => __FUNCTION__,
        '%exception' => $exception->getMessage(),
      ]);
      watchdog_exception('vicgovau_demo', $exception);
    }
  }

  return $nodes;
}
