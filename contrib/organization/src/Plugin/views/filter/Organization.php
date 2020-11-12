<?php

namespace Drupal\organization\Plugin\views\filter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views_plus\Plugin\views\filter\EntityReferenceBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ViewsFilter("organization")
 */
class Organization extends EntityReferenceBrowser {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value']['include_children'] = [
      '#title' => $this->t('Include children'),
      '#type' => 'checkbox',
    ];

    parent::valueForm($form, $form_state);
  }

}
