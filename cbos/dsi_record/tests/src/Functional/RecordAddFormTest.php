<?php


namespace Drupal\Tests\dsi_record\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_record
 */
class RecordAddFormTest extends RecordTestBase {

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
      'add record entities'
    ]);
  }

  /**
   * 添加测试
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testRecordAdd() {
    //登录验证
    $this->drupalLogin($this->webUser);
    //断言回话
    $assertSession = $this->assertSession();
    //访问验证
    $this->drupalGet(Url::fromRoute('entity.dsi_record.collection'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists(t('Add'));
    //点击验证
    $this->clickLink(t('Add'));
    $assertSession->statusCodeEquals(200);
    //定义模拟添加数据
    $edit = [
      'name'=>$this->randomMachineName(),
      'detail'=>$this->randomMachineName(32),
      'status'=>rand(0,3),
      'effective_dates'=>time(),
      'take_time'=>time(),
      'created'=>time(),
      'changed'=>time(),
    ];
    //验证是否成功添加
    $this->drupalPostForm('entity.dsi_record.add_form', $edit, t('Save'));
    $assertSession->statusCodeEquals(200);

  }



}