<?php

namespace Drupal\dsi_hardware\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Hardware edit forms.
 *
 * @ingroup dsi_hardware
 */
class HardwareForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\dsi_hardware\Entity\HardwareInterface $entity */
    $form = parent::buildForm($form, $form_state);

    // TODO, Move to dsi_port
    if (!\Drupal::moduleHandler()->moduleExists('dsi_port')) {
      return $form;
    }
    $form['direct_ports'] = [
      '#type' => 'table',
      '#caption' => $this->t('Direct Ports'),
      '#header' => [
        $this->t('Port name'),
        $this->t('Port type'),
        $this->t('Location'),
        $this->t('Delete'),
      ],
    ];

    if ($this->entity->isNew()) {
      return $form;
    }
    $query_port_types = $this->entityTypeManager->getStorage('dsi_port_type')->loadByProperties([
      'target_entity_type_id' => $this->entity->getEntityType(),
    ]);
    $query_port_types = array_map(function ($port_type) {
      return $port_type->id();
    }, $query_port_types);
    $query = $this->entityTypeManager->getStorage('dsi_port')->getQuery();
    $ids = $query->condition('entity_id', $this->entity->id())
      ->condition('type', $query_port_types, 'IN')
      ->execute();
    $ports = $this->entityTypeManager->getStorage('dsi_port')->loadMultiple($ids);

    $port_types = $this->entityTypeManager->getStorage('dsi_port_type')->loadMultiple();
    $port_types = array_map(function ($port_type) {
      return $port_type->label();
    }, $port_types);
    foreach ($ports as $id => $port) {
      $row['name'] = [
        '#type' => 'textfield',
        '#default_value' => $port->label(),
      ];
      $row['type'] = [
        '#type' => 'select',
        '#options' => $port_types,
        '#default_value' => $port->type->entity->id(),
      ];
      $row['location'] = [
        '#type' => 'select',
        '#options' => [
          'front' => $this->t('Front'),
          'back' => $this->t('Back'),
        ],
        '#default_value' => $port->location->value,
      ];
      $row['delete'] = [
        '#type' => 'checkbox',
      ];
      $form['direct_ports'][] = $row;
    }
    $form['direct_ports'][] = [
      'add_more' => [
        '#wrapper_attributes' => ['colspan' => 4, 'class' => ['foo', 'bar']],
        'add' => [
          [
            '#type' => 'button',
            '#value' => $this->t('Add another Direct Port'),
            '#attributes' => [
              'class' => ['button--small'],
            ],
          ],
          [
            '#type' => 'link',
            '#title' => $this->t('Bulk add Direct Port'),
            '#url' => Url::fromRoute('entity.dsi_port.bulk_form', [
              'dsi_port_type' => 'none',
              'entity_type_id' => 'dsi_hardware',
              'entity_id' => $this->entity->id(),
            ], [
              'query' => [
                \Drupal::destination()->getAsArray(),
              ],
            ]),
            '#attributes' => [
              'class' => ['use-ajax', 'button', 'button--small'],
              'data-dialog-type' => 'modal',
              'data-dialog-options' => Json::encode([
                'width' => '50%',
                'title' => $this->t('Bulk Add Direct Ports'),
              ]),
            ],
          ],
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Hardware.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Hardware.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.dsi_hardware.canonical', ['dsi_hardware' => $entity->id()]);
  }

}
