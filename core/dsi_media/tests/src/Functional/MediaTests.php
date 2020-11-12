<?php

namespace Drupal\Tests\dsi_media\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Dsi Media tests.
 *
 * @group dsi_media
 */
class DsiMediaTests extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'metatag',
    'metatag_views',
    'better_exposed_filters',
    'dsi_media',
    'dsi_media_instagram',
    'dsi_media_twitter',
  ];

  /**
   * Specify the theme to be used in testing.
   *
   * @var string
   */
  protected $defaultTheme = 'bartik';

  /**
   * The profile to install as a basis for testing.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalLogin($this->rootUser);
  }

  /**
   * Check Dsi Media Types.
   */
  public function testCheckDsiMediaTypesPage() {
    $assert_session = $this->assertSession();

    $this->drupalGet('/admin/structure/media');

    $assert_session->pageTextContains($this->t('Image'));
    $assert_session->pageTextContains($this->t('Remote video'));
    $assert_session->pageTextContains($this->t('Video'));
    $assert_session->pageTextContains($this->t('Gallery'));
    $assert_session->pageTextContains($this->t('Instagram'));
    $assert_session->pageTextContains($this->t('Tweet'));
    $assert_session->pageTextContains($this->t('Document'));

  }

  /**
   * Check Dsi Media settings page.
   */
  public function testCheckDsiMediaSettings() {
    $assert_session = $this->assertSession();

    $this->drupalGet('/admin/people/permissions#module-dsi_media');
    $assert_session->pageTextContains($this->t('Administer Dsi Media settings'));
  }

  /**
   * Check permissions to Administer Dsi Media settings.
   */
  public function testCheckDsiMediaSettingsPermissions() {
    $assert_session = $this->assertSession();

    $this->drupalGet('/admin/people/permissions');
    $assert_session->pageTextContains($this->t('Administer Dsi Media settings'));
  }

}
