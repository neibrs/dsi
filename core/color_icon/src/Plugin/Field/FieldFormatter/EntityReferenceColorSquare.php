<?php

namespace Drupal\color_icon\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldFormatter(
 *   id = "entity_reference_color_square",
 *   label = @Translation("Color square"),
 *   field_types = {
 *     "entity_reference",
 *   }
 * )
 */
class EntityReferenceColorSquare extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'label' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['label'] = [
      '#title' => t('Display label'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('label'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->getSetting('label') ? t('Display label') : t('No label');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $color = $entity->get('color');
      if ($entity instanceof ContentEntityInterface) {
        $color = $color->value;
      }

      $label = $entity->label();
      $build = [
        '#markup' => '<span class="color-square" data-fill-color="' . $color . '" title="' . $label . '"></span>',
        '#attached' => ['library' => ['color_icon/color_icon']],
      ];
      if ($this->getSetting('label')) {
        $build['#markup'] .= $label;
      }
      $elements[$delta] = $build;
    }

    return $elements;
  }

}
