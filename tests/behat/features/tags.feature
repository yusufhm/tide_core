@tide
Feature: Check the Tags taxonomy

  Ensure Tags vocabulary exist.

  @api
  Scenario: Tags taxonomy exists
    Given vocabulary "tags" with name "Tags" exists
