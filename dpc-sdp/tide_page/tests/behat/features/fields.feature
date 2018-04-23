Feature: Fields for Page content type

  Ensure that Page content has the expected fields.

  @api
  Scenario: The content type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create page content" permission
    When I visit "node/add/page"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I see field "Body"
    And I should see a "textarea#edit-body-0-value" element
    And I should see a "textarea#edit-body-0-value.required" element

    And I see field "Topic"
    And I should see an "input#edit-field-topic-0-target-id" element
    And I should see an "input#edit-field-topic-0-target-id.required" element

    And the "#edit-field-page-feature-image" element should contain "Feature Image"
    And I should see an "input#edit-field-page-feature-image-entity-browser-entity-browser-open-modal" element

    And I see field "Introduction Text"
    And I should see an "textarea#edit-field-page-intro-text-0-value" element
    And I should not see an "textarea#edit-field-page-intro-text-0-value.required" element

    And I see field "Show Content Rating?"
    And I should see an "input#edit-field-page-show-rating-value" element
    And I should not see an "input#edit-field-page-show-rating-value.required" element

    And I see field "Show Social Sharing?"
    And I should see an "input#edit-field-page-show-social-value" element
    And I should not see an "input#edit-field-page-show-social-value.required" element

    And I see field "Show Related Content?"
    And I should see an "input#edit-field-page-show-related-value" element
    And I should not see an "input#edit-field-page-show-related-value.required" element

  @api
  Scenario: The content type has the menu settings.
    Given I am logged in as a user with the "create page content, administer menu" permission
    When I visit "node/add/page"
    And I should see the text "Menu settings"
    And I see field "Provide a menu link"
