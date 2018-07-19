<?php

/**
 * @file
 * MYSITE Drupal context for Behat testing.
 */

use Drupal\DrupalExtension\Context\DrupalContext;
use IntegratedExperts\BehatSteps\FieldTrait;
use IntegratedExperts\BehatSteps\PathTrait;
use IntegratedExperts\BehatSteps\ResponseTrait;
use IntegratedExperts\BehatSteps\D8\TaxonomyTrait;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends DrupalContext {

  use PathTrait;
  use FieldTrait;
  use TaxonomyTrait;
  use ResponseTrait;
  use TideCommonTrait;

}
