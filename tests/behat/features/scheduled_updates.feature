@tide
Feature: Scheduled updates

  As an Approver, I can schedule a time for content to be published or archived.

  @api
  Scenario: Approver schedule content to be published or archived.
    Given test content:
      | title                                       | body:value          | body:format | moderation_state | uid |
      | [TEST] Test article for scheduled published | [TEST] Test article | rich_text   | draft            | 1   |
      | [TEST] Test article for scheduled archived  | [TEST] Test article | rich_text   | published        | 1   |

    # Editors should not have access to Scheduled Updates.
    Given I am logged in as a user with the "editor" role
    When I edit test "[TEST] Test article for scheduled published"
    Then I should not see the text "Scheduled Publishing"
    And I should not see the text "Scheduled Archiving"

    # Approvers should have access to Scheduled Updates.
    Given I am logged in as a user with the "approver" role

    # Add a Scheduled Publishing for Needs Review content.
    When I edit test "[TEST] Test article for scheduled published"
    And I press the 'Add new Scheduled Publishing' button
    And I fill in "scheduled_publishing[form][inline_entity_form][update_timestamp][0][value][date]" with "2018-01-01"
    And I fill in "scheduled_publishing[form][inline_entity_form][update_timestamp][0][value][time]" with "00:00:00"
    And I press the 'Create Scheduled Publishing' button
    And I select "Needs Review" from "Change to"
    And I press the 'Save' button

    # Add a Scheduled Archiving for Published content.
    When I edit test "[TEST] Test article for scheduled archived"
    And I press the 'Add new Scheduled Archiving' button
    And I fill in "scheduled_archiving[form][inline_entity_form][update_timestamp][0][value][date]" with "2018-01-01"
    And I fill in "scheduled_archiving[form][inline_entity_form][update_timestamp][0][value][time]" with "00:00:00"
    And I press the 'Create Scheduled Archiving' button
    And I select "Published" from "Change to"
    And I press the 'Save' button

    Given I am logged in as a user with the "administrator" role
    And I visit "/admin/config/workflow/schedule-updates/run"
    And I press the "Run Updates" button
    And the cache has been cleared

    Given I am an anonymous user

    And I visit test "[TEST] Test article for scheduled published"
    And I should get a 200 HTTP response

    And I visit test "[TEST] Test article for scheduled archived"
    And I should not get a 200 HTTP response
