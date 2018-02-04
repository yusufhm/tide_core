<?php

/**
 * @file
 * Vicgovau Mink context for Behat testing.
 */

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class VicgovauMinkContext extends RawMinkContext {

  use TideResponseTrait;

  /**
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

  /**
   * @BeforeScenario
   */
  public function getMinkContext(BeforeScenarioScope $scope) {
    /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
    $environment = $scope->getEnvironment();
    $this->minkContext = $environment->getContext('Drupal\DrupalExtension\Context\MinkContext');
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
    $this->minkContext->getSession()->executeScript("CKEDITOR.instances[\"$field\"].setData(\"$value\");");
  }

}
