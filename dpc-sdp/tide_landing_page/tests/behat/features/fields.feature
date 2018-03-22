Feature: Fields for Landing Page content type

  Ensure that Landing Page content has the expected fields.

  @api
  Scenario: The content type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create landing_page content" permission
    When I visit "node/add/landing_page"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I see field "Body"
    And I should see a "textarea#edit-field-landing-page-paragraph-0-subform-field-paragraph-body-0-value" element
    And I should see a "textarea#edit-field-landing-page-paragraph-0-subform-field-paragraph-body-0-value.required" element

    And the "#edit-field-landing-page-feature-image" element should contain "Feature Image"
    And I should see an "input#edit-field-landing-page-feature-image-0-target-id" element
    And I should not see an "input#edit-field-landing-page-feature-image-0-target-id.required" element

    And I see field "Introduction Text"
    And I should see an "textarea#edit-field-landing-page-intro-text-0-value" element
    And I should not see an "textarea#edit-field-landing-page-intro-text-0-value.required" element

    # And I see field "Components" - does not work for Paragraphs
    And I should see an "div#edit-field-landing-page-paragraph-0" element
    And I should see an "#field-landing-page-paragraph-values .form-required" element

    And I see field "Layout"
    And I should see an "select#edit-field-landing-page-layout" element
    And I should see an "select#edit-field-landing-page-layout.required" element

    And I see field "Show Content Rating?"
    And I should see an "input#edit-field-landing-page-show-rating-value" element
    And I should not see an "input#edit-field-landing-page-show-rating-value.required" element

    And I see field "Show Social Sharing?"
    And I should see an "input#edit-field-landing-page-show-social-value" element
    And I should not see an "input#edit-field-landing-page-show-social-value.required" element

  @api
  Scenario: The content type has the menu settings.
    Given I am logged in as a user with the "create landing_page content, administer menu" permission
    When I visit "node/add/landing_page"
    And I should see the text "Menu settings"
    And I see field "Provide a menu link"
