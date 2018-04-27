Feature: Route lookup

  @api
  Scenario: Request to route lookup API to find a route by existing alias
    And vocabulary "sites" with name "Sites" exists
    And sites terms:
      | name            | parent      | tid   |
      | Test Site 1     | 0           | 10001 |
      | Test Section 11 | Test Site 1 | 10011 |
      | Test Site 2     | 0           | 10002 |

    Given test content:
      | title                         | body | moderation_state | path                      | field_node_primary_site | field_node_site              |
      | [TEST] Article with no site   | test | published        | /test-api-article-no-site |                         |                              |
      | [TEST] Article with one site  | test | published        | /test-api-article-1-site  | Test Site 1             | Test Site 1, Test Section 11 |
      | [TEST] Article with two sites | test | published        | /test-api-article-2-sites | Test Site 2             | Test Site 2, Test Site 1     |

    And I am an anonymous user

    When I send a GET request to "api/v1/route?path=/test-api-article-no-site"
    Then the rest response status code should be 200
    And the JSON node "data" should exist
    And the JSON node "errors" should not exist
    And the response should be in JSON

    When I send a GET request to "api/v1/route?path=/test-api-article-1-site"
    Then the rest response status code should be 400
    And the response should be in JSON
    And the JSON node "data" should not exist
    And the JSON node "errors" should exist
    And the JSON array node "errors" should contain "URL query parameter "site" is required." element

    When I send a GET request to "api/v1/route?path=/test-api-article-1-site&site=10001"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should exist
    And the JSON node "data.section" should not contain "10001"
    And the JSON node "data.section" should contain "10011"
    And the JSON node "errors" should not exist

    When I send a GET request to "api/v1/route?path=/test-api-article-1-site&site=10011"
    Then the rest response status code should be 404
    And the response should be in JSON
    And the JSON node "data" should not exist
    And the JSON node "errors" should exist
    And the JSON array node "errors" should contain "Path not found." element

    When I send a GET request to "api/v1/route?path=/test-api-article-2-sites&site=10001"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should exist
    And the JSON node "data.section" should contain "10001"
    And the JSON node "errors" should not exist

    When I send a GET request to "api/v1/route?path=/test-api-article-2-sites&site=10002"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should exist
    And the JSON node "data.section" should contain "10002"
    And the JSON node "errors" should not exist
