<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;
use Drupal\Tests\location\Traits\LocationTestTraits;

/**
 * Simple test for organization list.
 *
 * @group organization
 */
class OrganizationListTest extends OrganizationTestBase {

  use LocationTestTraits;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['organization_manager', 'employee_assignment', 'views_template'];

  public function testList() {
    $organization = $this->createOrganization();

    $user = $this->drupalCreateUser([
      'view organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.organization.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists($organization->label());

    $location = $this->createLocation(['name' => 'CHONGQING']);
    $settings = [
      'name' => 'CHONGQING',
      'location' => $location->id(),
    ];
    $this->createOrganization($settings);

    // search full name
    $keywords = [
      'combine' => $settings['name'],
    ];
    $this->drupalPostForm(NULL, $keywords, t('Search'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('health branch'));

    $keywords = [
      'combine' => 'CHONGQING',
    ];
    $this->drupalPostForm(NULL, $keywords, t('Search'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('health branch'));
  }

  /**
   * 测试栏目设置：添加在职人数后不报错.
   */
  public function testFieldSetting() {
    $this->drupalPlaceBlock('view_template_block');

    // 添加组织，让列表有数据.
    $this->createOrganization();

    $user = $this->drupalCreateUser([
      'view organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    // 栏目设置：添加在职人数字段
    $this->drupalGet(Url::fromRoute('entity.organization.collection'));
    $this->clickLink(t('Fields setting'));
    $this->clickLink(t('Holder count'));
    $this->clickLink(t('Save'));
    $values = [
      'fields[organization_employee_assignment_statistics_holder_count][options]' => '{&quot;id&quot;:&quot;organization_employee_assignment_statistics_holder_count&quot;,&quot;table&quot;:&quot;organization_employee_assignment_statistics&quot;,&quot;field&quot;:&quot;holder_count&quot;,&quot;entity_type&quot;:null,&quot;entity_field&quot;:null,&quot;plugin_id&quot;:&quot;numeric&quot;,&quot;label&quot;:&quot;Holder count&quot;,&quot;exclude&quot;:false,&quot;settings&quot;:{&quot;link_to_entity&quot;:false},&quot;element_default_classes&quot;:true,&quot;alter&quot;:{&quot;alter_text&quot;:false,&quot;make_link&quot;:false,&quot;path&quot;:&quot;&quot;,&quot;path_case&quot;:&quot;none&quot;,&quot;link_class&quot;:&quot;&quot;,&quot;alt&quot;:&quot;&quot;,&quot;rel&quot;:&quot;&quot;,&quot;prefix&quot;:&quot;&quot;,&quot;target&quot;:&quot;&quot;,&quot;trim&quot;:false,&quot;max_length&quot;:&quot;0&quot;,&quot;word_boundary&quot;:true,&quot;ellipsis&quot;:true,&quot;more_link&quot;:false,&quot;more_link_text&quot;:&quot;&quot;,&quot;more_link_path&quot;:&quot;&quot;,&quot;preserve_tags&quot;:&quot;&quot;},&quot;empty&quot;:&quot;&quot;,&quot;hide_empty&quot;:false,&quot;empty_zero&quot;:false,&quot;hide_alter_empty&quot;:true}',
    ];
    $this->drupalPostForm(NULL, $values, t('Save configuration'));

    $this->drupalGet(Url::fromRoute('entity.organization.collection'));
    $assert_session->statusCodeEquals(200);
  }

}
