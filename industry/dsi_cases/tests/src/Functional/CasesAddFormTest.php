<?php


namespace Drupal\Tests\dsi_cases\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_cases
 */
class CasesAddFormTest extends CasesTestBase {

  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * 创建用户登录
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'add cases entities'
    ]);
  }

  /**
   * 添加测试
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testCasesAdd() {
    //登录验证
    $this->drupalLogin($this->webUser);
    //断言回话
    $assertSession = $this->assertSession();
    //访问验证
    $this->drupalGet(Url::fromRoute('entity.dsi_cases.collection'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists(t('Add'));
    //点击验证
    $this->clickLink(t('Add'));
    $assertSession->statusCodeEquals(200);
    //定义模拟添加数据
    $edit = [
      'name'=>$this->randomMachineName(),
      'case_category'=>rand(0,3),
      'case_procedure'=>$this->randomMachineName(32),
      'case_cause'=>$this->randomMachineName(32),
      'number'=> mt_rand(1000, 1000000),
      'delegate_datetime'=>time(),
      'subject_matter'=>mt_rand(100,100000),
      'person_in_charge'=>rand(1,100),
      'status'=>rand(0,3),
      'created'=>time(),
      'changed'=>time(),
    ];
    //验证是否成功添加
    $this->drupalPostForm('entity.dsi_cases.add_form', $edit, t('Save'));
    $assertSession->statusCodeEquals(200);

  }



}