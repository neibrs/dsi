<?php

namespace Drupal\field_plus\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter as ImageFormatterBase;

class ImageFormatter extends ImageFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'only_first' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['only_first'] = [
      '#title' => t('Only first'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('only_first'),
      '#description' => t('Display the first image.'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    if (!empty($elements) && $this->getSetting('only_first')) {
      return $elements[0];
    }
    return $elements;
  }
}
