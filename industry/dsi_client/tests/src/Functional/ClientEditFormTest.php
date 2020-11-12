<?php


namespace Drupal\Tests\dsi_client\Functional;



use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_client
 */
class ClientEditFormTest extends ClientTestBase {
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
      'edit client entities'
    ]);
  }

  public function testClientEdit() {
    //登录验证
    $this->drupalLogin($this->webUser);
    //新建数据
    $client = $this->createClient();
    //断言会话
    $assertSession = $this->assertSession();
    //访问验证
    $this->drupalGet(Url::fromRoute('entity.dis_client.canonical',['dsi_client'=>$client->id()]));
    $assertSession->statusCodeEquals(200);
    //点击验证
    $this->clickLink(t('Save'));
    $assertSession->assertResponse(200);
    //修改数组
    $edit = [
      'name' => $this->randomMachineName(),
      'id' => $client->id()
    ];
    //修改验证
    $this->drupalPostForm(Url::fromRoute('entity.dsi_client.edit_form'),$edit,t('Save'));
    $assertSession->statusCodeEquals(200);

  }
}