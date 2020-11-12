<?php

namespace Drupal\person\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\person\Entity\PersonInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Create a user.
 *
 * @Action(
 *   id = "person_create_user",
 *   label = @Translation("Create user account for selected person"),
 *   type = "person"
 * )
 */
class CreateUser extends ActionBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new CreateUser object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  protected function createUser(PersonInterface $person) {
    $userStorage = $this->entityTypeManager->getStorage('user');
    $users = $userStorage->loadByProperties([
      'person' => $person->id(),
    ]);
    if (!$users) {
      $values = [
        'name' => $person->label(),
        // TODO add phone? field
        'mail' => $person->email->value,
        'person' => $person->id(),
        'status' => 1,
      ];
      /** @var \Drupal\user\UserInterface $user */
      $user = $userStorage->create($values);
      $user->setExistingPassword(\Drupal::config('person.settings')
        ->get('initial_password'));
      return $user;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($user = $this->createUser($entity)) {
      if (in_array($entity->bundle(), ['contingent_worker', 'employee'])) {
        $user->addRole('employee');
      }
      $user->save();
      _user_mail_notify('register_admin_created', $user);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\person\PersonInterface $object */
    $result = $object->access('update', $account, TRUE);

    return $return_as_object ? $result : $result->isAllowed();
  }

}
