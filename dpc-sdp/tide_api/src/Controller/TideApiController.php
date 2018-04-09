<?php

namespace Drupal\tide_api\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\Core\Url;
use Drupal\jsonapi\ResourceType\ResourceTypeRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TideApiController.
 *
 * @package Drupal\tide_api\Controller
 */
class TideApiController extends ControllerBase {

  /**
   * The path alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The JSONAPI resource type repository.
   *
   * @var \Drupal\jsonapi\ResourceType\ResourceTypeRepository
   */
  protected $resourceTypeRepository;

  /**
   * Constructs a new PathController.
   *
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\jsonapi\ResourceType\ResourceTypeRepository $resource_type_repository
   *   JSONAPI resource type repository.
   */
  public function __construct(AliasManagerInterface $alias_manager, EntityTypeManagerInterface $entity_type_manager, ResourceTypeRepository $resource_type_repository) {
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->resourceTypeRepository = $resource_type_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('path.alias_manager'),
      $container->get('entity_type.manager'),
      $container->get('jsonapi.resource_type.repository')
    );
  }

  /**
   * Get route details from provided source or alias.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response.
   */
  public function getRoute(Request $request) {
    $json_response = [
      'links' => [
        'self' => Url::fromRoute('tide_api.jsonapi.alias')->setAbsolute()->toString(),
      ],
    ];
    $code = Response::HTTP_NOT_FOUND;
    $json_response['errors'] = [$this->t('Path not found.')];

    $path = $request->query->get('path');

    try {
      if ($path) {
        $cid = 'tide_api:route:path:' . md5($path);

        // First load from cache_data.
        $cached_route_data = $this->cache('data')->get($cid);
        if ($cached_route_data) {
          // Check if the current has permission to access the path.
          $url = Url::fromUri($cached_route_data->data['uri']);
          if ($url->access()) {
            $code = Response::HTTP_OK;
            $json_response['data'] = $cached_route_data->data['json_response'];
            unset($json_response['errors']);
          }
          else {
            $code = Response::HTTP_FORBIDDEN;
            $json_response['errors'] = [$this->t('Permission denied.')];
          }
        }
        // Cache miss.
        else {
          $alias = $path;
          $source = $this->aliasManager->getPathByAlias($path);
          if ($source == $path) {
            $alias = $this->aliasManager->getAliasByPath($source);
          }

          $url = $this->findUrlFromPath($source);
          if ($url) {
            // Check if the current has permission to access the path.
            if ($url->access()) {
              $entity = $this->findEntityFromUrl($url);
              if ($entity) {
                $endpoint = $this->findEndpointFromEntity($entity);
                $entity_type = $entity->getEntityTypeId();
                $json_response['data'] = [
                  'alias' => $alias,
                  'source' => $source,
                  'entity_type' => $entity_type,
                  'bundle' => $entity->bundle(),
                  'uuid' => $entity->uuid(),
                  'endpoint' => $endpoint,
                ];

                // Cache the response with the same tags with the entity.
                $cached_route_data = [
                  'json_response' => $json_response['data'],
                  'uri' => $url->toUriString(),
                ];
                $this->cache('data')
                  ->set($cid, $cached_route_data, Cache::PERMANENT, $entity->getCacheTags());

                $code = Response::HTTP_OK;
                unset($json_response['errors']);
              }
            }
            else {
              $code = Response::HTTP_FORBIDDEN;
              $json_response['errors'] = [$this->t('Permission denied.')];
            }
          }
        }
      }
      else {
        $code = Response::HTTP_BAD_REQUEST;
        $json_response['errors'] = [$this->t('URL query parameter "path" is required.')];
      }
    }
    catch (\Exception $exception) {
      $code = Response::HTTP_BAD_REQUEST;
      $json_response['errors'] = [$exception->getMessage()];
    }

    return new JsonResponse($json_response, $code);
  }

  /**
   * Return a URL object from the given path.
   *
   * @param string $path
   *   The path, eg. /node/1 or /about-us.
   *
   * @return \Drupal\Core\Url|null
   *   The URL. NULL if the path has no scheme.
   */
  protected function findUrlFromPath($path) {
    $url = NULL;
    if ($path) {
      try {
        $url = Url::fromUri('internal:' . $path);
      }
      catch (\Exception $exception) {
        return NULL;
      }
    }

    return $url;
  }

  /**
   * Return an entity from a URL object.
   *
   * @param \Drupal\Core\Url $url
   *   The Url object.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity. NULL if not found.
   */
  protected function findEntityFromUrl(Url $url) {
    try {
      // Try to resolve URL to entity-based path.
      $params = $url->getRouteParameters();
      $entity_type = key($params);
      $entity = $this->entityTypeManager->getStorage($entity_type)
        ->load($params[$entity_type]);
      return $entity;
    }
    catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * Return absolute endpoint for the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return string|null
   *   The endpoint. NULL if not found.
   */
  protected function findEndpointFromEntity(EntityInterface $entity) {
    $endpoint = NULL;
    try {
      // Get JSONAPI configured path for this entity.
      $jsonapi_entity_path = $this->getEntityJsonapiPath($entity);
      if ($jsonapi_entity_path) {
        // Build an endpoint as an absolute URL.
        $endpoint = Url::fromRoute('jsonapi.' . $jsonapi_entity_path . '.individual', [$entity->getEntityTypeId() => $entity->uuid()])
          ->setAbsolute()
          ->toString();
      }
    }
    catch (\Exception $exception) {
      return NULL;
    }

    return $endpoint;
  }

  /**
   * Lookup JSONAPI path for a provided entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Drupal entity to lookup the JSONAPI path for.
   *
   * @return string|null
   *   JSONAPI path for provided entity or NULL if no path was found.
   */
  protected function getEntityJsonapiPath(EntityInterface $entity) {
    $resource_type = $this->resourceTypeRepository->get($entity->getEntityTypeId(), $entity->bundle());
    /** @var \Drupal\jsonapi_extras\ResourceType\ConfigurableResourceType $resource_type */
    $resource_config = $resource_type->getJsonapiResourceConfig();
    $config_path = $resource_config->get('path');

    return $config_path;
  }

}
