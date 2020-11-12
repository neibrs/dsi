<?php

namespace Drupal\entity_plus\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EntityPlusController extends ControllerBase {

  public static function executeEntityAction(EntityInterface $entity, $action_id) {

    /** @var \Drupal\Core\Action\ActionInterface $action */
    $action = \Drupal::service('plugin.manager.action')
      ->createInstance($action_id);
    $action->executeMultiple([$entity]);

    $url = $entity->urlInfo('canonical', [
      'query' => [
        'compares' => implode(',', [$entity->id()]),
      ],
    ]);
    return new RedirectResponse($url->toString());
  }

  /**
   * Calls a method and reloads the listing page.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being acted upon.
   * @param $op
   *   The operation to perform, e.g., 'enable' or 'disable'.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect back to the listing page.
   */
  public function performOperation(EntityInterface $_entity, $_op) {
    $_entity->$_op()->save();
    $this->messenger()->addStatus($this->t('The settings have been updated.'));
    return $this->redirect('entity.' . $_entity->getEntityTypeId() . '.collection');
  }

}
