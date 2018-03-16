Feature: Site and Primary Site fields

  As a site administrator, I want to know that Site and Primary Site fields
  automatically added to new content types upon creation.

  @api
  Scenario: Site and Primary Site fields are added to content types upon creation.
    Given no "site_test" content type
    When I am logged in as a user with the "administer content types, administer node fields, administer node display, administer node form display" permission
    And I go to "admin/structure/types/add"
    Then the response status code should be 200

    When I fill in "Name" with "site_test"
    And I fill in "Machine-readable name" with "site_test"
    And I press "Save and manage fields"
    And I should see the text "Site" in the "field_site" row
    And I should see the text "Primary Site" in the "field_primary_site" row
    And I should see the following success messages:
      | success messages                                                          |
      | The content type site_test has been added.                                     |
      | Added field field_site to the site_test content type and form display.         |
      | Added field field_primary_site to the site_test content type and form display. |

    When I go to "admin/structure/types/manage/site_test/form-display"
    Then the response status code should be 200
    And the "#edit-fields-field-site-region option[selected='selected']" element should contain "Content"
    And the "#edit-fields-field-primary-site-region option[selected='selected']" element should contain "Content"

    When I go to "admin/structure/types/manage/site_test/display"
    Then the response status code should be 200
    And the "#edit-fields-field-site-region option[selected='selected']" element should contain "Disabled"
    And the "#edit-fields-field-primary-site-region option[selected='selected']" element should contain "Disabled"

    And no "site_test" content type

  @api
  Scenario: Site and Primary Site fields show terms only from specific depths.
    Given no "site_test" content type
    And vocabulary "sites" with name "Sites" exists
    And sites terms:
      | name                 | parent          |
      | Test Site 1          | 0               |
      | Test Section 11      | Test Site 1     |
      | Test Sub Section 111 | Test Section 11 |
      | Test Sub Section 112 | Test Section 11 |
      | Test Section 12      | Test Site 1     |
      | Test Site 2          | 0               |
      | Test Site 3          | 0               |

    When I am logged in as a user with the "administer content types, administer node fields, administer node display, administer node form display" permission
    And I go to "admin/structure/types/add"
    Then the response status code should be 200

    When I fill in "Name" with "site_test"
    And I fill in "Machine-readable name" with "site_test"
    And I press "Save and manage fields"
    And I should see the following success messages:
      | success messages                      |
      | The content type site_test has been added. |

    When I go to "node/add/site_test"
    And the response status code should be 200

    Then the "#edit-field-site" element should contain "Test Site 1"
    Then the "#edit-field-site" element should contain "Test Section 11"
    Then the "#edit-field-site" element should not contain "Test Sub Section 111"
    Then the "#edit-field-site" element should contain "Test Section 12"
    Then the "#edit-field-site" element should contain "Test Site 2"
    Then the "#edit-field-site" element should contain "Test Site 3"

    Then the "#edit-field-primary-site" element should contain "Test Site 1"
    Then the "#edit-field-primary-site" element should not contain "Test Section 11"
    Then the "#edit-field-primary-site" element should not contain "Test Sub Section 111"
    Then the "#edit-field-primary-site" element should not contain "Test Section 12"
    Then the "#edit-field-primary-site" element should contain "Test Site 2"
    Then the "#edit-field-primary-site" element should contain "Test Site 3"

    And no "site_test" content type
