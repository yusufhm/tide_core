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
    And I should see an "label[for=edit-field-media-file-0-upload].form-required" element
    And I should see an "input#edit-field-media-file-0-upload" element

    And I should see "Allowed types: pdf doc docx xls xlsx xlsm csv txt ppt pptx dot dotm dotx." in the "#edit-field-media-file-0-upload--description" element

    And I should see "License" in the "label[for=edit-field-media-license]" element
    And I should see an "label[for=edit-field-media-license].form-required" element
    And I should see an "select#edit-field-media-license" element

    And I should see "Restricted" in the "label[for=edit-field-media-restricted-value]" element
    And I should not see an "label[for=edit-field-media-restricted-value].required" element
    And I should see an "input#edit-field-media-restricted-value" element

    And I should see text matching "Metadata"

    And I should see "Audience" in the "label[for=edit-field-media-audience-0-target-id]" element
    And I should not see an "label[for=edit-field-media-audience-0-target-id].required" element
    And I should see an "input#edit-field-media-audience-0-target-id" element

    And I should see "Department" in the "label[for=edit-field-media-department-0-target-id]" element
    And I should not see an "label[for=edit-field-media-department-0-target-id].required" element
    And I should see an "input#edit-field-media-department-0-target-id" element

    And I should see "Topic" in the "label[for=edit-field-media-topic-0-target-id]" element
    And I should not see an "label[for=edit-field-media-topic-0-target-id].required" element
    And I should see an "input#edit-field-media-topic-0-target-id" element

  @api
  Scenario: The Image media type has the expected fields (and labels where we can use them).
    Given I am logged in as a user with the "create media" permission
    When I visit "media/add/image"
    Then I see field "Name"
    And I should see an "input#edit-name-0-value.required" element

    And I should see "Image" in the "label[for=edit-field-media-image-0-upload]" element
    And I should see an "label[for=edit-field-media-image-0-upload].form-required" element
    And I should see an "input#edit-field-media-image-0-upload" element

    And I should see "Allowed types: png gif jpg jpeg." in the "#edit-field-media-image-0-upload--description" element

    And I should see "License" in the "label[for=edit-field-media-license]" element
    And I should see an "label[for=edit-field-media-license].form-required" element
    And I should see an "select#edit-field-media-license" element

    And I should see "Restricted" in the "label[for=edit-field-media-restricted-value]" element
    And I should not see an "label[for=edit-field-media-restricted-value].required" element
    And I should see an "input#edit-field-media-restricted-value" element

    And I should see text matching "Metadata"

    And I should see "Audience" in the "label[for=edit-field-media-audience-0-target-id]" element
    And I should not see an "label[for=edit-field-media-audience-0-target-id].required" element
    And I should see an "input#edit-field-media-audience-0-target-id" element

    And I should see "Department" in the "label[for=edit-field-media-department-0-target-id]" element
    And I should not see an "label[for=edit-field-media-department-0-target-id].required" element
    And I should see an "input#edit-field-media-department-0-target-id" element

    And I should see "Topic" in the "label[for=edit-field-media-topic-0-target-id]" element
    And I should not see an "label[for=edit-field-media-topic-0-target-id].required" element
    And I should see an "input#edit-field-media-topic-0-target-id" element