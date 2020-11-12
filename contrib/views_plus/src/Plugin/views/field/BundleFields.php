<?php

namespace Drupal\views_plus\Plugin\views\field;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\views\Plugin\views\field\PrerenderList;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ViewsField("bundle_fields")
 */
class BundleFields extends PrerenderList {

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManagerInterface $entity_field_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preRender(&$values) {
    $entity_type_id = $this->getEntityType();
    $base_fields = array_keys($this->entityFieldManager->getBaseFieldDefinitions($entity_type_id));

    foreach ($values as $result) {
      $entity = $this->getEntity($result);
      $fields = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $entity->bundle());
      foreach ($fields as $key => $field) {
        if (!in_array($key, $base_fields)) {
          if ($field->getFieldStorageDefinition()->getType() == 'entity_reference') {
            if ($entity->get($key)->target_id) {
              if ($entity->hasLinkTemplate('canonical')) {
                $this->items[$entity->id()][$key]['field'] = $field->getLabel() . ': ' . $entity->get($key)->entity->toLink()->toString();
              }
              else {
                $this->items[$entity->id()][$key]['field'] = $field->getLabel() . ': ' . $entity->get($key)->entity->label();
              }
            }
          }
          else {
            $this->items[$entity->id()][$key]['field'] = $field->getLabel() . ': ' . $entity->get($key)->value;
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function render_item($count, $item) {
    return $item['field'];
  }

}
