<?php

namespace Drupal\tide_publication\Routing;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\jsonapi\ParamConverter\ResourceTypeConverter;
use Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface;
use Drupal\jsonapi\Routing\Routes as JsonapiRoutes;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Routes.
 *
 * @package Drupal\tide_publication\Routing
 */
class Routes implements ContainerInjectionInterface {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The JSON:API resource type repository.
   *
   * @var \Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface
   */
  protected $resourceTypeRepository;

  /**
   * List of providers.
   *
   * @var string[]
   */
  protected $providerIds;

  /**
   * The JSON:API base path.
   *
   * @var string
   */
  protected $jsonApiBasePath;

  /**
   * Instantiates a Routes object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface $resource_type_repository
   *   The JSON:API resource type repository.
   * @param string[] $authentication_providers
   *   The authentication providers, keyed by ID.
   * @param string $jsonapi_base_path
   *   The JSON:API base path.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ResourceTypeRepositoryInterface $resource_type_repository, array $authentication_providers, $jsonapi_base_path) {
    $this->moduleHandler = $module_handler;
    $this->resourceTypeRepository = $resource_type_repository;
    $this->providerIds = array_keys($authentication_providers);
    $this->jsonApiBasePath = $jsonapi_base_path;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('jsonapi.resource_type.repository'),
      $container->getParameter('authentication_providers'),
      $container->getParameter('jsonapi.base_path')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = new RouteCollection();

    if ($this->moduleHandler->moduleExists('jsonapi')) {
      $resource_type = $this->resourceTypeRepository->get('node', 'publication');
      if (!$resource_type) {
        return $routes;
      }

      $path = $resource_type->getPath();
      $entity_type_id = $resource_type->getEntityTypeId();

      $hierarchy_route = new Route("/{$this->jsonApiBasePath}{$path}/{entity}/hierarchy");
      $hierarchy_route->addDefaults([RouteObjectInterface::CONTROLLER_NAME => JsonapiRoutes::CONTROLLER_SERVICE_NAME . '.publication:getHierarchy']);
      $hierarchy_route->addDefaults([JsonapiRoutes::RESOURCE_TYPE_KEY => $resource_type->getTypeName()]);
      $hierarchy_route->setMethods(['GET']);
      $hierarchy_route->setRequirement('_access', 'TRUE');
      $hierarchy_route->addRequirements(['_format' => 'api_json']);
      $hierarchy_route->addOptions(['parameters' => ['entity' => ['type' => 'entity:' . $entity_type_id]]]);

      $parameters = $hierarchy_route->getOption('parameters') ?: [];
      $parameters[JsonapiRoutes::RESOURCE_TYPE_KEY] = ['type' => ResourceTypeConverter::PARAM_TYPE_ID];
      $hierarchy_route->setOption('parameters', $parameters);

      $routes->add(JsonapiRoutes::getRouteName($resource_type, 'individual.hierarchy'), $hierarchy_route);

      // Require the JSON:API media type header on every route.
      $routes->addRequirements(['_content_type_format' => 'api_json']);
      // Enable all available authentication providers.
      $routes->addOptions(['_auth' => $this->providerIds]);
      // Flag every route as belonging to the JSON:API module.
      $routes->addDefaults([JsonapiRoutes::JSON_API_ROUTE_FLAG_KEY => TRUE]);
      // All routes serve only the JSON:API media type.
      $routes->addRequirements(['_format' => 'api_json']);
    }

    return $routes;
  }

}
