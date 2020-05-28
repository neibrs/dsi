<?php

namespace Drupal\eabax_workflows\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\workflows\Entity\Workflow;
use Drupal\workflows\StateInterface;

/**
 * Plugin implementation of the 'entity_status' field type.
 *
 * @FieldType(
 *   id = "entity_status",
 *   label = @Translation("Workflow status"),
 *   default_widget = "options_select",
 *   default_formatter = "list_default",
 * )
 */
class EntityStatusFieldItem extends FieldItemBase implements OptionsProviderInterface {

  /**
   * The workflow type plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $workflowTypePluginManager;

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'workflow_type' => NULL,
      'workflow' => NULL,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('State'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 64,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];

    $workflow_types = array_column($this->workflowTypePluginManager->getDefinitions(), 'label', 'id');
    $workflow_type = $this->getSetting('workflow_type');
    $elements['workflow_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Workflow type'),
      '#required' => TRUE,
      '#options' => $workflow_types,
      '#default_value' => $workflow_type,
      '#ajax' => [
        'callback' => '::workflowTypeSwitch',
        'wrapper' => 'workflow-wrapper',
      ],
    ];

    $options = [];
    if (!empty($workflow_type)) {
      $workflows = Workflow::loadMultipleByType($workflow_type);
      foreach ($workflows as $workflow) {
        $options[$workflow->id()] = $workflow->label();
      }
    }
    $elements['workflow'] = [
      '#type' => 'select',
      '#title' => $this->t('Workflow'),
      '#required' => TRUE,
      '#options' => $options,
      '#default_value' => $this->getSetting('workflow'),
      '#prefix' => '<div id="workflow-wrapper">',
      '#suffix' => '</div>',
    ];

    return $elements;
  }

  /**
   * Handles switching the available workflow based on the selected workflow type.
   */
  public function workflowTypeSwitch($form, FormStateInterface $form_state) {
    $workflows = Workflow::loadMultipleByType($form_state->getValue('workflow_type'));
    $options = [];
    foreach ($workflows as $workflow) {
      $options[$workflow->id()] = $workflow->label();
    }
    $form['workflow']['#options'] = $options;

    return $form['workflow'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return array_keys($this->getPossibleOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $workflow = $this->getWorkflow();
    if (!$workflow) {
      // The workflow is not known yet, the field is probably being created.
      return [];
    }
    $state_labels = array_map(function ($state) {
      return $state->label();
    }, $workflow->getTypePlugin()->getStates());

    return $state_labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return array_keys($this->getSettableOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    // $this->value is unpopulated due to https://www.drupal.org/node/2629932
    $field_name = $this->getFieldDefinition()->getName();
    $value = $this->getEntity()->get($field_name)->value;

    $workflow = $this->getWorkflow();
    $type = $workflow->getTypePlugin();

    $allowed_states = $type->getStates();
    if (!empty($value) && $type->hasState($value) && ($current = $type->getState($value))) {
      $allowed_states = array_filter($allowed_states, function (StateInterface $state) use ($current) {
        return $current->id() === $state->id() || $current->canTransitionTo($state->id());
      });
    }
    $state_labels = array_map(function ($state) {
      return $state->label();
    }, $allowed_states);

    return $state_labels;
  }

  public function getWorkflow() {
    return !empty($this->getSetting('workflow')) ? Workflow::load($this->getSetting('workflow')) : NULL;
  }

}
