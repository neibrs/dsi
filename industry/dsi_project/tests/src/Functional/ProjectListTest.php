<?php


namespace Drupal\Tests\dsi_project\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_project
 */
class ProjectListTest extends ProjectTestBase{
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'view published project entities'
    ]);
  }

  /**
   * 检测列表
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testCasesList() {

    $this->drupalLogin($this->webUser);

    // 验证
    $assertSession = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.dsi_project.collection'));
    $assertSession->statusCodeEquals(200);

    //创建新数据
    $project = $this->createProject();
    // search full name 搜索验证
    $keywords = [
      'name' => $project->label(),
    ];
    $this->drupalPostForm('entity.dsi_project.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($project->label());
  }

}