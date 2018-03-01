Feature: Route lookup

  Scenario: Request to route lookup API to find a route by non-existing alias
    Given I am an anonymous user
    When I send a GET request to "api/v1/route?alias=/non-existing-alias"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data" should exist
    And the JSON node "data.alias" should be equal to "/non-existing-alias"
    And the JSON node "data.source" should be equal to "/non-existing-alias"
    And the JSON node "data.endpoint" should be equal to "null"
    And the JSON node "error" should not exist

  Scenario: Request to route lookup API to find a route by non-existing source
    Given I am an anonymous user
    When I send a GET request to "api/v1/route?source=/non-existing-source"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data" should exist
    And the JSON node "data.alias" should be equal to "/non-existing-source"
    And the JSON node "data.source" should be equal to "/non-existing-source"
    And the JSON node "data.endpoint" should be equal to "null"
    And the JSON node "error" should not exist

  Scenario: Request to route lookup API without either parameters specified.
    Given I am an anonymous user
    When I send a GET request to "api/v1/route"
    Then the rest response status code should be 400
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "error" should exist
    And the JSON node "error" should be equal to "URL query parameter "source" or "alias" is required."
    And the JSON node "data" should not exist

  Scenario: Request to route lookup API without too many parameters specified.
    Given I am an anonymous user
    When I send a GET request to "api/v1/route?source=/non-existing-source&alias=/non-existing-alias"
    Then the rest response status code should be 400
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "error" should exist
    And the JSON node "error" should be equal to "Only one of either "source" or "alias" query parameter is allowed."
    And the JSON node "data" should not exist

  @api
  Scenario: Request to route lookup API to find a route by existing alias
    Given page content:
      | title                | path                |
      | [TEST] Page title | /test-page-alias |
    And I am an anonymous user
    When I send a GET request to "api/v1/route?alias=/test-page-alias"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "api/v1/route"
    And the JSON node "data" should exist
    And the JSON node "data.alias" should be equal to "/test-page-alias"
    And the JSON node "data.source" should contain "/node/"
    And the JSON node "data.endpoint" should contain "api/v1/page/"
    And the JSON node "error" should not exist
