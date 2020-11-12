<?php

namespace Drupal\organization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\organization\Entity\OrganizationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Organization routes.
 */
class OrganizationController extends ControllerBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity.repository')
    );
  }

  public function editForm(OrganizationInterface $organization) {
    /** @var \Drupal\layout_builder\Section $section */
    $section = \Drupal::service('organization.manager')->getOrganizationSection();

    $build = $section->toRenderArray();

    $build['second']['form'] = $this->entityFormBuilder()->getForm($organization);

    return $build;
  }

}
