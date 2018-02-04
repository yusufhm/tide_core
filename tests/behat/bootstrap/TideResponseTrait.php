<?php

use Behat\Mink\Exception\ExpectationException;

/**
 * Trait TideResponseTrait.
 */
trait TideResponseTrait {

  /**
   * @Then response contains header :name
   */
  public function assertResponseContainsHeader($name) {
    $header = $this->getSession()->getResponseHeader($name);

    if (!$header) {
      throw new ExpectationException(sprintf('Response does not contain header %s', $name), $this->getSession()->getDriver());
    }
  }

  /**
   * @Then response does not contain header :name
   */
  public function assertResponseNotContainsHeader($name) {
    $header = $this->getSession()->getResponseHeader($name);

    if ($header) {
      throw new ExpectationException(sprintf('Response contains header %s, but should not', $name), $this->getSession()->getDriver());
    }
  }

  /**
   * @Then response header :name contains :value
   */
  public function assertResponseHeaderContains($name, $value) {
    $this->assertResponseContainsHeader($name);
    $this->assertSession()->responseHeaderContains($name, $value);
  }

  /**
   * @Then response header :name does not contain :value
   */
  public function assertResponseHeaderNotContains($name, $value) {
    $this->assertResponseContainsHeader($name);
    $this->assertSession()->responseHeaderNotContains($name, $value);
  }

}
