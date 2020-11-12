<?php

namespace Drupal\Tests\entity_plus\Functional;

use Drupal\Core\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group entity_plus
 */
class EntityCanonicalLocalActionTest extends EntityPlusTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'node'];

  /**
   * Tests that the home page loads with a 200 response.
   */
  public function testLocalAction() {
    $user = $this->drupalCreateUser([
      'bypass node access',
    ]);
    $this->drupalLogin($user);

    $assert_sesstion = $this->assertSession();

    $node = $this->createNode();

    $this->drupalGet(Url::fromRoute('entity.node.edit_form', [
      'node' => $node->id(),
    ]));
    $assert_sesstion->statusCodeEquals(200);
    $assert_sesstion->linkExists(t('List'));
  }

}
