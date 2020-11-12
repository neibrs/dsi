<?php

namespace Drupal\materialize\Plugin\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\materialize\Utility\Element;

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @ingroup plugins_form
 *
 * @MaterializeForm("user_login_form")
 */
class UserLoginForm extends FormBase {

  public function alterFormElement(Element $form, FormStateInterface $form_state, $form_id = NULL) {
    // 修改登录页面输入框样式.
    $form->getArray()['login'] = [
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
      '#type' => 'item',
      '#markup' => '<h5 class="ml-4">' . t('Sign in') . '</h5>',
      '#weight' => -10,
    ];
    $form->getArray()['#attributes']['class'][] = 'login-form';

    unset($form->getArray()['name']['#description']);
    unset($form->getArray()['pass']['#description']);
    $form->getArray()['name']['#prefix'] = '<div class="row margin">';
    $form->getArray()['name']['#suffix'] = '</div>';
    $form->getArray()['name']['#field_prefix'] = '<i class="material-icons prefix pt-2">person_outline</i>';
    $form->getArray()['name']['#title_display'] = 'after';
    $form->getArray()['name']['#wrapper_attributes']['class'][] = 'input-field col s12';

    $form->getArray()['pass']['#prefix'] = '<div class="row margin">';
    $form->getArray()['pass']['#suffix'] = '</div>';
    $form->getArray()['pass']['#field_prefix'] = '<i class="material-icons prefix pt-2">lock_outline</i>';
    $form->getArray()['pass']['#title_display'] = 'after';
    $form->getArray()['pass']['#wrapper_attributes']['class'][] = 'input-field col s12';

    $form->getArray()['persistent_login']['#prefix'] = '<div class="row">';
    $form->getArray()['persistent_login']['#suffix'] = '</div>';
    $form->getArray()['persistent_login']['#wrapper_attributes']['class'][] = 'm12 l12 ml-2 mt-1 input-field col s12';

    $form->getArray()['actions']['#attributes']['class'][] = 'input-field s12 col';
    $form->getArray()['actions']['submit']['#attributes']['class'] = ['btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12'];
    $form->getArray()['register_forgot'] = [
      '#type' => 'container',
      '#weight' => 900,
      '#attributes' => [
        'class' => 'row',
      ],
    ];
    $form->getArray()['register_forgot']['register'] = [
      '#type' => 'item',
      '#markup' => '<p class="margin medium-small">' . Link::createFromRoute('Register Now!', 'user.register')->toString() . '</p>',
      '#wrapper_attributes' => [
        'class' => [
          'input-field',
          'col',
          's6',
        ],
      ],
    ];
    $form->getArray()['register_forgot']['forgot'] = [
      '#type' => 'item',
      '#markup' => '<p class="margin right-align medium-small">' . Link::createFromRoute('Forgot password?', 'user.pass')->toString() . '</p>',
      '#wrapper_attributes' => [
        'class' => [
          'input-field',
          'col',
          's6',
        ],
      ],
    ];
  }

}
