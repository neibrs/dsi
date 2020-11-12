<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Entity\ContentEntityForm as ContentEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

class ContentEntityForm extends ContentEntityFormBase {

  use EntityFormTrait;

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    // 设置删除按钮 url 的 language 为当前语言.
    if (isset($actions['delete'])) {
      $current_language = \Drupal::languageManager()->getCurrentLanguage();

      /** @var \Drupal\Core\Url $route_info */
      $route_info = $actions['delete']['#url'];
      $route_info->setOption('language', $current_language);

      $actions['delete']['#url'] = $route_info;
    }

    return $actions;
  }

}
