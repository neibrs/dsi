<?php

namespace Drupal\dsi_record\Form;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Record edit forms.
 *
 * @ingroup dsi_record
 */
class RecordForm extends ContentEntityForm {

  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info, TimeInterface $time) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    $instance->routeMatch = $container->get('current_route_match');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\dsi_record\Entity\Record $entity */
    $form = parent::buildForm($form, $form_state);

    $start = \Drupal::request()->query->get('start');
    if (isset($start)) {
      // Set default value on date 2020-11-20 00:00:00.
      $form['start']['widget'][0]['value']['#default_value'] = new DrupalDateTime($start . ' 00:00:00');
    }

    // Get route parameters.
    $route_match = \Drupal::routeMatch();
    if ($route_match->getRouteName() == 'entity.dsi_record.add_todo') {
      // TODO, Add entity_type, entity_id form element.
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    if ($this->routeMatch->getRouteName() == 'entity.dsi_record.add_todo') {
      $this->routeMatch->getParameters()->all();
      foreach ($this->routeMatch->getParameters()->all() as $id => $val) {
        $entity->set($id, $val);
      }
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Record.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Record.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.dsi_record.canonical', ['dsi_record' => $entity->id()]);
  }

}
