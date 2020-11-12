<?php

namespace Drupal\import\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\migrate_plus\Entity\Migration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ImportRulesForm.
 */
class ImportRulesForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, MigrationPluginManagerInterface $migration_plugin_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->migrationPluginManager = $migration_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.migration')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_rules_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];
    $definitions = $this->migrationPluginManager->getDefinitions();
    foreach ($definitions as $id => $definition) {
      $options[$id] = $definition['label'];
    }

    $form['migration'] = [
      '#type' => 'select',
      '#title' => $this->t('Migration'),
      '#options' => $options,
      '#ajax' => [
        'callback' => '::migrationSwitch',
        'wrapper' => 'process-wrapper',
      ],
    ];

    $definition = reset($definitions);
    $form['process'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Process'),
      '#rows' => 20,
      '#default_value' => Yaml::encode($definition['process']),
      '#prefix' => '<div id="process-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  public function migrationSwitch(array $form, FormStateInterface $form_state) {
    $definitions = $this->migrationPluginManager->getDefinitions();
    $migration_id = $form_state->getValue('migration');
    $form['process']['#value'] = Yaml::encode($definitions[$migration_id]['process']);
    return $form['process'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($form_state->isSubmitted()) {
      $migration_id = $form_state->getValue('migration');
      if (!$migration_id) {
        $form_state->setErrorByName('migration', $this->t('Must select a migration.'));
      }

      $process_string = $form_state->getValue('process');
      try {
        Yaml::decode($process_string);
      }
      catch (\Exception $e) {
        $form_state->setErrorByName('process', $this->t('The process rule failed with the following message: %message', [
          '%message' => $e->getMessage(),
        ]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $plugin_id = $form_state->getValue('migration');
    $migration = $this->entityTypeManager->getStorage('migration')->load($plugin_id);
    if (!$migration) {
      $migration = Migration::createEntityFromPlugin($plugin_id, $plugin_id);
    }
    $migration->set('process', Yaml::decode($form_state->getValue('process')));
    $migration->save();

    drupal_set_message($this->t('Migration %migration saved.', [
      '%migration' => $migration->label(),
    ]));
  }

}
