<?php

namespace Drupal\entity_plus\Entity\Entity;

trait EntityViewDisplayTrait {

  /**
   * {@inheritdoc}
   */
  public function init() {
    // 为extra_fields设置third_party_settings.
    if ($this->mode !== static::CUSTOM_MODE) {
      $default_region = $this->getDefaultRegion();
      // Fill in defaults for extra fields.
      $context = $this->displayContext == 'view' ? 'display' : $this->displayContext;
      $extra_fields = \Drupal::entityManager()
        ->getExtraFields($this->targetEntityType, $this->bundle);
      $extra_fields = isset($extra_fields[$context]) ? $extra_fields[$context] : [];
      foreach ($extra_fields as $name => $definition) {
        if (!isset($this->content[$name]) && !isset($this->hidden[$name])) {
          // Extra fields are visible by default unless they explicitly say so.
          if (!isset($definition['visible']) || $definition['visible'] == TRUE) {
            $this->setComponent($name, $definition);
          }
          else {
            $this->removeComponent($name);
          }
        }
        // Ensure extra fields have a 'region'.
        if (isset($this->content[$name])) {
          $this->content[$name] += ['region' => $default_region];
        }
      }
    }

    parent::init();
  }

  /**
   * {@inheritdoc}
   */
  public function getHighestWeight() {
    $weights = [];

    // Collect weights for the components in the display.
    foreach ($this->content as $options) {
      if (isset($options['weight'])) {
        $weights[] = $options['weight'];
      }
    }

    // Let other modules feedback about their own additions.
    $weights = array_merge($weights, \Drupal::moduleHandler()->invokeAll('field_info_max_weight', [$this->targetEntityType, $this->bundle, $this->displayContext, $this->mode]));

    foreach($weights as $key => $value){
      if($value >= 1000){
        unset($weights[$key]);
      }
    }

    return $weights ? max($weights) : NULL;
  }

}