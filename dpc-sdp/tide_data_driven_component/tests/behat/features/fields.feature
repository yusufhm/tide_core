@vicgovau
Feature: Data Driven Component Field for the Landing Page content type

  Ensure that Landing Page content has the Data Driven Component field.

  @api @nosuggest
  Scenario: The Landing Page content type has the Data Driven Component fields (and labels where we can use them).
    Given I am logged in as a user with the "create landing_page content" permission
    When I visit "node/add/landing_page"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I should see text matching "Content components"
    And I should see "Data Driven Component" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element

  @api @suggest
  Scenario: The content type has the Data Driven Component fields (and labels where we can use them) including from suggested modules.
    Given I am logged in as a user with the "create landing_page content" permission
    When I visit "node/add/landing_page"
    And save screenshot
    Then I see field "Title"
    And I should see an "input#edit-title-0-value.required" element

    And I should see text matching "Content components"
    And I should see "Data Driven Component" in the "select[name='field_landing_page_component[add_more][add_more_select]']" element
