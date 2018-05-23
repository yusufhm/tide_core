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

    And I see field "Tags"
    And I should see an "input#edit-field-tags-0-target-id" element
    And I should not see an "input#edit-field-tags-0-target-id.required" element

    And I see field "Topic"
    And I should see an "input#edit-field-topic-0-target-id" element
    And I should see an "input#edit-field-topic-0-target-id.required" element

    And the "#edit-field-featured-image" element should contain "Feature Image"
    And I should see an "input#edit-field-featured-image-entity-browser-entity-browser-open-modal" element

    And I see field "Introduction Text"
    And I should see an "textarea#edit-field-page-intro-text-0-value" element
    And I should not see an "textarea#edit-field-page-intro-text-0-value.required" element

    And I see field "Show Social Sharing?"
    And I should see an "input#edit-field-show-social-sharing-value" element
    And I should not see an "input#edit-field-show-social-sharing-value.required" element

    And I see field "Show Related Content?"
    And I should see an "input#edit-field-show-related-content-value" element
    And I should not see an "input#edit-field-show-related-content-value.required" element

    And I should see text matching "Related links"
    And I should see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-uri" element
    And I should not see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-uri.required" element
    And I should see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-title" element
    And I should not see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-title.required" element

    And I should see text matching "What's Next"
    And I should see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-uri" element
    And I should not see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-uri.required" element
    And I should see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-title" element
    And I should not see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-title.required" element
    And I should see a "textarea#edit-field-whats-next-0-subform-field-paragraph-summary-0-value" element

    And I see field "Show What's Next?"
    And I should see an "input#edit-field-show-whats-next-value" element
    And I should not see an "input#edit-field-show-whats-next-value.required" element

  @api
  Scenario: The content type has the menu settings.
    Given I am logged in as a user with the "create page content, administer menu" permission
    When I visit "node/add/page"
    And I should see the text "Menu settings"
    And I see field "Provide a menu link"
