<?php


namespace Drupal\Tests\dsi_client\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_client
 */
class ClientDeleteTest  extends ClientTestBase {

  /**
   * A normal logged in user.
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  //赋予登录用户删除权限
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'delete client entities'
    ]);
  }

  public function testClientDelete() {
    //新建数据
    $client = $this->createClient();
    //验证登录
    $this->drupalLogin($this->webUser);
    //断言会话
    $assertSession = $this->assertSession();
    //访问验证
    $this->drupalGet(Url::fromRoute('entity.dsi_client.delete_form',['dsi_client'=>$client->id()]));
    $assertSession->statusCodeEquals(200);
    //点击验证
    $this->clickLink(t('Delete'));
    $assertSession->statusCodeEquals(200);




  }

}