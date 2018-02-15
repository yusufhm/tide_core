Feature: Article

  @api
  Scenario: Request to "article" collection endpoint with no articles
    Given I am an anonymous user
    When I send a GET request to "api/v1/article"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "jsonapi.version" should be equal to "1.0"
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/article"
    And the JSON node "meta.count" should exist
    And the JSON node "data" should exist

  @api
  Scenario: Request to "article" collection endpoint with
    Given article content:
      | title                | path                |
      | [TEST] Article title | /test-article-alias |
    And I am an anonymous user
    When I send a GET request to "api/v1/article?sort=-created"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "jsonapi.version" should be equal to "1.0"
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/article"
    And the JSON node "meta.count" should exist
    And the JSON node "data" should exist
    And the JSON node "data[0].type" should be equal to "article"
    And the JSON node "data[0].id" should exist
    And the JSON node "data[0].attributes.title" should be equal to "[TEST] Article title"

