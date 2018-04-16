Feature: Fields for Landing Page content type

  Ensure that Landing Page content has the expected fields.

  @api
  Scenario: The content type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create landing_page content" permission
    When I visit "node/add/landing_page"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I see field "Show Content Rating?"
    And I should see an "input#edit-field-landing-page-show-rating-value" element
    And I should not see an "input#edit-field-landing-page-show-rating-value.required" element

    And I see field "Show Social Sharing?"
    And I should see an "input#edit-field-landing-page-show-social-value" element
    And I should not see an "input#edit-field-landing-page-show-social-value.required" element

    And I see field "Introduction Text"
    And I should see an "textarea#edit-field-landing-page-intro-text-0-value" element
    And I should not see an "textarea#edit-field-landing-page-intro-text-0-value.required" element

    And I should see text matching "Banner"
    And I should see an "input#edit-field-landing-page-banner-top-0-subform-field-paragraph-title-0-value" element
    And I should not see an "input#edit-field-landing-page-banner-top-0-subform-field-paragraph-title-0-value.required" element
    And I should see an "input#edit-field-landing-page-banner-bottom-0-subform-field-paragraph-title-0-value" element
    And I should not see an "input#edit-field-landing-page-banner-bottom-0-subform-field-paragraph-title-0-value.required" element

    And I should see text matching "Key journey link"
    And I should see an "input#edit-field-landing-page-key-link-0-uri" element
    And I should not see an "input#edit-field-landing-page-key-link-0-uri.required" element
    And I should see an "input#edit-field-landing-page-key-link-0-title" element
    And I should not see an "input#edit-field-landing-page-key-link-0-title.required" element

    And I should see text matching "Key journey CTA"
    And I should see an "input#edit-field-landing-page-key-cta-0-uri" element
    And I should not see an "input#edit-field-landing-page-key-cta-0-uri.required" element
    And I should see an "input#edit-field-landing-page-key-cta-0-title" element
    And I should not see an "input#edit-field-landing-page-key-cta-0-title.required" element

    And I should see text matching "Popular links"
    And I should see an "input#edit-field-popular-links-0-subform-field-paragraph-link-0-uri" element
    And I should not see an "input#edit-field-popular-links-0-subform-field-paragraph-link-0-uri.required" element
    And I should see an "input#edit-field-popular-links-0-subform-field-paragraph-link-0-title" element
    And I should not see an "input#edit-field-popular-links-0-subform-field-paragraph-link-0-title.required" element

    And I should see text matching "Related links"
    And I should see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-uri" element
    And I should not see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-uri.required" element
    And I should see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-title" element
    And I should not see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-title.required" element

    And I should see text matching "Content components"
    And I should see text matching "No Banner added yet."
    And I should see an "input#field-landing-page-content-card-event-add-more" element
    And I should see an "input#field-landing-page-content-card-promotion-add-more" element

  @api
  Scenario: The content type has the menu settings.
    Given I am logged in as a user with the "create landing_page content, administer menu" permission
    When I visit "node/add/landing_page"
    And I should see the text "Menu settings"
    And I see field "Provide a menu link"
