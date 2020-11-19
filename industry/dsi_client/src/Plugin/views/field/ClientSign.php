<?php
namespace Drupal\dsi_client\Plugin\views\field;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Plugin\views\field\Standard;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ViewsField("client_sign")
 */
class ClientSign extends Standard {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );

  }

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $values, $field = NULL) {
    $data = parent::getValue($values, $field);

    // Get type label.
    $type = $this->entityTypeManager->getStorage('dsi_client_type')->load($values->_entity->bundle());

    $target_type = $type->getTargetEntityTypeId();
    $target_entity = $this->entityTypeManager->getStorage($target_type)->load($data);

    $label = '';
    if ($type->label()) {
      $label .= '(' . $type->label() . ')';
    }
    if ($target_entity->label()) {
      $label .= $target_entity->label();
    }

    if ($phone = $target_entity->get('field_phone')->value) {
      $label .= '-' . $phone;
    }

    return $label;
  }
}
