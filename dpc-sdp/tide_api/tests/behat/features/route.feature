Feature: Route lookup

  @api
  Scenario: Request to route lookup API to find a route by non-existing alias
    Given I am an anonymous user
    When I send a GET request to "api/v1/route?path=/test-non-existing-alias"
    Then the rest response status code should be 404
    And the response should be in JSON
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data" should not exist
    And the JSON node "errors" should exist
    And the JSON array node "errors" should contain "Path not found." element

  Scenario: Request to route lookup API without a parameter specified.
    Given I am an anonymous user
    When I send a GET request to "api/v1/route"
    Then the rest response status code should be 400
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data" should not exist
    And the JSON node "errors" should exist
    And the JSON array node "errors" should contain "URL query parameter "path" is required." element

  @api
  Scenario: Request to route lookup API to find a route by existing alias
    Given test content:
      | title                    | body | moderation_state | path                        |
      | [TEST] Draft article     | test | draft            | /test-api-draft-article     |
      | [TEST] Published article | test | published        | /test-api-published-article |
    And I am an anonymous user

    # Anonymous users should not have access to unpublished nodes.
    When I send a GET request to "api/v1/route?path=/test-api-draft-article"
    Then the rest response status code should be 403
    And the response should be in JSON
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data" should not exist
    And the JSON node "errors" should exist
    And the JSON array node "errors" should contain "Permission denied." element

    # Anonymous users should have access to unpublished nodes.
    When I send a GET request to "api/v1/route?path=/test-api-published-article"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data.bundle" should contain "test"
    And the JSON node "data.endpoint" should contain "api/v1/node/test/"
    And the JSON node "errors" should not exist

    # A published node is unpublished, and anonymous users no longer have access to it.
    # An unpublished node is published, and anonymous user can view it.
    When I am logged in as a user with the Approver role
    And I edit test "[TEST] Published article"
    Then the response status code should be 200
    And I select "Archived" from "Change to"
    And I press "Save"

    And I edit test "[TEST] Draft article"
    Then the response status code should be 200
    And I select "Published" from "Change to"
    And I press "Save"

    When I am an anonymous user
    Then I send a GET request to "api/v1/route?path=/test-api-published-article"
    Then the rest response status code should be 403
    And the JSON node "errors" should exist
    And the JSON array node "errors" should contain "Permission denied." element

    Then I send a GET request to "api/v1/route?path=/test-api-draft-article"
    Then the rest response status code should be 200
    And the JSON node "data" should exist
    And the JSON node "errors" should not exist
