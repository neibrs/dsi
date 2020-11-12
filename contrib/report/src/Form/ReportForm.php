<?php

namespace Drupal\report\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Plugin\PluginFormFactoryInterface;
use Drupal\report\Plugin\ReportPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReportForm.
 */
class ReportForm extends EntityForm {

  /**
   * The plugin form manager.
   *
   * @var \Drupal\Core\Plugin\PluginFormFactoryInterface
   */
  protected $pluginFormFactory;

  public function __construct(PluginFormFactoryInterface $plugin_form_factory) {
    $this->pluginFormFactory = $plugin_form_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin_form.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function prepareEntity() {
    parent::prepareEntity();

    if ($this->entity->isNew()) {
      $plugin = $this->getRouteMatch()->getRawParameter('plugin');
      $this->entity->setPluginId($plugin);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\report\Entity\ReportInterface $entity */
    $entity = $this->entity;

    $form['category'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category'),
      '#default_value' => $entity->getCategory(),
    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label', [], ['context' => 'report']),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\report\Entity\Report::load',
      ],
      '#disabled' => $entity->isLocked(),
    ];

    /** @var \Drupal\report\Plugin\ReportManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.report');
    $plugins = $plugin_manager->getDefinitions();
    $options = array_map(function ($plugin) {
      return $plugin['label'];
    }, $plugins);
    $form['plugin'] = [
      '#type' => 'select',
      '#title' => $this->t('Plugin'),
      '#options' => $options,
      '#default_value' => $entity->getPluginId(),
      '#disabled' => !$entity->isNew(),
    ];

    $form['#tree'] = TRUE;
    $form['settings'] = [];
    $subform_state = SubformState::createForSubform($form['settings'], $form, $form_state);
    $form['settings'] = $this->getPluginForm($entity->getPlugin())->buildConfigurationForm($form['settings'], $subform_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $this->getPluginForm($this->entity->getPlugin())->validateConfigurationForm($form['settings'], SubformState::createForSubform($form['settings'], $form, $form_state));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $entity = $this->entity;

    $sub_form_state = SubformState::createForSubform($form['settings'], $form, $form_state);
    // Call the plugin submit handler.
    $plugin = $entity->getPlugin();
    $this->getPluginForm($plugin)->submitConfigurationForm($form, $sub_form_state);

    // Save the settings of the plugin.
    $entity->save();

    $this->messenger()->addStatus($this->t('The configuration has been saved.'));

    $form_state->setRedirect('entity.report.canonical', [
      'report' => $entity->id(),
    ]);
  }

  /**
   * @param \Drupal\report\Plugin\ReportPluginInterface $plugin
   *   The report plugin.
   *
   * @return \Drupal\Core\Plugin\PluginFormInterface
   *   The plugin form for the report.
   */
  protected function getPluginForm(ReportPluginInterface $plugin) {
    return $this->pluginFormFactory->createInstance($plugin, 'configure');
  }
}
