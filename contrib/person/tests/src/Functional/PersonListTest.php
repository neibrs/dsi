<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;

/**
 * Simple test for person list.
 *
 * @group person
 */
class PersonListTest extends PersonTestBase {

  /**
   * Tests person list.
   */
  public function testList() {
    $person = $this->createPerson();

    $user = $this->drupalCreateUser([
      'view persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.person.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists($person->getName());

    $this->clickLink(t('Add'));
    $this->clickLink(t('Employee'));

    $edit = [
      'name[0][value]' => '汪维霞',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertSession()->statusCodeEquals(200);

    $keywords = [
      'combine' => '汪维霞',
    ];
    $this->drupalPostForm(NULL, $keywords, t('Search'));
    $this->assertSession()->linkExists($edit['name[0][value]']);

    $keywords = [
      'combine' => 'wwx',
    ];
    $this->drupalPostForm(NULL, $keywords, t('Search'));
    $this->assertSession()->linkExists($edit['name[0][value]']);

    $keywords = [
      'combine' => '汪',
    ];
    $this->drupalPostForm(NULL, $keywords, t('Search'));
    $this->assertSession()->linkExists($edit['name[0][value]']);

  }

}
