<?php

namespace Drupal\quick_code\Plugin\views\argument;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\quick_code\QuickCodeStorageInterface;
use Drupal\views\Plugin\views\argument\NumericArgument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Argument handler to accept quick_code.
 *
 * @ViewsArgument("quick_code")
 */
class QuickCode extends NumericArgument implements ContainerFactoryPluginInterface {

  /**
   * QuickCode storage handler.
   *
   * @var \Drupal\quick_code\QuickCodeStorageInterface
   */
  protected $quickCodeStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, QuickCodeStorageInterface $quick_code_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->quickCodeStorage = $quick_code_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('quick_code')
    );
  }

  /**
   * Override the behavior of title(). Get the title of the node.
   */
  public function title() {
    // There might be no valid argument.
    if ($this->argument) {
      $quick_code = $this->quickCodeStorage->load($this->argument);
      if (!empty($quick_code)) {
        return $quick_code->label();
      }
    }
    return $this->t('No name');
  }

}
