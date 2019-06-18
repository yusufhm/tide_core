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
  $created_pages = [];

  for ($i = 1; $i <= VicgovauDemoRepository::MAX_DEMO_PER_TYPE * 3; $i++) {
    $uid = VicgovauDemoHelper::randomUid();

    $level = ($i <= VicgovauDemoRepository::MAX_DEMO_PER_TYPE) ? 1 : 2;

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
      'field_node_site' => [
        ['target_id' => $primary_site_id],
      ],
      'field_node_primary_site' => [
        ['target_id' => $primary_site_id],
      ],
      'field_publication' => [
        [
          'target_id' => ($level == 1) ? $publication->id() : $created_pages[1][array_rand($created_pages[1])],
          'weight' => $i - 1,
        ],
      ],
      'field_landing_page_intro_text' => [
        'value' => VicgovauDemoHelper::randomPlainParagraph(),
      ],
      'field_landing_page_summary' => [
        'value' => VicgovauDemoHelper::randomSentence(10, 20),
      ],
    ];

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
    $components = VicgovauDemoHelper::randomLandingPageComponents(mt_rand(5, 10));
    foreach ($components as $component) {
      /** @var \Drupal\paragraphs\Entity\Paragraph $component */
      $node_data['field_landing_page_component'][] = [
        'target_id' => $component->id(),
        'target_revision_id' => $component->getRevisionId(),
      ];
    }

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
      $created_pages[$level][] = $node->id();
    }
    catch (Exception $exception) {

    }
  }

  return $nodes;
}
