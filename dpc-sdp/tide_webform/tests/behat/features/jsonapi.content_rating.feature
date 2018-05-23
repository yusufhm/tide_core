@tide @jsonapi
Feature: JSON API Webform submission

  Ensure that the Content Rating form receive submissions via API.

  Scenario: Send POST request to the Content Rating form
    When I send a POST request to "/api/v1/webform_submission/tide_webform_content_rating" with body:
        """
        {
          "data": {
            "type": "webform_submission",
            "attributes": {
                "webform_id": "tide_webform_content_rating",
                "data": {
                    "url": "/home",
                    "was_this_page_helpful": "Yes",
                    "comments": "TEST content rating comment"
                }
            }
          }
        }
        """
    Then the rest response status code should be 201
    And the response should be in JSON
    And the JSON node "data.type" should be equal to "webform_submission--tide_webform_content_rating"
    And the JSON node "data.id" should exist
    And the JSON node "data.attributes.uuid" should exist
