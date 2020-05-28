<?php

namespace Drupal\options_plus\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * @FieldType(
 *   id = "string_options",
 *   label = @Translation("String options"),
 *   category = @Translation("Text"),
 *   default_widget = "options_select",
 *   default_formatter = "list_default",
 * )
 */
class StringOptionsItem extends StringItem implements OptionsProviderInterface {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'string_options' => '',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = parent::storageSettingsForm($form, $form_state, $has_data);

    $entities = \Drupal::entityTypeManager()->getStorage('string_options')
      ->loadMultiple();
    $options = array_map(function ($entity) {
      return $entity->label();
    }, $entities);
    $element['string_options'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('String options'),
      '#default_value' => $this->getSetting('string_options'),
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Possible Options may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getPossibleOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return $this->getSettableOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Settable Options may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getSettableOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $string_options = $this->getSetting('string_options');
    /** @var \Drupal\options_plus\Entity\StringOptionsInterface $string_options */
    $string_options = \Drupal::entityTypeManager()
      ->getStorage('string_options')
      ->load($string_options);
    $allowed_values = [];
    foreach ($string_options->getAllowedValues() as $value) {
      $allowed_values[$value] = $value;
    }
    return $allowed_values;
  }

}
