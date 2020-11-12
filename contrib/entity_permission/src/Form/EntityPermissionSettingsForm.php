<?php

namespace Drupal\entity_permission\Form;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EntityPermissionSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['entity_permission.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_permission_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $entities = $this->config('entity_permission.settings')->get('entity_permission_settings');

    $form['entities'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Entity'),
        $this->t('Entity permission'),
        $this->t('Bundle permission'),
        $this->t('Field permission'),
      ],
      '#sticky' => TRUE,
    ];

    $definitions = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($definitions as $entity_type_id => $definition) {
      $form['entities'][$entity_type_id]['entity_type'] = [
        '#markup' => $definition->getLabel(),
      ];

      $form['entities'][$entity_type_id]['entity_permission'] = [
        '#type' => 'checkbox',
        '#default_value' => FALSE,
      ];
      if (isset($entities[$entity_type_id]['entity_permission'])) {
        $form['entities'][$entity_type_id]['entity_permission']['#default_value'] = $entities[$entity_type_id]['entity_permission'];
      }

      if ($definition instanceof ContentEntityTypeInterface) {
        if ($definition->getBundleEntityType()) {
          $form['entities'][$entity_type_id]['bundle_permission'] = [
            '#type' => 'checkbox',
            '#default_value' => FALSE,
          ];
          if (isset($entities[$entity_type_id]['bundle_permission'])) {
            $form['entities'][$entity_type_id]['bundle_permission']['#default_value'] = $entities[$entity_type_id]['bundle_permission'];
          }
        }
        else {
          $form['entities'][$entity_type_id]['bundle_permission'] = [
            '#markup' => '-',
          ];
        }

        $form['entities'][$entity_type_id]['field_permission'] = [
          '#type' => 'checkbox',
          '#default_value' => FALSE,
        ];
        if (isset($entities[$entity_type_id]['field_permission'])) {
          $form['entities'][$entity_type_id]['field_permission']['#default_value'] = $entities[$entity_type_id]['field_permission'];
        }
      }
      else {
        $form['entities'][$entity_type_id]['bundle_permission'] = [
          '#markup' => '-',
        ];
        $form['entities'][$entity_type_id]['field_permission'] = [
          '#markup' => '-',
        ];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('entity_permission.settings')
      ->set('entity_permission_settings', $form_state->getValue('entities'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}