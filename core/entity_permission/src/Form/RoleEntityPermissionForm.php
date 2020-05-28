<?php

namespace Drupal\entity_permission\Form;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\RoleInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RoleEntityPermissionForm extends FormBase {

  /**
   * @var \Drupal\user\RoleInterface
   */
  protected $user_role;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'role_entity_permission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, RoleInterface $user_role = NULL) {
    $settings = \Drupal::config('entity_permission.settings')->get('entity_permission_settings');
    if (!$settings) {
      $this->messenger()->addMessage($this->t('Need to setup entities to manage permissions.'));
      return new RedirectResponse(Url::fromRoute('entity_permission.settings')->setAbsolute()->toString());
    }

    $this->user_role = $user_role;

    $permissions = $user_role->getThirdPartySetting('entity_permission', 'entity_permissions', []);

    $definitions = \Drupal::entityTypeManager()->getDefinitions();

    $form['entities'] = [
      '#tree' => TRUE,
    ];

    foreach ($settings as $entity_type_id => $setting) {
      if (!$setting['entity_permission']) {
        continue;
      }

      $form['entities'][$entity_type_id] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'box-body',
            'container-inline',
          ],
        ],
        '#prefix' => '<div class="box box-default">',
        '#suffix' => '</div>',
      ];

      // Entity permission
      $form['entities'][$entity_type_id]['entity_type'] = [
        '#markup' => $definitions[$entity_type_id]->getLabel() . ': ',
      ];

      $form['entities'][$entity_type_id]['view'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('View'),
        '#default_value' => TRUE,
      ];
      if (isset($permissions[$entity_type_id]['view'])) {
        $form['entities'][$entity_type_id]['view']['#default_value'] = $permissions[$entity_type_id]['view'];
      }

      $form['entities'][$entity_type_id]['update'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Update'),
        '#default_value' => TRUE,
      ];
      if (isset($permissions[$entity_type_id]['update'])) {
        $form['entities'][$entity_type_id]['update']['#default_value'] = $permissions[$entity_type_id]['update'];
      }

      $form['entities'][$entity_type_id]['delete'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Delete'),
        '#default_value' => TRUE,
      ];
      if (isset($permissions[$entity_type_id]['delete'])) {
        $form['entities'][$entity_type_id]['delete']['#default_value'] = $permissions[$entity_type_id]['delete'];
      }

      $form['entities'][$entity_type_id]['create'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create'),
        '#default_value' => TRUE,
      ];
      if (isset($permissions[$entity_type_id]['create'])) {
        $form['entities'][$entity_type_id]['create']['#default_value'] = $permissions[$entity_type_id]['create'];
      }

      // Bundle permission
      if ($setting['bundle_permission']) {
        $form['entities'][$entity_type_id]['bundle_permission'] = [
          '#type' => 'table',
          '#caption' => $this->t('Bundle permission'),
          '#header' => [
            $this->t('Bundle'),
            $this->t('View'),
            $this->t('Update'),
            $this->t('Delete'),
            $this->t('Create'),
          ],
        ];

        $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_type_id);
        foreach ($bundles as $bundle_id => $bundle) {
          $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['label'] = [
            '#markup' => $bundle['label'],
          ];
          $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['view'] = [
            '#type' => 'checkbox',
            '#default_value' => TRUE,
          ];
          if (isset($permissions[$entity_type_id]['bundle_permission'][$bundle_id]['view'])) {
            $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['view']['#default_value'] = $permissions[$entity_type_id]['view'];
          }

          $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['update'] = [
            '#type' => 'checkbox',
            '#default_value' => TRUE,
          ];
          if (isset($permissions[$entity_type_id]['bundle_permission'][$bundle_id]['update'])) {
            $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['update']['#default_value'] = $permissions[$entity_type_id]['update'];
          }

          $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['delete'] = [
            '#type' => 'checkbox',
            '#default_value' => TRUE,
          ];
          if (isset($permissions[$entity_type_id]['bundle_permission'][$bundle_id]['delete'])) {
            $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['delete']['#default_value'] = $permissions[$entity_type_id]['delete'];
          }

          $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['create'] = [
            '#type' => 'checkbox',
            '#default_value' => TRUE,
          ];
          if (isset($permissions[$entity_type_id]['bundle_permission'][$bundle_id]['create'])) {
            $form['entities'][$entity_type_id]['bundle_permission'][$bundle_id]['create']['#default_value'] = $permissions[$entity_type_id]['create'];
          }
        }
      }

      // Field permission
      if ($setting['field_permission']) {
        $form['entities'][$entity_type_id]['field_permission'] = [
          '#type' => 'details',
          '#title' => $this->t('Field permissions')
        ];

        $ignore = ['id', 'uuid', 'langcode', 'changed', 'created'];

        // Base fields
        $form['entities'][$entity_type_id]['field_permission']['_base'] = [
          '#type' => 'table',
          '#header' => [
            $this->t('Field'),
            $this->t('View'),
            $this->t('Edit'),
          ],
          '#caption' => $this->t('Base field permissions'),
        ];

        $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_type_id);
        foreach ($bundles as $bundle_id => $bundle) {
          // Bundle fields
          $form['entities'][$entity_type_id]['field_permission'][$bundle_id] = [
            '#type' => 'table',
            '#header' => [
              $this->t('Field'),
              $this->t('View'),
              $this->t('Edit'),
            ],
            '#caption' => $this->t('@bundle field permissions', ['@bundle' => $bundle['label']]),
          ];

          $field_definitions = \Drupal::entityManager()->getFieldDefinitions($entity_type_id, $bundle_id);
          foreach ($field_definitions as $field_name => $field_definition) {
            if (in_array($field_name, $ignore)) {
              continue;
            }

            if ($field_definition instanceof BaseFieldDefinition) {
              $form['entities'][$entity_type_id]['field_permission']['_base'][$field_name] = $this->buildFieldPermission($field_definition, isset($permissions[$entity_type_id]['field_permission'][$field_name]) ?: []);
            }
            else {
              $form['entities'][$entity_type_id]['field_permission'][$bundle_id][$field_name] = $this->buildFieldPermission($field_definition, isset($permissions[$entity_type_id]['field_permission'][$field_name]) ?: []);
            }

            $ignore[] = $field_name;
          }
        }
      }
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  protected function buildFieldPermission(FieldDefinitionInterface $field_definition, $permissions) {
    $build = [];

    $build['field'] = [
      '#markup' => $field_definition->getLabel(),
    ];

    $build['view'] = [
      '#type' => 'checkbox',
      '#default_value' => TRUE,
    ];
    if (isset($permissions['view'])) {
      $build['view']['#default_value'] = $permissions['view'];
    }

    $build['edit'] = [
      '#type' => 'checkbox',
      '#default_value' => TRUE,
    ];
    if (isset($permissions['edit'])) {
      $build['edit']['#default_value'] = $permissions['edit'];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entities = $form_state->getValue('entities');

    foreach ($entities as $id => $entity) {
      $field_permissions = [];
      foreach ($entity['field_permission'] as $bundle => $bundle_permission) {
        if (is_array($bundle_permission)) {
          $field_permissions += $bundle_permission;
        }
        unset($entities[$id]['field_permission'][$bundle]);
      }
      $entities[$id]['field_permission'] = $field_permissions;
    }

    $this->user_role->setThirdPartySetting('entity_permission', 'entity_permissions', $entities);
    $this->user_role->save();
    
    drupal_set_message($this->t('Entity permissions for @role has saved.', ['@role' => $this->user_role->label()]));
  }

}
