Feature: Fields for Landing Page content type

  Ensure that Landing Page content has the expected fields.

  @api
  Scenario: The content type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create landing_page content" permission
    When I visit "node/add/landing_page"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I see field "Show Social Sharing?"
    And I should see an "input#edit-field-show-social-sharing-value" element
    And I should not see an "input#edit-field-show-social-sharing-value.required" element

    And I see field "Introduction Text"
    And I should see an "textarea#edit-field-landing-page-intro-text-0-value" element
    And I should not see an "textarea#edit-field-landing-page-intro-text-0-value.required" element

    And I should see text matching "Campaign Primary"
    And I should see an "input#edit-field-landing-page-c-primary-0-target-id" element
    And I should not see an "input#edit-field-landing-page-c-primary-0-target-id.required" element

    And I should see text matching "Campaign Secondary"
    And I should see an "input#edit-field-landing-page-c-secondary-0-target-id" element
    And I should not see an "input#edit-field-landing-page-c-secondary-0-target-id.required" element

    And I should see text matching "Key journeys"
    And I should see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-title-0-value" element
    And I should see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-title-0-value.required" element
    And I should see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-links-0-uri" element
    And I should not see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-links-0-uri.required" element
    And I should see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-links-0-title" element
    And I should not see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-links-0-title.required" element
    And I should see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-cta-0-uri" element
    And I should not see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-cta-0-uri.required" element
    And I should see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-cta-0-title" element
    And I should not see an "input#edit-field-landing-page-key-journeys-0-subform-field-paragraph-cta-0-title.required" element

    And I see field "Show Related Content?"
    And I should see an "input#edit-field-show-related-content-value" element
    And I should not see an "input#edit-field-show-related-content-value.required" element

    And I should see text matching "Related links"
    And I should see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-uri" element
    And I should not see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-uri.required" element
    And I should see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-title" element
    And I should not see an "input#edit-field-related-links-0-subform-field-paragraph-link-0-title.required" element

    And I should see text matching "Content components"
    And I should see text matching "No Component added yet."
    And I should see "Basic Text" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Accordion" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Card Event" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Promotion" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Navigation featured" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Navigation featured Automated" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Navigation" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Navigation Automated" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
    And I should see "Key dates" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element

    And I see field "Tags"
    And I should see an "input#edit-field-tags-0-target-id" element
    And I should not see an "input#edit-field-tags-0-target-id.required" element

    And I see field "Topic"
    And I should see an "input#edit-field-topic-0-target-id" element
    And I should see an "input#edit-field-topic-0-target-id.required" element

    And I should see text matching "What's Next"
    And I should see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-uri" element
    And I should not see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-uri.required" element
    And I should see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-title" element
    And I should not see an "input#edit-field-whats-next-0-subform-field-paragraph-link-0-title.required" element
    And I should see a "textarea#edit-field-whats-next-0-subform-field-paragraph-summary-0-value" element

    And I see field "Show What's Next?"
    And I should see an "input#edit-field-show-whats-next-value" element
    And I should not see an "input#edit-field-show-whats-next-value.required" element

    And I should see text matching "Contact Us"
    And I should see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-title-0-value" element
    And I should not see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-title-0-value.required" element
    And I should see an "textarea#edit-field-landing-page-contact-0-subform-field-paragraph-body-0-value" element
    And I should not see an "textarea#edit-field-landing-page-contact-0-subform-field-paragraph-body-0-value.required" element
    And I should see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-name-0-value" element
    And I should not see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-name-0-value.required" element
    And I should see text matching "Address"
    And I should see a "select#edit-field-landing-page-contact-0-subform-field-paragraph-location-0-address-country-code--2" element
    And I should see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-email-0-value" element
    And I should not see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-email-0-value.required" element
    And I should see text matching "Phones"
    And I should see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-phones-0-subform-field-paragraph-phone-title-0-value" element
    And I should not see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-phones-0-subform-field-paragraph-phone-title-0-value.required" element
    And I should see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-phones-0-subform-field-paragraph-phone-number-0-value" element
    And I should not see an "input#edit-field-landing-page-contact-0-subform-field-paragraph-phones-0-subform-field-paragraph-phone-number-0-value.required" element

    And I see field "Show Contact Us?"
    And I should see an "input#edit-field-landing-page-show-contact-value" element
    And I should not see an "input#edit-field-landing-page-show-contact-value.required" element

  @api
  Scenario: The content type has the menu settings.
    Given I am logged in as a user with the "create landing_page content, administer menu" permission
    When I visit "node/add/landing_page"
    And I should see the text "Menu settings"
    And I see field "Provide a menu link"
