<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Plugin\Block\SystemBrandingBlock;

/**
 * Provides a 'BrandSidebar' block.
 *
 * @Block(
 *  id = "brand_sidebar_block",
 *  admin_label = @Translation("Brand sidebar block"),
 * )
 */
class BrandSidebarBlock extends SystemBrandingBlock {

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration['use_site_logo_mini'] = TRUE;

    return $configuration;
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['block_branding']['use_site_logo_mini'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Site logo mini'),
      '#default_value' => $this->configuration['use_site_logo_mini'],
    ];
    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $block_branding = $form_state->getValue('block_branding');
    $this->configuration['use_site_logo_mini'] = $block_branding['use_site_logo_mini'];

    parent::blockSubmit($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    // TODO
    $build['site_logo_mini'] = [
      '#theme' => 'image',
      '#uri' => theme_get_setting('logo.mini.url'),
      '#alt' => $this->t('Home'),
      '#access' => $this->configuration['use_site_logo_mini'],
    ];

    return $build;
  }

}
