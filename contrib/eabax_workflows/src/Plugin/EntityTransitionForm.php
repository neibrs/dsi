<?php

namespace Drupal\eabax_workflows\Plugin;

use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Url;
use Drupal\workflows\Plugin\WorkflowTypeTransitionFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityTransitionForm extends WorkflowTypeTransitionFormBase implements ContainerInjectionInterface {

  /**
   * The context repository service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $manager;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $language;

  protected $workflow;

  protected $workflow_transition;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.condition'),
      $container->get('language_manager'),
      $container->get('context.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(ExecutableManagerInterface $manager, LanguageManagerInterface $language, ContextRepositoryInterface $context_repository) {
    $this->manager = $manager;
    $this->language = $language;
    $this->contextRepository = $context_repository;

    $route_match_parameters = \Drupal::routeMatch()->getParameters()->all();

    $this->workflow = $route_match_parameters['workflow'] ?: '';
    if (!empty($route_match_parameters['workflow_transition'])) {
      $this->workflow_transition = $this->workflow->getTypePlugin()->getTransition($route_match_parameters['workflow_transition']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form_state->setTemporaryValue('gathered_contexts', \Drupal::service('context.repository')->getAvailableContexts());

    $form['conditions'] = $this->buildConditionsInterface([], $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildConditionsInterface(array $form, FormStateInterface $form_state) {
    $form['visibility_tabs'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Conditions'),
      '#parents' => ['visibility_tabs'],
    ];

    $transition_conditions = [];
    if (!empty($this->workflow_transition)) {
      $transition_conditions = $this->workflow_transition->getConditions();
    }
    $definitions = $this->manager->getFilteredDefinitions('workflow_transition', $form_state->getTemporaryValue('gathered_contexts'));

    foreach ($definitions as $condition_id => $definition) {
      // Don't display the current theme condition.
      if ($condition_id == 'current_theme') {
        continue;
      }
      // Don't display the language condition until we have multiple languages.
      if ($condition_id == 'language' && !$this->language->isMultilingual()) {
        continue;
      }
      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $condition = $this->manager->createInstance($condition_id, isset($transition_conditions[$condition_id]) ? $transition_conditions[$condition_id] : []);
      $form_state->set(['conditions', $condition_id], $condition);
      $condition_form = $condition->buildConfigurationForm([], $form_state);
      $condition_form['#type'] = 'details';
      $condition_form['#title'] = $condition->getPluginDefinition()['label'];
      $condition_form['#group'] = 'visibility_tabs';
      $form[$condition_id] = $condition_form;
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $values = array_diff_key($values, ['conditions' => 'conditions']);
    $transition = $form_state->get('transition');
    $configuration = $this->workflowType->getConfiguration();

    $conditions = new ConditionPluginCollection(\Drupal::service('plugin.manager.condition'), $this->submitTransitionVisibilityForm($form, $form_state));

    $configuration['transitions'][$transition->id()] = $values + ['conditions' => $conditions->getConfiguration()] + $configuration['transitions'][$transition->id()];

    $this->workflowType->setConfiguration($configuration);
  }

  /**
   * Submit entity transition conditions
   */
  public function submitTransitionVisibilityForm(array &$form, FormStateInterface $form_state) {
    $conditions = [];
    // Get transition conditions
    foreach ($form_state->getValue('conditions') as $condition_id => $values) {
      // Allow the condition to submit the form.
      $condition = $form_state->get(['conditions', $condition_id]);
      $condition->submitConfigurationForm($form['conditions'][$condition_id], SubformState::createForSubform($form['conditions'][$condition_id], $form, $form_state));
      // Setting conditions' context mappings is the plugins' responsibility.
      // This code exists for backwards compatibility, because
      // \Drupal\Core\Condition\ConditionPluginBase::submitConfigurationForm()
      // did not set its own mappings until Drupal 8.2
      // @todo Remove the code that sets context mappings in Drupal 9.0.0.
      if ($condition instanceof ContextAwarePluginInterface) {
        $context_mapping = isset($values['context_mapping']) ? $values['context_mapping'] : [];
        $condition->setContextMapping($context_mapping);
      }
      $condition_configuration = array_filter($condition->getConfiguration());
      $conditions[$condition_id] = $condition_configuration;
    }

    return $conditions;
  }

}
