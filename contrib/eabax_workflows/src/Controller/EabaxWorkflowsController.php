<?php

namespace Drupal\eabax_workflows\Controller;

use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Component\Plugin\Exception\MissingValueContextException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Condition\ConditionAccessResolverTrait;
use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Routing\LocalRedirectResponse;
use Drupal\eabax_workflows\EntityTransitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EabaxWorkflowsController extends ControllerBase {

  use ConditionAccessResolverTrait;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $manager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The plugin context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected $contextHandler;

  /**
   * The context manager service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(ExecutableManagerInterface $manager, ContextHandlerInterface $context_handler, ContextRepositoryInterface $context_repository, EntityTypeManagerInterface $entity_type_manager) {
    $this->manager = $manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->contextHandler = $context_handler;
    $this->contextRepository = $context_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.condition'),
      $container->get('context.handler'),
      $container->get('context.repository'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Response for transition apply.
   */
  public function applyTransition(Request $request, $workflow_type, $transition_id, $entity_type, $field, $entity_id) {
    $entity = $this->entityTypeManager->getStorage($entity_type)
      ->load($entity_id);

    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    $workflow = $this->entityTypeManager->getStorage('workflow')
      ->load($workflow_type);
    $transition = $workflow->getTypePlugin()->getTransition($transition_id);
    $from = $transition->from();
    $from = array_map(function ($item) {
      return $item->id();
    }, $from);
    $entity_state_id = $entity->get($field)->value;
    if (in_array($entity_state_id, $from)) {
      $entity->set($field, $transition->to()->id());
      $entity->save();
      $this->messenger()
        ->addStatus($this->t("It's success to transition to %to from %from", [
          '%to' => $transition->to()
            ->label(),
          '%from' => $from[$entity_state_id],
        ]));
    }
    else {
      $this->messenger()
        ->addError($this->t('The status of transition not work.'));
    }

    if (!$request->query->has('destination')) {
      throw new HttpException(400, 'The original location is missing.');
    }

    return new LocalRedirectResponse($request->query->get('destination'));
  }

  /**
   * Route access for transition.
   */
  public function applyTransitionAccess($workflow_type, $transition_id, $entity_type, $entity_id) {
    $transition = $this->entityTypeManager->getStorage('workflow')
      ->load($workflow_type)
      ->getTypePlugin()
      ->getTransition($transition_id);
    $entity = $this->entityTypeManager->getStorage($entity_type)
      ->load($entity_id);

    $access_transition = $this->checkAccessTransitionConditions($transition);
    if ($access_transition instanceof AccessResultForbidden) {
      return $access_transition;
    }

    $from = array_map(function ($item) {
      return $item->label();
    }, $transition->from());
    if (in_array($entity->get('status')->value, array_flip($from))) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::neutral();
    }
  }

  /**
   * @param $transition
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   */
  private function checkAccessTransitionConditions(EntityTransitionInterface $transition) {
    $conditions = [];
    $missing_context = FALSE;
    $missing_value = FALSE;
    $transition_conditions = new ConditionPluginCollection(\Drupal::service('plugin.manager.condition'), $transition->getConditions());
    foreach ($transition_conditions as $condition_id => $condition) {
      if ($condition instanceof ContextAwarePluginInterface) {
        try {
          $contexts = $this->contextRepository->getRuntimeContexts(array_values($condition->getContextMapping()));
          $this->contextHandler->applyContextMapping($condition, $contexts);
        }
        catch (MissingValueContextException $e) {
          $missing_value = TRUE;
        }
        catch (ContextException $e) {
          $missing_context = TRUE;
        }
      }
      $conditions[$condition_id] = $condition;
    }

    if ($missing_context) {
      // If any context is missing then we might be missing cacheable
      // metadata, and don't know based on what conditions the block is
      // accessible or not. Make sure the result cannot be cached.
      $access = AccessResult::forbidden()->setCacheMaxAge(0);
    }
    elseif ($missing_value) {
      // The contexts exist but have no value. Deny access without
      // disabling caching. For example the node type condition will have a
      // missing context on any non-node route like the frontpage.
      $access = AccessResult::forbidden();
    }
    elseif ($this->resolveConditions($conditions, 'and') !== FALSE) {
      $access = AccessResult::allowed();
    }
    else {
      $access = AccessResult::forbidden();
    }

    $this->mergeCacheabilityFromConditions($access, $conditions);

    return $access;
  }

  /**
   * Merges cacheable metadata from conditions onto the access result object.
   *
   * @param \Drupal\Core\Access\AccessResult $access
   *   The access result object.
   * @param \Drupal\Core\Condition\ConditionInterface[] $conditions
   *   List of visibility conditions.
   */
  protected function mergeCacheabilityFromConditions(AccessResult $access, array $conditions) {
    foreach ($conditions as $condition) {
      if ($condition instanceof CacheableDependencyInterface) {
        $access->addCacheTags($condition->getCacheTags());
        $access->addCacheContexts($condition->getCacheContexts());
        $access->setCacheMaxAge(Cache::mergeMaxAges($access->getCacheMaxAge(), $condition->getCacheMaxAge()));
      }
    }
  }

}
