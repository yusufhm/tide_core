<?php

namespace Drupal\tide_site;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TideSiteHelper.
 *
 * @package Drupal\tide_site
 */
class TideSiteHelper {
  use ContainerAwareTrait;

  /**
   * List of allowed entity types.
   *
   * @var array
   */
  protected $supportedEntityTypes = ['node', 'media'];

  /**
   * The Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Entity Repository service.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * Returns list of supported entity types.
   *
   * @return array
   *   Array of supported entity types.
   */
  public function getSupportedEntityTypes(): array {
    return $this->supportedEntityTypes;
  }

  /**
   * Check if an entity type is supported.
   *
   * @param string $entity_type
   *   The entity type.
   *
   * @return bool
   *   TRUE if the entity type is supported.
   */
  public function isSupportedEntityType($entity_type) {
    return in_array($entity_type, $this->supportedEntityTypes);
  }

  /**
   * Loads a Site term by ID.
   *
   * @param int $tid
   *   The Site ID.
   *
   * @return \Drupal\taxonomy\TermInterface|null
   *   The Site term, or NULL if not exists.
   */
  public function getSiteById($tid) {
    try {
      $site = $this->entityTypeManager->getStorage('taxonomy_term')
        ->load($tid);

      return $site;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Returns the term trail of a Site or Section.
   *
   * @param int $tid
   *   The Site/Section ID.
   *
   * @return array|null
   *   The term tail.
   */
  public function getSiteTrail($tid) {
    try {
      $ancestors = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadAllParents($tid);
      $trail = [];
      foreach ($ancestors as $term) {
        /** @var \Drupal\taxonomy\TermInterface $term */
        $trail[] = $term->id();
      }

      return array_reverse($trail);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Returns the Sites of an entity.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The Entity object.
   *
   * @return array|null
   *   The Sites array with 2 keys:
   *     - ids      : List of site ids.
   *     - sections : List of sections: site_id => section_id.
   */
  public function getEntitySites(FieldableEntityInterface $entity) {
    // Static cache as this method maybe called multiple times per request.
    $static_cache = &drupal_static(__FUNCTION__, []);
    if (isset($static_cache[$entity->id()])) {
      return $static_cache[$entity->id()];
    }

    $entity_type = $entity->getEntityTypeId();
    $sites = NULL;

    if ($this->isSupportedEntityType($entity_type)) {
      $cid = 'tide_site:entity:' . $entity_type . ':' . $entity->id() . ':sites';
      // Attempt to load from data cache.
      $cached_sites = $this->cache('data')->get($cid);
      if ($cached_sites) {
        $sites = $cached_sites->data;
      }
      // Cache miss.
      else {
        $field_site_field_name = TideSiteFields::normaliseFieldName(TideSiteFields::FIELD_SITE, $entity_type);
        // Only process if the entity has Site field.
        if ($entity->hasField($field_site_field_name)) {
          $field_site = $entity->get($field_site_field_name);
          // Only process if its Site field has values.
          if (!$field_site->isEmpty()) {
            $cache_tags = [];
            // Fetch the trail of every term.
            $trails = [];
            foreach ($field_site->getValue() as $value) {
              $term_id = $value['target_id'];
              // Build the trail for each term.
              $trail = $this->getSiteTrail($term_id);
              $trails[$term_id] = $trail;
              // This term is a Site not a sub-section.
              if (count($trail) == 1) {
                $sites['ids'][$term_id] = $term_id;
              }
              $cache_tags[] = 'taxonomy_term:' . $term_id;
            }
            // Build the sections. Each Site should only have one section.
            foreach ($sites['ids'] as $site_id) {
              $sites['sections'][$site_id] = $site_id;
              foreach ($trails as $trail) {
                $trail_start = reset($trail);
                $trail_end = end($trail);
                if ($trail_end != $site_id) {
                  $sites['sections'][$trail_start] = $trail_end;
                }
              }
            }

            // Cache the results.
            $this->cache('data')->set($cid, $sites, Cache::PERMANENT, Cache::mergeTags($cache_tags, $entity->getCacheTags()));
            $static_cache[$entity->id()] = $sites;
          }
        }
      }
    }

    return $sites;
  }

  /**
   * Check if an entity belongs to a Site.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity object.
   * @param int $site_id
   *   The Site ID.
   *
   * @return bool
   *   TRUE if the entity belongs to the given Site.
   */
  public function isEntityBelongToSite(FieldableEntityInterface $entity, $site_id) {
    $entity_type = $entity->getEntityTypeId();
    if ($this->isSupportedEntityType($entity_type)) {
      $entity_sites = $this->getEntitySites($entity);
      if ($entity_sites && count($entity_sites)) {
        return in_array($site_id, $entity_sites['ids']);
      }
    }

    return FALSE;
  }

  /**
   * Load an entity by its UUID.
   *
   * @param string $uuid
   *   The UUID.
   * @param string $entity_type
   *   The entity type, eg. node or media.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The Entity. NULL if not found.
   */
  public function getEntityByUuid($uuid, $entity_type) {
    try {
      return $this->entityRepository->loadEntityByUuid($entity_type, $uuid);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Returns the requested cache bin.
   *
   * @param string $bin
   *   (optional) The cache bin for which the cache object should be returned,
   *   defaults to 'default'.
   *
   * @return \Drupal\Core\Cache\CacheBackendInterface
   *   The cache object associated with the specified bin.
   */
  protected function cache($bin = 'default') {
    return $this->container->get('cache.' . $bin);
  }

}
