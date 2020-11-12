<?php

namespace Drupal\person\Authentication\Provider;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\UserSession;
use Drupal\user\Authentication\Provider\Cookie as CookieBase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cookie extends CookieBase {

  protected function getUserFromSession(SessionInterface $session) {
    if ($uid = $session->get('uid')) {
      // @todo Load the User entity in SessionHandler so we don't need queries.
      // @see https://www.drupal.org/node/2345611
      $values = $this->connection
        ->query('SELECT * FROM {users_field_data} u WHERE u.uid = :uid AND u.default_langcode = 1', [':uid' => $uid])
        ->fetchAssoc();

      // Check if the user data was found and the user is active.
      if (!empty($values) && $values['status'] == 1) {
        // Add the user's roles.
        $rids = $this->connection
          ->query('SELECT roles_target_id FROM {user__roles} WHERE entity_id = :uid', [':uid' => $values['uid']])
          ->fetchCol();
        $values['roles'] = array_merge([AccountInterface::AUTHENTICATED_ROLE], $rids);

        // Users with an employee person type always have the employee user role.
        $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
        /** @var \Drupal\person\Entity\PersonInterface $person */
        if ($person = $user->person->entity) {
          if ($person->getType()->isEmployee()) {
            $values['roles'][] = 'employee';
          }
        }

        return new UserSession($values);
      }
    }

    // This is an anonymous session.
    return NULL;
  }

}
