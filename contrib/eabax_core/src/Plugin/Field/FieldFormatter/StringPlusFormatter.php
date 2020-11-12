<?php


namespace Drupal\eabax_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldFormatter(
 *   id = "string_plus",
 *   label = @Translation("Plain text plus"),
 *   field_types = {
 *     "string",
 *     "uri",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class StringPlusFormatter extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'rel' => 'canonical',
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['rel'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link relationship type'),
      '#default_value' => $this->getSetting('rel'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityUrl(EntityInterface $entity) {
    return $entity->toUrl($this->getSetting('rel'));
  }

}