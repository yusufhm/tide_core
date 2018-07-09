@tide
Feature: Check the Topic taxonomy

  Ensure Topic vocabulary exist.

  @api
  Scenario: Topic taxonomy exists
    Given vocabulary "topic" with name "Topic" exists
