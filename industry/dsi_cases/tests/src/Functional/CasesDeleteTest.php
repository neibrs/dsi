<?php


namespace Drupal\Tests\dsi_cases\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_cases
 */
class CasesDeleteTest  extends CasesTestBase {

  /**
   * A normal logged in user.
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  //赋予登录用户删除权限
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'delete cases entities'
    ]);
  }

  public function testCasesDelete() {
    //新建数据
    $cases = $this->createCases();
    //验证登录
    $this->drupalLogin($this->webUser);
    //断言会话
    $assertSession = $this->assertSession();
    //访问验证
    $this->drupalGet(Url::fromRoute('entity.dsi_cases.delete_form',['dsi_cases'=>$cases->id()]));
    $assertSession->statusCodeEquals(200);
    //点击验证
    $this->clickLink(t('Delete'));
    $assertSession->statusCodeEquals(200);




  }

}