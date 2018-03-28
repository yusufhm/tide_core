@tide
Feature: Fields for different Media types

  Ensure that all media types have the expected fields.

  @api
  Scenario: The Document media type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create media" permission
    When I visit "media/add/document"
    Then I see field "Name"
    And I should see an "input#edit-name-0-value.required" element

    And I should see "Document" in the "label[for=edit-field-media-file-0-upload]" element
    And I should not see an "label[for=edit-field-media-file-0-upload].required" element
    And I should see an "input#edit-field-media-file-0-upload" element

    And I should see "Allowed types: pdf doc docx xls xlsx xlsm csv txt ppt pptx dot dotm dotx." in the "#edit-field-media-file-0-upload--description" element
