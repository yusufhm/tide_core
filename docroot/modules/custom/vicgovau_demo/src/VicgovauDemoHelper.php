<?php

namespace Drupal\vicgovau_demo;

use Drupal\Component\Utility\Random;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\smart_trim\Truncate\TruncateHTML;

/**
 * Class VicgovauDemoHelper.
 *
 * @package Drupal\vicgovau_demo
 */
class VicgovauDemoHelper {

  /**
   * Generate a random title.
   *
   * @return string
   *   The title.
   */
  public static function randomSentence($min_word_count = 5, $max_word_count = 10) {
    $randomiser = new Random();
    $truncator = new TruncateHTML();

    $title = $randomiser->sentences($min_word_count);
    return $truncator->truncateWords($title, mt_rand($min_word_count, $max_word_count), '');
  }

  /**
   * Generate a random plain text paragraph.
   *
   * @return string
   *   The paragraph.
   */
  public static function randomPlainParagraph() {
    $randomiser = new Random();
    return str_replace(["\r", "\n"], '', $randomiser->paragraphs(1));
  }

  /**
   * Generate a random HTML paragraph.
   *
   * @return string
   *   The paragraph.
   */
  public static function randomHtmlParagraph() {
    return '<p>' . static::randomPlainParagraph() . '</p>';
  }

  /**
   * Generate a random Callout paragraph.
   *
   * @return string
   *   The paragraph.
   */
  public static function randomCalloutParagraph() {
    return '<p class="wysiwyg-callout">' . static::randomPlainParagraph() . '</p>';
  }

  /**
   * Generate a random Blockquote paragraph.
   *
   * @return string
   *   The paragraph.
   */
  public static function randomBlockquoteParagraph() {
    return '<blockquote class="quotation">
      <p>' . static::randomPlainParagraph() . '</p>
      <footer><cite>
      <span class="quotation__author">' . static::randomSentence(1, 5) . '</span><br />
      <span class="quotation__author-title">' . static::randomSentence(1, 5) . '</span><br />
      </cite></footer>
      </blockquote>';
  }

  /**
   * Generate a random HTML heading.
   *
   * @return string
   *   The heading.
   */
  public static function randomHtmlHeading($min_word_count = 5, $max_word_count = 10, $heading_level = 0) {
    if (!$heading_level) {
      $heading_level = mt_rand(2, 5);
    }
    return '<h' . $heading_level . '>' . static::randomSentence($min_word_count, $max_word_count) . '</h' . $heading_level . '>';
  }

  /**
   * Generate random HTML paragraphs.
   *
   * @param int $min_paragraph_count
   *   Minimum number of paragraphs to generate.
   * @param int $max_paragraph_count
   *   Maximum number of paragraphs to generate.
   *
   * @return string
   *   Paragraphs.
   */
  public static function randomRichText($min_paragraph_count = 3, $max_paragraph_count = 12) {
    $paragraphs = [];
    $paragraph_count = mt_rand($min_paragraph_count, $max_paragraph_count);
    for ($i = 1; $i <= $paragraph_count; $i++) {
      if ($i % 2) {
        $paragraphs[] = static::randomHtmlHeading();
      }
      $paragraphs[] = static::randomHtmlParagraph();
    }

    return implode(PHP_EOL, $paragraphs);
  }

  /**
   * Generate random HTML paragraphs with embedded media.
   *
   * @param int $min_paragraph_count
   *   Minimum number of paragraphs to generate.
   * @param int $max_paragraph_count
   *   Maximum number of paragraphs to generate.
   *
   * @return string
   *   Paragraphs.
   */
  public static function randomRichTextWithMedia($min_paragraph_count = 5, $max_paragraph_count = 12) {
    $paragraphs = [];
    $paragraph_count = mt_rand($min_paragraph_count, $max_paragraph_count);
    for ($i = 1; $i <= $paragraph_count; $i++) {
      if ($i % 2) {
        $paragraphs[] = static::randomHtmlHeading();
      }
      if (!($i % 4)) {
        $paragraphs[] = static::randomEmbeddedMedia();
      }

      if (!($i % 5)) {
        $paragraphs[] = static::randomCalloutParagraph();
      }
      elseif (!($i % 7)) {
        $paragraphs[] = static::randomBlockquoteParagraph();
      }
      else {
        $paragraphs[] = static::randomHtmlParagraph();
      }
    }

    return implode(PHP_EOL, $paragraphs);
  }

  /**
   * Generate a random boolean value.
   *
   * @return bool
   *   Random value.
   */
  public static function randomBool() {
    return mt_rand(0, 1000) < 500;
  }

  /**
   * Select a random uid.
   *
   * @return int
   *   The uid.
   */
  public static function randomUid() {
    $repository = VicgovauDemoRepository::getInstance();

    $users = [1 => 1];
    $users += $repository->getDemoEntities('user', 'user');
    return array_rand($users);
  }

  /**
   * Select random sites and site sections.
   *
   * @return array
   *   The sites and section.
   */
  public static function randomSiteSections() {
    $repository = VicgovauDemoRepository::getInstance();
    $sites = $repository->getDemoEntities('site', 'site');
    $sections = $repository->getDemoEntities('site_section');

    $site_ids = array_rand($sites, min(2, count($sites)));
    $section_ids = [];
    foreach ($site_ids as $site_id) {
      $section_ids[$site_id] = $site_id;
      $section_id = array_rand($sections['site:' . $site_id]);
      $section_ids[$section_id] = $section_id;
    }

    return $section_ids;
  }

  /**
   * Return the array for field value from randomSiteSections().
   *
   * @param array $site_section_ids
   *   The list of IDs.
   *
   * @return array
   *   The array.
   */
  public static function getFieldValueForSiteSections(array $site_section_ids) {
    $values = [];
    foreach ($site_section_ids as $site_section_id) {
      $values[] = ['target_id' => $site_section_id];
    }
    return $values;
  }

  /**
   * Select a random site from randomSiteSections().
   *
   * @param array $site_section_ids
   *   The list of IDs.
   *
   * @return int
   *   The site id.
   */
  public static function randomSite(array $site_section_ids) {
    $repository = VicgovauDemoRepository::getInstance();
    $sites = $repository->getDemoEntities('site', 'site');
    $sites = array_keys($sites);
    $site_ids = array_intersect($sites, $site_section_ids);
    return $site_ids[array_rand($site_ids)];
  }

  /**
   * Select a random Topic.
   *
   * @return int
   *   The topic tid.
   */
  public static function randomTopic() {
    $repository = VicgovauDemoRepository::getInstance();
    $topics = $repository->getDemoEntities('taxonomy_term', 'topic');
    return count($topics) ? array_rand($topics) : 0;
  }

  /**
   * Select a random Audience.
   *
   * @return int
   *   The audience tid.
   */
  public static function randomAudience() {
    $repository = VicgovauDemoRepository::getInstance();
    $topics = $repository->getDemoEntities('taxonomy_term', 'audience');
    return count($topics) ? array_rand($topics) : 0;
  }

  /**
   * Select a random Event Category.
   *
   * @return int
   *   The event tid.
   */
  public static function randomEventCategory() {
    $repository = VicgovauDemoRepository::getInstance();
    $topics = $repository->getDemoEntities('taxonomy_term', 'event');
    return count($topics) ? array_rand($topics) : 0;
  }

  /**
   * Select random Tags.
   *
   * @param int $count
   *   The number of tags to return.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   The list of tags, keyed by tag id.
   */
  public static function randomTags($count = 3) {
    $repository = VicgovauDemoRepository::getInstance();
    $tags = $repository->getDemoEntities('taxonomy_term', 'tags');
    if (count($tags)) {
      $random_tags = [];
      foreach (array_rand($tags, $count) as $id) {
        /** @var \Drupal\taxonomy\TermInterface $tag */
        $tag = $tags[$id];
        $random_tags[$tag->id()] = $tag;
      }
      return $random_tags;
    }
    return [];
  }

  /**
   * Select a random Campaign.
   *
   * @return \Drupal\block_content\Entity\BlockContent
   *   The Campaign block.
   */
  public static function randomCampaign() {
    $repository = VicgovauDemoRepository::getInstance();
    $campaigns = $repository->getDemoEntities('block_content', 'campaign');
    return count($campaigns) ? $campaigns[array_rand($campaigns)] : NULL;
  }

  /**
   * Select a random Image Gallery.
   *
   * @return \Drupal\block_content\Entity\BlockContent
   *   The Gallery block.
   */
  public static function randomImageGallery() {
    $repository = VicgovauDemoRepository::getInstance();
    $galleries = $repository->getDemoEntities('block_content', 'media_gallery');
    return count($galleries) ? $galleries[array_rand($galleries)] : NULL;
  }

  /**
   * Select a random image.
   *
   * @return int
   *   The image id.
   */
  public static function randomImage() {
    $repository = VicgovauDemoRepository::getInstance();
    $images = $repository->getDemoEntities('media', 'image');
    return count($images) ? array_rand($images) : 0;
  }

  /**
   * Select a random media to embed in WYSIWYG.
   *
   * @return string
   *   Embedded media.
   */
  public static function randomEmbeddedMedia() {
    $repository = VicgovauDemoRepository::getInstance();
    $media = $repository->getDemoEntities('media');
    if (count($media)) {
      $bundle = array_rand($media);
      /** @var \Drupal\media\Entity\Media $embed */
      $embed = $media[$bundle][array_rand($media[$bundle])];

      return '<drupal-entity data-embed-button="tide_media" data-entity-embed-display="view_mode:media.embedded" data-entity-type="media" data-entity-uuid="' . $embed->uuid() . '"></drupal-entity>';
    }

    return '';
  }

  /**
   * Select a random Page.
   *
   * @param bool $return_entity
   *   Whether to return the full entity or just entity ID.
   *
   * @return int|\Drupal\node\Entity\Node
   *   Entity ID or full entity.
   */
  public static function randomPage($return_entity = FALSE) {
    $repository = VicgovauDemoRepository::getInstance();
    $pages = $repository->getDemoEntities('node', 'page');
    if (count($pages)) {
      $page_id = array_rand($pages);
      return $return_entity ? $pages[$page_id] : $page_id;
    }
    return NULL;
  }

  /**
   * Select random News items.
   *
   * @param bool $return_entity
   *   Whether to return the full entity or just entity ID.
   * @param int $count
   *   Number of News items to select.
   *
   * @return int[]|\Drupal\node\Entity\Node[]
   *   Array of Entity IDs or full entities.
   */
  public static function randomNews($return_entity = FALSE, $count = 3) {
    $repository = VicgovauDemoRepository::getInstance();
    $news = $repository->getDemoEntities('node', 'news');
    if (count($news)) {
      $news_ids = array_rand($news, $count);
      $results = [];
      foreach ($news_ids as $news_id) {
        $results[$news_id] = $return_entity ? $news[$news_id] : $news_id;
      }
      return $results;
    }
    return [];
  }

  /**
   * Generate a random link field from a random page.
   *
   * @return array
   *   Link field.
   */
  public static function randomLinkFieldValue() {
    $page = static::randomPage(TRUE);
    if ($page) {
      return [
        'uri' => 'entity:node/' . $page->id(),
        'title' => $page->getTitle(),
      ];
    }
    return [];
  }

  /**
   * Generate a random CTA link field from a random page.
   *
   * @return array
   *   CTA Link field.
   */
  public static function randomCtaLinkFieldValue() {
    $page = static::randomPage();
    if ($page) {
      return [
        'uri' => 'entity:node/' . $page,
        'title' => 'Read more',
      ];
    }
    return [];
  }

  /**
   * Return a random timestamp in the range [-1y, +1y].
   *
   * @return int
   *   Timestamp.
   */
  public static function randomTimestamp() {
    $current = time();
    $one_year = 31536000;
    return mt_rand($current - $one_year, $current + $one_year);
  }

  /**
   * Generate a random date.
   *
   * @return string
   *   Date string.
   */
  public static function randomDate() {
    $random_timestamp = static::randomTimestamp();
    return date('Y-m-d\TH:i:00', $random_timestamp);
  }

  /**
   * Generate a random Keydate paragraph.
   *
   * @return \Drupal\paragraphs\Entity\Paragraph
   *   The Keydate.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function randomKeydate() {
    $random_timestamp = static::randomTimestamp();
    $keydate = Paragraph::create([
      'type' => 'keydates',
      'field_paragraph_title' => [['value' => static::randomSentence()]],
      'field_paragraph_keydate' => [['value' => date('F d', $random_timestamp)]],
      'field_paragraph_link' => [VicgovauDemoHelper::randomLinkFieldValue()],
      'field_paragraph_summary' => [
        'value' => static::randomPlainParagraph(),
      ],
    ]);
    $keydate->save();
    $repository = VicgovauDemoRepository::getInstance();
    $repository->trackEntity($keydate);
    return $keydate;
  }

  /**
   * Generate a random Accordion Content paragraph.
   *
   * @return \Drupal\paragraphs\Entity\Paragraph
   *   The Accordion content.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function randomAccordionContent() {
    $accordion = Paragraph::create([
      'type' => 'accordion_content',
      'field_paragraph_accordion_name' => [['value' => static::randomSentence()]],
      'field_paragraph_accordion_body' => [
        'value' => static::randomRichText(1, 3),
        'format' => 'rich_text',
      ],
    ]);
    $accordion->save();
    $repository = VicgovauDemoRepository::getInstance();
    $repository->trackEntity($accordion);
    return $accordion;
  }

  /**
   * Generate a random Introduction Banner paragraph.
   *
   * @return \Drupal\paragraphs\Entity\Paragraph
   *   The Intro banner.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function randomIntroductionBanner() {
    $banner_data = [
      'type' => 'introduction_banner',
      'field_paragraph_title' => [['value' => static::randomSentence()]],
      'field_paragraph_summary' => [
        'value' => static::randomPlainParagraph(),
      ],
    ];
    for ($j = 1; $j <= mt_rand(0, 3); $j++) {
      $banner_data['field_paragraph_links'][] = static::randomLinkFieldValue();
    }
    $banner = Paragraph::create($banner_data);
    $banner->save();
    $repository = VicgovauDemoRepository::getInstance();
    $repository->trackEntity($banner);
    return $banner;
  }

  /**
   * Generate random landing page components.
   *
   * @param int $component_count
   *   Number of components to generate.
   * @param bool $random
   *   If FALSE, all component types will be randomly generated.
   *
   * @return array
   *   The components.
   */
  public static function randomLandingPageComponents($component_count = 20, $random = TRUE) {
    $repository = VicgovauDemoRepository::getInstance();

    $supported_components = [
      'accordion',
      'basic_text',
      'call_to_action',
      'card_event',
      'card_promotion',
      'card_promotion_auto',
      'card_navigation',
      'card_navigation_auto',
      'card_navigation_featured',
      'card_navigation_featured_auto',
      'card_keydates',
      'featured_news',
      'media_gallery',
      'news_listing',
    ];

    $accordion_styles = ['basic', 'numbered'];
    if (!$random) {
      // All 2 styles of accordion should be generated.
      $supported_components[] = 'accordion';
      $component_count = count($supported_components);
    }

    $components = [];
    while ($component_count) {
      if ($random) {
        $component_type = $supported_components[array_rand($supported_components)];
      }
      else {
        shuffle($supported_components);
        $component_type = array_pop($supported_components);
      }

      $component_data = [
        'type' => $component_type,
      ];
      switch ($component_type) {
        case 'accordion':
          $component_data += [
            'field_paragraph_title' => [['value' => static::randomSentence()]],
            'field_paragraph_accordion_style' => [
              'value' => $random ? $accordion_styles[array_rand($accordion_styles)] : array_pop($accordion_styles),
            ],
            'field_paragraph_accordion' => [],
          ];
          $count = mt_rand(3, 5);
          for ($i = 1; $i <= $count; $i++) {
            try {
              $accordion_content = static::randomAccordionContent();
              $component_data['field_paragraph_accordion'][] = [
                'target_id' => $accordion_content->id(),
                'target_revision_id' => $accordion_content->getRevisionId(),
              ];
            }
            catch (\Exception $exception) {
              watchdog_exception('vicgovau_demo', $exception);
            }
          }
          break;

        case 'basic_text':
          $component_data['field_paragraph_body'][] = [
            'value' => static::randomRichText(1, 4),
            'format' => 'rich_text',
          ];
          break;

        case 'call_to_action':
          $component_data += [
            'field_paragraph_title' => [['value' => static::randomSentence()]],
            'field_paragraph_media' => [['target_id' => static::randomImage()]],
            'field_paragraph_body' => [
              'value' => static::randomPlainParagraph(),
            ],
            'field_paragraph_cta' => [VicgovauDemoHelper::randomCtaLinkFieldValue()],
            'field_paragraph_cta_style' => ['value' => static::randomBool() ? 'banner' : 'card'],
          ];
          break;

        case 'card_event':
          $component_data += [
            'field_paragraph_title' => [['value' => static::randomSentence()]],
            'field_paragraph_date' => [['value' => static::randomDate()]],
            'field_paragraph_media' => [['target_id' => static::randomImage()]],
            'field_paragraph_summary' => [
              'value' => static::randomPlainParagraph(),
            ],
            'field_paragraph_topic' => [['target_id' => static::randomTopic()]],
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
            'field_paragraph_cta' => [VicgovauDemoHelper::randomCtaLinkFieldValue()],
          ];
          break;

        case 'card_promotion':
          $component_data += [
            'field_paragraph_title' => [['value' => static::randomSentence()]],
            'field_paragraph_date' => [['value' => static::randomDate()]],
            'field_paragraph_media' => [['target_id' => static::randomImage()]],
            'field_paragraph_summary' => [
              'value' => static::randomPlainParagraph(),
            ],
            'field_paragraph_topic' => [['target_id' => static::randomTopic()]],
            'field_paragraph_link' => [VicgovauDemoHelper::randomLinkFieldValue()],
          ];
          break;

        case 'card_navigation':
          $component_data += [
            'field_paragraph_title' => [['value' => static::randomSentence()]],
            'field_paragraph_summary' => [
              'value' => static::randomPlainParagraph(),
            ],
            'field_paragraph_link' => [VicgovauDemoHelper::randomLinkFieldValue()],
          ];
          break;

        case 'card_navigation_featured':
          $component_data += [
            'field_paragraph_title' => [['value' => static::randomSentence()]],
            'field_paragraph_summary' => [
              'value' => static::randomPlainParagraph(),
            ],
            'field_paragraph_link' => [VicgovauDemoHelper::randomLinkFieldValue()],
            'field_paragraph_media' => [['target_id' => static::randomImage()]],
          ];
          break;

        case 'card_promotion_auto':
          $component_data += [
            'field_paragraph_cta_text' => [['value' => static::randomSentence(2, 5)]],
            'field_paragraph_reference' => [['target_id' => static::randomPage()]],
          ];
          break;

        case 'card_navigation_auto':
        case 'card_navigation_featured_auto':
          $component_data += [
            'field_paragraph_reference' => [['target_id' => static::randomPage()]],
          ];
          break;

        case 'card_keydates':
          $component_data += [
            'field_paragraph_cta' => [static::randomCtaLinkFieldValue()],
            'field_paragraph_keydates' => [],
          ];
          for ($i = 1; $i <= mt_rand(1, 2); $i++) {
            try {
              $keydate = static::randomKeydate();
              $component_data['field_paragraph_keydates'][] = [
                'target_id' => $keydate->id(),
                'target_revision_id' => $keydate->getRevisionId(),
              ];
            }
            catch (\Exception $exception) {
              watchdog_exception('vicgovau_demo', $exception);
            }
          }
          break;

        case 'featured_news':
          $component_data += [
            'field_paragraph_news_reference' => [],
          ];
          $news = static::randomNews();
          if (!empty($news)) {
            foreach ($news as $news_id) {
              $component_data['field_paragraph_news_reference'][] = ['target_id' => $news_id];
            }
          }
          break;

        case 'media_gallery':
          $gallery = static::randomImageGallery();
          if ($gallery) {
            $component_data['field_paragraph_media_gallery'] = [
              ['target_id' => $gallery->id()],
            ];
          }
          break;
      }

      try {
        $component = Paragraph::create($component_data);
        $component->save();
        $components[] = $component;
        $repository->trackEntity($component);
      }
      catch (\Exception $exception) {
        watchdog_exception('vicgovau_demo', $exception);
      }

      $component_count--;
    };

    return $components;
  }

}
