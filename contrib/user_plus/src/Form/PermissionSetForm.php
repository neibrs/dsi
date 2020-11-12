<?php

namespace Drupal\user_plus\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_plus\Entity\ContentEntityForm;
use Drupal\user\PermissionHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Permission set edit forms.
 *
 * @ingroup user_plus
 */
class PermissionSetForm extends ContentEntityForm {

  /**
   * The permission handler.
   *
   * @var \Drupal\user\PermissionHandlerInterface
   */
  protected $permissionHandler;

  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, PermissionHandlerInterface $permission_handler) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);

    $this->permissionHandler = $permission_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('user.permissions')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\user_plus\Entity\PermissionSet $entity */
    $form = parent::buildForm($form, $form_state);

    $this->buildPermissionsForm($form, $form_state);

    return $form;
  }

  /**
   * @see \Drupal\user\Form\UserPermissionsForm
   */
  protected function buildPermissionsForm(array &$form, FormStateInterface $form_state) {
    $hide_descriptions = FALSE;

    $form['permissions'] = [
      '#type' => 'table',
      '#header' => [$this->t('Permission'), $this->t('Include')],
      '#sticky' => TRUE,
    ];

    $permissions = $this->permissionHandler->getPermissions();
    $permissions_by_provider = [];
    foreach ($permissions as $permission_name => $permission) {
      $permissions_by_provider[$permission['provider']][$permission_name] = $permission;
    }

    foreach ($permissions_by_provider as $provider => $permissions) {
      // Module name.
      $form['permissions'][$provider] = [
        [
          '#wrapper_attributes' => [
            'colspan' => 2,
          ],
          '#markup' => $this->moduleHandler->getName($provider),
        ],
      ];
      foreach ($permissions as $perm => $perm_item) {
        // Fill in default values for the permission.
        $perm_item += [
          'description' => '',
          'restrict access' => FALSE,
          'warning' => !empty($perm_item['restrict access']) ? $this->t('Warning: Give to trusted roles only; this permission has security implications.') : '',
        ];
        $form['permissions'][$perm]['description'] = [
          '#type' => 'inline_template',
          '#template' => '<div class="permission"><span class="title">{{ title }}</span>{% if description or warning %}<div class="description">{% if warning %}<em class="permission-warning">{{ warning }}</em> {% endif %}{{ description }}</div>{% endif %}</div>',
          '#context' => [
            'title' => $perm_item['title'],
          ],
        ];
        // Show the permission description.
        if (!$hide_descriptions) {
          $form['permissions'][$perm]['description']['#context']['description'] = $perm_item['description'];
          $form['permissions'][$perm]['description']['#context']['warning'] = $perm_item['warning'];
        }
        $form['permissions'][$perm]['include'] = [
          '#title' => $perm_item['title'],
          '#title_display' => 'invisible',
          '#wrapper_attributes' => [
            'class' => ['checkbox'],
          ],
          '#type' => 'checkbox',
          '#default_value' => in_array($perm, $this->entity->getPermissions()) ? 1 : 0,
          '#parents' => ['permissions', $perm],
        ];
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $permissions = $form_state->getValue('permissions');
    $permissions = array_filter($permissions);
    $entity->setPermissions(array_keys($permissions));

    $status = parent::save($form, $form_state);

    $form_state->setRedirect('entity.permission_set.canonical', ['permission_set' => $entity->id()]);
  }

}
