<?php

namespace Drupal\dsi_device_other_subtype;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;

/**
 * Provides a listing of Other subtype entities.
 */
class OtherSubtypeListBuilder extends ConfigEntityListBuilder {
  /**
 * @var \Drupal\dsi_device_other_subtype\Entity\LocationChoicesInterface[]*/
  protected $location_choices;

  /**
   * {@inheritDoc}
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage) {
    parent::__construct($entity_type, $storage);
    $this->location_choices = \Drupal::entityTypeManager()->getStorage('dsi_device_oslc')->loadMultiple();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    foreach ($this->location_choices as $id => $location_choice) {
      $header[$id] = $location_choice->label();
    }
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = Link::createFromRoute($entity->label(), 'entity.dsi_device_other_subtype.canonical', ['dsi_device_other_subtype' => $entity->id()]);
    foreach ($this->location_choices as $id => $location_choice) {
      $row[$id] = !empty($entity->getLocations()[$id]) ? 'Y' : 'N';
    }
    return $row;
  }

}
