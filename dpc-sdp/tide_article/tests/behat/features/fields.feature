Feature: Fields for Article content type

  Ensure that Article content has the expected fields.

  @api
  Scenario: The content type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create article content" permission
    When I visit "node/add/article"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I see field "Body"
    And I should see a "textarea#edit-body-0-value" element
    And I should see a "textarea#edit-body-0-value.required" element

    And I see field "Topic"
    And I should see an "input#edit-field-topic-0-target-id" element
    And I should not see an "input#edit-field-topic-0-target-id.required" element

  @api
  Scenario: The content type has the menu settings.
    Given I am logged in as a user with the "create article content, administer menu" permission
    When I visit "node/add/article"
    And I should see the text "Menu settings"
    And I see field "Provide a menu link"
