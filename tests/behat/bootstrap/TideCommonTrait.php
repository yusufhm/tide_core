<?php

use Behat\Gherkin\Node\TableNode;
use Drupal\system\Entity\Menu;

/**
 * Trait TideTrait.
 */
trait TideCommonTrait {

  /**
   * @Then I am in the :path path
   */
  public function assertCurrentPath($path) {
    $current_path = $this->getSession()->getCurrentUrl();
    $current_path = parse_url($current_path, PHP_URL_PATH);
    $current_path = ltrim($current_path, '/');
    $current_path = $current_path == '' ? '<front>' : $current_path;

    if ($current_path != $path) {
      throw new \Exception(sprintf('Current path is "%s", but expected is "%s"', $current_path, $path));
    }
  }

  /**
   * Visit a page of specified content type and with specified title.
   *
   * @When I visit :type :title
   */
  public function visitContentTypePageWithTitle($type, $title) {
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'title' => $title,
        'type' => $type,
      ]);

    if (empty($nodes)) {
      throw new Exception(sprintf('Unable to find %s page "%s"', $type, $title));
    }

    ksort($nodes);

    $node = end($nodes);
    $path = $this->locatePath($node->toUrl()->getInternalPath());
    print $path;
    $this->getSession()->visit($path);
  }

  /**
   * Edit a page of specified content type and with specified title.
   *
   * @When I edit :type :title
   */
  public function editContentTypePageWithTitle($type, $title) {
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'title' => $title,
        'type' => $type,
      ]);

    if (empty($nodes)) {
      throw new Exception(sprintf('Unable to find %s page "%s"', $type, $title));
    }

    $node = current($nodes);
    $path = $this->locatePath('/node/' . $node->id()) . '/edit';
    print $path;
    $this->getSession()->visit($path);
  }

  /**
   * {@inheritdoc}
   */
  public function assertAuthenticatedByRole($role) {
    // Override parent assertion to allow using 'anonymous user' role without
    // actually creating a user with role. By default,
    // assertAuthenticatedByRole() will create a user with 'authenticated role'
    // even if 'anonymous user' role is provided.
    if ($role == 'anonymous user') {
      if (!empty($this->loggedInUser)) {
        $this->logout();
      }
    }
    else {
      $this->assertAuthenticatedByRole($role);
    }
  }

  /**
   * @Then I should see the link :text with :href in :locator
   */
  public function assertLinkTextHref($text, $href, $locator = NULL) {
    $page = $this->getSession()->getPage();
    if ($locator) {
      $element = $page->find('css', $locator);
      if (!$element) {
        throw new Exception(sprintf('Locator "%s" does not exist on the page', $locator));
      }
    }
    else {
      $element = $page;
    }

    $link = $element->findLink($text);
    if (!$link) {
      throw new \Exception(sprintf('The link "%s" is not found', $text));
    }

    if (!$link->hasAttribute('href')) {
      throw new \Exception('The link does not contain a href attribute');
    }

    $pattern = '/' . preg_quote($href, '/') . '/';
    // Support for simplified wildcard using '*'.
    $pattern = strpos($href, '*') !== FALSE ? str_replace('\*', '.*', $pattern) : $pattern;
    if (!preg_match($pattern, $link->getAttribute('href'))) {
      throw new \Exception(sprintf('The link href "%s" does not match the specified href "%s"', $link->getAttribute('href'), $href));
    }
  }

  /**
   * @Then I wait for :sec second(s)
   */
  public function waitForSeconds($sec) {
    sleep($sec);
  }

  /**
   * @Given no :type content type
   */
  public function removeContentType($type) {
    $content_type_entity = \Drupal::entityManager()->getStorage('node_type')->load($type);
    if ($content_type_entity) {
      $content_type_entity->delete();
    }
  }

  /**
   * @Given no :type media type
   */
  public function removeMediaType($type) {
    $type_entity = \Drupal::entityManager()->getStorage('media_type')->load($type);
    if ($type_entity) {
      $type_entity->delete();
    }
  }

  /**
   * @Given no menus:
   */
  public function removeMenus(TableNode $menusTable) {
    foreach ($menusTable->getColumn(0) as $name) {

      $menu = Menu::load($name);
      if ($menu) {
        $menu->delete();
      }
    }
  }

  /**
   * Change moderation state of a content with specified title.
   *
   * @When the moderation state of :type :title changes from :old_state to :new_state
   */
  public function moderateContentTypePageWithTitle($type, $title, $old_state, $new_state) {
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'title' => $title,
        'type' => $type,
      ]);

    if (empty($nodes)) {
      throw new Exception(sprintf('Unable to find %s page "%s"', $type, $title));
    }

    /** @var \Drupal\node\Entity\Node $node */
    $node = current($nodes);
    $current_old_state = $node->get('moderation_state')->first()->getString();
    if ($current_old_state != $old_state) {
      throw new Exception(sprintf('The current state "%s" is different from "%s"', $current_old_state, $old_state));
    }

    $node->set('moderation_state', $new_state);
    $node->save();
  }

  /**
   * Fills in form CKEditor field with specified id.
   *
   * Example: When I fill in CKEditor on field "edit-body-0-value" with "Test"
   * Example: And I fill in CKEditor on field "edit-body-0-value" with "Test"
   *
   * @When /^I fill in CKEditor on field "([^"]*)" with "([^"]*)"$/
   */
  public function fillCkEditorField($field, $value) {
    $this->getSession()->executeScript("CKEDITOR.instances[\"$field\"].setData(\"$value\");");
  }

}
