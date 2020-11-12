<?php

namespace Drupal\entity_plus\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget as EntityReferenceAutocompleteWidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provide data-add-path for autocomplete.
 */
class EntityReferenceAutocompleteWidget extends EntityReferenceAutocompleteWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();

    // Decrease the default size.
    $settings['size'] = '40';

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Provides data-add-path attributes.
    $target_type = $items->getFieldDefinition()->getSetting('target_type');
    $entity_type = \Drupal::entityTypeManager()->getDefinition($target_type);
    $access_control_handler = \Drupal::entityTypeManager()->getAccessControlHandler($target_type);
    if ($access_control_handler->createAccess()) {
      if ($entity_type->hasLinkTemplate('add-page')) {
        $add_route = 'entity.' . $target_type . '.add_page';
      }
      elseif ($entity_type->hasLinkTemplate('add-form')) {
        $add_route = 'entity.' . $target_type . '.add_form';
        $element['target_id']['#attributes']['data-add-path'] = Url::fromRoute('entity.' . $target_type . '.add_form')->toString();
      }
      if (isset($add_route)) {
        $element['target_id']['#attributes']['data-add-path'] = Url::fromRoute($add_route, [], [
          'query' => \Drupal::destination()->getAsArray(),
        ])->toString();

      }
    }

    return $element;
  }

}
