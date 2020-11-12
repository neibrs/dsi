<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\eabax_core\Entity\EffectiveDatesTrait;

abstract class EffectiveDatesBusinessGroupEntity extends BusinessGroupEntity implements EffectiveDatesBusinessGroupEntityInterface {

  use EffectiveDatesTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['effective_dates'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Effective dates'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setSetting('optional_start_date', TRUE)
      ->setSetting('optional_end_date', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'daterange_default',
        'weight' => 90,
        'label' => 'inline',
        'settings'=> [
          'format_type'=>'html_date'
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 90,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']
      ->setLabel(t('Enabled'))
      ->setSettings([
        'on_label' => new TranslatableMarkup('Enabled'),
        'off_label' => new TranslatableMarkup('Disabled'),
      ])
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'weight' => 95,
        'label' => 'inline',
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      // status 根据 effective_dates 自动设置，不需要通过表单编辑。
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    $this->setPublishedByEffectiveDates();
  }

}
