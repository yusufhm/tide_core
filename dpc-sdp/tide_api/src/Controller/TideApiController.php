<?php

namespace Drupal\tide_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Entity;
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
   *
   * @todo: Add route constraints to support only selected query sting params.
   * @todo: Add caching support.
   */
  public function getRoute(Request $request) {
    $json_response = [
      'links' => [
        'self' => Url::fromRoute('tide_api.jsonapi.alias')->setAbsolute()->toString(),
      ],
    ];
    $code = Response::HTTP_OK;

    $alias = $request->query->get('alias');
    $source = $request->query->get('source');

    try {
      if ($source && !$alias) {
        $source = '/' . ltrim($source, '/');
        $alias = $this->aliasManager->getAliasByPath($source);
      }
      elseif ($alias && !$source) {
        $alias = '/' . ltrim($alias, '/');
        $source = $this->aliasManager->getPathByAlias($alias);
      }
      elseif ($source && $alias) {
        // @todo: Move route params validation to appropriate place for automatic
        // validation.
        throw new \Exception('Only one of either "source" or "alias" query parameter is allowed.');
      }
      else {
        // @todo: Move route params validation to appropriate place for automatic
        // validation.
        throw new \Exception('URL query parameter "source" or "alias" is required.');
      }

      $json_response['data'] = [
        'alias' => $alias,
        'source' => $source,
        'endpoint' => $this->findEndpointFromSource($source),
      ];
    }
    catch (\Exception $exception) {
      $json_response['error'] = $exception->getMessage();
      $code = Response::HTTP_BAD_REQUEST;
    }

    return new JsonResponse($json_response, $code);
  }

  /**
   * Given alias, return absolute endpoint URL.
   *
   * Lookup is performed trying to match on entity type.
   *
   * @param string $source
   *   String source path.
   *
   * @return string|null
   *   Endpoint as an absolute URL or NULL if no entities were found for
   *   provided $source.
   */
  protected function findEndpointFromSource($source) {
    $endpoint = NULL;

    if (!$source) {
      return NULL;
    }

    try {
      // Try to resolve source to entity-based path.
      $params = Url::fromUri('internal:' . $source)->getRouteParameters();
      $entity_type = key($params);
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($params[$entity_type]);
      // Get JSONAPI configured path for this entity.
      $jsonapi_entity_path = $this->getEntityJsonapiPath($entity);
      if ($jsonapi_entity_path) {
        // Build an endpoint as an absolute URL.
        $endpoint = Url::fromRoute('jsonapi.' . $jsonapi_entity_path . '.individual', [$entity_type => $entity->uuid()])->setAbsolute()->toString();
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
   * @param \Drupal\Core\Entity\Entity $entity
   *   Drupal entity to lookup the JSONAPI path for.
   *
   * @return string|null
   *   JSONAPI path for provided entity or NULL if no path was found.
   */
  protected function getEntityJsonapiPath(Entity $entity) {
    $resource_type = $this->resourceTypeRepository->get($entity->getEntityTypeId(), $entity->bundle());
    /** @var \Drupal\jsonapi_extras\ResourceType\ConfigurableResourceType $resource_type */
    $resource_config = $resource_type->getJsonapiResourceConfig();
    $config_path = $resource_config->get('path');

    return $config_path;
  }

}
