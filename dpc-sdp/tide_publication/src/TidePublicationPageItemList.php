<?php

namespace Drupal\tide_publication;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Class TidePublicationComputedPageList.
 */
class TidePublicationPageItemList extends EntityReferenceFieldItemList {
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
    if ($entity->getEntityTypeId() !== 'node' || $entity->bundle() !== 'publication') {
      return;
    }

    $container = static::getContainer();
    /** @var \Drupal\entity_hierarchy\Storage\NestedSetStorageFactory $nested_set_storage_factory */
    $nested_set_storage_factory = $container->get('entity_hierarchy.nested_set_storage_factory');
    /** @var \Drupal\entity_hierarchy\Storage\NestedSetNodeKeyFactory $nested_set_node_factory */
    $nested_set_node_factory = $container->get('entity_hierarchy.nested_set_node_factory');
    /** @var \Drupal\entity_hierarchy\Information\ParentCandidateInterface $parent_candidate */
    $parent_candidate = $container->get('entity_hierarchy.information.parent_candidate');
    /** @var \Drupal\entity_hierarchy\Storage\EntityTreeNodeMapperInterface $entity_tree_node_mapper */
    $entity_tree_node_mapper = $container->get('entity_hierarchy.entity_tree_node_mapper');

    /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $fields */
    $fields = $parent_candidate->getCandidateFields($entity);
    if (!$fields) {
      return;
    }

    $field_name = reset($fields);
    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($entity);

    /** @var \PNX\NestedSet\NestedSetInterface $storage */
    $storage = $nested_set_storage_factory->get($field_name, $entity->getEntityTypeId());
    /** @var \PNX\NestedSet\Node[] $children */
    $children = $storage->findChildren($nested_set_node_factory->fromEntity($entity));
    $child_entities = $entity_tree_node_mapper->loadAndAccessCheckEntitysForTreeNodes($entity->getEntityTypeId(), $children, $cache);

    foreach ($children as $weight => $nested_node) {
      if (!$child_entities->contains($nested_node)) {
        // Doesn't exist or is access hidden.
        continue;
      }
      /** @var \Drupal\Core\Entity\ContentEntityInterface $child_entity */
      $child_entity = $child_entities->offsetGet($nested_node);
      if (!$child_entity->isDefaultRevision()) {
        continue;
      }
      $child_id = $nested_node->getId();

      $this->list[] = $this->createItem($weight, ['target_id' => $child_id]);
    }
  }

}
