Feature: Sites menu autocreate

  As a site admin, I want to be able to automatically create and assign new
  Main and Footer menus to sites and sections on:
  - new site creation
  - new section creation
  - editing existing site
  - editing existing section

  Background:
    Given vocabulary "sites" with name "Sites" exists
    And no "sites" terms:
      | [TEST] Site name |
    And no menus:
      | main-menu-site-test-site-name                            |
      | main-menu-site-test-parent-site-name-test-section-name   |
      | footer-menu-site-test-site-name                          |
      | footer-menu-site-test-parent-site-name-test-section-name |

  @api
  Scenario: Menu autocreate - new and existing sites
    Given I am logged in as a user with the "administer taxonomy" permission
    And I go to "admin/structure/taxonomy/manage/sites/add"
    And I fill in "Name" with "[TEST] Site name"
    And I fill in "Domains" with "test.example.com"
    And I see field "Main menu"
    And I see field "autocreate_main_menu"
    And the "autocreate_main_menu" checkbox should be checked
    And I see field "Footer menu"
    And I see field "autocreate_footer_menu"
    And the "autocreate_footer_menu" checkbox should be checked

    # Assert auto-creation and association.
    When I press "Save"
    Then I should see the following success messages:
      | success messages                                                                             |
      | Created new term [TEST] Site name.                                                           |
      | Automatically created Main menu - [TEST] Site name menu and assigned to Main menu field.     |
      | Automatically created Footer menu - [TEST] Site name menu and assigned to Footer menu field. |
    When I click "[TEST] Site name"
    And I click "Edit"
    Then the "Name" field should contain "[TEST] Site name"
    And the "Domains" field should contain "test.example.com"
    And the "Main menu" field should contain "Main menu - [TEST] Site name (main-menu-site-test-site-name)"
    And I don't see field "autocreate_main_menu"
    And the "Footer menu" field should contain "Footer menu - [TEST] Site name (footer-menu-site-test-site-name)"
    And I don't see field "autocreate_footer_menu"

    # Assert that menus are not auto-created again.
    When I fill in "Domains" with "test2.example.com"
    And I press "Save"

    Then I should see the following success messages:
      | success messages               |
      | Updated term [TEST] Site name. |
    And I should not see the following success messages:
      | success messages                                                                             |
      | Automatically created Main menu - [TEST] Site name menu and assigned to Main menu field.     |
      | Automatically created Footer menu - [TEST] Site name menu and assigned to Footer menu field. |

    # Assert that menu association was not lost after editing term.
    And I click "Edit"
    Then the "Name" field should contain "[TEST] Site name"
    And the "Domains" field should contain "test2.example.com"
    And the "Main menu" field should contain "Main menu - [TEST] Site name (main-menu-site-test-site-name)"
    And I don't see field "autocreate_main_menu"
    And the "Footer menu" field should contain "Footer menu - [TEST] Site name (footer-menu-site-test-site-name)"
    And I don't see field "autocreate_footer_menu"

    # Cleanup. Doing this manually since entities were created through UI.
    And no "sites" terms:
      | [TEST] Site name |
    And no menus:
      | main-menu-site-test-site-name                            |
      | main-menu-site-test-parent-site-name-test-section-name   |
      | footer-menu-site-test-site-name                          |
      | footer-menu-site-test-parent-site-name-test-section-name |

  @api
  Scenario: Menu autocreate - new and existing sections
    Given "sites" terms:
      | name                    | field_site_slogan:value | field_site_footer_text:value | field_site_domains |
      | [TEST] Parent site name | Parent test site slogan | parent test site footer      | www.example.com    |
    And I am logged in as a user with the "administer taxonomy" permission
    And I go to "admin/structure/taxonomy/manage/sites/add"
    And I fill in "Name" with "[TEST] Section name"
    And I fill in "Domains" with "test.example.com"
    And I select "[TEST] Parent site name" from "Parent terms"
    And I see field "Main menu"
    And I see field "autocreate_main_menu"
    # Since term does not have parents during creation, the state is default.
    And the "autocreate_main_menu" checkbox should be checked
    And I see field "Footer menu"
    And I see field "autocreate_footer_menu"
    And the "autocreate_footer_menu" checkbox should be checked

    # Assert auto-creation and association. Footer menu will not be created just
    # yet - it will be created after the term is edited.
    When I uncheck "autocreate_footer_menu"
    And I press "Save"
    Then I should see the following success messages:
      | success messages                                                                                                      |
      | Created new term [TEST] Section name.                                                                                 |
      | Automatically created Main menu - [TEST] Parent site name - [TEST] Section name menu and assigned to Main menu field. |
    Then I should not see the following success messages:
      | success messages                                                                                                          |
      | Automatically created Footer menu - [TEST] Parent site name - [TEST] Section name menu and assigned to Footer menu field. |
    When I click "[TEST] Section name"
    And I click "Edit"
    Then the "Name" field should contain "[TEST] Section name"
    And the "Domains" field should contain "test.example.com"
    And the "Main menu" field should contain "Main menu - [TEST] Parent site name - [TEST] Section name (main-menu-site-test-parent-site-name-test-section-name)"
    And I don't see field "autocreate_main_menu"
    And the "Footer menu" field should not contain "Footer menu - [TEST] Parent site name - [TEST] Section name (footer-menu-site-test-parent-site-name-test-section-name)"
    And I see field "autocreate_footer_menu"
    # Assert that for sections menus are not set to be created by default.
    And the "autocreate_footer_menu" checkbox should not be checked

    # Assert that follow-up menu creation works\.
    When I check "autocreate_footer_menu"
    And I press "Save"

    Then I should see the following success messages:
      | success messages                                                                                                          |
      | Updated term [TEST] Section name.                                                                                         |
      | Automatically created Footer menu - [TEST] Parent site name - [TEST] Section name menu and assigned to Footer menu field. |
    And I should not see the following success messages:
      | success messages                                                                                                      |
      | Automatically created Main menu - [TEST] Parent site name - [TEST] Section name menu and assigned to Main menu field. |

    # Assert that menu association was not lost after editing term.
    And I click "Edit"
    Then the "Name" field should contain "[TEST] Section name"
    And the "Domains" field should contain "test.example.com"
    And the "Main menu" field should contain "Main menu - [TEST] Parent site name - [TEST] Section name (main-menu-site-test-parent-site-name-test-section-name)"
    And I don't see field "autocreate_main_menu"
    And the "Footer menu" field should contain "Footer menu - [TEST] Parent site name - [TEST] Section name (footer-menu-site-test-parent-site-name-test-section-name)"
    And I don't see field "autocreate_footer_menu"

    # Cleanup. Doing this manually since entities were created through UI.
    And no "sites" terms:
      | [TEST] Site name |
    And no menus:
      | main-menu-site-test-site-name                            |
      | main-menu-site-test-parent-site-name-test-section-name   |
      | footer-menu-site-test-site-name                          |
      | footer-menu-site-test-parent-site-name-test-section-name |
