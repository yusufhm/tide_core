<?php

namespace Drupal\tide_publication;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Class TidePublicationPageNavigationItemList.
 */
class TidePublicationPageNavigationItemList extends EntityReferenceFieldItemList {
  use ComputedItemListTrait;

  /**
   * Returns the currently active global container.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
   *   The container.
   *
   * @throws \Drupal\Core\DependencyInjection\ContainerNotInitializedException
   */
  public static function getContainer() {
    return \Drupal::getContainer();
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\entity_hierarchy\Form\HierarchyChildrenForm::form()
   */
  protected function computeValue() {
    $entity = $this->getEntity();
    if ($entity->getEntityTypeId() !== 'node' || $entity->bundle() !== 'publication_page') {
      return;
    }

    $container = static::getContainer();
    /** @var \Drupal\entity_hierarchy\Storage\NestedSetStorageFactory $nested_set_storage_factory */
    $nested_set_storage_factory = $container->get('entity_hierarchy.nested_set_storage_factory');
    /** @var \Drupal\entity_hierarchy\Storage\NestedSetNodeKeyFactory $nested_set_node_factory */
    $nested_set_node_factory = $container->get('entity_hierarchy.nested_set_node_factory');
    /** @var \Drupal\entity_hierarchy\Storage\EntityTreeNodeMapperInterface $entity_tree_node_mapper */
    $entity_tree_node_mapper = $container->get('entity_hierarchy.entity_tree_node_mapper');

    $publication_field_name = 'field_publication';

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($entity);

    /** @var \PNX\NestedSet\NestedSetInterface $storage */
    $storage = $nested_set_storage_factory->get($publication_field_name, $entity->getEntityTypeId());

    $current_node = $storage->getNode($nested_set_node_factory->fromEntity($entity));

    /** @var \PNX\NestedSet\Node $parent */
    $parent = $storage->findParent($nested_set_node_factory->fromEntity($entity));
    /** @var \PNX\NestedSet\Node[] $children */
    $siblings = $storage->findChildren($parent->getNodeKey());
    $siblings = array_values($siblings);

    $sibling_entities = $entity_tree_node_mapper->loadAndAccessCheckEntitysForTreeNodes('node', $siblings, $cache);

    foreach ($siblings as $delta => $nested_node) {
      /** @var \PNX\NestedSet\Node $nested_node */
      if ($nested_node->getLeft() == $current_node->getLeft()) {
        switch ($this->getName()) {
          case 'publication_navigation_prev':
            $nav_index = $delta - 1;
            break;

          case 'publication_navigation_next':
            $nav_index = $delta + 1;
            break;

          default:
            return;
        }

        if (isset($siblings[$nav_index])) {
          /** @var \PNX\NestedSet\Node $nav_node */
          $nav_node = $siblings[$nav_index];
          if (!$sibling_entities->contains($nav_node)) {
            // Doesn't exist or is access hidden.
            return;
          }

          /** @var \Drupal\Core\Entity\ContentEntityInterface $child_entity */
          $child_entity = $sibling_entities->offsetGet($nav_node);
          if (!$child_entity->isDefaultRevision()) {
            return;
          }
          $child_id = $nav_node->getId();

          $this->list[] = $this->createItem(0, ['target_id' => $child_id]);
        }

        break;
      }
    }
  }

}
