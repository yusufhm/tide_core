Feature: Page

  @api
  Scenario: Request to "page" collection endpoint with no articles
    Given I am an anonymous user
    When I send a GET request to "api/v1/page"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "jsonapi.version" should be equal to "1.0"
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/page"
    And the JSON node "meta.count" should exist
    And the JSON node "data" should exist

  @api
  Scenario: Request to "page" collection endpoint with
    Given page content:
      | title             | path             |
      | [TEST] Page title | /test-page-alias |
    And I am an anonymous user
    When I send a GET request to "api/v1/page?sort=-created"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "jsonapi.version" should be equal to "1.0"
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/page"
    And the JSON node "meta.count" should exist
    And the JSON node "data" should exist
    And the JSON node "data[0].type" should be equal to "page"
    And the JSON node "data[0].id" should exist
    And the JSON node "data[0].attributes.title" should be equal to "[TEST] Page title"

