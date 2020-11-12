<?php


namespace Drupal\Tests\dsi_client\Functional;

use Composer\Util\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_client
 */
class ClientListTest extends ClientTestBase {

  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'add client entities'
    ]);
  }

  public function testClientList(){
    $this->drupalLogin($this->webUser);

    $assertSession = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.dsi_client.collection'));
    $assertSession->statusCodeEquals(200);

    $client = $this->createClient();

    // search full name 搜索验证
    $keywords = [
      'name' => $client->label(),
    ];
    $this->drupalPostForm('entity.dsi_client.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($client->label());
  }


}