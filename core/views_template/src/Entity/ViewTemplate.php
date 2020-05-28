<?php

namespace Drupal\views_template\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the View template entity.
 *
 * @ConfigEntityType(
 *   id = "view_template",
 *   label = @Translation("View template"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\views_template\ViewTemplateListBuilder",
 *     "form" = {
 *       "add" = "Drupal\views_template\Form\ViewTemplateForm",
 *       "edit" = "Drupal\views_template\Form\ViewTemplateForm",
 *       "delete" = "Drupal\views_template\Form\ViewTemplateDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\views_template\ViewTemplateHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "view_template",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/view_template/{view_template}",
 *     "add-form" = "/view_template/add",
 *     "edit-form" = "/view_template/{view_template}/edit",
 *     "delete-form" = "/view_template/{view_template}/delete",
 *     "collection" = "/view_template"
 *   }
 * )
 */
class ViewTemplate extends ConfigEntityBase implements ViewTemplateInterface {

  /**
   * The View template ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The View template label.
   *
   * @var string
   */
  protected $label;
  
  protected $view_id;
  
  protected $filters;
  
  protected $fields;
  
  protected $is_public;
  
  protected $user_id;
  
  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return $this->filters;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setFilters($filters) {
    $this->filters = $filters;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return $this->fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setFields($fields) {
    $this->fields = $fields;
  }
}
