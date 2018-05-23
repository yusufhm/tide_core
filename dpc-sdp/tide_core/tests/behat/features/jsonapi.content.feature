Feature: Page

  @api @tide
  Scenario: Request to "test" collection endpoint with no articles
    Given vocabulary "sites" with name "Sites" exists
    And sites terms:
      | name        | parent | tid   |
      | Test Site 1 | 0      | 10001 |

    Given I am an anonymous user
    When I send a GET request to "/api/v1/node/test?site=10001"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "jsonapi.version" should be equal to "1.0"
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "/api/v1/node/test"
    And the JSON node "meta.count" should exist
    And the JSON node "data" should exist

  @api @tide
  Scenario: Request to "test" collection endpoint with
    Given vocabulary "sites" with name "Sites" exists
    And sites terms:
      | name        | parent | tid   |
      | Test Site 1 | 0      | 10001 |

    Given test content:
        | title             | path             | body | moderation_state | uuid                                | field_node_site              |
        | [TEST] Test title | /test-page-alias | body | published        | 99999999-aaaa-bbbb-ccc-000000000000 | Test Site 1                  |

    Given I am an anonymous user

    When I send a GET request to "/api/v1/node/test?site=10001"
    Then the rest response status code should be 200
    And the response should be in JSON
    And the JSON node "jsonapi.version" should be equal to "1.0"
    And the JSON node "links" should exist
    And the JSON node "links.self" should contain "/api/v1/node/test"
    And the JSON node "meta.count" should exist
    And the JSON node "data" should exist
    And the JSON node "data[0].type" should be equal to "node--test"
    And the JSON node "data[0].id" should exist
    And the JSON node "data[0].attributes.title" should be equal to "[TEST] Test title"
    And the JSON node "data[0].attributes.body" should exist
    And the JSON node "data[0].attributes.metatag.title" should contain "[TEST] Test title"
    And the JSON node "data[0].attributes.metatag.description" should be equal to "body"
